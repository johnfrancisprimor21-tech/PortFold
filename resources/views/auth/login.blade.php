@extends('layouts.app')
@section('title', 'Sign in - Portfold')

@section('content')
<section class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Sign in</h1>
    <p>Access your portfolios and continue editing.</p>
    @include('auth.partials.supabase-config')
    @include('auth.partials.google-button', ['flow' => 'login'])
    <p class="field-error" role="alert" data-auth-status hidden></p>
    <form action="{{ route('login') }}" method="POST" data-supabase-form="login" novalidate>
        @csrf
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" required maxlength="254" autocomplete="email" autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
        </div>
        <button class="btn btn-primary" type="submit" data-busy-text="Signing in…">Sign in</button>
    </form>
    <div class="auth-links"><a href="{{ route('password.request') }}">Forgot password?</a><a href="{{ route('register') }}">Create account</a></div>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
