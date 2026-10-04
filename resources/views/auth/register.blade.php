@extends('layouts.app')
@section('title', 'Create account - Portfold')

@section('content')
<section class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Create your account</h1>
    <p>Your portfolios will stay private to your account until you share their preview links.</p>
    @include('auth.partials.supabase-config')
    @include('auth.partials.google-button', ['flow' => 'register'])
    <p class="field-error" role="alert" data-auth-status hidden></p>
    <form action="{{ route('register') }}" method="POST" data-supabase-form="register" novalidate>
        @csrf
        <div class="form-group">
            <label for="name">Your name</label>
            <input id="name" name="name" type="text" required maxlength="255" autocomplete="name" autofocus>
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" required maxlength="254" autocomplete="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="12" autocomplete="new-password" aria-describedby="password-help">
            <span class="help" id="password-help">Use at least 12 characters.</span>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password">
        </div>
        <button class="btn btn-primary" type="submit" data-busy-text="Creating account…">Create account</button>
    </form>
    <div class="auth-links"><span>Already registered?</span><a href="{{ route('login') }}">Sign in</a></div>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
