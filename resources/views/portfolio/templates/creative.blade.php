@php
    $name = trim((string) ($info->full_name ?? '')) ?: 'Your Name';
    $parts = array_values(array_filter(preg_split('/\s+/', $name)));
    $lastWord = count($parts) > 1 ? array_pop($parts) : null;
    $firstWords = implode(' ', $parts);

    $hasProjects = $projects->count() > 0;
    $gh = $links->first(function ($l) { return strtolower((string) $l->platform) === 'github'; });

    $paths = [
        'pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'monitor' => '<rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/>',
        'server' => '<rect width="20" height="8" x="2" y="2" rx="2"/><rect width="20" height="8" x="2" y="14" rx="2"/><path d="M6 6h.01"/><path d="M6 18h.01"/>',
        'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z"/>',
        'bolt' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
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

    // Skills can be typed as "Laravel:4" (level 1 to 5). Without a level, no dots are shown.
    $skillList = collect($skills ?? [])->map(function ($sk) {
        $p = explode(':', (string) $sk->name, 2);
        $level = (isset($p[1]) && is_numeric(trim($p[1]))) ? max(1, min(5, (int) trim($p[1]))) : null;
        return (object) ['name' => trim($p[0]), 'level' => $level];
    });
    $orbitPositions = [[50, 7], [81, 20], [93, 50], [81, 80], [50, 93], [19, 80], [7, 50], [19, 20]];
    $skillMonogram = function ($name) {
        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) $name));
        $known = [
            'html' => 'H5', 'html5' => 'H5', 'css' => 'C3', 'css3' => 'C3',
            'javascript' => 'JS', 'js' => 'JS', 'typescript' => 'TS', 'ts' => 'TS',
            'react' => 'R', 'vue' => 'V', 'angular' => 'A', 'svelte' => 'S',
            'tailwind' => 'TW', 'tailwindcss' => 'TW', 'bootstrap' => 'B',
            'php' => 'PHP', 'laravel' => 'LV', 'python' => 'PY', 'java' => 'JV',
            'node' => 'N', 'nodejs' => 'N', 'mysql' => 'SQL', 'postgresql' => 'PG',
            'mongodb' => 'DB', 'firebase' => 'FB', 'flutter' => 'FL',
        ];
        if (isset($known[$key])) { return $known[$key]; }
        $words = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        return strtoupper(substr($words[0] ?? 'SK', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0] ?? 'K', 1, 1)));
    };
    $skillDevicon = function ($name) {
        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', (string) $name));
        $icons = [
            'html' => 'devicon-html5-plain', 'html5' => 'devicon-html5-plain',
            'css' => 'devicon-css3-plain', 'css3' => 'devicon-css3-plain',
            'javascript' => 'devicon-javascript-plain', 'js' => 'devicon-javascript-plain',
            'typescript' => 'devicon-typescript-plain', 'ts' => 'devicon-typescript-plain',
            'react' => 'devicon-react-original', 'vue' => 'devicon-vuejs-plain',
            'angular' => 'devicon-angularjs-plain', 'php' => 'devicon-php-plain',
            'laravel' => 'devicon-laravel-plain', 'python' => 'devicon-python-plain',
            'java' => 'devicon-java-plain', 'rust' => 'devicon-rust-original',
            'c' => 'devicon-csharp-plain', 'csharp' => 'devicon-csharp-plain',
            'node' => 'devicon-nodejs-plain', 'nodejs' => 'devicon-nodejs-plain',
            'mysql' => 'devicon-mysql-plain', 'postgresql' => 'devicon-postgresql-plain',
            'mongodb' => 'devicon-mongodb-plain', 'flutter' => 'devicon-flutter-plain',
            'go' => 'devicon-go-plain', 'golang' => 'devicon-go-plain',
            'tailwind' => 'devicon-tailwindcss-original', 'tailwindcss' => 'devicon-tailwindcss-original',
            'bootstrap' => 'devicon-bootstrap-plain', 'svelte' => 'devicon-svelte-plain',
        ];
        return $icons[$key] ?? null;
    };

    $frontKeys = ['html', 'html5', 'css', 'css3', 'javascript', 'js', 'typescript', 'ts', 'react', 'vue', 'angular', 'svelte', 'tailwind', 'tailwindcss', 'bootstrap', 'sass', 'next', 'nextjs', 'nuxt', 'flutter'];
    $backKeys = ['php', 'laravel', 'python', 'java', 'c#', 'csharp', 'c++', 'c', 'node', 'nodejs', 'express', 'django', 'flask', 'mysql', 'postgresql', 'mongodb', 'redis', 'firebase', 'graphql', 'rust', 'go', 'golang', 'sql'];
    $key = function ($sk) { return strtolower(trim($sk->name)); };
    $groups = [
        'Frontend' => ['monitor', $skillList->filter(function ($s) use ($frontKeys, $key) { return in_array($key($s), $frontKeys); })],
        'Backend' => ['server', $skillList->filter(function ($s) use ($backKeys, $key) { return in_array($key($s), $backKeys); })],
        'Tools & Others' => ['wrench', $skillList->filter(function ($s) use ($frontKeys, $backKeys, $key) { return !in_array($key($s), $frontKeys) && !in_array($key($s), $backKeys); })],
    ];

    $fmtDate = function ($v) {
        $v = trim((string) $v);
        if ($v === '') { return ''; }
        try {
            return strtotime($v) ? \Carbon\Carbon::parse($v)->format('M Y') : $v;
        } catch (\Throwable $e) {
            return $v;
        }
    };

    $nav = [['home', 'Home']];
    if (!empty($info->bio)) { $nav[] = ['about', 'About']; }
    if ($skillList->count()) { $nav[] = ['skills', 'Skills']; }
    if ($hasProjects) { $nav[] = ['projects', 'Projects']; }
    if ($experiences->count()) { $nav[] = ['experience', 'Experience']; }
    if ($education->count()) { $nav[] = ['education', 'Education']; }
    $nav[] = ['contact', 'Contact'];
    $num = [];
    foreach ($nav as $i => $item) { $num[$item[0]] = str_pad($i + 1, 2, '0', STR_PAD_LEFT); }
