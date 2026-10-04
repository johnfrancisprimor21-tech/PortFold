<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function showResetPassword(): View
    {
        return view('auth.reset-password');
    }

    public function callback(): View
    {
        return view('auth.callback');
    }

    public function clientAuthFallback(): RedirectResponse
    {
        return back()->with('error', 'The secure account form did not load. Refresh the page and try again.');
    }

    /**
     * Validate a Supabase access token against the Auth service before creating
     * a Laravel session. No user identity fields supplied by the browser are trusted.
     */
    public function establishSupabaseSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string', 'max:8192'],
            'refresh_token' => ['required', 'string', 'max:8192'],
            'expires_in' => ['nullable', 'integer', 'min:1', 'max:86400'],
        ]);

        $projectUrl = rtrim((string) config('services.supabase.url'), '/');
        $publicKey = (string) config('services.supabase.anon_key');

        if ($projectUrl === '' || $publicKey === '') {
            return response()->json([
                'message' => 'Account sign-in is temporarily unavailable. Please try again later.',
            ], 503);
        }

        try {
            $response = Http::connectTimeout(3)
                ->timeout(8)
                ->withHeaders([
                    'apikey' => $publicKey,
                    'Authorization' => 'Bearer '.$validated['access_token'],
                    'Accept' => 'application/json',
                ])
                ->get($projectUrl.'/auth/v1/user');
        } catch (Throwable $exception) {
            Log::warning('Supabase session verification request failed.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'We could not verify your sign-in. Please try again.',
            ], 503);
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Your sign-in expired. Please sign in again.',
            ], 401);
        }

        $supabaseUser = $response->json();
        $userId = is_array($supabaseUser) ? (string) ($supabaseUser['id'] ?? '') : '';
        $email = is_array($supabaseUser) ? Str::lower(trim((string) ($supabaseUser['email'] ?? ''))) : '';

        if (! Str::isUuid($userId) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return response()->json([
                'message' => 'This account does not have a valid email address. Please contact support.',
            ], 401);
        }

        $metadata = is_array($supabaseUser['user_metadata'] ?? null) ? $supabaseUser['user_metadata'] : [];
        $fullName = trim((string) ($metadata['full_name'] ?? $metadata['name'] ?? Str::before($email, '@')));
        $fullName = Str::limit($fullName !== '' ? $fullName : Str::before($email, '@'), 255, '');
        $avatarUrl = $metadata['avatar_url'] ?? $metadata['picture'] ?? null;
        if (! is_string($avatarUrl)
            || mb_strlen($avatarUrl) > 2048
            || filter_var($avatarUrl, FILTER_VALIDATE_URL) === false
            || ! in_array(strtolower((string) parse_url($avatarUrl, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            $avatarUrl = null;
        }

        try {
            $user = User::query()->firstOrNew(['id' => $userId]);
            $user->fill([
                'full_name' => $fullName,
                'email' => $email,
                'avatar_url' => $avatarUrl,
            ]);
            if (! $user->exists) {
                $user->setAttribute('created_at', now());
            }
            $user->save();
        } catch (Throwable $exception) {
            Log::error('Supabase profile synchronization failed.', [
                'user_id' => $userId,
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'We could not prepare your account profile. Please try again.',
            ], 503);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('supabase.tokens', Crypt::encryptString(json_encode([
            'access_token' => $validated['access_token'],
            'refresh_token' => $validated['refresh_token'],
            'expires_at' => now()->addSeconds((int) ($validated['expires_in'] ?? 3600))->timestamp,
        ], JSON_THROW_ON_ERROR)));

        $destination = route('portfolio.manage', absolute: false);
        $intendedUrl = $request->session()->pull('url.intended');
        if (is_string($intendedUrl)) {
            $parts = parse_url($intendedUrl);
            $path = is_array($parts) ? (string) ($parts['path'] ?? '') : '';
            $sameHost = ! isset($parts['host'])
                || strtolower((string) $parts['host']) === strtolower($request->getHost());

            if ($sameHost && str_starts_with($path, '/') && ! str_starts_with($path, '//')) {
                $destination = $path;
                if (! empty($parts['query'])) {
                    $destination .= '?'.$parts['query'];
                }
            }
        }

        return response()->json(['redirect' => $destination]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $encryptedTokens = $request->session()->get('supabase.tokens');
        $tokens = [];
        if (is_string($encryptedTokens) && $encryptedTokens !== '') {
            try {
                $tokens = json_decode(Crypt::decryptString($encryptedTokens), true, flags: JSON_THROW_ON_ERROR);
            } catch (Throwable $exception) {
                Log::notice('Supabase session tokens could not be decrypted during sign-out.', [
                    'exception' => $exception::class,
                ]);
            }
        }
        if (! is_array($tokens)) {
            $tokens = [];
        }

        $projectUrl = rtrim((string) config('services.supabase.url'), '/');
        $publicKey = (string) config('services.supabase.anon_key');
        $accessToken = is_array($tokens) ? (string) ($tokens['access_token'] ?? '') : '';

        if ($projectUrl !== '' && $publicKey !== '' && $accessToken !== ''
            && (int) ($tokens['expires_at'] ?? 0) <= now()->addMinute()->timestamp
            && ! empty($tokens['refresh_token'])) {
            try {
                $refreshResponse = Http::connectTimeout(2)
                    ->timeout(5)
                    ->withHeaders(['apikey' => $publicKey])
                    ->post($projectUrl.'/auth/v1/token?grant_type=refresh_token', [
                        'refresh_token' => $tokens['refresh_token'],
                    ]);

                if ($refreshResponse->successful()) {
                    $refreshedTokens = $refreshResponse->json();
                    $accessToken = (string) ($refreshedTokens['access_token'] ?? $accessToken);
                }
            } catch (Throwable $exception) {
                Log::notice('Supabase token refresh during sign-out failed.', [
                    'exception' => $exception::class,
                ]);
            }
        }

        if ($projectUrl !== '' && $publicKey !== '' && $accessToken !== '') {
            try {
                Http::connectTimeout(2)
                    ->timeout(5)
                    ->withHeaders([
                        'apikey' => $publicKey,
                        'Authorization' => 'Bearer '.$accessToken,
                    ])
                    ->post($projectUrl.'/auth/v1/logout?scope=local');
            } catch (Throwable $exception) {
                Log::notice('Supabase sign-out request failed.', [
                    'exception' => $exception::class,
                ]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
