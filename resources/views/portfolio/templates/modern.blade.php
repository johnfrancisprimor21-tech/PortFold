@php
    $name = trim((string) ($info->full_name ?? '')) ?: 'Your Name';
    $words = array_values(array_filter(preg_split('/\s+/', $name)));
    $initials = count($words) > 1
        ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1))
        : mb_strtoupper(mb_substr($name, 0, 2));
    $greetEnd = \Illuminate\Support\Str::endsWith($name, '.') ? '' : '.';

    $hl = trim((string) ($info->headline ?? '')) ?: 'Developer';

    $hlWords = explode(' ', $hl);
    $hlLast = array_pop($hlWords);
    $hlRest = implode(' ', $hlWords);

    $hasLinks = $links && $links->count() > 0;

    $paths = [
        'pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'code' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'arrow' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'home' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'grid' => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
        'folder' => '<path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.7-.9L9.6 3.9A2 2 0 0 0 7.9 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>',
        'briefcase' => '<rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'cap' => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
    ];
    $icon = function ($n, $s = 16) use ($paths) {
        return '<svg class="ico" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $s . '" height="' . $s . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$n] . '</svg>';
    };
    $brand = [
        'github' => 'devicon-github-original', 'linkedin' => 'devicon-linkedin-plain',
        'facebook' => 'devicon-facebook-plain', 'twitter' => 'devicon-twitter-original',
        'x' => 'devicon-twitter-original', 'instagram' => 'devicon-instagram-plain',
        'youtube' => 'devicon-youtube-plain',
    ];
    $brandIcon = function ($p) use ($brand, $icon) {
        $k = strtolower(trim((string) $p));
        return isset($brand[$k]) ? '<i class="' . $brand[$k] . '" aria-hidden="true"></i>' : $icon('globe');
    };

    $alias = ['html5' => 'html', 'js' => 'javascript', 'ts' => 'typescript', 'csharp' => 'c#', 'node' => 'nodejs',
        'node.js' => 'nodejs', 'golang' => 'go', 'tailwind' => 'tailwindcss', 'next' => 'nextjs', 'next.js' => 'nextjs',
        'postgres' => 'postgresql', 'vue.js' => 'vue', 'react.js' => 'react', 'mongo' => 'mongodb', 'css3' => 'css'];
    $dev = [
        'html' => 'devicon-html5-plain colored', 'css' => 'devicon-css3-plain colored',
        'javascript' => 'devicon-javascript-plain colored', 'typescript' => 'devicon-typescript-plain colored',
        'react' => 'devicon-react-original colored', 'vue' => 'devicon-vuejs-plain colored',
        'angular' => 'devicon-angularjs-plain colored', 'svelte' => 'devicon-svelte-plain colored',
        'tailwindcss' => 'devicon-tailwindcss-plain colored', 'bootstrap' => 'devicon-bootstrap-plain colored',
        'sass' => 'devicon-sass-original colored', 'php' => 'devicon-php-plain colored',
        'laravel' => 'devicon-laravel-plain colored', 'python' => 'devicon-python-plain colored',
        'java' => 'devicon-java-plain colored', 'c#' => 'devicon-csharp-plain colored',
        'c++' => 'devicon-cplusplus-plain colored', 'c' => 'devicon-c-plain colored',
        'nodejs' => 'devicon-nodejs-plain colored', 'mysql' => 'devicon-mysql-plain colored',
        'postgresql' => 'devicon-postgresql-plain colored', 'mongodb' => 'devicon-mongodb-plain colored',
        'redis' => 'devicon-redis-plain colored', 'firebase' => 'devicon-firebase-plain colored',
        'graphql' => 'devicon-graphql-plain colored', 'go' => 'devicon-go-plain colored',
        'git' => 'devicon-git-plain colored', 'docker' => 'devicon-docker-plain colored',
        'kubernetes' => 'devicon-kubernetes-plain colored', 'linux' => 'devicon-linux-plain colored',
        'azure' => 'devicon-azure-plain colored', 'vscode' => 'devicon-vscode-plain colored',
        'figma' => 'devicon-figma-plain colored', 'wordpress' => 'devicon-wordpress-plain colored',
        'nuxt' => 'devicon-nuxtjs-plain colored', 'flutter' => 'devicon-flutter-plain colored',
        'kotlin' => 'devicon-kotlin-plain colored', 'swift' => 'devicon-swift-plain colored',
        'webpack' => 'devicon-webpack-plain colored', 'vite' => 'devicon-vitejs-plain colored',
        'jest' => 'devicon-jest-plain colored',
        'rust' => ['devicon-rust-plain', '#DEA584'], 'github' => ['devicon-github-original', '#E9ECFB'],
        'express' => ['devicon-express-original', '#E9ECFB'], 'flask' => ['devicon-flask-original', '#E9ECFB'],
        'django' => ['devicon-django-plain', '#44B78B'], 'nextjs' => ['devicon-nextjs-original', '#E9ECFB'],
        'aws' => ['devicon-amazonwebservices-original', '#FF9900'],
    ];
    $resolveIcon = function ($label) use ($dev, $alias) {
        $k = strtolower(trim((string) $label));
        $k = $alias[$k] ?? $k;
        $v = $dev[$k] ?? null;
        if (!$v) { return [null, null]; }
        return is_array($v) ? $v : [$v, null];
    };

    // Skills can be typed as "Laravel:4"; the optional number is ignored in this template.
    $skillList = collect($skills ?? [])->map(function ($sk) {
        $parts = explode(':', (string) $sk->name, 2);
        return (object) ['name' => trim($parts[0])];
    });

    $fmtDate = function ($v) {
        $v = trim((string) $v);
        if ($v === '') { return ''; }
        try {
            return strtotime($v) ? \Carbon\Carbon::parse($v)->format('M Y') : $v;
        } catch (\Throwable $e) {
            return $v;
        }
    };

    $hasBio = !empty($info->bio);
    $hasContact = !empty($info->contact_email) || !empty($info->phone);

    $sideNav = [['home', 'Home', 'home']];
    if ($hasBio) { $sideNav[] = ['about', 'About', 'user']; }
    if ($skillList->count()) { $sideNav[] = ['skills', 'Skills', 'grid']; }
    if ($projects->count()) { $sideNav[] = ['projects', 'Projects', 'folder']; }
    if ($experiences->count()) { $sideNav[] = ['experience', 'Experience', 'briefcase']; }
    if ($education->count()) { $sideNav[] = ['education', 'Education', 'cap']; }
    if ($hasContact) { $sideNav[] = ['contact', 'Contact', 'mail']; }