@endphp
<!DOCTYPE html>
<html lang="en" data-default-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
    @include('portfolio.partials.theme-init')
    @vite('resources/js/creative-hero.js')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #080713; --panel: #100d20; --ink: #F6F2FF; --muted: #B9B2CD; --faint: #8A83A3; --line: #2B2443;
            --red: #A878FF; --red-dark: #8D51F5; --red-soft: #25183D; --side: #090714; --side-dim: #A49BBC;
            --theme-control-bg: rgba(255,255,255,.04); --theme-control-border: rgba(174,145,255,.34); --theme-control-hover: rgba(174,145,255,.14); --theme-control-active: #A878FF;
            --head: 'Syne', system-ui, sans-serif; --body: 'Inter', system-ui, sans-serif; --script: 'Caveat', cursive;
        }
        :root[data-theme="light"] {
            color-scheme: light;
            --bg: #f6f3fb; --panel: #fff; --ink: #241d31; --muted: #5c536c; --faint: #746a82; --line: #e1d9ec;
            --red: #7544c2; --red-dark: #6538af; --red-soft: #eee7f8; --side: #eee9f5; --side-dim: #5e556a;
            --theme-control-bg: rgba(255,255,255,.82); --theme-control-border: #d5c8e8; --theme-control-hover: #f0e9fb; --theme-control-active: #7544c2;
        }
        html { background: var(--bg); color: var(--ink); font-family: var(--body); scroll-behavior: smooth; overflow-x: clip; }
        body { min-width: 320px; overflow-x: clip; }
        a:focus-visible, button:focus-visible { outline: 3px solid var(--red); outline-offset: 3px; }
        @include('portfolio.partials.theme-toggle-styles')
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition: none !important; }
            .orbit-skill, .portal-aura, .portal-ring { animation: none !important; }
        }
        .ico { flex-shrink: 0; }

        .shell { display: grid; grid-template-columns: clamp(150px, 10.6vw, 190px) minmax(0, 1fr); min-height: 100vh; background: var(--bg); }

        /* Sidebar */
        .side { background: var(--side); position: sticky; top: var(--pf-preview-bar-height, 0px); height: calc(100vh - var(--pf-preview-bar-height, 0px)); overflow-y: auto; padding: 180px 16px 20px; display: flex; flex-direction: column; border-right: 1px solid rgba(174,145,255,.13); scrollbar-width: thin; scrollbar-color: rgba(174,145,255,.28) transparent; }
        .side::-webkit-scrollbar { width: 6px; }
        .side::-webkit-scrollbar-thumb { border-radius: 8px; background: rgba(174,145,255,.28); }
        .s-name, .s-role { display: none; }
        .s-meta { display: none; font-size: .72rem; color: var(--side-dim); flex-direction: column; gap: 6px; }
        .s-meta span { display: flex; align-items: center; gap: 8px; }
        .s-meta .ico { color: var(--red); }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: #2ECC71; margin: 0 3px; }
        .s-line { width: 72%; height: 1px; background: rgba(174,145,255,.22); margin: 0 auto 18px; }
        .s-nav { display: grid; gap: 7px; }
        .s-nav a { position: relative; display: flex; align-items: center; gap: 10px; min-height: 54px; padding: 10px 10px 10px 12px; border-radius: 8px; font-size: .84rem; color: var(--side-dim); text-decoration: none; transition: background .18s, color .18s; white-space: nowrap; }
        .s-nav a b { font-weight: 500; font-size: .68rem; color: #827a9e; width: 28px; flex: 0 0 28px; }
        .s-nav a:hover, .s-nav a.active { color: #fff; background: rgba(158,116,255,.14); }
        .s-nav a.active::before { content: ''; position: absolute; left: -16px; top: 8px; bottom: 8px; width: 3px; border-radius: 0 3px 3px 0; background: #b47bff; box-shadow: 0 0 14px #9d55ff; }
        .s-links { margin-top: 32px; padding-top: 17px; border-top: 1px solid rgba(174,145,255,.13); }
        .s-links h4 { font-size: .62rem; letter-spacing: .15em; color: #827a9e; margin-bottom: 9px; }
        .s-links a { display: flex; align-items: center; gap: 10px; min-height: 40px; padding: 5px 0; font-size: .78rem; color: var(--side-dim); text-decoration: none; white-space: nowrap; }
        .s-links a:hover { color: #fff; }
        .s-foot { margin-top: 18px; font-size: .62rem; line-height: 1.65; color: #827a9e; overflow-wrap: anywhere; }

        /* Hero */
        .content { min-width: 0; padding: 80px 0 0; }
        .topbar { position: fixed; top: calc(var(--pf-preview-bar-height, 0px) + 12px); left: 18px; right: 18px; z-index: 30; min-height: 68px; display: grid; grid-template-columns: minmax(220px, 1fr) auto minmax(120px, 1fr); align-items: center; gap: 18px; padding: 0 30px; border: 1px solid rgba(174,145,255,.27); border-radius: 17px; background: radial-gradient(ellipse 42% 330% at 51% 135%, rgba(157,83,255,.55), rgba(100,48,227,.22) 40%, transparent 78%), linear-gradient(100deg, rgba(12,9,29,.91), rgba(18,12,43,.82) 52%, rgba(12,9,29,.91)); box-shadow: inset 0 1px rgba(255,255,255,.06), 0 8px 34px rgba(0,0,0,.28), 0 0 36px rgba(126,62,255,.18); backdrop-filter: blur(20px); }
        .top-brand { display: inline-flex; align-items: center; gap: 16px; min-width: 0; color: #f7f3ff; text-decoration: none; font-size: .82rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; white-space: nowrap; }
        .top-brand svg { width: 30px; height: 32px; flex: 0 0 auto; filter: drop-shadow(0 0 9px rgba(163,105,255,.75)); }
        .top-brand span { overflow: hidden; text-overflow: ellipsis; }
        .top-nav { display: flex; align-items: center; justify-content: center; gap: clamp(12px, 2.2vw, 36px); min-width: 0; }
        .top-nav a { position: relative; display: grid; place-items: center; min-height: 54px; padding: 0 3px; color: #b8b1cf; font-size: .88rem; text-decoration: none; white-space: nowrap; }
        .top-nav a:hover, .top-nav a.active { color: #fff; }
        .top-nav a.active::after { content: ''; position: absolute; bottom: 2px; left: 0; right: 0; height: 2px; border-radius: 3px; background: #aa78ff; box-shadow: 0 0 12px #9a56ff; }
        .top-social { display: flex; align-items: center; justify-content: flex-end; gap: 18px; }
        .top-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; min-width: 0; }
        .top-social a { display: grid; place-items: center; width: 28px; height: 36px; color: #ded8ef; font-size: 1.02rem; text-decoration: none; }
        .top-social a:hover { color: #bd98ff; }
        .hero { position: relative; display: flex; align-items: center; min-height: max(620px, calc(100svh - 82px)); margin: 0; border: 0; border-radius: 0; overflow: hidden; background: #080713; isolation: isolate; }
        .hero .fb { position: absolute; z-index: -1; inset: 0; background: linear-gradient(135deg, #080713, #100b25 58%, #090817); }
        .hero-scene { position: absolute; z-index: 0; inset: 0; overflow: hidden; pointer-events: none; opacity: 1; background: radial-gradient(ellipse 54% 40% at 44% 8%, rgba(128,57,255,.34), transparent 88%), radial-gradient(ellipse 42% 68% at 78% 38%, rgba(111,61,255,.2), transparent 77%), radial-gradient(ellipse 80% 54% at 52% 100%, rgba(102,47,220,.2), transparent 80%); }
        .visual-filters { position: absolute; width: 0; height: 0; overflow: hidden; }
        .hero-canvas { position: absolute; z-index: 0; inset: 0; width: 100%; height: 100%; display: block; }
        .hero::after { content: ''; position: absolute; z-index: 1; inset: 0; background: linear-gradient(90deg, rgba(8,7,19,.68) 0%, rgba(8,7,19,.42) 27%, rgba(8,7,19,.14) 49%, rgba(8,7,19,.03) 68%, transparent 100%); pointer-events: none; }
        .hero-in { position: relative; z-index: 4; width: min(58%, 760px); padding: 56px clamp(44px, 4.35vw, 74px); display: flex; flex-direction: column; justify-content: center; }
        .role-pill { display: inline-flex; align-items: center; gap: 10px; width: fit-content; min-height: 40px; margin-bottom: 27px; padding: 7px 14px; border: 1px solid rgba(153,96,255,.35); border-radius: 99px; background: rgba(97,52,176,.12); color: #c3adf1; font-size: .78rem; }
        .role-pill i { width: 7px; height: 7px; border-radius: 50%; background: #a45cff; box-shadow: 0 0 10px #a45cff; }
        .hello { font-size: 1rem; font-weight: 600; letter-spacing: .18em; color: #b782ff; text-transform: uppercase; margin-bottom: 12px; }
        .hero h1 { max-width: 650px; font-family: var(--head); font-weight: 800; font-size: clamp(3rem, 5.15vw, 5.15rem); line-height: .94; letter-spacing: -.05em; color: #fff; text-transform: uppercase; overflow-wrap: anywhere; }
        .hero h1 span, .hero h1 em { display: block; font-style: normal; }
        .hero h1 em { color: #9957ff; }
        .hero p { font-size: 1rem; line-height: 1.7; color: #F1E9E9; max-width: min(540px, 40vw); margin: 20px 0 24px; }
        .hbtns { display: flex; flex-wrap: wrap; gap: 10px; }
        .hbtn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 58px; padding: 13px 26px; font-size: .9rem; font-weight: 600; text-decoration: none; border-radius: 10px; transition: background .15s, border-color .15s, transform .15s; }
        .hbtn:hover { transform: translateY(-1px); }
        .hbtn.red { background: linear-gradient(135deg, #a551ff, #7330e8); color: #fff; box-shadow: 0 0 20px rgba(149,74,255,.38); }
        .hbtn.red:hover { background: linear-gradient(135deg, #b46bff, #8245f2); }
        .hbtn.line { border: 1px solid rgba(183,151,255,.6); color: #fff; }
        .hbtn.line:hover { background: rgba(255,255,255,.12); }
        .hero-tag { display: none; position: absolute; z-index: 3; right: 34px; top: 30px; gap: 12px; }
        .hero-tag div { text-align: right; font-size: .6rem; letter-spacing: .2em; line-height: 2; color: rgba(255,255,255,.6); font-weight: 600; }
        .hero-tag i { width: 1px; background: rgba(255,255,255,.4); }
        .skill-orbit { position: absolute; z-index: 2; top: 51%; right: clamp(28px, 4.3vw, 74px); width: clamp(320px, 36vw, 610px); aspect-ratio: 1; transform: translateY(-50%); border-radius: 50%; background: radial-gradient(circle, rgba(128,88,255,.22), rgba(28,19,65,.1) 48%, transparent 73%); pointer-events: none; }
        .skill-orbit::before, .skill-orbit::after { content: ''; position: absolute; inset: 8%; border: 1px solid rgba(184,164,255,.18); border-radius: 50%; }
        .skill-orbit::after { inset: 24%; border-color: rgba(184,164,255,.12); }
        .orbit-lines { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; }
        .orbit-lines circle, .orbit-lines path { fill: none; stroke: rgba(180,162,255,.18); stroke-width: .7; }
        .orbit-center { position: absolute; z-index: 1; left: 50%; top: 50%; display: grid; place-items: center; width: 36%; aspect-ratio: 1; border: 2px solid rgba(194,132,255,.95); border-radius: 50%; background: radial-gradient(circle, rgba(113,60,224,.3), rgba(13,9,31,.95) 68%); box-shadow: 0 0 0 10px rgba(146,83,255,.1), 0 0 42px rgba(151,71,255,.78), inset 0 0 26px rgba(154,89,255,.34); transform: translate(-50%, -50%); }
        .orbit-photo, .orbit-placeholder { width: calc(100% - 10px); height: calc(100% - 10px); overflow: hidden; border-radius: 50%; }
        .orbit-photo { object-fit: cover; object-position: center 28%; filter: saturate(.82) contrast(1.04); }
        .orbit-placeholder { display: grid; place-items: center; color: #e7dcff; background: radial-gradient(circle at 38% 28%, #7050a7, #17112c 68%); font: 800 clamp(2rem, 4vw, 3.5rem)/1 var(--head); }
        .orbit-placeholder svg { width: 48%; height: 48%; color: #e0cfff; filter: drop-shadow(0 0 12px rgba(183,120,255,.9)); }
        .orbit-skill { position: absolute; left: calc(var(--x) * 1%); top: calc(var(--y) * 1%); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 7px; width: clamp(78px, 5.7vw, 98px); min-height: clamp(78px, 5.7vw, 98px); padding: 11px 8px 9px; border: 1px solid rgba(151,97,255,.78); border-radius: 17px; background: linear-gradient(145deg, rgba(22,17,51,.94), rgba(9,10,30,.94)); box-shadow: 0 8px 30px rgba(3,3,13,.42), 0 0 24px rgba(142,102,255,.2), inset 0 0 18px rgba(123,72,255,.08); color: #f7f3ff; transform: translate(-50%, -50%); animation: skill-drift 5s ease-in-out infinite; animation-delay: var(--delay); backdrop-filter: blur(8px); }
        .orbit-skill-mark { display: grid; place-items: center; min-width: 42px; height: 42px; padding: 0 3px; color: #ddd0ff; font: 800 1.3rem/1 var(--head); letter-spacing: -.04em; text-shadow: 0 0 14px rgba(190,159,255,.75); }
        .orbit-skill-logo { display: grid; place-items: center; height: 42px; color: #f5f0ff; font-size: 2rem; line-height: 1; filter: drop-shadow(0 0 8px rgba(173,131,255,.78)); }
        .orbit-skill-icon { display: grid; place-items: center; height: 42px; color: #f7f2ff; font-size: 2rem; line-height: 1; filter: grayscale(1) brightness(1.75) drop-shadow(0 0 7px rgba(173,131,255,.82)); }
        .orbit-skill-name { max-width: 84px; overflow: hidden; color: rgba(241,235,255,.92); font-size: .68rem; font-weight: 600; letter-spacing: .035em; text-overflow: ellipsis; white-space: nowrap; }
        @keyframes skill-drift { 0%, 100% { transform: translate(-50%, -50%); } 50% { transform: translate(-50%, calc(-50% - 8px)); } }

        /* Sections */
        .sec { padding: 42px 22px; scroll-margin-top: calc(var(--pf-preview-bar-height, 0px) + 96px); }
        .sec + .sec { border-top: 1px solid var(--line); }
        .label { display: flex; align-items: center; gap: 10px; font-size: .6rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--faint); }
        .label b { color: var(--red-dark); }
        .label::after { content: ''; width: 34px; height: 1px; background: var(--red); }
        .sec h2 { font-family: var(--head); font-weight: 800; font-size: clamp(1.5rem, 2.3vw, 2rem); line-height: 1.15; margin: 8px 0 22px; }

        .about { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(190px, .85fr) minmax(170px, .7fr); }
        .about > div { min-width: 0; padding: 0 24px; border-left: 1px solid var(--line); }
        .about > div:first-child { padding-left: 0; border-left: 0; }
        .about p { font-size: .92rem; line-height: 1.8; color: var(--muted); }
        .focus h4 { font-size: .78rem; font-weight: 600; margin-bottom: 10px; }
        .focus ul { list-style: none; }
        .focus li { display: flex; align-items: center; gap: 10px; min-height: 36px; font-size: .84rem; color: var(--muted); padding: 5px 0; }
        .focus .ib { width: 24px; height: 24px; border: 1px solid var(--line); border-radius: 6px; display: grid; place-items: center; color: var(--red-dark); flex: 0 0 24px; }
        .quote { max-width: 19ch; font-family: var(--head); font-size: clamp(1.15rem, 1.8vw, 1.55rem); font-weight: 700; line-height: 1.35; color: var(--ink); padding-top: 4px; }
        .quote svg { display: block; margin-top: 4px; }

        .cap { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr)); gap: 16px; }
        .capcard { background: var(--panel); border: 1px solid var(--line); border-radius: 10px; padding: 20px 22px; }
        .capcard h3 { display: flex; align-items: center; gap: 10px; font-family: var(--head); font-weight: 700; font-size: .9rem; margin-bottom: 12px; }
        .capcard h3 .ico { color: var(--red); }
        .srow { display: flex; justify-content: space-between; align-items: center; gap: 10px; min-height: 38px; padding: 6px 0; font-size: .84rem; color: var(--muted); }
        .dots { display: flex; gap: 4px; }
        .dots i { width: 7px; height: 7px; border-radius: 50%; background: #E4DAD3; }
        .dots i.on { background: var(--red-dark); }

        .pgrid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 390px), 1fr)); gap: 18px; }
        .pcard { position: relative; display: grid; grid-template-columns: 132px minmax(0, 1fr); gap: 16px; background: var(--panel); border: 1px solid var(--line); border-radius: 10px; padding: 16px; transition: border-color .18s, transform .18s, box-shadow .18s; }
        .pcard:hover, .pcard:focus-within { border-color: var(--red); transform: translateY(-2px); box-shadow: 0 10px 24px rgba(27,18,20,.08); }
        .pthumb { width: 132px; height: 100px; border-radius: 7px; object-fit: cover; }
        .pph { width: 132px; height: 100px; border-radius: 7px; display: grid; place-items: center; background: var(--red-soft); color: var(--red-dark); font-family: var(--head); font-weight: 800; font-size: 1.2rem; }
        .pnum { position: absolute; top: 10px; right: 12px; font-size: .62rem; font-weight: 700; color: var(--red-dark); }
        .pcard h3 { font-family: var(--head); font-size: .9rem; font-weight: 700; margin: 2px 24px 4px 0; }
        .pcard p { font-size: .74rem; line-height: 1.55; color: var(--muted); }
        .plinks { display: flex; gap: 12px; margin-top: 8px; }
        .plinks a { display: inline-flex; align-items: center; min-height: 36px; margin-right: 8px; font-size: .74rem; font-weight: 700; color: var(--red-dark); text-decoration: none; }
        .plinks a:hover { text-decoration: underline; }

        .erow { display: grid; grid-template-columns: 150px minmax(0, 1fr) auto; gap: 20px; align-items: center; padding: 18px 0; }
        .erow + .erow { border-top: 1px solid var(--line); }
        .edate { display: flex; align-items: center; gap: 10px; font-size: .74rem; font-weight: 600; color: var(--red-dark); white-space: nowrap; }
        .edate::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--red); }
        .erow h3 { font-size: .9rem; font-weight: 600; }
        .erow small { display: block; font-size: .76rem; color: var(--muted); margin-top: 2px; line-height: 1.5; }
        .pill { font-size: .68rem; font-weight: 600; color: var(--red-dark); background: var(--red-soft); padding: 5px 12px; border-radius: 99px; }

        .contact { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; }
        .ci { display: flex; gap: 12px; align-items: center; min-height: 76px; padding: 16px; background: var(--panel); border: 1px solid var(--line); border-radius: 9px; text-decoration: none; color: inherit; min-width: 0; transition: border-color .18s, background .18s; }
        a.ci:hover, a.ci:focus-visible { border-color: var(--red); background: #19132a; }
        .ci-ico { width: 36px; height: 36px; border-radius: 6px; background: var(--red-soft); color: var(--red-dark); display: grid; place-items: center; flex-shrink: 0; }
        .ci small { display: block; font-size: .68rem; color: var(--faint); }
        .ci b { font-size: .82rem; font-weight: 500; overflow-wrap: anywhere; }

        .foot { padding: 24px 22px 30px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; font-size: .74rem; color: var(--muted); }

        :root[data-theme="light"] .topbar { border-color: rgba(117,68,194,.24); background: linear-gradient(100deg, rgba(255,255,255,.94), rgba(249,246,255,.9) 52%, rgba(255,255,255,.94)); box-shadow: inset 0 1px rgba(255,255,255,.8), 0 8px 34px rgba(50,36,77,.1), 0 0 34px rgba(126,62,255,.1); }
        :root[data-theme="light"] .top-brand, :root[data-theme="light"] .top-nav a.active,
        :root[data-theme="light"] .top-nav a:hover { color: var(--ink); }
        :root[data-theme="light"] .top-nav a { color: var(--muted); }
        :root[data-theme="light"] .top-social a { color: var(--muted); }
        :root[data-theme="light"] .hero { background: #f0eafa; color: var(--ink); }
        :root[data-theme="light"] .hero .fb { background: radial-gradient(ellipse at 44% 8%, #e6d7ff 0%, transparent 46%), linear-gradient(135deg, #f8f5ff, #eee8f7 58%, #f8f5ff); }
        :root[data-theme="light"] .hero-scene { background: radial-gradient(ellipse 54% 40% at 44% 8%, rgba(128,57,255,.17), transparent 88%), radial-gradient(ellipse 42% 68% at 78% 38%, rgba(111,61,255,.09), transparent 77%), radial-gradient(ellipse 80% 54% at 52% 100%, rgba(102,47,220,.1), transparent 80%); }
        :root[data-theme="light"] .hero::after { background: linear-gradient(90deg, rgba(248,245,255,.68) 0%, rgba(248,245,255,.38) 27%, rgba(248,245,255,.08) 49%, rgba(248,245,255,0) 68%, transparent 100%); }
        :root[data-theme="light"] .hero .hello, :root[data-theme="light"] .hero h1 { color: var(--ink); }
        :root[data-theme="light"] .hero .hero-in > p:not(.role-pill) { color: var(--muted); }
        :root[data-theme="light"] .role-pill { border-color: rgba(117,68,194,.28); background: rgba(255,255,255,.72); color: #51377b; }
        :root[data-theme="light"] .role-pill i { background: #7544c2; box-shadow: 0 0 10px rgba(117,68,194,.5); }
        :root[data-theme="light"] .hero h1 em { color: #7544c2; }
        :root[data-theme="light"] .hbtn.line { border-color: rgba(101,56,175,.48); color: var(--ink); }
        :root[data-theme="light"] .hbtn.line:hover { background: rgba(117,68,194,.1); }
        :root[data-theme="light"] .side { border-color: rgba(117,68,194,.18); scrollbar-color: rgba(117,68,194,.28) transparent; }
        :root[data-theme="light"] .s-nav a:hover, :root[data-theme="light"] .s-nav a.active { color: var(--ink); background: rgba(117,68,194,.11); }
        :root[data-theme="light"] .s-nav a b, :root[data-theme="light"] .s-links h4,
        :root[data-theme="light"] .s-foot { color: var(--faint); }
        :root[data-theme="light"] .s-links { border-color: rgba(117,68,194,.18); }
        :root[data-theme="light"] .s-links a:hover { color: var(--ink); }
        :root[data-theme="light"] .s-nav a.active::before { background: #7544c2; box-shadow: 0 0 14px rgba(117,68,194,.48); }

        @media (max-width: 1200px) {
            .topbar { grid-template-columns: minmax(220px, 1fr) auto minmax(108px, .65fr); gap: 10px; padding-right: 20px; padding-left: 20px; }
            .top-brand { gap: 10px; font-size: .76rem; letter-spacing: .07em; }
            .top-nav { gap: clamp(8px, 1.2vw, 18px); }
            .top-social { gap: 10px; }
            .skill-orbit { width: clamp(320px, 34vw, 430px); right: clamp(20px, 4vw, 48px); }
            .hero-in { width: 58%; padding-right: 34px; padding-left: clamp(30px, 4vw, 50px); }
            .hero h1 { font-size: clamp(2.7rem, 5vw, 4rem); }
        }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 78px minmax(0, 1fr); }
            .side { padding: 114px 6px 12px; }
            .s-nav { gap: 4px; }
            .s-nav a { min-height: 42px; gap: 5px; padding: 7px 4px; font-size: .66rem; }
            .s-nav a b { width: 20px; flex-basis: 20px; font-size: .58rem; }
            .s-nav a.active::before { left: -6px; }
            .s-links a { min-height: 34px; font-size: .64rem; }
            .content { padding: 64px 0 0; }
            .topbar { top: calc(var(--pf-preview-bar-height, 0px) + 8px); left: 12px; right: 12px; min-height: 56px; grid-template-columns: minmax(130px, 1fr) auto minmax(80px, 1fr); gap: 8px; padding: 0 14px; border-radius: 13px; }
            .top-brand { gap: 9px; font-size: .66rem; }
            .top-brand svg { width: 24px; height: 26px; }
            .top-nav { gap: 12px; }
            .top-nav a { min-height: 44px; font-size: .72rem; }
            .top-social { gap: 8px; }
            .hero { min-height: max(560px, calc(100svh - 72px)); }
            .hero-in { width: 61%; padding: 48px 30px; }
            .hero h1 { font-size: clamp(2.5rem, 5.3vw, 4rem); }
            .skill-orbit { right: 1%; width: clamp(230px, 33vw, 360px); }
            .orbit-skill { width: 74px; min-height: 74px; border-radius: 14px; }
            .orbit-skill-mark, .orbit-skill-logo, .orbit-skill-icon { min-width: 34px; height: 34px; font-size: 1.55rem; }
            .orbit-skill-name { font-size: .57rem; }
            .about { grid-template-columns: minmax(0, 1fr) minmax(190px, .8fr); row-gap: 22px; }
            .about > div { padding: 0 0 0 22px; border-left: 1px solid var(--line); }
            .about > div:first-child { border-top: 0; padding-top: 0; }
            .about > div:last-child { grid-column: 1 / -1; padding: 18px 0 0; border-left: 0; border-top: 1px solid var(--line); }
            .erow { grid-template-columns: minmax(110px, 150px) minmax(0, 1fr); gap: 10px 18px; }
            .erow .pill { grid-column: 2; justify-self: start; }
            .sec { padding: 34px 14px; }
        }
        @media (max-width: 560px) {
            .shell { grid-template-columns: 56px minmax(0, 1fr); }
            .side { padding: 92px 4px 10px; }
            .s-nav a { justify-content: center; gap: 0; padding: 6px 2px; font-size: 0; }
            .s-nav a b { width: auto; flex: 0 0 auto; font-size: .52rem; }
            .s-nav-label, .s-links h4, .s-links a span { display: none; }
            .s-links { margin-top: 14px; }
            .s-links a { justify-content: center; min-height: 32px; }
            .s-foot { font-size: 0; }
            .content { padding: 54px 0 0; }
            .topbar { top: calc(var(--pf-preview-bar-height, 0px) + 6px); left: 7px; right: 7px; grid-template-columns: auto minmax(0, 1fr) auto; min-height: 46px; gap: 6px; padding: 0 8px; }
            .top-brand { gap: 0; }
            .top-brand span, .top-social a { display: none; }
            .top-brand svg { width: 20px; }
            .top-nav { justify-content: flex-start; gap: 10px; overflow-x: auto; scrollbar-width: none; }
            .top-nav::-webkit-scrollbar { display: none; }
            .top-nav a { font-size: .54rem; }
            .hero { min-height: 570px; }
            .hero-in { width: 87%; padding: 48px 18px; }
            .hero h1 { font-size: clamp(1.85rem, 8vw, 2.7rem); }
            .hero p { font-size: .9rem; }
            .hero-tag { display: none; }
            .skill-orbit { right: -138px; top: 54%; width: 280px; opacity: .58; }
            .orbit-skill { min-width: 44px; padding: 6px 5px 5px; }
            .orbit-skill { width: 54px; min-height: 54px; gap: 3px; border-radius: 10px; }
            .orbit-skill-mark { min-width: 22px; height: 22px; font-size: .72rem; }
            .orbit-skill-logo, .orbit-skill-icon { height: 22px; font-size: 1.12rem; }
            .orbit-skill-name { max-width: 48px; font-size: .42rem; }
            .hero::after { background: linear-gradient(90deg, rgba(8,7,19,.92), rgba(8,7,19,.7)); }
            .about { grid-template-columns: 1fr; }
            .about > div, .about > div:last-child { grid-column: auto; padding: 16px 0 0; border-left: 0; border-top: 1px solid var(--line); }
            .about > div:first-child { border-top: 0; padding-top: 0; }
            .quote { max-width: none; }
            .sec { padding: 30px 10px; }
            .pcard { grid-template-columns: 1fr; }
            .pthumb, .pph { width: 100%; height: 170px; }
            .erow { grid-template-columns: 1fr; gap: 5px; }
            .erow .pill { grid-column: auto; }
            .foot { padding-right: 10px; padding-left: 10px; }
        }
        /* Liquid UI: opaque, glossy resin surfaces with flowing highlights (no frosted-glass blur). */
        :root {
            --liquid-surface: #21133d;
            --liquid-surface-deep: #130d27;
            --liquid-edge: rgba(197, 139, 255, .58);
            --liquid-shine: rgba(255, 255, 255, .34);
            --liquid-glow: rgba(149, 75, 255, .42);
            --liquid-cyan: rgba(106, 231, 255, .28);
        }
        :root[data-theme="light"] {
            --liquid-surface: #f6edff;
            --liquid-surface-deep: #e8daf8;
            --liquid-edge: rgba(117, 68, 194, .42);
            --liquid-shine: rgba(255, 255, 255, .86);
            --liquid-glow: rgba(144, 89, 220, .18);
            --liquid-cyan: rgba(88, 186, 219, .18);
        }
        .topbar {
            overflow: hidden; isolation: isolate;
            border: 1px solid var(--liquid-edge);
            border-radius: 24px 18px 27px 19px / 18px 24px 19px 27px;
            background:
                radial-gradient(ellipse at 14% 0%, rgba(255,255,255,.2), transparent 33%),
                radial-gradient(ellipse at 85% 120%, var(--liquid-cyan), transparent 45%),
                linear-gradient(145deg, #35205a 0%, #21133d 42%, #170e2e 72%, #392064 100%);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.28), inset 0 -8px 18px rgba(5,2,18,.34), 0 12px 32px rgba(5,2,18,.4), 0 0 26px var(--liquid-glow);
            backdrop-filter: none;
        }
        .topbar::before {
            content: ''; position: absolute; z-index: 0; left: -30%; top: -65%;
            width: 70%; height: 220%; border-radius: 50%;
            background: linear-gradient(105deg,transparent 28%,rgba(255,255,255,.14) 47%,rgba(255,255,255,.025) 58%,transparent 74%);
            transform: rotate(-12deg); pointer-events: none; animation: liquid-sheen 12s ease-in-out infinite;
        }
        .topbar > * { position: relative; z-index: 1; }
        .top-brand svg { color: #e9d7ff; filter: drop-shadow(0 0 10px rgba(203,151,255,.95)); }
        .top-nav a { transition: color .2s ease, transform .25s ease; }
        .top-nav a:hover { transform: translateY(-2px); }
        .top-nav a.active::after { height: 3px; border-radius: 50%; background: linear-gradient(90deg,#8b52ff,#f0a8ff,#8b52ff); box-shadow: 0 0 15px #a75cff; }
        .top-actions .theme-toggle {
            border-color: var(--liquid-edge); border-radius: 18px 14px 20px 15px / 14px 19px 15px 20px;
            background: linear-gradient(150deg,#52327c,#29194a 58%,#1b1233); color: #fff;
            box-shadow: inset 0 2px 1px rgba(255,255,255,.25), inset 0 -4px 9px rgba(0,0,0,.28), 0 5px 16px rgba(0,0,0,.24);
            backdrop-filter: none;
        }
        .top-actions .theme-toggle:hover { background: linear-gradient(150deg,#694296,#38205e 58%,#241540); }
        :root[data-theme="light"] .topbar {
            border-color: var(--liquid-edge);
            background: radial-gradient(ellipse at 14% 0%,rgba(255,255,255,.95),transparent 34%),radial-gradient(ellipse at 86% 115%,var(--liquid-cyan),transparent 46%),linear-gradient(145deg,#fff 0%,#f4eaff 44%,#e6d6fa 76%,#f9f2ff 100%);
            box-shadow: inset 0 2px 1px #fff,inset 0 -7px 16px rgba(113,73,163,.1),0 12px 30px rgba(53,34,82,.15),0 0 24px var(--liquid-glow);
        }
        :root[data-theme="light"] .top-brand, :root[data-theme="light"] .top-nav a, :root[data-theme="light"] .top-social a { color: var(--ink); }
        :root[data-theme="light"] .top-actions .theme-toggle {
            border-color: var(--liquid-edge); background: linear-gradient(150deg,#fff,#eee2fc 62%,#dfcdf4); color: var(--ink);
            box-shadow: inset 0 2px 1px #fff,inset 0 -4px 8px rgba(96,65,132,.12),0 5px 14px rgba(53,34,82,.12);
        }
        .side {
            background: radial-gradient(ellipse at 35% 8%,rgba(130,76,211,.2),transparent 42%),linear-gradient(165deg,#1c1232,#100b20 58%,#1b1030);
            border-right-color: var(--liquid-edge); box-shadow: inset -5px 0 18px rgba(120,67,197,.11);
        }
        .s-nav a { border-radius: 15px 11px 17px 12px / 12px 16px 11px 17px; transition: color .2s ease,background .25s ease,transform .25s ease,box-shadow .25s ease; }
        .s-nav a:hover, .s-nav a.active {
            color: #fff; background: linear-gradient(140deg,#65409a,#3b245f 54%,#291942);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.22),inset 0 -5px 12px rgba(10,3,24,.3),0 5px 15px rgba(6,3,18,.28); transform: translateX(2px);
        }
        .s-nav a.active::before { width: 4px; border-radius: 0 99px 99px 0; background: linear-gradient(#f3c8ff,#9a59ff); box-shadow: 0 0 16px #b464ff; }
        .s-links { border-color: var(--liquid-edge); }
        .s-links a { border-radius: 12px; padding-inline: 8px; transition: color .2s ease,background .2s ease,transform .2s ease; }
        .s-links a:hover { background: linear-gradient(120deg,rgba(163,104,235,.26),rgba(76,199,235,.1)); transform: translateX(2px); }
        :root[data-theme="light"] .side { background: linear-gradient(165deg,#f5eaff,#e8ddf3 58%,#f8f2ff); border-right-color: var(--liquid-edge); box-shadow: inset -5px 0 18px rgba(117,68,194,.08); }
        :root[data-theme="light"] .s-nav a:hover, :root[data-theme="light"] .s-nav a.active {
            color: #322044; background: linear-gradient(140deg,#fff,#e7d5fa 58%,#d8c0f2);
            box-shadow: inset 0 2px 1px #fff,inset 0 -4px 10px rgba(100,69,140,.12),0 5px 14px rgba(53,34,82,.1);
        }
        .role-pill {
            border: 1px solid rgba(224,174,255,.72); border-radius: 19px 14px 22px 15px / 15px 21px 14px 20px;
            background: radial-gradient(ellipse at 18% 0%,rgba(255,255,255,.5),transparent 38%),linear-gradient(145deg,#7144a7,#43256f 57%,#29164b);
            color: #fff; box-shadow: inset 0 2px 1px rgba(255,255,255,.28),inset 0 -5px 10px rgba(16,5,33,.32),0 6px 18px rgba(102,47,180,.3);
        }
        .role-pill i { animation: liquid-pulse 2.8s ease-in-out infinite; }
        .hbtn { position: relative; overflow: hidden; border-radius: 20px 14px 23px 16px / 15px 22px 14px 21px; box-shadow: inset 0 2px 1px rgba(255,255,255,.3),inset 0 -6px 11px rgba(23,5,53,.32),0 9px 20px rgba(7,3,20,.25); font-family: inherit; cursor: pointer; }
        .hbtn::before { content: ''; position: absolute; inset: 1px 1px auto; height: 48%; border-radius: inherit; background: linear-gradient(180deg,rgba(255,255,255,.28),transparent); pointer-events: none; }
        .hbtn.red { border: 1px solid rgba(226,187,255,.75); background: linear-gradient(145deg,#c17aff,#8d45ed 48%,#602bc2); }
        .hbtn.red:hover { background: linear-gradient(145deg,#d196ff,#9d59f2 48%,#7138d1); transform: translateY(-3px) scale(1.025); }
        .hbtn.line { border: 1px solid rgba(209,174,255,.72); background: linear-gradient(145deg,#39265c,#24163f 58%,#17102c); }
        .hbtn.line:hover { background: linear-gradient(145deg,#56367d,#342151 58%,#211538); transform: translateY(-3px) scale(1.02); }
        .hero h1 em { color: #bd83ff; text-shadow: 0 3px 18px rgba(166,91,255,.35); }
        :root[data-theme="light"] .role-pill {
            border-color: rgba(117,68,194,.45); background: radial-gradient(ellipse at 18% 0%,#fff,transparent 42%),linear-gradient(145deg,#fff,#eadcf9 58%,#d9c3f0);
            color: #4d2d76; box-shadow: inset 0 2px 1px #fff,inset 0 -5px 10px rgba(90,53,130,.12),0 6px 18px rgba(117,68,194,.13);
        }
        :root[data-theme="light"] .hbtn.red { color: #fff; }
        :root[data-theme="light"] .hbtn.line { color: #352448; background: linear-gradient(145deg,#fff,#eee3fa 58%,#dfcff1); box-shadow: inset 0 2px 1px #fff,inset 0 -5px 10px rgba(90,53,130,.12),0 8px 18px rgba(69,44,101,.12); }
        :root[data-theme="light"] .hbtn.line:hover { background: linear-gradient(145deg,#fff,#e5d2f8 58%,#d2b9ed); }

        /* Keep the black-hole scene and portrait orbit intact; only restyle the floating skill tokens. */
        .skill-orbit { filter: drop-shadow(0 12px 28px rgba(8,3,20,.24)); }
        .orbit-center { border-color: rgba(235,190,255,.98); box-shadow: 0 0 0 8px rgba(155,88,255,.16),0 0 48px rgba(168,83,255,.86),0 0 82px rgba(85,214,255,.16),inset 0 0 26px rgba(194,123,255,.38); }
        .orbit-skill {
            isolation: isolate; overflow: hidden; border: 1px solid rgba(226,188,255,.8);
            border-radius: 23px 17px 25px 19px / 18px 24px 17px 25px;
            background: radial-gradient(ellipse at 26% 9%,rgba(255,255,255,.46),transparent 30%),radial-gradient(ellipse at 95% 88%,rgba(83,220,255,.22),transparent 46%),linear-gradient(148deg,#8052be 0%,#543180 36%,#322052 70%,#1d1532 100%);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.38),inset 0 -8px 12px rgba(11,4,26,.42),inset 7px 0 12px rgba(200,143,255,.1),0 12px 25px rgba(5,2,15,.45),0 0 22px rgba(151,81,255,.3);
            backdrop-filter: none; animation: skill-liquid 6s ease-in-out infinite; animation-delay: var(--delay);
        }
        .orbit-skill::before {
            content: ''; position: absolute; z-index: 0; left: -35%; top: -18%; width: 78%; height: 56%; border-radius: 50%;
            background: radial-gradient(ellipse,rgba(255,255,255,.46),rgba(227,187,255,.16) 32%,transparent 72%); filter: blur(3px); pointer-events: none;
        }
        .orbit-skill::after { content: ''; position: absolute; z-index: 0; right: -22%; bottom: -47%; width: 95%; height: 90%; border-radius: 50%; background: radial-gradient(ellipse,rgba(117,218,255,.25),transparent 68%); pointer-events: none; }
        .orbit-skill > * { position: relative; z-index: 1; }
        .orbit-skill-mark, .orbit-skill-logo, .orbit-skill-icon { filter: drop-shadow(0 2px 5px rgba(9,3,24,.6)); }
        .orbit-skill-name { color: #fff; text-shadow: 0 1px 5px rgba(9,3,24,.7); }
        @keyframes skill-liquid {
            0%,100% { transform: translate(-50%,-50%) translateY(0); border-radius: 23px 17px 25px 19px / 18px 24px 17px 25px; }
            50% { transform: translate(-50%,-50%) translateY(-7px); border-radius: 17px 25px 19px 23px / 24px 17px 25px 18px; }
        }
        .sec {
            position: relative; isolation: isolate; overflow: hidden; margin: 14px 18px; padding: 38px clamp(20px,3vw,42px);
            border: 1px solid var(--liquid-edge); border-radius: 32px 24px 38px 27px / 27px 36px 24px 34px;
            background: radial-gradient(ellipse at 6% 0%,rgba(178,112,255,.2),transparent 39%),radial-gradient(ellipse at 100% 100%,var(--liquid-cyan),transparent 40%),linear-gradient(145deg,var(--liquid-surface),var(--liquid-surface-deep) 54%,#24133f);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.2),inset 0 -12px 22px rgba(5,2,17,.25),0 18px 42px rgba(4,2,13,.28),0 0 28px var(--liquid-glow);
        }
        .sec + .sec { border-top: 1px solid var(--liquid-edge); }
        .sec::before {
            content: ''; position: absolute; z-index: -1; top: -70%; left: -42%; width: 64%; height: 230%; border-radius: 48%;
            background: linear-gradient(105deg,transparent 30%,rgba(255,255,255,.1) 48%,rgba(255,255,255,.015) 59%,transparent 73%);
            transform: rotate(-18deg); pointer-events: none; animation: liquid-sheen 15s ease-in-out infinite;
        }
        @keyframes liquid-sheen { 0%,100% { translate: -8% 0; opacity: .45; } 50% { translate: 75% 0; opacity: .9; } }
        @keyframes liquid-pulse { 0%,100% { transform: scale(.92); box-shadow: 0 0 8px #a45cff; } 50% { transform: scale(1.18); box-shadow: 0 0 16px #cb8fff; } }
        .label b { color: #d7b0ff; }
        .label::after { height: 3px; border-radius: 50%; background: linear-gradient(90deg,#8e4fff,#eeaaff,#8e4fff); box-shadow: 0 0 11px rgba(185,113,255,.7); }
        .sec h2 { text-shadow: 0 2px 18px rgba(163,94,255,.22); }
        .about > div { border-color: rgba(217,183,255,.2); }
        .focus .ib { border-radius: 10px 7px 12px 8px; background: linear-gradient(145deg,#503276,#281942); box-shadow: inset 0 2px rgba(255,255,255,.19),inset 0 -4px 8px rgba(0,0,0,.28); }
        .capcard, .pcard, .ci {
            border: 1px solid rgba(203,157,255,.42); border-radius: 22px 17px 25px 18px / 17px 24px 18px 25px;
            background: radial-gradient(ellipse at 14% 0%,rgba(255,255,255,.13),transparent 34%),radial-gradient(ellipse at 100% 100%,rgba(82,212,255,.1),transparent 45%),linear-gradient(145deg,#32204f,#211638 58%,#17102a);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.17),inset 0 -7px 13px rgba(5,2,17,.25),0 9px 22px rgba(4,2,13,.22);
        }
        .capcard { transition: transform .28s ease,border-color .28s ease,box-shadow .28s ease; }
        .capcard:hover { transform: translateY(-4px) rotate(-.4deg); border-color: rgba(230,192,255,.8); box-shadow: inset 0 2px 1px rgba(255,255,255,.22),0 15px 30px rgba(4,2,13,.3),0 0 22px rgba(159,84,255,.2); }
        .pcard { transition: border-color .25s,transform .3s,box-shadow .3s,background-position .5s; background-size: 180% 180%; }
        .pcard:hover, .pcard:focus-within { border-color: rgba(235,195,255,.88); transform: translateY(-4px) rotate(.25deg); box-shadow: inset 0 2px 1px rgba(255,255,255,.22),0 16px 32px rgba(5,2,17,.32),0 0 26px rgba(151,81,255,.25); }
        .pthumb, .pph { border-radius: 17px 12px 20px 14px / 13px 19px 12px 20px; }
        .pph { background: radial-gradient(ellipse at 25% 10%,rgba(255,255,255,.42),transparent 42%),linear-gradient(145deg,#8b5cc2,#442b70 62%,#261941); color: #fff; box-shadow: inset 0 2px 1px rgba(255,255,255,.25),inset 0 -6px 10px rgba(0,0,0,.28); }
        .plinks a { display: inline-flex; align-items: center; min-height: 34px; margin-right: 8px; padding: 5px 12px; border: 1px solid rgba(213,177,255,.5); border-radius: 13px 10px 15px 11px; background: linear-gradient(145deg,#52347a,#30204d); color: #fff; box-shadow: inset 0 2px rgba(255,255,255,.2),0 5px 12px rgba(0,0,0,.18); text-decoration: none; }
        .plinks a:hover { border-color: #fff; background: linear-gradient(145deg,#70469d,#442b68); text-decoration: none; }
        .erow + .erow { border-color: rgba(217,183,255,.22); }
        .pill { border: 1px solid rgba(222,179,255,.55); background: linear-gradient(145deg,#7548a2,#3b245b); color: #fff; box-shadow: inset 0 2px rgba(255,255,255,.22),0 5px 12px rgba(0,0,0,.2); }
        .ci { transition: transform .24s ease,border-color .24s ease,box-shadow .24s ease; }
        a.ci:hover, a.ci:focus-visible { border-color: rgba(237,199,255,.9); background: linear-gradient(145deg,#513373,#2b1c45); transform: translateY(-3px); box-shadow: inset 0 2px rgba(255,255,255,.2),0 12px 24px rgba(0,0,0,.28),0 0 17px rgba(154,85,245,.22); }
        .ci-ico { border-radius: 12px 9px 14px 10px; background: linear-gradient(145deg,#7950a5,#382454); color: #fff; box-shadow: inset 0 2px rgba(255,255,255,.25),inset 0 -4px 8px rgba(0,0,0,.28); }
        .foot { border-color: var(--liquid-edge); }

        :root[data-theme="light"] .sec {
            background: radial-gradient(ellipse at 6% 0%,rgba(255,255,255,.95),transparent 38%),radial-gradient(ellipse at 100% 100%,var(--liquid-cyan),transparent 42%),linear-gradient(145deg,var(--liquid-surface),#eee2fa 55%,#e3d1f4);
            box-shadow: inset 0 2px 1px #fff,inset 0 -10px 20px rgba(102,69,142,.1),0 18px 38px rgba(64,43,91,.12),0 0 25px var(--liquid-glow);
        }
        :root[data-theme="light"] .sec + .sec { border-color: var(--liquid-edge); }
        :root[data-theme="light"] .capcard, :root[data-theme="light"] .pcard, :root[data-theme="light"] .ci {
            border-color: rgba(117,68,194,.32);
            background: radial-gradient(ellipse at 14% 0%,#fff,transparent 38%),radial-gradient(ellipse at 100% 100%,var(--liquid-cyan),transparent 44%),linear-gradient(145deg,#fff,#efe4fa 58%,#e4d4f4);
            box-shadow: inset 0 2px 1px #fff,inset 0 -7px 13px rgba(96,65,132,.08),0 9px 22px rgba(53,34,82,.1);
        }
        :root[data-theme="light"] .capcard:hover { border-color: rgba(117,68,194,.7); }
        :root[data-theme="light"] .plinks a, :root[data-theme="light"] .pill {
            border-color: rgba(117,68,194,.42); background: linear-gradient(145deg,#fff,#e7d8f7 65%,#d9c4ef); color: #39244f;
            box-shadow: inset 0 2px 1px #fff,0 5px 12px rgba(69,44,101,.1);
        }
        :root[data-theme="light"] a.ci:hover, :root[data-theme="light"] a.ci:focus-visible { border-color: rgba(117,68,194,.72); background: linear-gradient(145deg,#fff,#e9d9f8); box-shadow: inset 0 2px #fff,0 12px 24px rgba(69,44,101,.14); }
        :root[data-theme="light"] .ci-ico, :root[data-theme="light"] .focus .ib { background: linear-gradient(145deg,#fff,#dfcdf2); color: #603a8d; box-shadow: inset 0 2px #fff,inset 0 -4px 8px rgba(96,65,132,.1); }
        :root[data-theme="light"] .orbit-skill {
            border-color: rgba(244,228,255,.9);
            background: radial-gradient(ellipse at 26% 9%,rgba(255,255,255,.95),transparent 31%),radial-gradient(ellipse at 95% 88%,rgba(88,186,219,.2),transparent 46%),linear-gradient(148deg,#9a6bc9,#674295 43%,#49336a 75%,#30234b);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.72),inset 0 -8px 12px rgba(38,19,59,.22),0 12px 24px rgba(54,34,78,.2),0 0 20px rgba(138,83,201,.22);
        }
        :root[data-theme="light"] .orbit-skill-name { color: #fff; }
        @media (max-width: 900px) { .sec { margin: 10px; padding: 30px 20px; border-radius: 27px 21px 32px 23px / 22px 30px 20px 29px; } }
        @media (max-width: 560px) {
            .sec { margin: 8px 7px; padding: 26px 14px; border-radius: 23px 18px 28px 20px / 19px 26px 17px 25px; }
            .topbar { border-radius: 17px 13px 19px 14px / 13px 18px 12px 18px; }
            .topbar::before { animation-duration: 18s; }
            .hbtn { min-height: 50px; padding: 11px 18px; }
            .orbit-skill { border-radius: 15px 12px 17px 13px / 12px 16px 11px 17px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .topbar::before, .sec::before, .role-pill i, .orbit-skill { animation: none !important; }
            .s-nav a, .hbtn, .capcard, .pcard, .ci { transition: none !important; }
        }
        /* Opaque pigment in light mode keeps these surfaces resin-like, not frosted. */
        :root[data-theme="light"] .topbar {
            background:
                radial-gradient(ellipse at 16% 0%,rgba(255,255,255,.34),transparent 32%),
                radial-gradient(ellipse at 85% 115%,rgba(88,224,255,.28),transparent 42%),
                linear-gradient(135deg,#7449a9 0%,#50317a 38%,#744aa5 72%,#9163bd 100%);
            border-color: rgba(255,255,255,.55);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.62),inset 0 -8px 18px rgba(38,18,67,.24),0 12px 30px rgba(53,34,82,.2),0 0 22px rgba(144,89,220,.24);
        }
        :root[data-theme="light"] .top-brand,
        :root[data-theme="light"] .top-nav a,
        :root[data-theme="light"] .top-social a { color: #fff; }
        :root[data-theme="light"] .top-nav a:hover,
        :root[data-theme="light"] .top-nav a.active { color: #fff; }
        :root[data-theme="light"] .top-nav a.active::after { background: linear-gradient(90deg,#fff,#b8f5ff,#fff); box-shadow: 0 0 14px rgba(215,245,255,.9); }
        :root[data-theme="light"] .top-actions .theme-toggle {
            border-color: rgba(255,255,255,.68);
            background: linear-gradient(145deg,#a77bd2,#714b9f 62%,#56367f);
            color: #fff;
            box-shadow: inset 0 2px 1px rgba(255,255,255,.46),inset 0 -4px 8px rgba(44,23,71,.25),0 5px 14px rgba(38,20,58,.22);
        }
        :root[data-theme="light"] .side {
            background: linear-gradient(160deg,#d9c3ee 0%,#c4a7df 58%,#e7d8f4 100%);
            box-shadow: inset -5px 0 18px rgba(117,68,194,.12),inset 0 2px 1px rgba(255,255,255,.72);
        }
        :root[data-theme="light"] .s-nav a { color: #46335e; }
        :root[data-theme="light"] .s-nav a b { color: #70588a; }
        :root[data-theme="light"] .s-nav a:hover,
        :root[data-theme="light"] .s-nav a.active {
            color: #fff;
            background: linear-gradient(140deg,#8556b6,#62418d 58%,#4a306f);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.35),inset 0 -5px 10px rgba(36,16,60,.25),0 5px 14px rgba(63,39,91,.2);
        }
        :root[data-theme="light"] .s-links a { color: #46335e; }
        :root[data-theme="light"] .s-links a:hover { color: #38244f; }
        :root[data-theme="light"] .role-pill {
            border-color: rgba(255,255,255,.7);
            background: radial-gradient(ellipse at 18% 0%,rgba(255,255,255,.42),transparent 34%),linear-gradient(145deg,#a47acb,#76509f 60%,#543777);
            color: #fff;
            box-shadow: inset 0 2px 1px rgba(255,255,255,.45),inset 0 -5px 10px rgba(39,17,64,.23),0 6px 18px rgba(91,56,128,.2);
        }
        :root[data-theme="light"] .hbtn.line {
            border-color: rgba(255,255,255,.68);
            background: linear-gradient(145deg,#9e76c2,#704b9b 60%,#563679);
            color: #fff;
            box-shadow: inset 0 2px 1px rgba(255,255,255,.42),inset 0 -5px 10px rgba(39,17,64,.22),0 8px 18px rgba(69,44,101,.15);
        }
        :root[data-theme="light"] .hbtn.line:hover { background: linear-gradient(145deg,#af8ad0,#805aa9 60%,#634284); }
        :root[data-theme="light"] .sec {
            background:
                radial-gradient(ellipse at 6% 0%,rgba(255,255,255,.72),transparent 36%),
                radial-gradient(ellipse at 100% 100%,rgba(88,186,219,.17),transparent 40%),
                linear-gradient(145deg,#eadbf8 0%,#d8c2ee 54%,#c8abe2 100%);
            box-shadow: inset 0 2px 1px rgba(255,255,255,.8),inset 0 -10px 20px rgba(102,69,142,.12),0 18px 38px rgba(64,43,91,.13),0 0 24px rgba(144,89,220,.14);
        }
        :root[data-theme="light"] .capcard,
        :root[data-theme="light"] .pcard,
        :root[data-theme="light"] .ci {
            background:
                radial-gradient(ellipse at 14% 0%,rgba(255,255,255,.68),transparent 36%),
                radial-gradient(ellipse at 100% 100%,rgba(88,186,219,.13),transparent 44%),
                linear-gradient(145deg,#f8f0ff,#e7d8f6 62%,#d9c3ed);
        }

        /* Creative workstation scene: modern gaming hardware on a seamless studio floor. */
        :root {
            --studio-bg: #25272e;
            --studio-ink: #f9f7ff;
            --studio-muted: #b9bac5;
            --studio-accent: #a982ff;
            --desk-top: #282b33;
            --desk-edge: #11141b;
            --desk-front: #1c2028;
            --desk-side: #171a21;
            --desk-detail: #8053df;
            --monitor-frame: #11141a;
            --monitor-side: #343b47;
        }
        :root[data-theme="light"] {
            --studio-bg: #d7d9df;
            --studio-ink: #201c28;
            --studio-muted: #555865;
            --studio-accent: #6941a5;
            --desk-top: #383b44;
            --desk-edge: #171a21;
            --desk-front: #242830;
            --desk-side: #1e2229;
            --desk-detail: #8053df;
            --monitor-frame: #11141a;
            --monitor-side: #343b47;
        }
        html, body { background: var(--studio-bg); color: var(--studio-ink); }
        .creative-shell { display: block; min-height: 100svh; background: var(--studio-bg); }
        .creative-shell > .side { display: none; }
        .creative-shell > .content { min-width: 0; padding: 0; }
        .skip-link { position: fixed; z-index: 100; top: 8px; left: 8px; translate: 0 -150%; padding: 10px 14px; border-radius: 6px; background: #17141d; color: #fff; }
        .skip-link:focus { translate: 0 0; }
        .topbar {
            position: fixed; top: calc(var(--pf-preview-bar-height, 0px) + 22px); left: auto; right: clamp(18px, 4vw, 64px);
            z-index: 30; display: flex; width: max-content; max-width: calc(100% - 36px); min-height: 44px; gap: 16px;
            padding: 0; overflow: visible; border: 0; border-radius: 0; background: transparent; box-shadow: none; backdrop-filter: none;
            transition: top .22s ease, padding .22s ease, background .22s ease, border-color .22s ease, box-shadow .22s ease;
        }
        .topbar.is-scrolled {
            top: calc(var(--pf-preview-bar-height, 0px) + 8px); padding: 3px 10px; border: 1px solid color-mix(in srgb, var(--studio-ink) 16%, transparent);
            border-radius: 999px; background: color-mix(in srgb, var(--studio-bg) 84%, transparent); box-shadow: 0 8px 24px rgba(5,4,10,.13);
            backdrop-filter: blur(14px);
        }
        .topbar::before, .top-brand { display: none; }
        .top-nav { gap: clamp(8px, 1.3vw, 21px); overflow-x: auto; scrollbar-width: none; }
        .top-nav::-webkit-scrollbar { display: none; }
        .top-nav a {
            min-height: 38px; padding: 0 2px; color: var(--studio-ink); font-size: .76rem; font-weight: 500;
            text-shadow: 0 1px 12px color-mix(in srgb, var(--studio-bg) 40%, transparent);
        }
        .top-nav a:hover, .top-nav a.active { color: var(--studio-accent); }
        .top-nav a.active::after { bottom: 0; height: 2px; background: var(--studio-accent); box-shadow: 0 0 10px color-mix(in srgb, var(--studio-accent) 60%, transparent); }
        .top-actions { gap: 11px; }
        .top-social { gap: 6px; }
        .top-social a { width: 30px; height: 38px; color: var(--studio-ink); }
        .top-social a:hover { color: var(--studio-accent); }
        .top-actions .theme-toggle {
            min-height: 38px; padding: 0 12px; border-color: color-mix(in srgb, var(--studio-ink) 22%, transparent);
            border-radius: 6px; background: color-mix(in srgb, var(--studio-bg) 78%, transparent);
            color: var(--studio-ink); box-shadow: none; backdrop-filter: blur(10px);
        }
        .top-actions .theme-toggle:hover { background: color-mix(in srgb, var(--studio-bg) 92%, var(--studio-accent)); }
        .hero.workstation-hero {
            position: relative; display: grid; place-items: center; min-height: max(680px, calc(100svh - var(--pf-preview-bar-height, 0px)));
            margin: 0; padding: 115px 20px 78px; overflow: hidden; isolation: isolate; border: 0; border-radius: 0;
            background: var(--studio-bg); color: var(--studio-ink);
        }
        .hero.workstation-hero::after { display: none; background: none; }
        .hero.workstation-hero > .hero-scene { z-index: 0; inset: 0; background: none; opacity: 1; }
        .hero-canvas { pointer-events: none; }
        .hero.workstation-hero.is-exploring .hero-canvas { pointer-events: auto; cursor: grab; touch-action: none; }
        .hero.workstation-hero.is-exploring .hero-canvas.is-dragging { cursor: grabbing; }
        .hero.workstation-hero:fullscreen { width: 100vw; height: 100vh; min-height: 100vh; padding: 84px 20px 54px; }
        .studio-backdrop {
            position: absolute; z-index: -2; inset: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 36% 25% at 51% 58%, color-mix(in srgb, var(--studio-accent) 17%, transparent), transparent 100%),
                linear-gradient(180deg, color-mix(in srgb, var(--studio-bg) 86%, #fff) 0%, var(--studio-bg) 74%, color-mix(in srgb, var(--studio-bg) 88%, #000) 100%);
        }
        .studio-backdrop::before {
            display: none;
        }
        .studio-backdrop::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(180deg, color-mix(in srgb, var(--studio-bg) 16%, transparent), transparent 42%, color-mix(in srgb, var(--studio-bg) 8%, transparent));
        }
        .workstation-meta {
            position: absolute; z-index: 5; top: calc(var(--pf-preview-bar-height, 0px) + clamp(26px, 5vw, 62px)); left: clamp(18px, 4.2vw, 70px);
            display: grid; justify-items: start; gap: 3px; max-width: min(360px, calc(100vw - 36px)); color: #fff;
            font-family: var(--body); font-size: .78rem; letter-spacing: .015em;
        }
        .workstation-meta h1, .workstation-meta__role, .workstation-meta__status {
            display: flex; align-items: center; min-height: 28px; margin: 0; padding: 5px 12px;
            border: 1px solid rgba(255,255,255,.12); border-radius: 4px; background: rgba(19,17,24,.91);
            box-shadow: 0 3px 16px rgba(16,12,25,.12); line-height: 1.2;
        }
        .hero .workstation-meta__role, .hero .workstation-meta__status { max-width: 100%; margin: 0; }
        .workstation-meta h1 { max-width: 100%; overflow: hidden; font-size: .83rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
        .workstation-meta__role { color: #ddd3f3; }
        .workstation-meta__status { gap: 9px; color: #d2ccd9; font-size: .69rem; }
        .workstation-meta__status time { font-variant-numeric: tabular-nums; }
        .workstation-meta__dot { width: 7px; height: 7px; border-radius: 50%; background: #81e0b3; box-shadow: 0 0 9px rgba(129,224,179,.72); }
        .workstation-meta__place { max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .studio-workstation {
            position: relative; z-index: 2; display: grid; place-items: center; width: min(78vw, 760px); margin-top: 22px;
            filter: drop-shadow(0 34px 24px rgba(4,3,9,.17)); animation: workstation-arrive .9s cubic-bezier(.2,.75,.25,1) both;
        }
        .workstation-art { display: block; width: 100%; height: auto; overflow: visible; }
        .desk-ground-shadow { fill: rgba(14,11,23,.22); filter: url(#shadow-soft); }
        .desk-line { fill: none; stroke: rgba(255,255,255,.2); stroke-width: 2; }
        .monitor-glass { fill: url(#display-bg); }
        .monitor-copy-kicker { fill: #bfa2ff; font: 700 8px var(--body); letter-spacing: 1.6px; }
        .monitor-copy-name { fill: #fbf9ff; font: 700 14px var(--head); }
        .monitor-copy-role { fill: #d7cce9; font: 500 9px var(--body); }
        .monitor-copy-line { stroke: rgba(220,202,255,.32); stroke-width: 1.2; stroke-linecap: round; }
        .monitor-copy-chip { fill: rgba(175,134,255,.28); stroke: rgba(226,210,255,.45); stroke-width: .8; }
        .monitor-hole-glow { fill: #9c69ef; opacity: .54; filter: url(#hole-glow); }
        .monitor-hole-ring { fill: none; stroke: #d9a4ff; stroke-width: 3; }
        .monitor-hole-core { fill: #100b1b; }
        .screen-profile-fallback { fill: #5c477a; stroke: #d7c3ff; stroke-width: 1.5; }
        .workstation-actions {
            position: absolute; z-index: 5; bottom: clamp(22px, 4vw, 48px); left: clamp(18px, 4.2vw, 70px);
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .workstation-actions .hbtn {
            min-height: 38px; padding: 8px 14px; border: 1px solid color-mix(in srgb, var(--studio-ink) 20%, transparent);
            border-radius: 5px; background: color-mix(in srgb, var(--studio-bg) 82%, transparent);
            color: var(--studio-ink); font-size: .73rem; box-shadow: none; backdrop-filter: blur(10px);
        }
        .workstation-actions .hbtn.red {
            border-color: color-mix(in srgb, var(--studio-accent) 54%, transparent);
            background: color-mix(in srgb, var(--studio-accent) 18%, var(--studio-bg)); color: var(--studio-ink);
        }
        .workstation-actions .hbtn:hover { transform: translateY(-2px); }
        .workstation-hint {
            position: absolute; right: clamp(18px, 4.2vw, 70px); bottom: clamp(29px, 4.5vw, 54px);
            color: var(--studio-muted); font-size: .66rem; letter-spacing: .1em; text-transform: uppercase;
        }
        .workstation-controls {
            position: absolute; z-index: 7; top: calc(var(--pf-preview-bar-height, 0px) + 82px); right: clamp(18px, 4.2vw, 70px);
            display: flex; align-items: center; gap: 11px; padding: 7px 8px 7px 13px; border: 1px solid rgba(206,190,255,.2);
            border-radius: 999px; background: rgba(16,18,25,.68); color: #ded8eb; box-shadow: 0 8px 26px rgba(4,5,10,.2);
            backdrop-filter: blur(14px); font-size: .67rem;
        }
        .workstation-controls[hidden] { display: none !important; }
        .workstation-controls__view { color: #b9b5c5; font-size: .62rem; letter-spacing: .09em; text-transform: uppercase; }
        .workstation-camera-toggle {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 34px; padding: 0 12px;
            border: 1px solid rgba(176,139,255,.46); border-radius: 999px; background: linear-gradient(135deg,rgba(123,75,219,.38),rgba(29,32,44,.92));
            color: #f5f0ff; font: 600 .69rem/1 var(--body); cursor: pointer; transition: border-color .18s, background .18s, transform .18s, box-shadow .18s;
        }
        .workstation-camera-toggle svg { width: 15px; height: 15px; flex: 0 0 15px; }
        .workstation-camera-toggle:hover { transform: translateY(-1px); border-color: rgba(199,174,255,.82); box-shadow: 0 0 18px rgba(151,93,255,.28); }
        .workstation-camera-toggle:focus-visible { outline: 3px solid #58dbff; outline-offset: 3px; }
        .workstation-camera-toggle[aria-pressed="true"] { border-color: rgba(92,220,255,.72); background: linear-gradient(135deg,rgba(37,128,163,.44),rgba(29,32,44,.94)); }
        .workstation-camera-toggle:disabled { cursor: progress; opacity: .72; }
        .workstation-explore-toggle, .workstation-fullscreen-toggle {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 34px; padding: 0 12px;
            border: 1px solid rgba(176,139,255,.38); border-radius: 999px; background: rgba(28,31,42,.88); color: #f5f0ff;
            font: 600 .68rem/1 var(--body); cursor: pointer; transition: border-color .18s, background .18s, transform .18s, box-shadow .18s;
        }
        .workstation-explore-toggle:hover, .workstation-fullscreen-toggle:hover { transform: translateY(-1px); border-color: rgba(199,174,255,.82); box-shadow: 0 0 18px rgba(151,93,255,.24); }
        .workstation-explore-toggle:focus-visible, .workstation-fullscreen-toggle:focus-visible { outline: 3px solid #58dbff; outline-offset: 3px; }
        .workstation-explore-toggle[aria-pressed="true"] { border-color: rgba(92,220,255,.72); background: linear-gradient(135deg,rgba(37,128,163,.52),rgba(29,32,44,.96)); }
        .workstation-explore-toggle svg, .workstation-fullscreen-toggle svg { width: 15px; height: 15px; flex: 0 0 15px; }
        .workstation-explore-hint { position: absolute; z-index: 7; bottom: 24px; left: 50%; padding: 8px 13px; border: 1px solid rgba(206,190,255,.25); border-radius: 999px; background: rgba(16,18,25,.76); color: #eee8fa; font-size: .69rem; pointer-events: none; transform: translateX(-50%); backdrop-filter: blur(12px); }
        .workstation-explore-hint[hidden] { display: none !important; }
        .workstation-sr-only { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important; }
        :root[data-theme="light"] .top-nav a { color: #332d3a; text-shadow: none; }
        :root[data-theme="light"] .topbar { border: 0; background: transparent; box-shadow: none; }
        :root[data-theme="light"] .topbar.is-scrolled {
            border-color: rgba(42,34,52,.16); background: rgba(248,247,250,.86); box-shadow: 0 8px 24px rgba(49,42,59,.1);
        }
        :root[data-theme="light"] .hero.workstation-hero { background: var(--studio-bg); color: var(--studio-ink); }
        :root[data-theme="light"] .top-nav a:hover, :root[data-theme="light"] .top-nav a.active { color: var(--studio-accent); }
        :root[data-theme="light"] .top-social a { color: #332d3a; }
        :root[data-theme="light"] .top-actions .theme-toggle { border-color: rgba(42,34,52,.2); color: #332d3a; }
        :root[data-theme="light"] .workstation-meta h1,
        :root[data-theme="light"] .workstation-meta__role,
        :root[data-theme="light"] .workstation-meta__status {
            border-color: rgba(255,255,255,.22); background: rgba(28,26,31,.92); color: #fff;
        }
        :root[data-theme="light"] .workstation-meta__role { color: #e5d9fb; }
        :root[data-theme="light"] .workstation-controls { border-color: rgba(46,41,58,.2); background: rgba(242,241,246,.86); color: #332d3a; }
        :root[data-theme="light"] .workstation-controls__view { color: #5a5564; }
        :root[data-theme="light"] .workstation-camera-toggle { border-color: rgba(101,70,151,.44); background: linear-gradient(135deg,#eee7f8,#d8cce9); color: #332641; }
        :root[data-theme="light"] .workstation-camera-toggle[aria-pressed="true"] { border-color: rgba(41,133,158,.58); background: linear-gradient(135deg,#ddf4f6,#d8cce9); }
        :root[data-theme="light"] .workstation-explore-toggle, :root[data-theme="light"] .workstation-fullscreen-toggle { border-color: rgba(101,70,151,.36); background: rgba(242,241,246,.94); color: #332641; }
        :root[data-theme="light"] .workstation-explore-toggle[aria-pressed="true"] { border-color: rgba(41,133,158,.58); background: linear-gradient(135deg,#ddf4f6,#d8cce9); }
        :root[data-theme="light"] .workstation-explore-hint { border-color: rgba(46,41,58,.2); background: rgba(242,241,246,.9); color: #332d3a; }
        @keyframes workstation-arrive { from { opacity: 0; transform: translateY(16px) scale(.985); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @media (max-width: 760px) {
            .topbar { top: calc(var(--pf-preview-bar-height, 0px) + 126px); left: 16px; right: 16px; width: auto; max-width: none; justify-content: space-between; gap: 7px; }
            .top-nav { justify-content: flex-start; gap: 14px; min-width: 0; }
            .top-nav a { min-height: 34px; font-size: .68rem; }
            .top-actions { flex: 0 0 auto; }
            .top-social { display: none; }
            .hero.workstation-hero { min-height: max(650px, calc(100svh - var(--pf-preview-bar-height, 0px))); padding: 175px 12px 94px; }
            .studio-workstation { width: min(92vw, 640px); margin-top: 14px; }
            .workstation-meta { top: calc(var(--pf-preview-bar-height, 0px) + 16px); left: 16px; max-width: calc(100% - 32px); }
            .workstation-controls { top: calc(var(--pf-preview-bar-height, 0px) + 132px); right: auto; left: 50%; width: max-content; max-width: calc(100% - 24px); justify-content: center; flex-wrap: wrap; gap: 6px; padding: 4px; transform: translateX(-50%); }
            .workstation-controls__view, .workstation-fullscreen-toggle { display: none; }
            .workstation-actions { bottom: 26px; left: 16px; }
            .workstation-hint { display: none; }
        }
        @media (max-width: 420px) {
            .workstation-meta { top: calc(var(--pf-preview-bar-height, 0px) + 16px); right: 12px; left: 12px; width: auto; max-width: none; gap: 3px; font-size: .7rem; }
            .workstation-meta h1, .workstation-meta__role, .workstation-meta__status { min-height: 26px; padding: 4px 9px; }
            .workstation-meta__role { max-width: 100%; overflow-wrap: anywhere; }
            .workstation-meta__status { max-width: 100%; flex-wrap: wrap; }
            .workstation-controls { top: calc(var(--pf-preview-bar-height, 0px) + 132px); right: auto; left: 50%; width: max-content; max-width: calc(100% - 24px); flex-wrap: wrap; justify-content: center; gap: 5px; padding: 4px; transform: translateX(-50%); }
            .workstation-controls__view { display: none; }
            .workstation-camera-toggle, .workstation-explore-toggle, .workstation-fullscreen-toggle { min-height: 36px; padding: 0 9px; gap: 5px; font-size: .62rem; }
            .workstation-fullscreen-toggle { display: none; }
            .workstation-explore-hint { bottom: 68px; max-width: calc(100% - 24px); text-align: center; }
            .topbar { top: calc(var(--pf-preview-bar-height, 0px) + 116px); left: 12px; right: 12px; }
            .top-nav { gap: 11px; }
            .top-nav a { font-size: .62rem; }
            .top-actions .theme-toggle { min-width: 38px; min-height: 34px; padding: 0 9px; }
            .hero.workstation-hero { min-height: max(620px, calc(100svh - var(--pf-preview-bar-height, 0px))); }
            .studio-workstation { width: min(calc(100vw - 20px), 420px); margin-top: 8px; }
            .workstation-actions .hbtn { min-height: 36px; padding: 7px 11px; font-size: .68rem; }
            .workstation-actions { right: 12px; bottom: 18px; left: 12px; justify-content: center; }
        }
        @media (prefers-reduced-motion: reduce) {
            .studio-workstation { animation: none !important; }
            .workstation-actions .hbtn { transition: none !important; }
            .topbar { transition: none !important; }
        }

        /* The workstation opens into a small, keyboard-friendly portfolio desktop. */
        :root {
            --os-wallpaper: #08101f;
            --os-wallpaper-light: #121d35;
            --os-chrome: #43336a;
            --os-chrome-hi: #7964a8;
            --os-window: #171520;
            --os-window-ink: #f2eef8;
            --os-window-muted: #c0b8ca;
            --os-sidebar-bg: #211e2b;
            --os-sidebar-edge: #3d3749;
            --os-sidebar-ink: #eee9f5;
            --os-sidebar-muted: #b9afc6;
            --os-card: #211e2b;
            --os-border: #393344;
            --os-taskbar-bg: linear-gradient(#302d38,#211f29);
            --os-taskbar-ink: #f0edf5;
            --os-taskbar-edge: #4a4552;
            --os-button-bg: linear-gradient(#413c4a,#2b2833);
            --os-button-hover: linear-gradient(#51495d,#383141);
            --os-button-ink: #f5f2fa;
            --os-menu-bg: #201d29;
        }
        :root[data-theme="light"] {
            --os-wallpaper: #23334b;
            --os-wallpaper-light: #344d6b;
            --os-chrome: #604889;
            --os-chrome-hi: #9375b8;
            --os-window: #fff;
            --os-window-ink: #211b2c;
            --os-window-muted: #655c70;
            --os-sidebar-bg: linear-gradient(180deg,#f1edf5,#e7e1ed);
            --os-sidebar-edge: #d7d1dd;
            --os-sidebar-ink: #30263d;
            --os-sidebar-muted: #70647c;
            --os-card: #fff;
            --os-border: #ded8e5;
            --os-taskbar-bg: linear-gradient(#edeaf1,#d1ccd8);
            --os-taskbar-ink: #2b2433;
            --os-taskbar-edge: #bbb5c2;
            --os-button-bg: linear-gradient(#fff,#d7d2df);
            --os-button-hover: linear-gradient(#fff,#e4d9f1);
            --os-button-ink: #282230;
            --os-menu-bg: #f5f2f8;
        }
        [hidden] { display: none !important; }
        .topbar { top: calc(var(--pf-preview-bar-height, 0px) + 14px); right: 20px; left: auto; width: auto; min-height: 42px; padding: 0; }
        .topbar .top-brand, .topbar .top-nav, .topbar .top-social { display: none !important; }
        .topbar .top-actions { gap: 0; }
        .topbar .theme-toggle { min-height: 40px; }
        body.creative-os-active .topbar { display: none; }
        .hero.workstation-hero { cursor: default; transition: opacity .38s ease, transform .55s cubic-bezier(.2,.75,.25,1); }
        .workstation-art { pointer-events: none; }
        .workstation-actions { z-index: 6; }
        .workstation-actions .hbtn { cursor: pointer; }
        .workstation-hint { pointer-events: none; }
        body.creative-os-active { overflow: hidden; }
        body.creative-os-active .workstation-hero { opacity: 0; transform: scale(1.04); pointer-events: none; }
        .creative-os { position: fixed; z-index: 25; inset: var(--pf-preview-bar-height, 0px) 0 0; overflow: hidden; color: #f8f6fc; background: var(--os-wallpaper); isolation: isolate; }
        .creative-os:not([hidden]) { display: block; animation: os-arrive .55s cubic-bezier(.2,.75,.25,1) both; }
        .os-wallpaper { position: absolute; z-index: -1; inset: 0; overflow: hidden; background: radial-gradient(ellipse at 67% 42%, rgba(92,52,165,.2), transparent 37%), radial-gradient(ellipse at 15% 83%, rgba(19,91,133,.12), transparent 42%), linear-gradient(135deg, var(--os-wallpaper-light), var(--os-wallpaper) 66%); }
        .os-wallpaper::before { content: ''; position: absolute; inset: 0; opacity: .42; background-image: radial-gradient(1px 1px at 8% 17%,rgba(255,255,255,.8) 50%,transparent 100%), radial-gradient(1px 1px at 22% 69%,rgba(194,219,255,.75) 50%,transparent 100%), radial-gradient(1.5px 1.5px at 39% 28%,rgba(255,255,255,.72) 50%,transparent 100%), radial-gradient(1px 1px at 72% 16%,rgba(255,255,255,.8) 50%,transparent 100%), radial-gradient(1px 1px at 87% 77%,rgba(190,202,255,.8) 50%,transparent 100%), radial-gradient(1.5px 1.5px at 94% 37%,rgba(255,255,255,.75) 50%,transparent 100%), linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px); background-size: 100% 100%,100% 100%,100% 100%,100% 100%,100% 100%,100% 100%,48px 48px,48px 48px; mask-image: linear-gradient(130deg,#000,transparent 82%); }
        .os-wallpaper::after { content: ''; position: absolute; width: min(58vw, 720px); aspect-ratio: 1; top: 49%; left: 62%; transform: translate(-50%,-50%); border-radius: 50%; background: radial-gradient(ellipse at center,transparent 0 20%,rgba(97,61,180,.08) 34%,rgba(133,85,223,.17) 43%,rgba(72,49,143,.08) 56%,transparent 69%); filter: blur(16px); pointer-events: none; }
        .os-blackhole { position: absolute; z-index: 0; top: 49%; left: 62%; width: min(46vw, 580px); min-width: 300px; aspect-ratio: 1; transform: translate(-50%,-50%); pointer-events: none; filter: drop-shadow(0 0 34px rgba(110,83,255,.2)); }
        .os-blackhole__halo { position: absolute; inset: 6%; border-radius: 50%; background: radial-gradient(ellipse at 50% 50%,transparent 0 25%,rgba(76,55,171,.12) 39%,rgba(133,99,255,.23) 47%,rgba(39,58,143,.09) 57%,transparent 70%); filter: blur(15px); }
        .os-blackhole__disk { position: absolute; top: 50%; left: 50%; width: 112%; height: 37%; transform: translate(-50%,-50%) rotate(-13deg); border-radius: 50%; background: radial-gradient(ellipse at center,transparent 0 34%,rgba(6,7,18,.95) 38% 42%,rgba(255,226,174,.9) 44%,rgba(255,166,91,.92) 46%,rgba(236,104,255,.85) 49%,rgba(116,91,255,.62) 53%,rgba(46,84,203,.24) 59%,transparent 69%); filter: blur(1.4px); box-shadow: 0 0 18px rgba(255,136,221,.34),0 0 54px rgba(133,81,255,.34); animation: os-disk-pulse 8s ease-in-out infinite alternate; }
        .os-blackhole__lens { position: absolute; top: 50%; left: 50%; width: 55%; height: 55%; transform: translate(-50%,-50%); border-radius: 50%; background: radial-gradient(circle, #010207 0 53%,rgba(2,3,10,.99) 56%,rgba(18,12,38,.96) 60%,rgba(183,122,255,.98) 64%,rgba(255,225,255,.95) 66%,rgba(210,148,255,.48) 68%,rgba(107,75,235,.12) 74%,transparent 83%); box-shadow: 0 0 9px 2px rgba(250,220,255,.74),0 0 27px 8px rgba(165,103,255,.58),0 0 70px 18px rgba(111,72,255,.22); }
        .os-blackhole__photon-ring { position: absolute; top: 50%; left: 50%; width: 64%; height: 64%; transform: translate(-50%,-50%); border: 1px solid rgba(245,222,255,.4); border-radius: 50%; box-shadow: inset 0 0 12px rgba(186,137,255,.26),0 0 14px rgba(201,158,255,.26); }
        @keyframes os-disk-pulse { from { filter: blur(1.4px) brightness(.88); } to { filter: blur(1.1px) brightness(1.13); } }
        .os-shortcuts { position: absolute; z-index: 1; top: 14px; bottom: 54px; left: 10px; display: flex; flex-direction: column; flex-wrap: wrap; align-content: flex-start; gap: 5px; width: 94px; overflow: hidden; }
        .os-shortcut { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; width: 78px; min-height: 76px; padding: 5px; border: 1px solid transparent; border-radius: 4px; background: transparent; color: #fff; font: 500 .65rem/1.2 var(--body); text-align: center; text-shadow: 0 1px 3px rgba(0,0,0,.72); cursor: pointer; }
        .os-shortcut:hover, .os-shortcut:focus-visible, .os-shortcut.is-selected { border-color: rgba(255,255,255,.32); background: rgba(255,255,255,.12); }
        .os-shortcut__icon { display: grid; place-items: center; width: 36px; height: 34px; border: 1px solid rgba(255,255,255,.7); border-radius: 5px; background: linear-gradient(145deg,#fff,#cfc8dd 45%,#8f83a9); color: #493469; box-shadow: 1px 2px 0 rgba(15,12,24,.48); }
        .os-shortcut__icon .ico { width: 20px; height: 20px; }
        .os-window { position: absolute; z-index: 2; inset: 15px 22px 56px clamp(104px, 10vw, 148px); display: flex; flex-direction: column; min-width: 0; min-height: 0; overflow: hidden; border: 2px solid #eeeaf3; background: var(--os-window); color: var(--os-window-ink); box-shadow: 4px 5px 0 rgba(4,3,9,.24), 0 20px 64px rgba(3,3,12,.27); transition: inset .2s ease, opacity .18s ease, transform .18s ease; }
        .os-window.is-minimized { display: none; }
        .os-window.is-maximized { inset: 0 0 44px !important; }
        .os-window__titlebar { flex: 0 0 32px; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 3px 4px 3px 8px; background: linear-gradient(90deg,var(--os-chrome),var(--os-chrome-hi)); color: #fff; font: 600 .76rem/1 var(--body); cursor: grab; touch-action: none; user-select: none; }
        .os-window__title { display: flex; align-items: center; gap: 8px; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .os-window__title .ico { width: 17px; height: 17px; }
        .os-window__controls { display: flex; gap: 3px; flex: 0 0 auto; }
        .os-window__control { display: grid; place-items: center; width: 27px; height: 23px; border: 1px solid rgba(255,255,255,.56); border-radius: 2px; background: var(--os-button-bg); color: var(--os-button-ink); font: 700 .8rem/1 var(--body); cursor: pointer; box-shadow: inset 1px 1px rgba(255,255,255,.24), 1px 1px rgba(0,0,0,.2); }
        .os-window__control:hover { background: var(--os-button-hover); }
        .os-window__control--close { background: var(--os-button-bg); }
        .os-window__layout { flex: 1 1 auto; display: grid; grid-template-columns: 168px minmax(0,1fr); min-width: 0; min-height: 0; border: 1px solid var(--os-sidebar-edge); }
        .os-sidebar { min-width: 0; overflow-y: auto; padding: 22px 12px; border-right: 1px solid var(--os-sidebar-edge); background: var(--os-sidebar-bg); color: var(--os-sidebar-ink); }
        .os-sidebar__identity { padding: 0 5px 17px; border-bottom: 1px solid var(--os-sidebar-edge); }
        .os-sidebar__identity strong { display: block; font: 800 1rem/1.05 var(--head); overflow-wrap: anywhere; }
        .os-sidebar__identity span { display: block; margin-top: 5px; color: var(--os-sidebar-muted); font-size: .68rem; }
        .os-nav { display: grid; gap: 3px; margin-top: 12px; }
        .os-nav a { display: flex; align-items: center; gap: 8px; min-height: 38px; padding: 7px 8px; border: 1px solid transparent; color: var(--os-sidebar-ink); font-size: .75rem; font-weight: 600; text-decoration: none; }
        .os-nav a:hover, .os-nav a.active { border-color: var(--os-border); background: var(--os-card); color: var(--studio-accent); }
        .os-nav a b { display: grid; place-items: center; width: 21px; height: 21px; flex: 0 0 21px; border: 1px solid var(--os-sidebar-edge); background: var(--os-card); font-size: .59rem; }
        .os-sidebar__links { display: grid; gap: 3px; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--os-sidebar-edge); }
        .os-sidebar__links a { display: flex; align-items: center; gap: 8px; min-height: 32px; padding: 4px 7px; color: var(--os-sidebar-muted); font-size: .68rem; text-decoration: none; overflow-wrap: anywhere; }
        .os-sidebar__links a:hover { background: var(--os-card); color: var(--studio-accent); }
        .os-sidebar__links .ico { width: 15px; height: 15px; }
        .os-page { min-width: 0; min-height: 0; overflow: auto; overscroll-behavior: contain; background: var(--os-window); color: var(--os-window-ink); scrollbar-color: var(--os-sidebar-edge) transparent; }
        .os-page .sec { margin: 0; padding: 28px clamp(16px,3vw,42px); border: 0; border-radius: 0; background: transparent; box-shadow: none; scroll-margin-top: 12px; }
        .os-page .sec + .sec { border-top: 1px solid var(--os-border); }
        .os-page .label { color: var(--os-window-muted); }
        .os-page .label b, .os-page .label::after { color: var(--studio-accent); background: var(--studio-accent); }
        .os-page .sec h2 { color: var(--os-window-ink); }
        .os-page .capcard, .os-page .pcard, .os-page .ci { color: var(--os-window-ink); }
        .os-page .capcard h3, .os-page .pcard h3, .os-page .quote { color: var(--os-window-ink); }
        .os-page .about p, .os-page .srow, .os-page .pcard p, .os-page .erow small { color: var(--os-window-muted); }
        .os-page .capcard, .os-page .pcard, .os-page .ci { background: var(--os-card); border-color: var(--os-border); box-shadow: 0 2px 8px rgba(26,18,36,.06); }
        .os-page .pcard:hover, .os-page .pcard:focus-within { box-shadow: 0 7px 18px rgba(26,18,36,.12); }
        .os-page .foot { border-color: var(--os-border); color: var(--os-window-muted); }
        .os-home__grid { display: grid; grid-template-columns: 112px minmax(0,1fr); align-items: center; gap: 20px; }
        .os-home__photo, .os-home__placeholder { width: 112px; height: 112px; overflow: hidden; border: 4px solid var(--os-card); border-radius: 50%; background: var(--os-sidebar-bg); box-shadow: 0 0 0 2px var(--studio-accent), 0 0 0 8px color-mix(in srgb, var(--studio-accent) 14%, transparent); object-fit: cover; }
        .os-home__placeholder { display: grid; place-items: center; color: var(--os-window-ink); font: 800 2.7rem/1 var(--head); }
        .os-home__prompt { margin-bottom: 7px; color: var(--studio-accent); font: 700 .72rem/1.4 ui-monospace,monospace; }
        .os-home h1 { font: 800 clamp(1.8rem,4vw,3.2rem)/1.04 var(--head); overflow-wrap: break-word; word-break: normal; }
        .os-home__headline { margin-top: 6px; color: var(--os-window-muted); font-size: 1rem; font-weight: 600; }
        .os-home__location { margin-top: 12px; color: var(--os-window-muted); font-size: .8rem; }
        .os-home__bio { margin-top: 20px; color: var(--os-window-muted); font-size: .9rem; line-height: 1.75; }
        .os-home__actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 17px; }
        .os-home__actions .hbtn { min-height: 39px; padding: 9px 14px; border-radius: 3px; font-size: .76rem; }
        .os-window__status { flex: 0 0 24px; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 3px 8px; border-top: 1px solid var(--os-sidebar-edge); color: var(--os-window-muted); background: var(--os-sidebar-bg); font: 500 .63rem/1 var(--body); }
        .os-taskbar { position: absolute; z-index: 6; right: 0; bottom: 0; left: 0; display: flex; align-items: center; gap: 6px; height: 44px; padding: 4px 8px; border-top: 1px solid var(--os-taskbar-edge); background: var(--os-taskbar-bg); color: var(--os-taskbar-ink); box-shadow: 0 -3px 15px rgba(12,9,17,.16); }
        .os-start, .os-task-app, .os-return-stage { display: inline-flex; align-items: center; gap: 7px; min-height: 33px; padding: 4px 11px; border: 1px solid var(--os-taskbar-edge); border-radius: 2px; background: var(--os-button-bg); color: var(--os-button-ink); font: 700 .69rem/1 var(--body); cursor: pointer; box-shadow: inset 1px 1px rgba(255,255,255,.24), 1px 1px rgba(0,0,0,.16); }
        .os-start:hover, .os-task-app:hover, .os-task-app.is-active, .os-return-stage:hover { background: var(--os-button-hover); border-color: var(--os-chrome-hi); }
        .os-return-stage { color: var(--studio-accent); }
        .os-return-stage:focus-visible { outline: 3px solid #58dbff; outline-offset: 2px; }
        .os-return-stage .ico { width: 16px; height: 16px; }
        .os-start .ico { width: 17px; height: 17px; color: var(--studio-accent); }
        .os-task-app { min-width: min(220px, 32vw); justify-content: flex-start; font-weight: 500; }
        .os-taskbar__spacer { flex: 1; }
        .os-tray { display: flex; align-items: center; gap: 10px; min-height: 32px; padding: 0 10px; border: 1px solid var(--os-taskbar-edge); background: var(--os-button-bg); color: var(--os-button-ink); font-size: .68rem; }
        .os-theme-slot .theme-toggle { min-height: 32px; min-width: 35px; padding: 0 8px; border-color: var(--os-taskbar-edge); border-radius: 2px; background: var(--os-button-bg); color: var(--os-button-ink); box-shadow: inset 1px 1px rgba(255,255,255,.24),1px 1px rgba(0,0,0,.16); }
        .os-theme-slot .theme-toggle:hover { background: var(--os-button-hover); }
        .os-tray time { font-variant-numeric: tabular-nums; }
        .os-start-menu { position: absolute; z-index: 7; bottom: 48px; left: 8px; width: min(270px,calc(100vw - 16px)); padding: 9px; border: 1px solid var(--os-taskbar-edge); background: var(--os-menu-bg); color: var(--os-window-ink); box-shadow: 3px 5px 22px rgba(0,0,0,.26); }
        .os-start-menu__brand { margin: 0 0 6px; padding: 8px 9px 11px; border-bottom: 1px solid var(--os-border); color: var(--os-window-muted); font: 700 .67rem/1.2 ui-monospace,monospace; text-transform: uppercase; letter-spacing: .08em; }
        .os-start-menu button { display: flex; align-items: center; gap: 9px; width: 100%; min-height: 40px; padding: 6px 9px; border: 0; background: transparent; color: inherit; font: 600 .76rem/1.2 var(--body); text-align: left; cursor: pointer; }
        .os-start-menu button:hover, .os-start-menu button:focus-visible { background: var(--os-card); }
        .os-start-menu button .ico { width: 17px; height: 17px; color: var(--studio-accent); }
        :root[data-theme="light"] .os-shortcut { color: #202633; text-shadow: 0 1px 1px rgba(255,255,255,.72); }
        :root[data-theme="light"] .os-wallpaper { background: radial-gradient(ellipse at 67% 42%,rgba(162,117,239,.3),transparent 38%),radial-gradient(ellipse at 15% 83%,rgba(70,165,206,.2),transparent 42%),linear-gradient(135deg,var(--os-wallpaper-light),var(--os-wallpaper) 66%); }
        @keyframes os-arrive { from { opacity: 0; transform: scale(.985); } to { opacity: 1; transform: scale(1); } }
        @media (max-width: 760px) {
            .os-window { inset: 10px 10px 54px 10px; }
            .os-shortcuts { display: none; }
            .os-window.is-maximized { inset: 0 0 48px !important; }
            .os-blackhole { left: 61%; width: min(64vw, 500px); opacity: .9; }
        }
        @media (max-width: 560px) {
            .os-window { inset: 7px 6px 57px; }
            .os-window.is-maximized { inset: 0 0 48px !important; }
            .os-window__titlebar { flex-basis: 38px; }
            .os-window__control { width: 34px; height: 30px; }
            .os-window__layout { width: 100%; max-width: 100%; box-sizing: border-box; grid-template-columns: minmax(0,1fr); grid-template-rows: auto minmax(0,1fr); }
            .os-sidebar { width: 100%; max-width: 100%; box-sizing: border-box; padding: 6px 8px; border-right: 0; border-bottom: 1px solid #d7d1dd; overflow: hidden; }
            .os-sidebar__identity, .os-sidebar__links { display: none; }
            .os-nav { display: grid; width: min(100%, calc(100vw - 36px)); max-width: calc(100vw - 36px); box-sizing: border-box; grid-template-columns: repeat(4, minmax(0, 1fr)); overflow: visible; gap: 2px; margin: 0; }
            .os-nav::-webkit-scrollbar { display: none; }
            .os-nav a { display: flex; flex: none; flex-direction: column; justify-content: center; gap: 2px; min-width: 0; min-height: 46px; padding: 3px 2px; font-size: .6rem; line-height: 1.1; white-space: normal; text-align: center; }
            .os-nav a b { width: 17px; height: 17px; flex: 0 0 17px; }
            .os-page .sec { padding: 20px 12px; }
            .os-page .sec, .os-page .about, .os-page .about > div, .os-page .about p { min-width: 0; max-width: 100%; }
            .os-page .about { grid-template-columns: minmax(0, 1fr); }
            .os-page .about p, .os-page .pcard p, .os-page .erow small { overflow-wrap: anywhere; }
            .os-home__grid { grid-template-columns: 58px minmax(0,1fr); gap: 10px; align-items: start; }
            .os-home__photo, .os-home__placeholder { width: 58px; height: 58px; border-width: 3px; }
            .os-home__placeholder { font-size: 1.55rem; }
            .os-home__prompt { margin-bottom: 4px; font-size: .66rem; }
            .os-home h1 { font-size: clamp(1.2rem,5.6vw,1.42rem); line-height: 1.08; overflow-wrap: break-word; word-break: normal; }
            .os-home__headline { font-size: .8rem; }
            .os-home__location { margin-top: 7px; font-size: .7rem; }
            .os-home__bio { display: -webkit-box; min-width: 0; max-width: 100%; margin-top: 12px; overflow: hidden; overflow-wrap: anywhere; font-size: .8rem; line-height: 1.55; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
            .os-home__actions { gap: 6px; margin-top: 12px; }
            .os-home__actions .hbtn { min-height: 36px; padding: 8px 10px; font-size: .7rem; }
            .os-taskbar { height: 48px; padding: 3px 5px; gap: 4px; }
            body.creative-os-active #pf-show { right: 10px; bottom: calc(56px + env(safe-area-inset-bottom, 0px)); min-height: 40px; padding: 8px 12px; font-size: .72rem; }
            .os-start, .os-task-app { min-height: 40px; }
            .os-return-stage { min-height: 40px; padding-inline: 8px; }
            .os-return-stage span { display: none; }
            .os-start { padding-inline: 8px; }
            .os-task-app { min-width: 0; max-width: 45vw; padding-inline: 8px; overflow: hidden; white-space: nowrap; }
            .os-tray { min-height: 40px; gap: 5px; padding-inline: 6px; font-size: .6rem; }
            .os-theme-slot .theme-toggle { min-height: 40px; }
            .os-window__status { font-size: .58rem; }
            .os-window__status span:last-child { display: none; }
            .os-blackhole { top: 47%; left: 58%; width: min(82vw, 390px); min-width: 250px; opacity: .82; }
            .os-wallpaper::before { opacity: .3; background-size: 100% 100%,100% 100%,100% 100%,100% 100%,100% 100%,100% 100%,36px 36px,36px 36px; }
        }
        @media (max-width: 360px) {
            .workstation-controls__view { display: none; }
            .os-blackhole { left: 55%; width: 290px; min-width: 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            .creative-os:not([hidden]), .hero.workstation-hero { animation: none !important; transition: none !important; }
            .os-blackhole__disk { animation: none !important; }
        }

    </style>
</head>
<body>
<a class="skip-link" href="#{{ $nav[1][0] ?? 'contact' }}" data-nav="{{ $nav[1][0] ?? 'contact' }}">Skip to portfolio content</a>
<div class="shell creative-shell">

    <aside class="side">
        <div class="s-line"></div>

        <nav class="s-nav" aria-label="Page sections">
            @foreach($nav as $item)
                <a href="#{{ $item[0] }}" data-nav="{{ $item[0] }}"><b>{{ $num[$item[0]] }}</b><span class="s-nav-label">{{ $item[1] }}</span></a>
            @endforeach
        </nav>

        <div class="s-links">
            <h4>LINKS</h4>
            @foreach($links as $l)
                <a href="{{ $l->url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($l->platform) }}">{!! $brandIcon($l->platform) !!}<span>{{ ucfirst($l->platform) }}</span></a>
            @endforeach
            @if(!empty($info->contact_email))
                <a href="mailto:{{ $info->contact_email }}" aria-label="Email">{!! $icon('mail') !!}<span>Email</span></a>
            @endif
            <p class="s-foot">&copy; {{ date('Y') }} {{ $name }}<br>All rights reserved.</p>
        </div>
    </aside>

    <div class="content">

<header class="topbar">
            <div class="top-actions">
                @include('portfolio.partials.theme-toggle')
            </div>
        </header>

        <header class="hero workstation-hero" data-creative-splash aria-label="{{ $name }}'s interactive gaming workstation. Enable Explore 3D to drag and zoom the camera, or switch between front and rear views.">
            <div class="studio-backdrop" aria-hidden="true"></div>
            <div class="workstation-meta" aria-label="Portfolio identity">
                <h1>{{ $name }}</h1>
                <p class="workstation-meta__role">{{ $info->headline ?: 'Creative portfolio' }}</p>
                <p class="workstation-meta__status">
                    <span class="workstation-meta__dot" aria-hidden="true"></span>
                    <time data-portfolio-clock aria-label="Local time">--:--:--</time>
                    @if(!empty($info->location))
                        <span class="workstation-meta__place">{{ $info->location }}</span>
                    @endif
                </p>
            </div>
            <div class="hero-scene" data-creative-scene aria-hidden="true" data-profile-name="{{ $name }}" data-profile-role="{{ $info->headline ?: 'Creative portfolio' }}"></div>
            <div class="workstation-controls" data-camera-controls role="group" aria-label="3D workstation camera controls">
                <span class="workstation-controls__view" data-camera-view aria-live="polite">Front view</span>
                <button class="workstation-explore-toggle" type="button" data-camera-explore aria-pressed="false" aria-label="Explore the 3D workstation">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 10a7 7 0 1 0 2-4.95L3 7m0-4v4h4M10 6v4l2.7 1.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span data-explore-action>Explore 3D</span>
                </button>
                <button class="workstation-camera-toggle" type="button" data-camera-toggle aria-pressed="false" aria-label="Switch to rear camera view">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3.2 6.4h8.2a4.2 4.2 0 0 1 0 8.4H6.8M6.2 3.5 3.1 6.4l3.1 2.8M16.8 13.6H8.6a4.2 4.2 0 0 1 0-8.4H13M13.8 16.5l3.1-2.9-3.1-2.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span data-camera-action>View rear</span>
                </button>
                <button class="workstation-fullscreen-toggle" type="button" data-workstation-fullscreen aria-pressed="false" aria-label="View workstation fullscreen">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7 3H3v4m10-4h4v4M3 13v4h4m10-4v4h-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span data-fullscreen-action>Fullscreen</span>
                </button>
            </div>
            <p class="workstation-explore-hint" data-explore-hint hidden>Drag to orbit · scroll or pinch to zoom</p>
            <p class="workstation-sr-only">A three-dimensional gaming workstation with a modern ultrawide monitor showing the live portfolio preview and animated black hole. Turn on Explore 3D to orbit and zoom, use View rear to see the back of the setup, and use the fullscreen control for a larger view. Choose Open portfolio to enter the interactive desktop.</p>
            <div class="workstation-actions" aria-label="Open portfolio">
                <button class="hbtn red" type="button" data-launch-desktop>Open portfolio <span aria-hidden="true">&#8594;</span></button>
            </div>
            <span class="workstation-hint">Explore the setup · Open the portfolio</span>
        </header>

        <div class="creative-os" id="creative-os" aria-label="{{ $name }}'s interactive portfolio desktop" hidden>
            <div class="os-wallpaper" aria-hidden="true">
                <div class="os-blackhole">
                    <span class="os-blackhole__halo"></span>
                    <span class="os-blackhole__disk"></span>
                    <span class="os-blackhole__lens"></span>
                    <span class="os-blackhole__photon-ring"></span>
                </div>
            </div>
            <nav class="os-shortcuts" aria-label="Portfolio desktop shortcuts">
                @foreach($nav as $item)
                    @php
                        $shortcutIcon = ['home' => 'monitor', 'about' => 'pin', 'skills' => 'wrench', 'projects' => 'monitor', 'experience' => 'server', 'education' => 'bolt', 'contact' => 'mail'][$item[0]] ?? 'globe';
                    @endphp
                    <button class="os-shortcut" type="button" data-open-section="{{ $item[0] }}" aria-label="Open {{ $item[1] }}">
                        <span class="os-shortcut__icon" aria-hidden="true">{!! $icon($shortcutIcon, 20) !!}</span>
                        <span>{{ $item[0] === 'home' ? 'My Portfolio' : $item[1] }}</span>
                    </button>
                @endforeach
            </nav>

            <section class="os-window" data-os-window aria-label="{{ $name }} portfolio window">
                <div class="os-window__titlebar" data-window-drag>
                    <span class="os-window__title">{!! $icon('monitor', 17) !!}<span>{{ $name }} — Portfolio Showcase</span></span>
                    <div class="os-window__controls" aria-label="Window controls">
                        <button class="os-window__control" type="button" data-window-minimize aria-label="Minimize portfolio window" title="Minimize">&#8211;</button>
                        <button class="os-window__control" type="button" data-window-maximize aria-label="Maximize portfolio window" aria-pressed="false" title="Maximize">&#9633;</button>
                        <button class="os-window__control os-window__control--close" type="button" data-window-close aria-label="Close portfolio window" title="Close">&#215;</button>
                    </div>
                </div>
                <div class="os-window__layout">
                    <aside class="os-sidebar" aria-label="Portfolio sections">
                        <div class="os-sidebar__identity">
                            <strong>{{ $name }}</strong>
                            <span>Portfolio showcase</span>
                        </div>
                        <nav class="os-nav" aria-label="Portfolio pages">
                            @foreach($nav as $item)
                                <a href="#{{ $item[0] }}" data-nav="{{ $item[0] }}"><b>{{ $num[$item[0]] }}</b><span>{{ $item[1] }}</span></a>
                            @endforeach
                        </nav>
                        <div class="os-sidebar__links" aria-label="External links">
                            @foreach($links as $l)
                                <a href="{{ $l->url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($l->platform) }}">{!! $brandIcon($l->platform) !!}<span>{{ ucfirst($l->platform) }}</span></a>
                            @endforeach
                            @if(!empty($info->contact_email))
                                <a href="mailto:{{ $info->contact_email }}" aria-label="Email">{!! $icon('mail') !!}<span>Email</span></a>
                            @endif
                        </div>
                    </aside>
                    <main class="os-page" data-os-page tabindex="0" aria-label="Portfolio content">
                        <section class="sec os-home" id="home" data-os-section>
                            <p class="label"><b>{{ $num['home'] }}</b> Welcome</p>
                            <div class="os-home__grid">
                                @if(!empty($info->photo_url))
                                    <img class="os-home__photo" src="{{ $info->photo_url }}" alt="Portrait of {{ $name }}">
                                @else
                                    <div class="os-home__placeholder" aria-hidden="true">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <p class="os-home__prompt">$ whoami</p>
                                    <h1>{{ $name }}</h1>
                                    @if(!empty($info->headline))<p class="os-home__headline">{{ $info->headline }}</p>@endif
                                    @if(!empty($info->location))<p class="os-home__location">{!! $icon('pin', 14) !!} {{ $info->location }}</p>@endif
                                </div>
                            </div>
                            @if(!empty($info->bio))<p class="os-home__bio">{{ $info->bio }}</p>@endif
                            <div class="os-home__actions">
                                @if($hasProjects)
                                    <a class="hbtn red" href="#projects" data-nav="projects">Explore projects <span aria-hidden="true">&#8594;</span></a>
                                @endif
                                @if(!empty($info->contact_email))
                                    <a class="hbtn line" href="mailto:{{ $info->contact_email }}">Contact me {!! $icon('mail', 14) !!}</a>
                                @endif
                                @if($gh)
                                    <a class="hbtn line" href="{{ $gh->url }}" target="_blank" rel="noopener">GitHub <span aria-hidden="true">&#8599;</span></a>
                                @endif
                            </div>
                        </section>

        @if(!empty($info->bio))
        <section class="sec" id="about" data-os-section>
            <p class="label"><b>{{ $num['about'] }}</b> About</p>
            <h2>Who I Am</h2>
            <div class="about">
                <div><p>{{ $info->bio }}</p></div>
                @if($skillList->count())
                <div class="focus">
                    <h4>Core skills</h4>
                    <ul>
                        @foreach($skillList->take(4) as $sk)
                            <li><span class="ib">{!! $icon('bolt', 12) !!}</span> {{ $sk->name }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div>
                    <p class="quote">Better code for a better tomorrow.
                        <svg viewBox="0 0 120 10" width="120" height="10" aria-hidden="true"><path d="M2 7 C 30 1, 70 1, 118 5" fill="none" stroke="#A878FF" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </p>
                </div>
            </div>
        </section>
        @endif

        @if($skillList->count())
        <section class="sec" id="skills" data-os-section>
            <p class="label"><b>{{ $num['skills'] }}</b> Skills</p>
            <h2>My Capabilities</h2>
            <div class="cap">
                @foreach($groups as $title => $g)
                    @if($g[1]->count())
                    <div class="capcard">
                        <h3>{!! $icon($g[0], 18) !!} {{ $title }}</h3>
                        @foreach($g[1] as $sk)
                            <div class="srow">
                                <span>{{ $sk->name }}</span>
                                @if($sk->level)
                                    <span class="dots" aria-label="Level {{ $sk->level }} of 5">
                                        @for($d = 1; $d <= 5; $d++)
                                            <i class="{{ $d <= $sk->level ? 'on' : '' }}"></i>
                                        @endfor
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        @if($hasProjects)
        <section class="sec" id="projects" data-os-section>
            <p class="label"><b>{{ $num['projects'] }}</b> Projects</p>
            <h2>Featured Projects</h2>
            <div class="pgrid">
                @foreach($projects as $project)
                <article class="pcard">
                    <span class="pnum">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    @if(!empty($project->screenshot_url))
                        <img class="pthumb" src="{{ $project->screenshot_url }}" alt="{{ $project->title }}">
                    @else
                        <div class="pph">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    @endif
                    <div>
                        <h3>{{ $project->title }}</h3>
                        @if(!empty($project->description))
                            <p>{{ $project->description }}</p>
                        @endif
                        <div class="plinks">
                            @if(!empty($project->live_url))
                                <a href="{{ $project->live_url }}" target="_blank" rel="noopener">Live Site &#8599;</a>
                            @endif
                            @if(!empty($project->repo_url))
                                <a href="{{ $project->repo_url }}" target="_blank" rel="noopener">Source Code &#8599;</a>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

        @if($experiences->count())
        <section class="sec" id="experience" data-os-section>
            <p class="label"><b>{{ $num['experience'] }}</b> Experience</p>
            <h2>My Experience</h2>
            @foreach($experiences as $exp)
                @php
                    $s = $fmtDate($exp->start_date);
                    $e = $fmtDate($exp->end_date) ?: 'Present';
                @endphp
                <div class="erow">
                    <div class="edate">{{ $s }}{{ $s ? ' - ' : '' }}{{ $e }}</div>
                    <div>
                        <h3>{{ $exp->company }}</h3>
                        <small>{{ $exp->role ?? '' }}{{ !empty($exp->description) ? ' | ' . $exp->description : '' }}</small>
                    </div>
                    @if(!empty($exp->is_internship))
                        <span class="pill">Internship</span>
                    @endif
                </div>
            @endforeach
        </section>
        @endif

        @if($education->count())
        <section class="sec" id="education" data-os-section>
            <p class="label"><b>{{ $num['education'] }}</b> Education</p>
            <h2>My Education</h2>
            @foreach($education as $edu)
                @php
                    $inProgress = empty($edu->end_year) || (is_numeric($edu->end_year) && (int) $edu->end_year >= (int) date('Y'));
                @endphp
                <div class="erow">
                    <div class="edate">{{ $edu->start_year }}{{ ($edu->start_year && $edu->end_year) ? ' - ' : '' }}{{ $edu->end_year }}</div>
                    <div>
                        <h3>{{ $edu->institution }}</h3>
                        @if(!empty($edu->degree))
                            <small>{{ $edu->degree }}{{ !empty($edu->field) ? ', ' . $edu->field : '' }}</small>
                        @endif
                    </div>
                    @if($inProgress)
                        <span class="pill">In progress</span>
                    @endif
                </div>
            @endforeach
        </section>
        @endif

        <section class="sec" id="contact" data-os-section>
            <p class="label"><b>{{ $num['contact'] }}</b> Contact</p>
            <h2>Get in Touch</h2>
            <div class="contact">
                @if(!empty($info->contact_email))
                    <a class="ci" href="mailto:{{ $info->contact_email }}">
                        <span class="ci-ico">{!! $icon('mail', 18) !!}</span>
                        <span><small>Email</small><b>{{ $info->contact_email }}</b></span>
                    </a>
                @endif
                @if(!empty($info->phone))
                    <a class="ci" href="tel:{{ preg_replace('/[^0-9+]/', '', $info->phone) }}">
                        <span class="ci-ico">{!! $icon('phone', 18) !!}</span>
                        <span><small>Phone</small><b>{{ $info->phone }}</b></span>
                    </a>
                @endif
                @if(!empty($info->location))
                    <div class="ci">
                        <span class="ci-ico">{!! $icon('pin', 18) !!}</span>
                        <span><small>Location</small><b>{{ $info->location }}</b></span>
                    </div>
                @endif
            </div>
        </section>

        <footer class="foot">
            <span>&copy; {{ date('Y') }} {{ $name }}</span>
            <span>Built with Portfold</span>
        </footer>

                    </main>
                </div>
                <div class="os-window__status"><span>{{ $name }} portfolio · Ready</span><span>{{ $projects->count() }} projects · {{ $skillList->count() }} skills</span></div>
            </section>

            <div class="os-start-menu" data-start-menu hidden>
                <p class="os-start-menu__brand">{{ $name }} · Portfolio</p>
                @foreach($nav as $item)
                    @php
                        $menuIcon = ['home' => 'monitor', 'about' => 'pin', 'skills' => 'wrench', 'projects' => 'monitor', 'experience' => 'server', 'education' => 'bolt', 'contact' => 'mail'][$item[0]] ?? 'globe';
                    @endphp
                    <button type="button" data-open-section="{{ $item[0] }}">{!! $icon($menuIcon, 16) !!}<span>{{ $item[1] }}</span></button>
                @endforeach
            </div>
            <footer class="os-taskbar" aria-label="Desktop taskbar">
                <button class="os-return-stage" type="button" data-return-workstation aria-label="Return to the 3D workstation">{!! $icon('monitor', 16) !!}<span>Workstation</span></button>
                <button class="os-start" type="button" data-start-toggle aria-expanded="false">{!! $icon('bolt', 17) !!}<span>Start</span></button>
                <button class="os-task-app is-active" type="button" data-task-app aria-label="Show portfolio window">{!! $icon('monitor', 15) !!}<span>My Portfolio</span></button>
                <span class="os-taskbar__spacer"></span>
                <span class="os-theme-slot" data-theme-slot></span>
                <div class="os-tray"><time data-portfolio-clock aria-label="Local time">--:--</time></div>
            </footer>
        </div>

    </div>
</div>

@include('portfolio.partials.theme-toggle-script')
<script>
    (function () {
        var splash = document.querySelector('[data-creative-splash]');
        var desktop = document.querySelector('#creative-os');
        var win = document.querySelector('[data-os-window]');
        var page = document.querySelector('[data-os-page]');
        var startMenu = document.querySelector('[data-start-menu]');
        var startButton = document.querySelector('[data-start-toggle]');
        var taskApp = document.querySelector('[data-task-app]');
        var launchButton = document.querySelector('[data-launch-desktop]');
        var returnButton = document.querySelector('[data-return-workstation]');
        var themeToggle = document.querySelector('[data-theme-toggle]');
        var themeSlot = document.querySelector('[data-theme-slot]');
        var themeHome = themeToggle ? themeToggle.parentElement : null;
        var themeHomeNext = themeToggle ? themeToggle.nextSibling : null;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var updateClocks = function () {
            var now = new Date();
            document.querySelectorAll('[data-portfolio-clock]').forEach(function (clock) {
                clock.textContent = new Intl.DateTimeFormat(undefined, {
                    hour: 'numeric', minute: '2-digit', second: clock.closest('.workstation-meta') ? '2-digit' : undefined
                }).format(now);
                clock.dateTime = now.toISOString();
            });
        };
        updateClocks();
        window.setInterval(updateClocks, 1000);

        var closeMenu = function () {
            if (!startMenu || !startButton) { return; }
            startMenu.hidden = true;
            startButton.setAttribute('aria-expanded', 'false');
        };
        var pendingLaunchSection = null;
        var showDesktop = function (sectionId) {
            if (!desktop || !win) { return; }
            document.body.classList.add('creative-os-active');
            desktop.hidden = false;
            desktop.inert = false;
            if (splash) { splash.inert = true; }
            win.hidden = false;
            win.classList.remove('is-minimized');
            if (themeToggle && themeSlot && themeToggle.parentElement !== themeSlot) { themeSlot.appendChild(themeToggle); }
            if (taskApp) { taskApp.classList.add('is-active'); }
            closeMenu();
            if (sectionId) { scrollToSection(sectionId, false); }
            else if (page && !document.body.dataset.creativeLaunched) { page.scrollTop = 0; }
            document.body.dataset.creativeLaunched = 'true';
        };
        var openDesktop = function (sectionId) {
            if (!desktop || !win) { return; }
            if (document.fullscreenElement === splash) {
                Promise.resolve(document.exitFullscreen ? document.exitFullscreen() : null).catch(function () { return null; }).then(function () { openDesktop(sectionId); });
                return;
            }
            if (splash && splash.dataset.threeReady === 'true' && !document.body.dataset.creativeLaunched) {
                pendingLaunchSection = sectionId || null;
                splash.dispatchEvent(new CustomEvent('creative:launch', { bubbles: true }));
                return;
            }
            showDesktop(sectionId);
        };
        var returnToWorkstation = function () {
            if (!desktop || !splash) { return; }
            closeMenu();
            desktop.hidden = true;
            desktop.inert = true;
            splash.inert = false;
            document.body.classList.remove('creative-os-active');
            delete document.body.dataset.creativeLaunched;
            if (themeToggle && themeHome) {
                themeHome.insertBefore(themeToggle, themeHomeNext && themeHomeNext.parentElement === themeHome ? themeHomeNext : null);
            }
            splash.dispatchEvent(new CustomEvent('creative:return-to-scene', { bubbles: true }));
            launchButton?.focus({ preventScroll: true });
        };
        if (splash) {
            splash.addEventListener('creative:launch-complete', function () {
                showDesktop(pendingLaunchSection);
                pendingLaunchSection = null;
            });
        }
        launchButton?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            openDesktop();
        });
        returnButton?.addEventListener('click', returnToWorkstation);

        var navItems = Array.prototype.slice.call(document.querySelectorAll('.os-nav [data-nav]'));
        var setActiveNav = function (id) {
            navItems.forEach(function (item) {
                var active = item.getAttribute('data-nav') === id;
                item.classList.toggle('active', active);
                if (active) { item.setAttribute('aria-current', 'location'); }
                else { item.removeAttribute('aria-current'); }
            });
        };
        function scrollToSection(id, smooth) {
            if (!page) { return; }
            var section = document.getElementById(id);
            if (!section || !section.hasAttribute('data-os-section')) { return; }
            openDesktopWithoutScroll();
            section.scrollIntoView({ behavior: smooth && !reducedMotion ? 'smooth' : 'auto', block: 'start' });
            setActiveNav(id);
        }
        function openDesktopWithoutScroll() {
            if (!desktop || !win) { return; }
            document.body.classList.add('creative-os-active');
            desktop.hidden = false;
            win.hidden = false;
            win.classList.remove('is-minimized');
            if (themeToggle && themeSlot && themeToggle.parentElement !== themeSlot) { themeSlot.appendChild(themeToggle); }
            if (taskApp) { taskApp.classList.add('is-active'); }
            closeMenu();
            document.body.dataset.creativeLaunched = 'true';
        }

        document.querySelectorAll('[data-nav]').forEach(function (item) {
            item.addEventListener('click', function (event) {
                var id = item.getAttribute('data-nav');
                if (!id || !document.getElementById(id)) { return; }
                event.preventDefault();
                scrollToSection(id, true);
            });
        });
        document.querySelectorAll('[data-open-section]').forEach(function (shortcut) {
            shortcut.addEventListener('click', function () {
                scrollToSection(shortcut.getAttribute('data-open-section'), true);
                document.querySelectorAll('.os-shortcut').forEach(function (item) { item.classList.toggle('is-selected', item === shortcut); });
            });
        });

        if (page && 'IntersectionObserver' in window) {
            var navObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) { setActiveNav(entry.target.id); }
                });
            }, { root: page, rootMargin: '-12% 0px -70% 0px', threshold: 0 });
            page.querySelectorAll('[data-os-section]').forEach(function (section) { navObserver.observe(section); });
        }

        if (startButton && startMenu) {
            startButton.addEventListener('click', function () {
                var willOpen = startMenu.hidden;
                startMenu.hidden = !willOpen;
                startButton.setAttribute('aria-expanded', String(willOpen));
            });
        }
        if (taskApp) {
            taskApp.addEventListener('click', function () {
                if (!win) { return; }
                if (win.hidden || win.classList.contains('is-minimized')) {
                    openDesktopWithoutScroll();
                } else {
                    win.classList.add('is-minimized');
                    taskApp.classList.remove('is-active');
                }
            });
        }
        var minimizeButton = document.querySelector('[data-window-minimize]');
        if (minimizeButton) {
            minimizeButton.addEventListener('click', function () {
                win.classList.add('is-minimized');
                taskApp?.classList.remove('is-active');
            });
        }
        var maximizeButton = document.querySelector('[data-window-maximize]');
        if (maximizeButton) {
            maximizeButton.addEventListener('click', function () {
                var isMaximized = win.classList.toggle('is-maximized');
                maximizeButton.setAttribute('aria-pressed', String(isMaximized));
                maximizeButton.setAttribute('aria-label', isMaximized ? 'Restore portfolio window' : 'Maximize portfolio window');
                maximizeButton.title = isMaximized ? 'Restore' : 'Maximize';
            });
        }
        var closeButton = document.querySelector('[data-window-close]');
        if (closeButton) {
            closeButton.addEventListener('click', function () {
                win.hidden = true;
                taskApp?.classList.remove('is-active');
                closeMenu();
            });
        }
        document.addEventListener('click', function (event) {
            if (!startMenu || startMenu.hidden || event.target.closest('[data-start-menu]') || event.target.closest('[data-start-toggle]')) { return; }
            closeMenu();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && startMenu && !startMenu.hidden) {
                closeMenu();
                startButton?.focus();
            } else if (event.key === 'Escape' && desktop && !desktop.hidden) {
                returnToWorkstation();
            }
        });

        var titlebar = document.querySelector('[data-window-drag]');
        if (titlebar && win && window.matchMedia('(min-width: 761px) and (pointer: fine)').matches) {
            titlebar.addEventListener('pointerdown', function (event) {
                if (event.target.closest('button') || win.classList.contains('is-maximized')) { return; }
                event.preventDefault();
                var desktopRect = desktop.getBoundingClientRect();
                var windowRect = win.getBoundingClientRect();
                var startX = event.clientX;
                var startY = event.clientY;
                var offsetX = windowRect.left - desktopRect.left;
                var offsetY = windowRect.top - desktopRect.top;
                win.style.inset = 'auto';
                win.style.right = 'auto';
                win.style.bottom = 'auto';
                win.style.width = windowRect.width + 'px';
                win.style.height = windowRect.height + 'px';
                win.style.left = offsetX + 'px';
                win.style.top = offsetY + 'px';
                titlebar.setPointerCapture(event.pointerId);
                var move = function (moveEvent) {
                    var maxLeft = Math.max(0, desktop.clientWidth - windowRect.width);
                    var maxTop = Math.max(0, desktop.clientHeight - 90);
                    var left = Math.max(0, Math.min(maxLeft, offsetX + moveEvent.clientX - startX));
                    var top = Math.max(0, Math.min(maxTop, offsetY + moveEvent.clientY - startY));
                    win.style.left = left + 'px';
                    win.style.top = top + 'px';
                };
                var stop = function () {
                    titlebar.removeEventListener('pointermove', move);
                    titlebar.removeEventListener('pointerup', stop);
                    titlebar.removeEventListener('pointercancel', stop);
                };
                titlebar.addEventListener('pointermove', move);
                titlebar.addEventListener('pointerup', stop);
                titlebar.addEventListener('pointercancel', stop);
            });
            maximizeButton?.addEventListener('dblclick', function () { maximizeButton.click(); });
        }

        if (window.location.hash) {
            var initialSection = window.location.hash.slice(1);
            if (document.getElementById(initialSection)?.hasAttribute('data-os-section')) {
                openDesktop(initialSection);
            }
        }
    })();
</script>

</body>
</html>
