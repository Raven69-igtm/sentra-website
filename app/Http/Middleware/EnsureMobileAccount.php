<?php

namespace App\Http\Middleware;

use App\Models\Akun;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan token yang dipakai memang milik akun mobile yang aktif.
 *
 * Middleware ini menjadi lapisan kedua setelah login: walaupun token admin
 * pernah tercipta, token tersebut tidak bisa dipakai di endpoint mobile.
 */
class EnsureMobileAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        if (! $user) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $isMobileRole = in_array($user->role ?? null, Akun::MOBILE_ROLES, true);
        $isActive = ($user->is_active ?? true) !== false;

        if (! $isMobileRole || ! $isActive) {
            $this->revokeCurrentToken($user);

            return response()->json([
                'message' => 'Akun ini tidak diizinkan mengakses aplikasi mobile.',
            ], 403);
        }

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('mobile')) {
            return response()->json([
                'message' => 'Token tidak valid untuk aplikasi mobile.',
            ], 403);
        }

        return $next($request);
    }

    private function revokeCurrentToken($user): void
    {
        if (! method_exists($user, 'currentAccessToken')) {
            return;
        }

        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
