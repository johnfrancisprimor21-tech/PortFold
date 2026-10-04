@extends('layouts.app')
@section('title', 'Choose a new password - Portfold')

@section('content')
<section class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Choose a new password</h1>
    <p>Use at least 12 characters.</p>
    @include('auth.partials.supabase-config')
    <p class="muted" role="status" data-auth-status>Checking your reset link…</p>
    <form action="{{ route('password.reset') }}" method="POST" data-supabase-form="reset" novalidate>
        @csrf
        <div class="form-group">
            <label for="password">New password</label>
            <input id="password" name="password" type="password" required minlength="12" autocomplete="new-password" disabled>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password" disabled>
        </div>
        <button class="btn btn-primary" type="submit" data-busy-text="Saving password…" disabled>Save new password</button>
    </form>
    <div class="auth-links"><a href="{{ route('login') }}">Back to sign in</a></div>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
