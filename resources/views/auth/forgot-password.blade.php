@extends('layouts.app')
@section('title', 'Reset password - Portfold')

@section('content')
<section class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Reset your password</h1>
    <p>Enter your account email. If it matches an account, we will send a reset link.</p>
    @include('auth.partials.supabase-config')
    <p class="muted" role="status" data-auth-status hidden></p>
    <form action="{{ route('password.request') }}" method="POST" data-supabase-form="forgot" novalidate>
        @csrf
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" required maxlength="254" autocomplete="email" autofocus>
        </div>
        <button class="btn btn-primary" type="submit" data-busy-text="Sending link…">Send reset link</button>
    </form>
    <div class="auth-links"><a href="{{ route('login') }}">Back to sign in</a></div>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