@endphp
<!DOCTYPE html>
<html lang="en" data-default-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
    @include('portfolio.partials.theme-init')
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            color-scheme: dark;
            --bg: #0d0f14; --bg-rgb: 13,15,20;
            --card: #0a0c10; --card-2: #0f1117;
            --line: #1e2130; --line-2: #2a3050;
            --ink: #e2e8f0; --muted: #8b949e; --dim: #2d3748; --body-ink: #c9d1d9; --on-accent: #04130a;
            --accent: #22c55e; --accent-2: #16a34a; --accent-hi: #4ade80;
            --accent-blue: #818cf8; --accent-orange: #fb923c;
            --theme-control-bg: #0f1117; --theme-control-border: #1e2130;
            --theme-control-hover: #141720; --theme-control-active: #22c55e;
            --raised-shadow: 0 4px 16px rgba(0,0,0,.4);
            --pressed-shadow: inset 0 2px 4px rgba(0,0,0,.4);
            --mono: 'JetBrains Mono', 'Courier New', monospace;
            --body: 'Inter', system-ui, sans-serif;
            --head: 'JetBrains Mono', monospace;
        }
        :root[data-theme="light"] {
            color-scheme: light;
            --bg: #f5f0e8; --bg-rgb: 245,240,232;
            --card: #faf6ee; --card-2: #ede8dc;
            --line: #d4c9b0; --line-2: #c0b090;
            --ink: #2d2a22; --muted: #665d4c; --dim: #a09880; --body-ink: #3d392e; --on-accent: #ffffff;
            --accent: #5c6a2e; --accent-2: #4a5524; --accent-hi: #7a8f3a;
            --accent-blue: #3a5f8a; --accent-orange: #8a5a1e;
            --theme-control-bg: #ede8dc; --theme-control-border: #c8bb9a;
            --theme-control-hover: #e0d8c8; --theme-control-active: #5c6a2e;
            --raised-shadow: 0 4px 16px rgba(80,70,50,.15);
            --pressed-shadow: inset 0 2px 4px rgba(80,70,50,.12);
        }
        html { min-height: 100%; scroll-behavior: smooth; background: var(--bg); color: var(--ink); font-family: var(--body); }
        body { min-height: 100vh; margin: 0; position: relative; isolation: isolate; overflow-x: clip; background: var(--bg); color: var(--ink); }
        a { color: inherit; }
        a:focus-visible, button:focus-visible { outline: 2px solid var(--accent-hi); outline-offset: 3px; }
        img { max-width: 100%; }
        .ico { display: inline-block; flex: 0 0 auto; vertical-align: middle; }
        @include('portfolio.partials.theme-toggle-styles')

        .bg-orb { position: fixed; border-radius: 50%; pointer-events: none; z-index: -1; filter: blur(80px); }
        .bg-orb:nth-child(1) { width: 400px; height: 400px; background: rgba(34,197,94,.05); top: -80px; right: -80px; animation: orb-drift 22s ease-in-out infinite alternate; }
        .bg-orb:nth-child(2) { width: 350px; height: 350px; background: rgba(129,140,248,.04); bottom: -60px; left: -60px; animation: orb-drift 18s ease-in-out infinite alternate; animation-delay: -9s; }
        @keyframes orb-drift { 0% { transform: translate(0,0); } 50% { transform: translate(20px,-30px); } 100% { transform: translate(-15px,15px); } }
        .scroll-progress { position: fixed; inset: 0 auto auto 0; width: 0; height: 2px; background: var(--accent); z-index: 9999; pointer-events: none; }

        .topbar { position: sticky; top: var(--pf-preview-bar-height, 0px); z-index: 100; display: flex; align-items: center; justify-content: space-between; gap: 18px; min-height: 62px; padding: 8px 22px; background: var(--card); border-bottom: 1px solid var(--line); transition: background .2s ease, border-color .2s ease; }
        .topbar.scrolled { background: rgba(var(--bg-rgb), 0.9); backdrop-filter: blur(16px); border-bottom: 1px solid var(--line); }
        .logo-prompt { justify-self: start; color: var(--accent); font: 600 .9rem var(--mono); text-decoration: none; white-space: nowrap; }
        .top-actions { display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
        .sr-only { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .skip-link { position: fixed; top: calc(var(--pf-preview-bar-height, 0px) + 8px); left: 12px; z-index: 200; padding: 10px 14px; transform: translateY(-200%); border-radius: 6px; background: var(--accent); color: var(--on-accent); font: 600 .85rem var(--mono); }
        .skip-link:focus { transform: none; }
        .hero-body { display: flex; flex-wrap: wrap; align-items: center; gap: 24px; }
        .hero-photo { width: 120px; height: 120px; flex: 0 0 120px; object-fit: cover; border: 2px solid var(--accent); border-radius: 50%; background: var(--card-2); }
        .hero-text { min-width: 0; flex: 1 1 280px; }
        .hero-name { margin: 8px 0 4px; color: var(--ink); font: 700 clamp(1.8rem,4.5vw,2.8rem)/1.15 var(--mono); overflow-wrap: anywhere; }
        .hero-role { margin: 0 0 6px; color: var(--ink); font: 500 clamp(1rem,2.2vw,1.25rem)/1.5 var(--mono); overflow-wrap: anywhere; }
        .term-btn.primary { border-color: var(--accent); background: var(--accent); color: var(--on-accent); font-weight: 700; }
        .term-btn.primary:hover { border-color: var(--accent-hi); background: var(--accent-hi); color: var(--on-accent); }
        .hero-links { margin-top: 12px; }
        .nav-sentinel { height: 1px; margin-top: -1px; pointer-events: none; }

        .layout { display: grid; grid-template-columns: 174px minmax(0,1fr); gap: 18px; align-items: start; padding: 18px; }
        .side { position: sticky; top: calc(var(--pf-preview-bar-height, 0px) + 80px); display: flex; flex-direction: column; gap: 4px; min-width: 0; max-height: calc(100vh - var(--pf-preview-bar-height, 0px) - 96px); overflow-y: auto; padding: 12px 8px; background: var(--card); border: 1px solid var(--line); border-radius: 10px; }
        .side a { display: flex; align-items: center; gap: 9px; min-height: 40px; padding: 8px 10px; border-left: 2px solid transparent; border-radius: 0 6px 6px 0; color: var(--muted); font: .85rem var(--mono); text-decoration: none; transition: color .18s ease, background .18s ease, border-color .18s ease; }
        .side a:hover { color: var(--ink); background: var(--card-2); }
        .side a.active { color: var(--accent); border-left-color: var(--accent); background: rgba(34,197,94,.07); }
        .side-note { margin: auto 8px 0; padding: 16px 6px 4px; color: var(--muted); font: 11px/1.6 var(--mono); }
        .side-logo { margin-top: 12px; color: var(--accent); font-weight: 700; }
        .main { display: grid; gap: 16px; min-width: 0; }

        .term-win { min-width: 0; overflow: hidden; background: var(--card); border: 1px solid var(--line); border-radius: 10px; font-family: var(--mono); box-shadow: var(--raised-shadow); }
        .term-bar { display: flex; align-items: center; gap: 6px; min-height: 36px; padding: 8px 12px; background: var(--card-2); border-bottom: 1px solid var(--line); }
        .term-dots { width: 10px; height: 10px; flex: 0 0 10px; margin-right: 34px; border-radius: 50%; background: #ff5f57; box-shadow: 16px 0 0 #febc2e, 32px 0 0 #28c840; }
        .term-title { min-width: 0; flex: 1; margin: 0 0 0 4px; color: var(--muted); font: 500 12px/1.4 var(--mono); }
        .term-body { padding: 16px; }
        .hero-main .term-body { min-height: 250px; padding: clamp(18px,3vw,30px); }
        .term-line { display: flex; align-items: center; gap: 6px; margin: 0 0 4px; color: var(--muted); font: 13px/1.6 var(--mono); overflow-wrap: anywhere; }
        .term-ps { color: var(--accent); font-weight: 700; }
        .term-cmd { color: var(--ink); }
        .term-hl { color: var(--accent); }
        .term-bio { max-width: 68ch; margin: 6px 0; color: var(--body-ink); font: 15px/1.7 var(--body); overflow-wrap: anywhere; }
        .term-cursor { display: inline-block; width: 8px; height: 14px; margin-top: 8px; background: var(--accent); vertical-align: text-bottom; animation: blink 1s step-end 8; }
        @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
        .term-meta { display: flex; flex-wrap: wrap; gap: 10px 16px; margin-top: 12px; color: var(--body-ink); font: 14px/1.6 var(--body); }
        .term-meta span { display: inline-flex; align-items: center; gap: 6px; overflow-wrap: anywhere; }
        .term-meta .ico { color: var(--accent); }

        .content-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 16px; align-items: start; }
        .content-grid > .full-width { grid-column: 1 / -1; }
        .section-heading { color: var(--accent); }
        #home, #about, #skills, #projects, #experience, #education, #contact { scroll-margin-top: 82px; }
        .skill-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(110px,1fr)); gap: 8px; }
        .skill-tile { display: flex; align-items: center; gap: 8px; min-height: 38px; padding: 8px 10px; border: 1px solid var(--line); border-radius: 6px; background: var(--card-2); color: var(--ink); font: 13px/1.4 var(--mono); transition: opacity .38s ease, transform .38s ease, border-color .2s ease, background .2s ease; opacity: 0; transform: translateX(-8px); }
        .skill-grid.visible .skill-tile { opacity: 1; transform: none; }
        .skill-tile:hover { border-color: var(--accent); background: rgba(34,197,94,.05); }
        .skill-tile i { font-size: 17px; }
        .skill-fb { color: var(--accent); font-size: 10px; font-weight: 700; }

        .btn-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .term-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 44px; padding: 8px 14px; border: 1px solid var(--line); border-radius: 6px; background: var(--card-2); color: var(--ink); box-shadow: var(--raised-shadow); font: 500 13px/1.4 var(--mono); text-decoration: none; transition: border-color .2s ease, color .2s ease, background .2s ease, transform .2s ease, box-shadow .2s ease; }
        .term-btn:hover { border-color: var(--accent); color: var(--accent); }
        .term-btn:active { box-shadow: var(--pressed-shadow); transform: translateY(1px); }
        .term-btn .term-ps { font-weight: 700; }
        .proj-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
        .proj { min-width: 0; overflow: hidden; border: 1px solid var(--line); border-radius: 8px; background: var(--card-2); transform-style: preserve-3d; transition: border-color .2s ease, box-shadow .2s ease, transform .35s ease; }
        .proj:hover { border-color: var(--line-2); box-shadow: var(--raised-shadow); }
        .proj-img, .proj-ph { display: block; width: 100%; height: 170px; object-fit: cover; border-bottom: 1px solid var(--line); background: var(--card); }
        .proj-ph { display: grid; place-items: center; color: var(--accent); }
        .proj-body { padding: 14px; }
        .proj-body h3, .row h3 { margin: 0; color: var(--ink); font: 600 15px/1.5 var(--mono); }
        .proj-body p, .row p { margin: 7px 0 0; color: var(--body-ink); font: 14px/1.7 var(--body); overflow-wrap: anywhere; }
        .proj-links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .row { display: grid; grid-template-columns: minmax(90px,150px) minmax(0,1fr); gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); }
        .row:last-child { border-bottom: 0; }
        .row-date { color: var(--accent); font: 12px/1.6 var(--mono); }
        .row .sub { color: var(--accent-blue); }
        .contact { display: grid; grid-template-columns: repeat(auto-fit,minmax(190px,1fr)); gap: 9px; }
        .ci { display: flex; align-items: center; gap: 10px; min-width: 0; padding: 10px; border: 1px solid var(--line); border-radius: 7px; background: var(--card-2); color: var(--ink); text-decoration: none; transition: border-color .2s ease, background .2s ease; }
        .ci:hover { border-color: var(--accent); background: rgba(34,197,94,.05); }
        .ci-ico { display: grid; place-items: center; width: 34px; height: 34px; flex: 0 0 34px; border-radius: 6px; background: var(--card); color: var(--accent); }
        .ci span:last-child { display: grid; min-width: 0; gap: 3px; }
        .ci small { color: var(--muted); font: 12px var(--mono); }
        .ci b { color: var(--ink); font: 500 13px/1.5 var(--mono); overflow-wrap: anywhere; }
        .foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 0 18px; padding: 18px 4px 24px; border-top: 1px solid var(--line); color: var(--muted); font: 11px/1.6 var(--mono); }
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity .5s ease, transform .5s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .term-win.reveal.visible { transform: none; }
        .skill-tile.reveal { transform: translateX(-8px); }

        @media (max-width: 1120px) {
            .layout { grid-template-columns: 150px minmax(0,1fr); }
        }
        @media (max-width: 800px) {
            .layout { grid-template-columns: 1fr; gap: 12px; padding: 12px; }
            .side { position: static; flex-direction: row; max-height: none; overflow-x: auto; overflow-y: hidden; padding: 6px; scrollbar-width: thin; }
            .side a { flex: 0 0 auto; min-height: 38px; }
            .side-note { display: none; }
            .main { grid-row: 2; }
            .content-grid { grid-template-columns: minmax(0,1fr); }
            .content-grid > .full-width { grid-column: auto; }
            .foot { margin-inline: 12px; }
        }
        @media (max-width: 520px) {
            .hero-main .term-body { min-height: 0; }
            .term-body { padding: 13px; }
            .proj-grid { grid-template-columns: minmax(0,1fr); }
            .row { grid-template-columns: minmax(0,1fr); gap: 4px; }
            .foot { align-items: flex-start; flex-direction: column; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition: none !important; animation: none !important; }
            .reveal { opacity: 1; transform: none; }
            .skill-tile { opacity: 1; transform: none; }
        }
        /* NEOMORPHIC TERMINAL SURFACES */
        :root {
            --bg: #192637; --bg-rgb: 25,38,55;
            --card: #1e2b3d; --card-2: #1a2738;
            --line: rgba(174,196,225,.12); --line-2: rgba(174,196,225,.24);
            --ink: #e5edf9; --muted: #a6b5cb; --dim: #71839e; --body-ink: #cad5e6; --on-accent: #07170f;
            --accent: #21e39a; --accent-2: #12bb7b; --accent-hi: #6bffc0;
            --accent-blue: #9db5df; --accent-orange: #f2b66e;
            --theme-control-bg: #1e2b3d; --theme-control-border: rgba(174,196,225,.16);
            --theme-control-hover: #24344a; --theme-control-active: #21e39a;
            --raised-shadow: 8px 8px 18px rgba(7,13,22,.66), -7px -7px 18px rgba(63,84,110,.34), inset 1px 1px 0 rgba(255,255,255,.035);
            --pressed-shadow: inset 4px 4px 9px rgba(7,13,22,.68), inset -4px -4px 9px rgba(65,86,112,.27);
            --neo-edge: rgba(176,200,229,.13); --neo-glow: rgba(33,227,154,.2);
            --neo-nav-active: #183c37;
        }
        :root[data-theme="light"] {
            color-scheme: light;
            --bg: #e8eef5; --bg-rgb: 232,238,245;
            --card: #eaf0f7; --card-2: #e4ebf3;
            --line: rgba(91,112,139,.14); --line-2: rgba(91,112,139,.24);
            --ink: #26374b; --muted: #586c83; --dim: #899ab0; --body-ink: #394e66; --on-accent: #fff;
            --accent: #168d64; --accent-2: #0e7652; --accent-hi: #2bad80;
            --accent-blue: #4a6e9a; --accent-orange: #8a5a1e;
            --theme-control-bg: #eaf0f7; --theme-control-border: rgba(91,112,139,.17);
            --theme-control-hover: #e1e9f2; --theme-control-active: #168d64;
            --raised-shadow: 8px 8px 17px rgba(160,175,194,.7), -7px -7px 17px rgba(255,255,255,.96), inset 1px 1px 0 rgba(255,255,255,.62);
            --pressed-shadow: inset 4px 4px 9px rgba(160,175,194,.66), inset -4px -4px 9px rgba(255,255,255,.95);
            --neo-edge: rgba(255,255,255,.62); --neo-glow: rgba(22,141,100,.15);
            --neo-nav-active: #d5ece3;
        }
        html, body { background-color: var(--bg); }
        body {
            background-image: radial-gradient(ellipse at 12% 0%, rgba(35,227,154,.055), transparent 34%), radial-gradient(ellipse at 92% 82%, rgba(119,155,220,.07), transparent 40%);
        }
        .topbar {
            top: var(--pf-preview-bar-height, 0px); margin: 12px 18px 0; min-height: 64px;
            border: 1px solid var(--neo-edge); border-radius: 22px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); box-shadow: var(--raised-shadow);
        }
        .topbar.scrolled { background: var(--card); backdrop-filter: none; border-color: var(--neo-edge); box-shadow: var(--raised-shadow); }
        .logo-prompt { display: inline-flex; align-items: center; color: var(--accent); text-shadow: 0 0 14px var(--neo-glow); }
        .logo-prompt::before { content: ''; width: 10px; height: 10px; margin-right: 12px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 8px var(--accent), 0 0 18px var(--neo-glow); }
        .top-links a { border-radius: 10px; transition: color .2s ease, background .2s ease; }
        .top-links a:hover, .top-links a.active { color: var(--accent); }
        .top-actions .theme-toggle, .theme-toggle {
            min-height: 42px; padding: 8px 15px; border: 1px solid var(--neo-edge); border-radius: 999px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); color: var(--ink); box-shadow: var(--raised-shadow);
        }
        .theme-toggle:hover { background: var(--theme-control-hover); }
        .theme-toggle:active { box-shadow: var(--pressed-shadow); }
        .layout { gap: 20px; padding: 20px 18px; }
        .side {
            gap: 8px; padding: 14px 10px; border: 1px solid var(--neo-edge); border-radius: 22px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); box-shadow: var(--raised-shadow);
        }
        .side a {
            position: relative; min-height: 44px; padding: 9px 12px; border: 1px solid transparent; border-left: 0; border-radius: 15px;
            color: var(--muted); transition: color .2s ease, background .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .side a:hover { color: var(--ink); background: var(--card-2); box-shadow: var(--pressed-shadow); }
        .side a.active {
            color: var(--accent); border-color: var(--neo-edge); background: var(--neo-nav-active);
            box-shadow: inset 3px 3px 7px rgba(7,13,22,.34), inset -3px -3px 7px rgba(90,128,115,.13), 0 0 18px var(--neo-glow);
        }
        .side a.active::after { content: ''; width: 8px; height: 8px; margin-left: auto; border-radius: 50%; background: var(--accent); box-shadow: 0 0 10px var(--accent); }
        .side a .ico { width: 18px; height: 18px; }
        .side-note { margin-inline: 8px; padding-top: 18px; border-top: 1px solid var(--line); }
        .side-logo { text-shadow: 0 0 12px var(--neo-glow); }
        .main { gap: 20px; }
        .term-win {
            border: 1px solid var(--neo-edge); border-radius: 22px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); box-shadow: var(--raised-shadow);
        }
        .term-bar { min-height: 46px; padding: 12px 16px 9px; background: transparent; border-bottom: 0; }
        .term-title { color: var(--muted); letter-spacing: .01em; }
        .term-body {
            margin: 0 11px 11px; padding: 18px; border: 1px solid var(--neo-edge); border-radius: 16px;
            background: linear-gradient(145deg, var(--card-2), var(--bg)); box-shadow: var(--pressed-shadow);
        }
        .hero-main .term-body { padding: clamp(20px,3vw,32px); }
        .term-line { color: var(--muted); }
        .term-cmd, .term-val { color: var(--ink); }
        .term-bio, .proj-body p, .row p { color: var(--body-ink); }
        .term-meta .ico { color: var(--accent); }
        .hero-name { letter-spacing: -.045em; text-shadow: 0 2px 18px rgba(0,0,0,.16); }
        .hero-role { color: var(--body-ink); }
        .hero-photo, .about-photo {
            border: 2px solid var(--accent); box-shadow: 0 0 0 6px var(--card-2), 0 0 0 8px var(--line-2), 0 0 24px var(--neo-glow);
        }
        .term-hl, .term-ps, .term-key, .term-dot-green { color: var(--accent); }
        .term-cursor { box-shadow: 0 0 12px var(--accent); }
        .section-heading { color: var(--accent); }
        .skill-grid { gap: 10px; }
        .skill-tile {
            min-height: 42px; padding: 9px 13px; border: 1px solid var(--neo-edge); border-radius: 999px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); color: var(--ink); box-shadow: var(--raised-shadow);
        }
        .skill-tile:hover { border-color: var(--accent); background: var(--card); box-shadow: var(--pressed-shadow), 0 0 13px var(--neo-glow); }
        .term-btn {
            min-height: 42px; padding: 8px 15px; border: 1px solid var(--neo-edge); border-radius: 999px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); color: var(--ink); box-shadow: var(--raised-shadow);
        }
        .term-btn:hover { border-color: var(--accent); color: var(--accent); box-shadow: var(--pressed-shadow), 0 0 14px var(--neo-glow); }
        .term-btn:active { box-shadow: var(--pressed-shadow); transform: translateY(1px); }
        .term-btn.primary {
            border-color: transparent; background: linear-gradient(145deg, var(--accent-hi), var(--accent-2)); color: var(--on-accent);
            box-shadow: 5px 5px 12px rgba(4,14,14,.42), -3px -3px 9px rgba(95,255,190,.16), 0 0 18px var(--neo-glow);
        }
        .term-btn.primary:hover { border-color: transparent; background: linear-gradient(145deg, var(--accent), var(--accent-2)); color: var(--on-accent); }
        .proj {
            border: 1px solid var(--neo-edge); border-radius: 17px; background: linear-gradient(145deg, var(--card), var(--card-2)); box-shadow: var(--raised-shadow);
        }
        .proj:hover { border-color: var(--line-2); box-shadow: var(--raised-shadow), 0 0 18px var(--neo-glow); }
        .proj-img, .proj-ph { border-color: var(--line); border-radius: 13px 13px 0 0; }
        .proj-body { padding: 17px; }
        .row { border-color: var(--line); }
        .ci {
            min-height: 58px; border: 1px solid var(--neo-edge); border-radius: 15px;
            background: linear-gradient(145deg, var(--card), var(--card-2)); color: var(--ink); box-shadow: var(--raised-shadow);
        }
        .ci:hover { border-color: var(--accent); background: var(--card); box-shadow: var(--pressed-shadow), 0 0 12px var(--neo-glow); }
        .ci-ico { border: 1px solid var(--neo-edge); border-radius: 11px; background: var(--card-2); box-shadow: var(--pressed-shadow); }
        .foot { border-color: var(--line); }
        :root[data-theme="light"] .side a.active { box-shadow: var(--pressed-shadow), 0 0 14px var(--neo-glow); }
        :root[data-theme="light"] .term-btn.primary { color: #fff; }
        @media (max-width: 800px) {
            .topbar { margin: 10px 12px 0; border-radius: 18px; }
            .layout { gap: 14px; padding: 14px 12px; }
            .side { border-radius: 18px; }
        }
        @media (max-width: 520px) {
            .topbar { margin: 8px 8px 0; }
            .term-win { border-radius: 18px; }
            .term-body { margin: 0 8px 8px; padding: 14px; }
            .hero-main .term-body { padding: 16px; }
        }
        /* END NEOMORPHIC TERMINAL SURFACES */
    </style>
</head>
<body>
<noscript><style>.reveal, .skill-tile { opacity: 1 !important; transform: none !important; }</style></noscript>
<a class="skip-link" href="#main">Skip to content</a>
<div class="bg-orb" aria-hidden="true"></div>
<div class="bg-orb" aria-hidden="true"></div>
<div class="scroll-progress" aria-hidden="true"></div>

<nav class="topbar" aria-label="Primary navigation">
    <a class="logo-prompt" href="#home" data-nav="home" aria-label="Go to portfolio home">root@portfolio:~</a>
    @include('portfolio.partials.theme-toggle')
</nav>

<div class="layout">
    <nav class="side" aria-label="Section navigation">
        @foreach($sideNav as $item)
            <a href="#{{ $item[0] }}" data-nav="{{ $item[0] }}">{!! $icon($item[2]) !!} {{ $item[1] }}</a>
        @endforeach
        <div class="side-note">Let's build something awesome together.<div class="side-logo">{{ $initials }}</div></div>
    </nav>

    <main class="main" id="main">
        <section class="term-win hero-main" id="home" aria-labelledby="home-heading">
            <div class="term-bar">
                <span class="term-dots" aria-hidden="true"></span>
                <span class="term-title">bash — {{ $initials }}@portfolio</span>
            </div>
            <div class="term-body hero-body">
                @if(!empty($info->photo_url))
                    <img class="hero-photo" src="{{ $info->photo_url }}" alt="Photo of {{ $name }}">
                @endif
                <div class="hero-text">
                    <p class="term-line"><span class="term-ps">$</span> <span class="term-cmd">whoami</span></p>
                    <h1 class="hero-name" id="home-heading">{{ $name }}</h1>
                    <p class="hero-role" data-roles='@json(explode(" / ", $hl))'><span class="sr-only">{{ $hl }}</span><span aria-hidden="true"><span id="typewriter-prefix">{{ $hlRest }}@if($hlRest) @endif</span><span class="term-hl" id="typewriter-target">{{ $hlLast }}</span></span><span class="term-cursor" aria-hidden="true"></span></p>
                    @if(!empty($info->location))
                        <p class="term-meta"><span>{!! $icon('pin', 14) !!} {{ $info->location }}</span></p>
                    @endif
                    @if($projects->count() || $hasContact)
                        <div class="btn-row">
                            @if($projects->count())
                                <a class="term-btn primary" href="#projects"><span class="term-ps">&gt;</span> View projects</a>
                            @endif
                            @if($hasContact)
                                <a class="term-btn {{ $projects->count() ? '' : 'primary' }}" href="#contact">Contact me</a>
                            @endif
                        </div>
                    @endif
                    @if($hasLinks)
                        <div class="btn-row hero-links">
                            @foreach($links as $l)
                                <a class="term-btn" href="{{ $l->url }}" target="_blank" rel="noopener">{!! $brandIcon($l->platform) !!} {{ ucfirst($l->platform) }}<span class="sr-only"> (opens in a new tab)</span></a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="nav-sentinel" aria-hidden="true"></div>

        <div class="content-grid">
            @if($hasBio)
                <section class="term-win reveal full-width" id="about" aria-labelledby="about-title">
                    <div class="term-bar">
                        <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="about-title">cat about.md</h2>
                    </div>
                    <div class="term-body">
                        <p class="term-bio">{{ $info->bio }}</p>
                    </div>
                </section>
            @endif

            @if($skillList->count())
                <section class="term-win reveal" id="skills" aria-labelledby="skills-title">
                    <div class="term-bar">
                    <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="skills-title">ls skills/</h2>
                    </div>
                    <div class="term-body">
                        <div class="skill-grid">
                            @foreach($skillList as $sk)
                                @php [$cls, $col] = $resolveIcon($sk->name); @endphp
                                <div class="skill-tile reveal" style="transition-delay: {{ $loop->index * 60 }}ms">
                                    @if($cls)
                                        <i class="{{ $cls }}" style="{{ $col ? 'color:'.$col : '' }}" aria-hidden="true"></i>
                                    @else
                                        <span class="skill-fb" aria-hidden="true">{{ mb_strtoupper(mb_substr($sk->name,0,2)) }}</span>
                                    @endif
                                    <span>{{ $sk->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if($projects->count())
                <section class="term-win reveal full-width" id="projects" aria-labelledby="projects-title">
                    <div class="term-bar">
                    <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="projects-title">ls projects/</h2>
                    </div>
                    <div class="term-body">
                        <div class="proj-grid">
                            @foreach($projects as $project)
                                <article class="proj reveal" style="transition-delay: {{ $loop->index * 80 }}ms">
                                    @if(!empty($project->screenshot_url))
                                        <img class="proj-img" src="{{ $project->screenshot_url }}" alt="Screenshot of {{ $project->title }}" loading="lazy">
                                    @else
                                        <div class="proj-ph" aria-hidden="true">{!! $icon('code', 32) !!}</div>
                                    @endif
                                    <div class="proj-body">
                                        <h3>{{ $project->title }}</h3>
                                        @if(!empty($project->description))
                                            <p>{{ $project->description }}</p>
                                        @endif
                                        <div class="proj-links">
                                            @if(!empty($project->live_url))
                                                <a class="term-btn" href="{{ $project->live_url }}" target="_blank" rel="noopener">$ open live site</a>
                                            @endif
                                            @if(!empty($project->repo_url))
                                                <a class="term-btn" href="{{ $project->repo_url }}" target="_blank" rel="noopener">$ source code</a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if($experiences->count())
                <section class="term-win reveal" id="experience" aria-labelledby="experience-title">
                    <div class="term-bar">
                    <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="experience-title">cat experience.json</h2>
                    </div>
                    <div class="term-body">
                        @foreach($experiences as $exp)
                            @php
                                $s = $fmtDate($exp->start_date);
                                $e = $fmtDate($exp->end_date) ?: 'Present';
                            @endphp
                            <div class="row reveal" style="transition-delay: {{ $loop->index * 80 }}ms">
                                <div class="row-date">{{ $s }}{{ $s ? ' - ' : '' }}{{ $e }}</div>
                                <div>
                                    <h3>{{ $exp->role ?? 'Role' }}</h3>
                                    <p class="sub">{{ $exp->company }}{{ !empty($exp->is_internship) ? ' (Internship)' : '' }}</p>
                                    @if(!empty($exp->description))
                                        <p>{{ $exp->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($education->count())
                <section class="term-win reveal" id="education" aria-labelledby="education-title">
                    <div class="term-bar">
                    <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="education-title">cat education.md</h2>
                    </div>
                    <div class="term-body">
                        @foreach($education as $edu)
                            <div class="row reveal" style="transition-delay: {{ $loop->index * 80 }}ms">
                                <div class="row-date">{{ $edu->start_year }}{{ ($edu->start_year && $edu->end_year) ? ' - ' : '' }}{{ $edu->end_year }}</div>
                                <div>
                                    <h3>{{ $edu->institution }}</h3>
                                    @if(!empty($edu->degree))
                                        <p class="sub">{{ $edu->degree }}{{ !empty($edu->field) ? ', ' . $edu->field : '' }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($hasContact)
                <section class="term-win reveal full-width" id="contact" aria-labelledby="contact-title">
                    <div class="term-bar">
                        <span class="term-dots" aria-hidden="true"></span>
                        <h2 class="term-title section-heading" id="contact-title">./contact.sh</h2>
                    </div>
                    <div class="term-body">
                        <div class="contact">
                            @if(!empty($info->contact_email))
                                <a class="ci reveal" href="mailto:{{ $info->contact_email }}">
                                    <span class="ci-ico">{!! $icon('mail', 18) !!}</span>
                                    <span><small>Email</small><b>{{ $info->contact_email }}</b></span>
                                </a>
                            @endif
                            @if(!empty($info->phone))
                                <a class="ci reveal" href="tel:{{ preg_replace('/[^0-9+]/', '', $info->phone) }}">
                                    <span class="ci-ico">{!! $icon('phone', 18) !!}</span>
                                    <span><small>Phone</small><b>{{ $info->phone }}</b></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </main>

</div>

<footer class="foot">
    <span>&copy; {{ date('Y') }} {{ $name }}</span>
    <span>Built with Portfold</span>
</footer>

@include('portfolio.partials.theme-toggle-script')
<script>
    (function () {
        var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var supportsIntersectionObserver = 'IntersectionObserver' in window;
        var navLinks = {};

        document.querySelectorAll('[data-nav]').forEach(function (link) {
            var key = link.getAttribute('data-nav');
            (navLinks[key] = navLinks[key] || []).push(link);
        });

        function setActiveNav(activeId) {
            Object.keys(navLinks).forEach(function (key) {
                navLinks[key].forEach(function (link) {
                    link.classList.toggle('active', key === activeId);
                    if (key === activeId) { link.setAttribute('aria-current', 'location'); } else { link.removeAttribute('aria-current'); }
                });
            });
        }

        if (supportsIntersectionObserver) {
            var sectionObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) { return; }
                    var activeId = entry.target.id;
                    setActiveNav(activeId);
                });
            }, { rootMargin: '-30% 0px -60% 0px' });
            document.querySelectorAll('#home, #about, section[id]').forEach(function (section) {
                sectionObserver.observe(section);
            });
        } else {
            setActiveNav('home');
        }

        var progressBar = document.querySelector('.scroll-progress');
        function updateScrollProgress() {
            var scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progressPercent = scrollableHeight > 0
                ? Math.min(100, Math.max(0, (window.scrollY / scrollableHeight) * 100))
                : 0;
            if (progressBar) { progressBar.style.width = progressPercent + '%'; }
        }
        window.addEventListener('scroll', updateScrollProgress, { passive: true });
        updateScrollProgress();

        var topbar = document.querySelector('.topbar');
        var navSentinel = document.querySelector('.nav-sentinel');
        if (supportsIntersectionObserver && topbar && navSentinel) {
            var navbarObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    topbar.classList.toggle('scrolled', entry.boundingClientRect.top < 60);
                });
            }, { rootMargin: '-60px 0px 0px 0px', threshold: 0 });
            navbarObserver.observe(navSentinel);
        }

        var revealElements = document.querySelectorAll('.reveal:not(.skill-tile)');
        var skillGrids = document.querySelectorAll('.skill-grid');
        if (prefersReducedMotion || !supportsIntersectionObserver) {
            revealElements.forEach(function (element) { element.classList.add('visible'); });
            skillGrids.forEach(function (grid) { grid.classList.add('visible'); });
        } else {
            var revealObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) { return; }
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                });
            }, { rootMargin: '0px 0px -60px 0px' });
            revealElements.forEach(function (element) { revealObserver.observe(element); });

            var skillsObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) { return; }
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                });
            }, { rootMargin: '0px 0px -40px 0px' });
            skillGrids.forEach(function (grid) { skillsObserver.observe(grid); });
        }

        var roleHeading = document.querySelector('.hero-role[data-roles]');
        if (roleHeading && !prefersReducedMotion) {
            try {
                var roles = JSON.parse(roleHeading.getAttribute('data-roles') || '[]')
                    .map(function (role) { return String(role).trim(); })
                    .filter(Boolean);
                if (roles.length > 1) {
                    var prefixNode = document.getElementById('typewriter-prefix');
                    var targetNode = document.getElementById('typewriter-target');
                    var roleParts = roles.map(function (role) {
                        var words = role.split(/\s+/);
                        return { prefix: words.slice(0, -1).join(' '), ending: words[words.length - 1] || '' };
                    });
                    var roleIndex = 0;
                    var typedText = roleParts[0].ending;
                    var deleting = false;
                    var cycles = 0;

                    function writePrefix(role) {
                        if (prefixNode) { prefixNode.textContent = role.prefix ? role.prefix + ' ' : ''; }
                    }
                    function renderEnding(text) {
                        if (targetNode) { targetNode.textContent = text; }
                    }
                    function characterDelay() { return 60 + Math.random() * 60; }
                    function typeNextCharacter() {
                        var role = roleParts[roleIndex];
                        if (deleting) {
                            typedText = typedText.slice(0, -1);
                            renderEnding(typedText);
                            if (!typedText) {
                                roleIndex = (roleIndex + 1) % roleParts.length;
                                role = roleParts[roleIndex];
                                writePrefix(role);
                                deleting = false;
                                if (roleIndex === 0 && ++cycles >= 2) { renderEnding(role.ending); return; }
                                window.setTimeout(typeNextCharacter, characterDelay());
                                return;
                            }
                            window.setTimeout(typeNextCharacter, characterDelay());
                            return;
                        }
                        typedText += role.ending.charAt(typedText.length);
                        renderEnding(typedText);
                        if (typedText === role.ending) {
                            deleting = true;
                            window.setTimeout(typeNextCharacter, 1800);
                            return;
                        }
                        window.setTimeout(typeNextCharacter, characterDelay());
                    }

                    writePrefix(roleParts[0]);
                    renderEnding(typedText);
                    window.setTimeout(function () {
                        deleting = true;
                        typeNextCharacter();
                    }, 1800);
                }
            } catch (error) {
                roleHeading.setAttribute('data-roles', '[]');
            }
        }

        var isTouchDevice = window.matchMedia('(hover: none)').matches;
        if (!isTouchDevice && !prefersReducedMotion) {
            document.querySelectorAll('.proj').forEach(function (card) {
                card.addEventListener('mousemove', function (event) {
                    var bounds = card.getBoundingClientRect();
                    if (!bounds.width || !bounds.height) { return; }
                    var horizontal = (event.clientX - bounds.left) / bounds.width;
                    var vertical = (event.clientY - bounds.top) / bounds.height;
                    var rotateY = (horizontal - .5) * 12;
                    var rotateX = (.5 - vertical) * 12;
                    card.style.transition = 'none';
                    card.style.transform = 'perspective(900px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
                });
                card.addEventListener('mouseleave', function () {
                    card.style.transition = 'transform .35s ease';
                    card.style.transform = 'perspective(900px) rotateX(0deg) rotateY(0deg)';
                    window.setTimeout(function () {
                        card.style.transition = '';
                        card.style.transform = '';
                    }, 380);
                });
            });
        }
    })();
</script>
</body>
</html>