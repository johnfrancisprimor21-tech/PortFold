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
        .hbtn { position: relative; overflow: hidden; border-radius: 20px 14px 23px 16px / 15px 22px 14px 21px; box-shadow: inset 0 2px 1px rgba(255,255,255,.3),inset 0 -6px 11px rgba(23,5,53,.32),0 9px 20px rgba(7,3,20,.25); }
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

    </style>
</head>
<body>
<div class="shell">

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
            <a class="top-brand" href="#home" aria-label="{{ $name }} home">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2.8 21.2 21h-4.6L12 11.8 7.4 21H2.8L12 2.8Z" stroke="currentColor" stroke-width="2.1" stroke-linejoin="round"/><path d="M9.4 15.2h5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>{{ $name }}</span>
            </a>
            <nav class="top-nav" aria-label="Primary navigation">
                @foreach($nav as $item)
                    <a href="#{{ $item[0] }}" data-nav="{{ $item[0] }}">{{ $item[1] }}</a>
                @endforeach
            </nav>
            <div class="top-actions">
                <div class="top-social" aria-label="Social links">
                    @foreach($links->take(2) as $l)
                        <a href="{{ $l->url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($l->platform) }}">{!! $brandIcon($l->platform) !!}</a>
                    @endforeach
                    @if(!empty($info->contact_email))
                        <a href="mailto:{{ $info->contact_email }}" aria-label="Email">{!! $icon('mail', 14) !!}</a>
                    @endif
                </div>
                @include('portfolio.partials.theme-toggle')
            </div>
        </header>

        <header class="hero" id="home">
            <div class="fb"></div>
            <div class="hero-scene" data-creative-scene aria-hidden="true"></div>
            @if($skillList->count())
                <div class="skill-orbit" aria-hidden="true">
                    <svg class="orbit-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <circle cx="50" cy="50" r="48"/><circle cx="50" cy="50" r="35"/><circle cx="50" cy="50" r="21"/>
                        <path d="M50 2v96M2 50h96"/>
                    </svg>
                    <div class="orbit-center">
                        @if(!empty($info->photo_url))
                            <img class="orbit-photo" src="{{ $info->photo_url }}" alt="" fetchpriority="high" decoding="async">
                        @else
                            <span class="orbit-placeholder" aria-hidden="true"><svg viewBox="0 0 120 120" fill="none"><circle cx="60" cy="42" r="17" stroke="currentColor" stroke-width="3"/><path d="M26 106c3.5-21 15-33 34-33s30.5 12 34 33" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M44 42c0-9 7-16 16-16s16 7 16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".45"/></svg></span>
                        @endif
                    </div>
                    @foreach($skillList->take(8) as $sk)
                        @php [$orbitX, $orbitY] = $orbitPositions[$loop->index]; @endphp
                        @php $devicon = $skillDevicon($sk->name); @endphp
                        <span class="orbit-skill" style="--x: {{ $orbitX }}; --y: {{ $orbitY }}; --delay: -{{ $loop->index * 0.55 }}s">
                            @if($devicon)
                                <i class="orbit-skill-logo {{ $devicon }}" aria-hidden="true"></i>
                            @else
                                <span class="orbit-skill-mark">{{ $skillMonogram($sk->name) }}</span>
                            @endif
                            <span class="orbit-skill-name">{{ $sk->name }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
            <div class="hero-in">
                @if(!empty($info->headline))
                    <p class="role-pill"><i aria-hidden="true"></i>{{ $info->headline }}</p>
                @endif
                <p class="hello">Hello, I'm</p>
                <h1><span>{{ $firstWords ?: $name }}</span>@if($lastWord)<em>{{ $lastWord }}</em>@endif</h1>
                @if(!empty($info->bio))
                    <p>{{ \Illuminate\Support\Str::limit($info->bio, 120) }}</p>
                @endif
                <div class="hbtns">
                    @if($hasProjects)
                        <a class="hbtn red" href="#projects">View Projects &rarr;</a>
                    @else
                        <a class="hbtn red" href="#contact">Contact me &rarr;</a>
                    @endif
                    @if($gh)
                        <a class="hbtn line" href="{{ $gh->url }}" target="_blank" rel="noopener">GitHub &#8599;</a>
                    @endif
                </div>
            </div>
        </header>

        @if(!empty($info->bio))
        <section class="sec" id="about">
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
        <section class="sec" id="skills">
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
        <section class="sec" id="projects">
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
        <section class="sec" id="experience">
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
        <section class="sec" id="education">
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

        <section class="sec" id="contact">
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

    </div>
</div>

@include('portfolio.partials.theme-toggle-script')
<script>
    (function () {
        var map = {};
        document.querySelectorAll('[data-nav]').forEach(function (a) {
            var key = a.getAttribute('data-nav');
            map[key] = map[key] || [];
            map[key].push(a);
        });
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) { return; }
                Object.keys(map).forEach(function (k) {
                    var isCurrent = k === e.target.id;
                    map[k].forEach(function (a) {
                        a.classList.toggle('active', isCurrent);
                        if (isCurrent) { a.setAttribute('aria-current', 'location'); }
                        else { a.removeAttribute('aria-current'); }
                    });
                });
            });
        }, { rootMargin: '-30% 0px -60% 0px' });
        document.querySelectorAll('header[id], section[id]').forEach(function (s) { io.observe(s); });
    })();
</script>

</body>
</html>
