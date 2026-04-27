<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OTPService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OTPController extends Controller
{
    protected OTPService $otpService;
    protected AuthService $authService;

    public function __construct(OTPService $otpService, AuthService $authService)
    {
        $this->otpService = $otpService;
        $this->authService = $authService;
    }

    /**
     * Request OTP for login
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $this->otpService->generateOtp($request->identifier);

        return response()->json([
            'message' => 'OTP sent successfully',
        ]);
    }

    /**
     * Verify OTP and Login
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string',
        ]);

        $user = $this->otpService->verifyOtp($request->identifier, $request->code);

        $deviceInfo = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $request->header('X-Device-Name', 'mobile-app'),
        ];

        $data = $this->authService->createSession($user, $deviceInfo);

        return response()->json([
            'message' => 'Login successful',
            'data' => $data,
        ]);
    }
}
