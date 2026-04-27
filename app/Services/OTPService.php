<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Jobs\SendOtpJob;

class OTPService
{
    /**
     * Generate and send OTP.
     */
    public function generateOtp(string $identifier): void
    {
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        
        $user = User::where($field, $identifier)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => ['User not found.'],
            ]);
        }

        // Rate limiting / Fraud prevention
        $recentOtp = Otp::where('identifier', $identifier)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($recentOtp) {
            throw ValidationException::withMessages([
                'identifier' => ['Please wait before requesting a new OTP.'],
            ]);
        }

        // Generate OTP
        $code = rand(100000, 999999);
        // For testing/development, you might want to log it or use a static OTP

        Otp::updateOrCreate(
            ['identifier' => $identifier],
            [
                'code' => bcrypt($code),
                'expires_at' => now()->addMinutes(config('auth.otp_expiry', 5)),
                'attempts' => 0,
            ]
        );

        // Dispatch Job to send SMS/WhatsApp/Email
        SendOtpJob::dispatch($identifier, $code);
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(string $identifier, string $code): User
    {
        $otpRecord = Otp::where('identifier', $identifier)->first();

        if (!$otpRecord || now()->greaterThan($otpRecord->expires_at)) {
            throw ValidationException::withMessages([
                'code' => ['OTP has expired or is invalid.'],
            ]);
        }

        if ($otpRecord->attempts >= 3) {
            $otpRecord->delete();
            throw ValidationException::withMessages([
                'code' => ['Too many failed attempts. Please request a new OTP.'],
            ]);
        }

        if (!\Hash::check($code, $otpRecord->code)) {
            $otpRecord->increment('attempts');
            throw ValidationException::withMessages([
                'code' => ['Invalid OTP code.'],
            ]);
        }

        // OTP is valid
        $otpRecord->delete();

        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        return User::where($field, $identifier)->firstOrFail();
    }
}
