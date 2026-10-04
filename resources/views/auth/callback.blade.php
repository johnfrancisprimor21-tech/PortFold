@extends('layouts.app')
@section('title', 'Finishing sign in - Portfold')

@section('content')
<section class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Finishing sign in</h1>
    @include('auth.partials.supabase-config')
    <p class="muted" role="status" aria-live="polite" data-auth-status>Verifying your account…</p>
    <p class="auth-links"><a href="{{ route('login') }}">Return to sign in</a></p>
</section>
@endsection

@section('scripts')
    @vite('resources/js/app.js')
@endsection
