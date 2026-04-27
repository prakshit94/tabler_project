<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginLog;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    /**
     * Authenticate user with email/mobile and password.
     */
    public function loginWithPassword(string $identifier, string $password, array $deviceInfo = []): array
    {
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $user = User::where($field, $identifier)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Invalid credentials provided.'],
            ]);
        }

        $this->checkUserStatus($user);

        return $this->createSession($user, $deviceInfo);
    }

    /**
     * Issue Sanctum token and log the session/device.
     */
    public function createSession(User $user, array $deviceInfo = []): array
    {
        $tokenName = $deviceInfo['device_name'] ?? 'web';
        $token = $user->createToken($tokenName);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Log device
        if (!empty($deviceInfo)) {
            UserDevice::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'ip_address' => $deviceInfo['ip'],
                    'user_agent' => $deviceInfo['user_agent'],
                ],
                [
                    'last_used_at' => now(),
                    'browser' => $deviceInfo['browser'] ?? null,
                    'os' => $deviceInfo['os'] ?? null,
                ]
            );
        }

        // Audit Log
        LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $deviceInfo['ip'] ?? null,
            'user_agent' => $deviceInfo['user_agent'] ?? null,
            'status' => 'success',
            'login_at' => now(),
        ]);

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user->load('roles', 'permissions'),
        ];
    }

    /**
     * Logout current device
     */
    public function logout(User $user, ?string $tokenId = null): void
    {
        if ($tokenId) {
            $user->tokens()->where('id', $tokenId)->delete();
        } else {
            $user->currentAccessToken()->delete();
        }
    }

    /**
     * Logout all devices
     */
    public function logoutAllDevices(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Ensure user is active
     */
    protected function checkUserStatus(User $user): void
    {
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'account' => ['Your account is ' . $user->status . '.'],
            ]);
        }
    }
}
