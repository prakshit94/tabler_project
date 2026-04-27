<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Standard Login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('identifier')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'identifier' => ['Too many login attempts. Please try again in '.$seconds.' seconds.'],
            ]);
        }

        $deviceInfo = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $request->header('X-Device-Name', 'web'),
        ];

        try {
            $data = $this->authService->loginWithPassword(
                $request->identifier,
                $request->password,
                $deviceInfo
            );
            RateLimiter::clear($throttleKey);
        } catch (ValidationException $e) {
            RateLimiter::hit($throttleKey);
            throw $e;
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => $data,
        ]);
    }

    /**
     * Logout current device
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout all devices
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAllDevices($request->user());

        return response()->json([
            'message' => 'Logged out from all devices',
        ]);
    }
}
