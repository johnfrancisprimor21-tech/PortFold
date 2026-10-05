@extends('layouts.app')
@section('title', 'Confirm your email - Portfold')

@section('styles')
    <style>
        .confirmation-card { max-width: 520px; margin: clamp(32px, 8vh, 88px) auto; padding: clamp(24px, 5vw, 40px); text-align: center; }
        .confirmation-icon { display: grid; place-items: center; width: 76px; height: 76px; margin: 0 auto 22px; border: 1px solid #2c7755; border-radius: 24px; background: linear-gradient(145deg, #153426, #10251d); color: #8ce0b3; box-shadow: 0 12px 34px rgba(34, 197, 94, .12); }
        .confirmation-icon svg { width: 36px; height: 36px; }
        .confirmation-eyebrow { margin: 0 0 8px !important; color: #8ce0b3 !important; font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .confirmation-card h1 { margin: 0 0 10px; font-size: clamp(26px, 5vw, 34px); line-height: 1.15; }
        .confirmation-copy { margin: 0 auto 24px !important; max-width: 390px; line-height: 1.65; }
        .confirmation-address { margin: 0 auto 24px; padding: 13px 16px; max-width: 390px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface-2); color: var(--text); font-weight: 600; overflow-wrap: anywhere; }
        .confirmation-steps { margin: 0 auto 24px; max-width: 390px; padding: 18px 20px; text-align: left; border: 1px solid var(--border); border-radius: 12px; background: rgba(255,255,255,.02); }
        .confirmation-steps h2 { margin: 0 0 10px; color: var(--text); font-size: 14px; }
        .confirmation-steps ol { padding-left: 20px; color: var(--muted); font-size: 14px; }
        .confirmation-steps li + li { margin-top: 7px; }
        .confirmation-status { margin: 0 auto 16px !important; padding: 11px 13px; border: 1px solid var(--border); border-radius: 9px; text-align: left; font-size: 14px; }
        .confirmation-status[data-kind="success"] { border-color: #2c7755; color: #a9ebc8; }
        .confirmation-status[data-kind="error"] { border-color: #7a2e2e; color: var(--danger); }
        .confirmation-actions { display: grid; gap: 10px; max-width: 390px; margin: 0 auto; }
        .confirmation-actions .btn { width: 100%; }
        .confirmation-actions .text-link { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; color: #bfdbfe; }
        @media (max-width: 520px) { .confirmation-card { margin: 28px auto; } }
    </style>
@endsection

@section('content')
<section class="auth-card confirmation-card" aria-labelledby="auth-title">
    <div class="confirmation-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="14" rx="2.5"></rect>
            <path d="m4 7 8 6 8-6"></path>
            <path d="m8.5 16 2 2 4-4"></path>
        </svg>
    </div>
    <p class="confirmation-eyebrow">One more step</p>
    <h1 id="auth-title">Check your inbox</h1>
    <p class="confirmation-copy">We sent a confirmation link to the email you entered. Confirm your address to finish creating your Portfold account.</p>
    <p class="confirmation-address" aria-label="Confirmation email address"><span data-confirmation-email>the email address you entered</span></p>

    @include('auth.partials.supabase-config')

    <div class="confirmation-steps">
        <h2>What to do next</h2>
        <ol>
            <li>Open the confirmation email from Portfold.</li>
            <li>Select the confirmation link to return here and finish signing in.</li>
            <li>If it is missing, check your spam or junk folder.</li>
        </ol>
    </div>

    <p class="confirmation-status" role="status" aria-live="polite" data-confirmation-status hidden></p>
    <div class="confirmation-actions">
        <button class="btn btn-primary" type="button" data-resend-confirmation>Resend confirmation email</button>
        <a class="btn btn-secondary" href="{{ route('login') }}">Return to sign in</a>
        <a class="text-link" href="{{ route('register') }}">Use a different email address</a>
    </div>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
