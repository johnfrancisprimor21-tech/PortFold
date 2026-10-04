<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portfold')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0d0d0d;  --surface: #141414;  --surface-2: #1a1a1a;
            --border: #2a2a2a;  --border-strong: #3a3a3a;
            --text: #ececec;  --muted: #a1a8b5;          /* muted text keeps >= 4.5:1 contrast */
            --primary: #2563eb;  --primary-hover: #1d4ed8;
            --success: #74c69d;  --warn: #f2c14e;  --danger: #fca5a5;
            --focus: #93c5fd;
            --radius: 10px;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 16px; line-height: 1.55; background: var(--bg); color: var(--text); min-height: 100vh; }
        a { color: inherit; }
        :focus-visible { outline: 2px solid var(--focus); outline-offset: 2px; border-radius: 4px; }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; scroll-behavior: auto !important; } }

        .skip-link { position: absolute; left: 12px; top: -60px; background: var(--primary); color: #fff; padding: 10px 16px; border-radius: 8px; z-index: 100; text-decoration: none; }
        .skip-link:focus { top: 12px; }

        /* ---------- Navigation ---------- */
        .topnav { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 0 clamp(16px, 4vw, 40px); min-height: 64px; border-bottom: 1px solid var(--border); background: #111; position: sticky; top: 0; z-index: 30; }
        .brand { font-size: 20px; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: -.5px; }
        .nav-links { display: flex; gap: 4px; }
        .nav-links a { color: var(--muted); text-decoration: none; font-size: 15px; padding: 10px 14px; border-radius: 8px; min-height: 44px; display: inline-flex; align-items: center; transition: color .2s, background .2s; }
        .nav-links a:hover { color: #fff; background: var(--surface-2); }
        .nav-links a[aria-current="page"] { color: #fff; background: var(--surface-2); box-shadow: inset 0 -2px 0 var(--primary); }
        .nav-action { color: var(--muted); background: transparent; border: 0; font: inherit; font-size: 15px; padding: 10px 14px; border-radius: 8px; min-height: 44px; cursor: pointer; }
        .nav-action:hover { color: #fff; background: var(--surface-2); }

        .container { max-width: 960px; margin: 0 auto; padding: 32px 20px 64px; }
        .container.wide { max-width: 1100px; }

        /* ---------- Progress (visibility of system status) ---------- */
        .stepper { list-style: none; display: flex; gap: 8px; margin-bottom: 28px; flex-wrap: wrap; }
        .stepper li { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted); }
        .stepper li + li::before { content: ""; width: 28px; height: 1px; background: var(--border-strong); margin-right: 4px; }
        .stepper .num { width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--border-strong); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; }
        .stepper .current { color: #fff; font-weight: 600; }
        .stepper .current .num { background: var(--primary); border-color: var(--primary); color: #fff; }
        .stepper .done .num { background: #1a3a2a; border-color: #2d6a4f; color: var(--success); }

        /* ---------- Alerts ---------- */
        .alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: var(--radius); margin-bottom: 20px; font-size: 15px; }
        .alert > div { flex: 1; }
        .alert ul { margin: 6px 0 0 18px; }
        .alert-success { background: #12281d; border: 1px solid #2d6a4f; color: var(--success); }
        .alert-error   { background: #2e1616; border: 1px solid #7a2e2e; color: var(--danger); }
        .alert-close { background: none; border: 0; color: inherit; cursor: pointer; font-size: 20px; line-height: 1; min-width: 32px; min-height: 32px; border-radius: 6px; }
        .alert-close:hover { background: rgba(255,255,255,.08); }

        /* ---------- Buttons (44px minimum target) ---------- */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 10px 20px; border-radius: 8px; font: inherit; font-size: 15px; font-weight: 500; cursor: pointer; text-decoration: none; border: 1px solid transparent; transition: background .2s, border-color .2s, color .2s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: #222; color: #ddd; border-color: var(--border-strong); }
        .btn-secondary:hover { background: #2a2a2a; color: #fff; }
        .btn-danger { background: #7f1d1d; color: #fecaca; border-color: #991b1b; }
        .btn-danger:hover { background: #991b1b; }
        .btn-sm { min-height: 40px; padding: 8px 14px; font-size: 14px; }
        .btn[disabled], .btn[aria-busy="true"] { opacity: .55; cursor: not-allowed; }

        /* ---------- Typography ---------- */
        .page-title { font-size: 28px; font-weight: 700; color: #fff; line-height: 1.25; margin-bottom: 8px; }
        .page-subtitle { font-size: 16px; color: var(--muted); margin-bottom: 28px; }
        .muted { color: var(--muted); }

        /* ---------- Forms ---------- */
        .form-section { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px; scroll-margin-top: 80px; }
        .form-section > h2 { font-size: 17px; font-weight: 600; color: #fff; margin-bottom: 4px; }
        .form-section > .section-help { font-size: 14px; color: var(--muted); margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .form-group { margin-bottom: 18px; }
        .form-group > label, .form-group > .label { display: block; font-size: 14px; font-weight: 500; color: #d4d8e0; margin-bottom: 6px; }
        .req { color: #f87171; margin-left: 2px; }
        .help { display: block; font-size: 13px; color: var(--muted); margin-top: 6px; }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="url"], .form-group textarea {
            width: 100%; min-height: 44px; padding: 10px 14px; background: var(--surface-2); border: 1px solid var(--border-strong); border-radius: 8px; color: var(--text); font: inherit; font-size: 15px; transition: border-color .2s;
        }
        .form-group input::placeholder, .form-group textarea::placeholder { color: #7d8594; }
        .form-group input:hover, .form-group textarea:hover { border-color: #555; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary); outline: 2px solid rgba(147,197,253,.5); outline-offset: 0; }
        .form-group textarea { resize: vertical; min-height: 96px; }
        .form-group input[aria-invalid="true"], .form-group textarea[aria-invalid="true"] { border-color: #ef4444; }
        .field-error { display: block; color: var(--danger); font-size: 13px; margin-top: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

        /* Mini template mock-ups, used on Home and Choose Template */
        .mock { height: 170px; position: relative; overflow: hidden; }
        .mock.simple { background: #f4f6fb; padding: 20px 26px; }
        .mock.modern { background: #1e1b4b; }
        .mock.creative { background: #0d1117; }
        .mock .bar { background: #cfd6ea; border-radius: 3px; height: 6px; margin-bottom: 8px; width: 80%; }
        .mock .bar.dark { background: #1b2a4a; height: 10px; width: 45%; margin-bottom: 14px; }
        .mock .bar.w60 { width: 60%; } .mock .bar.w70 { width: 70%; }
        .mock .pills { display: flex; gap: 5px; margin-top: 12px; } .mock .pills i { width: 28px; height: 10px; border-radius: 6px; background: #d8def4; display: block; }
        .m-nav { height: 22px; background: #2e2a6b; display: flex; align-items: center; gap: 6px; padding: 0 12px; } .m-nav i { width: 26px; height: 5px; border-radius: 3px; background: #6d5fd6; display: block; }
        .m-body { display: flex; height: calc(100% - 22px); } .m-side { width: 52px; background: #17153a; padding: 10px 8px; } .m-side i { display: block; height: 6px; border-radius: 3px; background: #4c46a8; margin-bottom: 8px; }
        .m-main { flex: 1; padding: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; align-content: start; } .m-hero { grid-column: 1 / -1; height: 52px; border-radius: 6px; background: linear-gradient(135deg, #7c3aed, #4f46e5); } .m-card { height: 44px; border-radius: 6px; background: #2b2870; border: 1px solid #3d3a8c; }
        .c-wrap { display: flex; height: 100%; } .c-side { width: 38%; background: #161b22; padding: 18px 14px; border-right: 3px solid #e11d48; } .c-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #e11d48, #fb7185); margin-bottom: 12px; } .c-side i { display: block; height: 5px; border-radius: 3px; background: #30363d; margin-bottom: 7px; }
        .c-main { flex: 1; padding: 18px 16px; } .c-main .big { height: 12px; width: 70%; background: #e11d48; border-radius: 3px; margin-bottom: 12px; } .c-main i { display: block; height: 5px; border-radius: 3px; background: #30363d; margin-bottom: 7px; } .c-main .tile { height: 38px; border-radius: 6px; background: #1c2129; border: 1px solid #30363d; margin-top: 12px; }
        .tpl-badge { font-size: 12px; font-weight: 500; padding: 2px 10px; border-radius: 10px; display: inline-block; margin-bottom: 10px; }
        .badge-simple { background: #1a3a2a; color: #74c69d; } .badge-modern { background: #1e1b4b; color: #a5b4fc; } .badge-creative { background: #2d1a2a; color: #f9a8d4; }
        .auth-card { width: min(100%, 460px); margin: 40px auto; padding: 28px; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; }
        .auth-card h1 { color: #fff; font-size: 26px; margin-bottom: 6px; }
        .auth-card > p { color: var(--muted); margin-bottom: 22px; }
        .auth-card .form-group input { width: 100%; min-height: 46px; padding: 10px 14px; background: var(--surface-2); border: 1px solid var(--border-strong); border-radius: 8px; color: var(--text); font: inherit; }
        .auth-card .form-group input:focus { border-color: var(--primary); outline: 2px solid rgba(147,197,253,.5); }
        .auth-card .auth-links { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 18px; color: var(--muted); font-size: 14px; }
        .auth-card .auth-links a { color: #bfdbfe; }
        .auth-card .remember { display: flex; align-items: center; gap: 9px; color: var(--muted); margin: 12px 0 18px; }
        .auth-card .remember input { width: 18px; height: 18px; }
        .auth-card .btn { width: 100%; }
        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: var(--muted); font-size: 13px; }
        .auth-divider::before, .auth-divider::after { content: ""; flex: 1; height: 1px; background: var(--border); }
        .google-auth-button { width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 12px; min-height: 46px; padding: 10px 16px; border: 1px solid var(--border-strong); border-radius: 8px; background: var(--surface-2); color: var(--text); font: inherit; font-weight: 600; text-decoration: none; transition: background .2s, border-color .2s, transform .2s; }
        .google-auth-button:hover { background: #252a32; border-color: #687386; transform: translateY(-1px); }
        .google-auth-button:active { transform: translateY(0); }
        .google-auth-button svg { width: 18px; height: 18px; flex: none; }
        .nav-links form { display: flex; }
        @media (max-width: 620px) { .topnav { gap: 6px; padding: 0 12px; } .nav-links { gap: 0; flex-wrap: wrap; justify-content: flex-end; } .nav-links a, .nav-action { font-size: 13px; padding: 8px; } }
    </style>
    @yield('styles')
</head>
<body>
@php
    $__step = (int) trim($__env->yieldContent('step'));
    $__steps = ['Your info', 'Choose template', 'Preview'];
@endphp
<a class="skip-link" href="#main">Skip to main content</a>

<header class="topnav">
    <a href="{{ route('home') }}" class="brand">Portfold</a>
    <nav class="nav-links" aria-label="Main">
        <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
        @auth
            <a href="{{ route('portfolio.create') }}" @if(request()->routeIs('portfolio.create')) aria-current="page" @endif>Create</a>
            <a href="{{ route('portfolio.manage') }}" @if(request()->routeIs('portfolio.manage')) aria-current="page" @endif>Manage</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline-flex;align-items:center">@csrf<button class="nav-action" type="submit">Sign out</button></form>
        @else
            <a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Sign in</a>
            <a href="{{ route('register') }}" @if(request()->routeIs('register')) aria-current="page" @endif>Create account</a>
        @endauth
    </nav>
</header>

<main id="main" class="container @yield('container_class')">
    @if($__step)
    <ol class="stepper" aria-label="Progress">
        @foreach($__steps as $n => $label)
            @php $num = $n + 1; $state = $num < $__step ? 'done' : ($num === $__step ? 'current' : 'todo'); @endphp
            <li class="{{ $state }}" @if($state === 'current') aria-current="step" @endif>
                <span class="num" aria-hidden="true">{{ $state === 'done' ? '✓' : $num }}</span>
                <span>{{ $label }}<span class="sr-only" style="position:absolute;left:-9999px;"> ({{ $state === 'done' ? 'completed' : ($state === 'current' ? 'current step' : 'upcoming') }})</span></span>
            </li>
        @endforeach
    </ol>
    @endif

    @if(session('success'))
        <div class="alert alert-success" role="status"><div>{{ session('success') }}</div><button type="button" class="alert-close" aria-label="Dismiss message">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" role="alert"><div>{{ session('error') }}</div><button type="button" class="alert-close" aria-label="Dismiss message">&times;</button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" role="alert" id="error-summary" tabindex="-1">
            <div>
                <strong>We couldn't save yet. Please fix {{ $errors->count() === 1 ? 'this' : 'these' }}:</strong>
                <ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<script>
(function () {
    // Dismissible messages (user control)
    document.querySelectorAll('.alert-close').forEach(function (b) {
        b.addEventListener('click', function () { b.closest('.alert').remove(); });
    });

    // Move focus to the error summary so keyboard / screen-reader users notice it
    var summary = document.getElementById('error-summary');
    if (summary) { summary.focus(); summary.scrollIntoView({ block: 'center' }); }

    // Submit feedback + double-submit prevention
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('[type="submit"][data-busy-text]');
            if (!btn) { return; }
            btn.dataset.label = btn.textContent;
            btn.textContent = btn.dataset.busyText;
            btn.setAttribute('aria-busy', 'true');
            setTimeout(function () { btn.disabled = true; }, 0);
        });
    });
    // Restore buttons when the user comes back with the browser Back button
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) { return; }
        document.querySelectorAll('[aria-busy="true"]').forEach(function (btn) {
            btn.disabled = false; btn.removeAttribute('aria-busy');
            if (btn.dataset.label) { btn.textContent = btn.dataset.label; }
        });
    });

    // Warn before leaving a form with unsaved changes (error prevention)
    var guarded = document.querySelector('form[data-warn-unsaved]');
    if (guarded) {
        var dirty = false;
        guarded.addEventListener('input', function () { dirty = true; });
        guarded.addEventListener('change', function () { dirty = true; });
        guarded.addEventListener('submit', function () { dirty = false; });
        window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    }
})();
</script>
@yield('scripts')
</body>
</html>
