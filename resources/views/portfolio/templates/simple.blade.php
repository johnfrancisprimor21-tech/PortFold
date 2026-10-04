@php
    $name = trim((string) ($info->full_name ?? '')) ?: 'Your Name';
    $location = trim((string) ($info->location ?? ''));
    $email = trim((string) ($info->contact_email ?? ''));
    $phone = trim((string) ($info->phone ?? ''));
    $bio = trim((string) ($info->bio ?? ''));
    $photo = trim((string) ($info->photo_url ?? ''));

    $words = array_values(array_filter(preg_split('/\s+/', $name)));
    $initials = count($words) > 1
        ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1))
        : mb_strtoupper(mb_substr($name, 0, 2));

    // Skills can be typed as "Laravel:4"; the optional number is ignored here.
    $skillList = collect($skills ?? [])->map(function ($sk) {
        $parts = explode(':', (string) $sk->name, 2);
        return trim($parts[0]);
    })->filter()->values();

    $fmtDate = function ($v) {
        $v = trim((string) $v);
        if ($v === '') { return ''; }
        try {
            return strtotime($v) ? \Carbon\Carbon::parse($v)->format('M Y') : $v;
        } catch (\Throwable $e) {
            return $v;
        }
    };

    $hasProjects = $projects && $projects->count() > 0;
    $hasExp      = $experiences && $experiences->count() > 0;
    $hasEdu      = $education && $education->count() > 0;
    $hasLinks    = $links && $links->count() > 0;

    $nav = [['home', 'Home']];
    if ($bio !== '' || $skillList->count()) { $nav[] = ['about', 'About']; }
    if ($hasProjects) { $nav[] = ['projects', 'Projects']; }
    if ($hasExp || $hasEdu) { $nav[] = ['background', 'Background']; }
    $nav[] = ['contact', 'Contact'];

    $paths = [
        'mail'  => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'pin'   => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'arrow' => '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
    ];
    $icon = function ($n, $s = 16) use ($paths) {
        return '<svg class="ico" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $s . '" height="' . $s . '" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$n] . '</svg>';
    };
@endphp
<!DOCTYPE html>
<html lang="en" data-default-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }}{{ !empty($info->headline) ? ' - ' . $info->headline : '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('portfolio.partials.theme-init')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #eaf2f8;
            --surface: rgba(235,245,252,.78);
            --ink: #142b3d;
            --ink-soft: #314e64;
            --label: #087f91;
            --accent: #078b9b;
            --pill-bg: rgba(72,190,201,.22);
            --pill-ink: #075967;
            --rule: rgba(80,131,160,.25);
            --rule-hover: rgba(6,151,171,.62);
            --hero-wash: rgba(76,184,229,.22);
            --nav-bg: rgba(192,222,246,.24);
            --photo-tint: rgba(202,229,246,.18);
            --photo-fade: linear-gradient(90deg, rgba(234,242,248,.3), transparent 42%);
            --photo-fade-mobile: linear-gradient(180deg, transparent 58%, rgba(234,242,248,.36));
            --placeholder-bg: linear-gradient(135deg, #d8ecf5, #b5d7e5);
            --graphic-line: rgba(7,139,155,.3);
            --theme-control-bg: rgba(202,227,246,.3);
            --theme-control-border: rgba(255,255,255,.66);
            --theme-control-hover: rgba(255,255,255,.52);
            --theme-control-active: #0aa4b1;
            --glass-panel: rgba(207,229,247,.48);
            --glass-card: rgba(215,235,249,.42);
            --glass-nav: rgba(160,204,235,.24);
            --glass-edge: rgba(245,253,255,.84);
            --glass-edge-cyan: rgba(31,197,215,.9);
            --glass-edge-blue: rgba(92,157,247,.84);
            --glass-edge-warm: rgba(246,212,109,.76);
            --glass-shadow: 0 24px 58px rgba(23,76,119,.24), 0 0 28px rgba(61,183,226,.15);
            --glass-inset: inset 0 1px 0 rgba(255,255,255,.88), inset 0 -1px 0 rgba(58,112,152,.11);
            --atmosphere-tint: rgba(205,230,248,.18);
            --photo-glass-tint: rgba(216,237,250,.28);
            --font:      'Manrope', 'Segoe UI', system-ui, sans-serif;
            --nav-h:     64px;
            --pad:       clamp(20px, 7vw, 93px);
        }

        :root[data-theme="dark"] {
            color-scheme: dark;
            --bg: #061421;
            --surface: rgba(8,23,39,.8);
            --ink: #edf7ff;
            --ink-soft: #c0d4e4;
            --label: #5be3e8;
            --accent: #20d2d8;
            --pill-bg: rgba(36,201,205,.16);
            --pill-ink: #c2fbff;
            --rule: rgba(134,179,210,.22);
            --rule-hover: rgba(70,222,228,.68);
            --hero-wash: rgba(34,146,203,.2);
            --nav-bg: rgba(4,18,34,.34);
            --photo-tint: rgba(3,15,31,.32);
            --photo-fade: linear-gradient(90deg, rgba(3,15,31,.25), transparent 42%);
            --photo-fade-mobile: linear-gradient(180deg, transparent 58%, rgba(6,20,33,.38));
            --placeholder-bg: linear-gradient(135deg, #153c54, #0b2038);
            --graphic-line: rgba(91,227,232,.26);
            --theme-control-bg: rgba(27,62,89,.45);
            --theme-control-border: rgba(128,203,231,.44);
            --theme-control-hover: rgba(36,89,121,.64);
            --theme-control-active: #5be3e8;
            --glass-panel: rgba(4,20,37,.66);
            --glass-card: rgba(8,27,45,.48);
            --glass-nav: rgba(4,20,37,.62);
            --glass-edge: rgba(86,194,228,.42);
            --glass-edge-cyan: rgba(39,234,226,.9);
            --glass-edge-blue: rgba(86,160,255,.86);
            --glass-edge-warm: rgba(244,211,104,.76);
            --glass-shadow: 0 26px 70px rgba(0,0,0,.38), 0 0 32px rgba(33,179,231,.18);
            --glass-inset: inset 0 1px 0 rgba(245,253,255,.17), inset 0 -1px 0 rgba(0,0,0,.22);
            --atmosphere-tint: rgba(0,9,24,.82);
            --photo-glass-tint: rgba(2,14,30,.58);
        }

        html { scroll-behavior: smooth; scroll-padding-top: calc(var(--nav-h) + var(--pf-preview-bar-height, 0px)); }
        body { min-width: 320px; font-family: var(--font); background: var(--bg); color: var(--ink); line-height: 1.7; font-size: 15px; -webkit-font-smoothing: antialiased; transition: background-color .2s ease, color .2s ease; }
        a { color: inherit; text-decoration: none; }
        .ico { flex: none; }
        :focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; border-radius: 4px; }
        @include('portfolio.partials.theme-toggle-styles')
        section[id], #about { scroll-margin-top: var(--nav-h); }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } * { transition: none !important; } }

        /* ---------- Top bar ---------- */
        .topbar {
            position: sticky; top: var(--pf-preview-bar-height, 0px); z-index: 20; min-height: var(--nav-h); height: auto;
            display: flex; align-items: center; justify-content: space-between;
            gap: 18px; margin: clamp(8px, 1.2vw, 18px) clamp(14px, 2.4vw, 40px) 0;
            padding: 0 clamp(16px, 2.1vw, 34px);
            border: 1px solid transparent; border-radius: 18px;
            background: var(--glass-nav);
            border-color: var(--glass-edge);
            box-shadow: 0 12px 36px rgba(11,41,72,.16), var(--glass-inset);
            backdrop-filter: blur(22px) saturate(1.45); -webkit-backdrop-filter: blur(22px) saturate(1.45);
        }
        .brand { display: flex; align-items: center; gap: 14px; font-weight: 500; font-size: 15.5px; letter-spacing: .01em; }
        .brand svg { width: 20px; height: 20px; color: var(--accent); }
        .brand span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 40vw; }
        .topbar-actions { display: flex; align-items: center; gap: 22px; }
        .menu { display: flex; align-items: center; gap: 24px; }
        .menu a { display: inline-flex; align-items: center; min-height: 44px; padding: 6px 2px; font-size: 13px; font-weight: 500; color: var(--ink-soft); border-bottom: 2px solid transparent; transition: color .2s, border-color .2s; white-space: nowrap; }
        .menu a:hover { color: var(--ink); }
        .menu a.active { color: var(--accent); border-color: var(--accent); }

        /* ---------- Hero ---------- */
        .hero { position: relative; isolation: isolate; min-height: clamp(560px, calc(100vh - var(--nav-h) - var(--pf-preview-bar-height, 0px)), 820px); min-height: clamp(560px, calc(100svh - var(--nav-h) - var(--pf-preview-bar-height, 0px)), 820px); display: flex; align-items: center; overflow: visible; background: radial-gradient(ellipse at 95% 42%, var(--hero-wash), transparent 52%), var(--bg); }
        .hero-atmosphere { position: absolute; z-index: 0; top: calc(-1 * (var(--nav-h) + 24px)); left: 0; width: 100%; height: calc(100% + var(--nav-h) + 24px); overflow: hidden; pointer-events: none; }
        .hero-atmosphere img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center 34%; filter: blur(12px) saturate(1.12); transform: scale(1.04); }
        .hero-atmosphere::after { content: ''; position: absolute; inset: 0; background: var(--atmosphere-tint); }
        .hero-atmosphere.no-img { background: radial-gradient(ellipse at 78% 15%, rgba(70,196,239,.42), transparent 40%), linear-gradient(135deg, #a6d2eb, #dcecf5 48%, #91c6d6); }
        :root[data-theme="dark"] .hero-atmosphere.no-img { background: radial-gradient(ellipse at 78% 15%, rgba(25,170,220,.38), transparent 42%), linear-gradient(135deg, #102b49, #061421 58%, #123d50); }
        .hero-inner { position: relative; z-index: 2; display: flex; align-items: center; width: 100%; padding: 46px clamp(20px, 4vw, 68px); }
        .hero-copy { width: min(34vw, 660px); max-width: 660px; }

        .eyebrow { font-size: 12px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--label); }
        .hero h1 { max-width: 13ch; font-size: clamp(42px, 4.6vw, 70px); line-height: 1.02; font-weight: 800; letter-spacing: -.055em; overflow-wrap: anywhere; margin: 10px 0 12px; }
        .where { font-size: 16px; color: var(--ink-soft); }
        .contact-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 18px; margin-top: 18px; font-size: 14px; color: var(--ink-soft); }
        .contact-row a, .contact-row span { display: inline-flex; align-items: center; gap: 10px; min-height: 44px; }
        .contact-row a { overflow-wrap: anywhere; }
        .contact-row a:hover { color: var(--accent); }
        .contact-row .sep { width: 1px; height: 16px; background: var(--rule); }
        .short-rule { width: 56px; height: 3px; background: var(--accent); margin: 26px 0; border-radius: 3px; }

        .label { font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--label); margin-bottom: 10px; }
        .about-text { color: var(--ink-soft); font-size: 15px; line-height: 1.8; white-space: pre-line; }
        .hero .block + .block { margin-top: 26px; }

        .pills { display: flex; flex-wrap: wrap; gap: 8px; }
        .pill { display: inline-flex; align-items: center; min-height: 36px; background: var(--pill-bg); color: var(--pill-ink); font-size: 12px; font-weight: 600; letter-spacing: .025em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px; }

        /* Photo panel with diagonal cut and soft fade into the page */
        .hero-photo { position: absolute; top: 12%; right: clamp(24px, 4vw, 68px); bottom: 12%; width: min(52vw, 920px); z-index: 1; overflow: hidden; border: 1px solid transparent; border-radius: 28px; background: linear-gradient(135deg, var(--glass-panel), var(--glass-card)) padding-box, linear-gradient(130deg, var(--glass-edge-cyan), var(--glass-edge-blue) 58%, var(--glass-edge-warm)) border-box; box-shadow: var(--glass-shadow), var(--glass-inset); backdrop-filter: blur(22px) saturate(1.2); -webkit-backdrop-filter: blur(22px) saturate(1.2); }
        .hero-photo img { width: 100%; height: 100%; object-fit: cover; object-position: center 30%; display: block; filter: blur(1.25px) saturate(.72) contrast(.94); opacity: .88; transform: scale(1.025); }
        .hero-photo.no-img { display: flex; align-items: center; justify-content: center; }
        .hero-photo.no-img b { position: relative; z-index: 3; font-size: clamp(80px, 14vw, 180px); font-weight: 800; color: rgba(255,255,255,.72); letter-spacing: .04em; }
        .hero-photo::before { content: ""; position: absolute; inset: 0; background: var(--photo-glass-tint); z-index: 1; pointer-events: none; }
        .hero-photo::after { content: ""; position: absolute; inset: 0; z-index: 2; border: 1px solid rgba(255,255,255,.2); border-radius: inherit; box-shadow: inset 0 1px 0 rgba(255,255,255,.28), inset 0 0 46px rgba(131,210,244,.12); pointer-events: none; }
        .stripe { position: absolute; z-index: 1; top: 0; bottom: 0; right: 0; pointer-events: none; }
        .stripe.s1 { width: 50%; clip-path: polygon(30% 0, 100% 0, 100% 100%, 0 100%); background: rgba(255,255,255,.10); }
        .stripe.s2 { width: 38%; clip-path: polygon(34% 0, 100% 0, 100% 100%, 0 100%); background: rgba(255,255,255,.08); }

        /* ---------- Content sections ---------- */
        main section.page { padding: 72px var(--pad); }
        main section.page:nth-of-type(even) { background: var(--surface); }
        .sec-head { margin-bottom: 30px; }
        .sec-head h2 { font-size: clamp(24px, 3vw, 30px); font-weight: 700; letter-spacing: -.025em; line-height: 1.2; }
        .wrap { max-width: 1050px; margin-inline: auto; }

        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap: 22px; }
        .card { background: var(--surface); border: 1px solid var(--rule); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: transform .2s, box-shadow .2s, border-color .2s; }
        .card:hover, .card:focus-within { transform: translateY(-2px); border-color: var(--rule-hover); box-shadow: 0 12px 28px rgba(35,55,70,.12); }
        .card img { width: 100%; height: 170px; object-fit: cover; display: block; background: var(--pill-bg); }
        .card .ph { position: relative; height: 170px; display: grid; place-items: center; overflow: hidden; background: var(--placeholder-bg); color: var(--accent); }
        .card .ph::before { position: absolute; width: 138px; height: 138px; border: 1px solid var(--graphic-line); border-radius: 50%; content: ''; }
        .card .ph span { position: relative; font-size: 24px; font-weight: 700; letter-spacing: .08em; }
        .card-body { padding: 22px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .card h3 { font-size: 16px; font-weight: 600; }
        .card p { color: var(--ink-soft); font-size: 13.5px; line-height: 1.65; flex: 1; }
        .card-links { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 6px; }
        .card-links a { display: inline-flex; align-items: center; gap: 6px; min-height: 44px; font-size: 13px; font-weight: 600; color: var(--accent); }
        .card-links a:hover { text-decoration: underline; }

        .two-col { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap: 44px; }
        .timeline { border-left: 2px solid var(--rule); padding-left: 24px; display: flex; flex-direction: column; gap: 28px; }
        .t-item { position: relative; }
        .t-item::before { content: ""; position: absolute; left: -31px; top: 7px; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 4px var(--bg); }
        main section.page:nth-of-type(even) .t-item::before { box-shadow: 0 0 0 4px var(--surface); }
        .t-date { font-size: 12px; font-weight: 700; letter-spacing: .05em; color: var(--label); text-transform: uppercase; }
        .t-title { font-size: 15.5px; font-weight: 600; margin-top: 2px; }
        .t-sub { font-size: 13.5px; color: var(--ink-soft); }
        .t-body { font-size: 13.5px; color: var(--ink-soft); margin-top: 6px; line-height: 1.65; }
        .tag { display: inline-block; font-size: 11px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; background: var(--pill-bg); color: var(--pill-ink); padding: 3px 9px; border-radius: 999px; margin-left: 8px; vertical-align: middle; }

        .contact-list { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; font-size: 15px; }
        .contact-list a, .contact-list span { display: inline-flex; align-items: center; gap: 12px; min-height: 44px; color: var(--ink-soft); overflow-wrap: anywhere; }
        .contact-list a:hover { color: var(--accent); }
        .social { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .social a { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 8px 18px; border: 1px solid var(--rule); border-radius: 999px; font-size: 13px; font-weight: 600; color: var(--ink-soft); background: var(--surface); transition: color .2s, background .2s, border-color .2s; }
        .social a:hover { color: #fff; background: var(--accent); border-color: var(--accent); }

        footer { margin: 0 var(--pad); padding: 22px 0 28px; border-top: 1px solid var(--rule); display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 13px; color: var(--ink-soft); }
        footer a { display: inline-flex; align-items: center; gap: 8px; min-height: 44px; }
        .skip-link { position: fixed; top: calc(var(--pf-preview-bar-height, 0px) + 8px); left: 12px; z-index: 100; padding: 10px 14px; transform: translateY(-160%); border-radius: 8px; background: var(--ink); color: #fff; font-weight: 600; }
        .skip-link:focus { transform: translateY(0); }

        /* ---------- Responsive ---------- */
        @media (max-width: 860px) {
            .menu { gap: 16px; }
            .topbar { flex-wrap: wrap; padding-top: 8px; padding-bottom: 8px; }
            .topbar-actions { flex: 1 1 100%; justify-content: space-between; gap: 12px; }
            .topbar-actions .menu { flex: 1; justify-content: space-between; gap: 8px; }
            .brand span { max-width: 34vw; }
            .hero { display: flex; flex-direction: column; min-height: 0; }
            .hero-atmosphere { top: calc(-1 * (var(--nav-h) + 20px)); height: calc(100% + var(--nav-h) + 20px); }
            .hero-photo { position: relative; inset: auto; order: 2; width: calc(100% - (2 * var(--pad))); height: clamp(220px, 42vw, 340px); margin: 0 var(--pad) 22px; border-radius: 24px; }
            .stripe { display: none; }
            .hero-inner { order: 1; display: block; padding: 28px var(--pad) 20px; }
            .hero-copy { max-width: 680px; }
            .hero-copy { width: min(100%, 680px); margin-inline: auto; }
            .hero h1 { font-size: clamp(38px, 7vw, 56px); }
            main section.page { padding-top: 58px; padding-bottom: 58px; }
        }
        @media (min-width: 861px) and (max-width: 1100px) {
            .hero-copy { width: min(38vw, 430px); }
            .hero-photo { width: 47vw; right: 3vw; }
        }
        @media (max-width: 600px) {
            :root { --nav-h: 132px; --pad: 20px; }
            .topbar { display: grid; grid-template-columns: 1fr; align-content: center; gap: 6px; margin-inline: 12px; padding: 8px 14px; border-radius: 16px; }
            .topbar-actions { width: 100%; gap: 8px; }
            .brand { min-height: 32px; gap: 9px; font-size: 14px; }
            .brand span { display: block; max-width: calc(100vw - 72px); }
            .menu { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); flex: 1; gap: 4px; width: auto; }
            .menu a { justify-content: center; min-height: 44px; padding: 5px 3px; border-bottom-width: 2px; font-size: 12px; text-align: center; }
            .hero-photo { height: 220px; margin-bottom: 18px; }
            .hero-inner { padding: 18px var(--pad) 18px; }
            .hero h1 { max-width: 12ch; font-size: clamp(36px, 10vw, 48px); }
            .where { font-size: 15px; }
            .contact-row { align-items: flex-start; flex-direction: column; gap: 2px; }
            .contact-row .sep { display: none; }
            .hero .block + .block { margin-top: 22px; }
            main section.page { padding: 46px var(--pad); }
            .cards { gap: 16px; }
            .card img, .card .ph { height: 150px; }
            .two-col { gap: 36px; }
            footer { margin-inline: var(--pad); }
        }

        /* Layered glass surfaces over a softly blurred version of the portfolio image. */
        .topbar {
            background: var(--glass-nav);
            border-color: var(--glass-edge);
            box-shadow: 0 14px 38px rgba(11,41,72,.18), var(--glass-inset);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
        }
        .hero-copy {
            padding: clamp(22px, 2.5vw, 34px);
            border: 1px solid var(--glass-edge-cyan);
            border-radius: 28px;
            background: var(--glass-panel);
            box-shadow: var(--glass-shadow), var(--glass-inset);
            backdrop-filter: blur(24px) saturate(1.35);
            -webkit-backdrop-filter: blur(24px) saturate(1.35);
        }
        .hero-atmosphere { filter: saturate(1.08); }
        .eyebrow, .label { color: var(--label); }
        .short-rule { box-shadow: 0 0 16px color-mix(in srgb, var(--accent) 52%, transparent); }
        .hero-photo.no-img { background: linear-gradient(135deg, var(--glass-panel), var(--glass-card)) padding-box, linear-gradient(130deg, var(--glass-edge-cyan), var(--glass-edge-blue) 58%, var(--glass-edge-warm)) border-box; }
        .hero-photo .stripe { display: none; }
        main section.page:nth-of-type(even) { background: var(--glass-card); }
        .card {
            background: var(--glass-card);
            border-color: var(--glass-edge);
            box-shadow: 0 18px 42px rgba(20,70,105,.14), var(--glass-inset);
            backdrop-filter: blur(18px) saturate(1.2);
            -webkit-backdrop-filter: blur(18px) saturate(1.2);
        }
        .card:hover, .card:focus-within {
            box-shadow: 0 22px 46px rgba(20,70,105,.2), var(--glass-inset);
        }
        .pill, .tag, .social a, .theme-toggle {
            border: 1px solid var(--glass-edge);
            box-shadow: 0 7px 20px rgba(12,62,94,.14), var(--glass-inset);
            backdrop-filter: blur(14px) saturate(1.35);
            -webkit-backdrop-filter: blur(14px) saturate(1.35);
        }
        .pill { background: linear-gradient(135deg, color-mix(in srgb, var(--pill-bg) 88%, transparent), color-mix(in srgb, var(--glass-panel) 72%, transparent)); }
        .tag { background: color-mix(in srgb, var(--pill-bg) 86%, transparent); }
        .social a, .theme-toggle { background: linear-gradient(135deg, var(--glass-panel), color-mix(in srgb, var(--glass-card) 84%, transparent)); }
        .social a:hover { box-shadow: 0 0 0 1px var(--glass-edge-cyan), 0 10px 26px rgba(12,62,94,.2), var(--glass-inset); }
        :root[data-theme="dark"] .card:hover,
        :root[data-theme="dark"] .card:focus-within,
        :root[data-theme="dark"] .pill,
        :root[data-theme="dark"] .tag,
        :root[data-theme="dark"] .social a,
        :root[data-theme="dark"] .theme-toggle {
            box-shadow: 0 12px 30px rgba(0,0,0,.32), 0 0 18px rgba(39,214,224,.1), var(--glass-inset);
        }
        @media (max-width: 860px) {
            .hero-copy { width: min(100%, 680px); max-width: 680px; }
            .hero-photo { width: calc(100% - (2 * var(--pad))); }
        }
        @media (max-width: 600px) {
            .hero-copy { padding: 20px; border-radius: 22px; }
            .hero-photo { border-radius: 20px; }
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .hero-copy { background: var(--surface); }
            .card { background: var(--surface); }
            .topbar { background: var(--nav-bg); }
        }
    </style>
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<header class="topbar">
    <a href="#home" class="brand" aria-label="{{ $name }} - home">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 21H2z"/></svg>
        <span>{{ $name }}</span>
    </a>
    <div class="topbar-actions">
        <nav class="menu" aria-label="Sections">
            @foreach($nav as [$id, $text])
                <a href="#{{ $id }}" data-target="{{ $id }}" @if($loop->first) class="active" aria-current="location" @endif>{{ $text }}</a>
            @endforeach
        </nav>
        @include('portfolio.partials.theme-toggle')
    </div>
</header>

<main id="main-content">
    {{-- ===== Home / hero (About + Skills live here, as in the reference) ===== --}}
    <section class="hero" id="home">
        <div class="hero-atmosphere {{ $photo === '' ? 'no-img' : '' }}" aria-hidden="true">
            @if($photo !== '')
                <img src="{{ $photo }}" alt="">
            @endif
        </div>
        <div class="hero-photo {{ $photo === '' ? 'no-img' : '' }}" aria-hidden="true">
            @if($photo !== '')
                <img src="{{ $photo }}" alt="">
            @else
                <b>{{ $initials }}</b>
            @endif
            <div class="stripe s1"></div>
            <div class="stripe s2"></div>
        </div>

        <div class="hero-inner">
            <div class="hero-copy">
                <p class="eyebrow">Hello, I'm</p>
                <h1>{{ $name }}</h1>
                @if(!empty($info->headline))
                    <p class="where" style="color:var(--accent); font-weight:500; margin-bottom:2px;">{{ $info->headline }}</p>
                @endif
                @if($location !== '')
                    <p class="where">{{ $location }}</p>
                @endif

                @if($email !== '' || $phone !== '')
                <div class="contact-row">
                    @if($email !== '')
                        <a href="mailto:{{ $email }}">{!! $icon('mail') !!}{{ $email }}</a>
                    @endif
                    @if($email !== '' && $phone !== '')<i class="sep" aria-hidden="true"></i>@endif
                    @if($phone !== '')
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{!! $icon('phone') !!}{{ $phone }}</a>
                    @endif
                </div>
                @endif

                <div class="short-rule"></div>

                <div id="about">
                    @if($bio !== '')
                    <div class="block">
                        <p class="label">About</p>
                        <p class="about-text">{{ $bio }}</p>
                    </div>
                    @endif

                    @if($skillList->count())
                    <div class="block">
                        <p class="label">Skills</p>
                        <div class="pills">
                            @foreach($skillList as $skill)
                                <span class="pill">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Projects ===== --}}
    @if($hasProjects)
    <section class="page" id="projects">
        <div class="wrap">
            <div class="sec-head"><p class="label">Selected work</p><h2>Projects</h2></div>
            <div class="cards">
                @foreach($projects as $project)
                <article class="card">
                    @if(!empty($project->screenshot_url))
                        <img src="{{ $project->screenshot_url }}" alt="Screenshot of {{ $project->title }}" loading="lazy">
                    @else
                        <div class="ph" aria-hidden="true"><span>{{ mb_strtoupper(mb_substr(trim((string) $project->title), 0, 2)) }}</span></div>
                    @endif
                    <div class="card-body">
                        <h3>{{ $project->title }}</h3>
                        @if(!empty($project->description))<p>{{ $project->description }}</p>@endif
                        @if(!empty($project->live_url) || !empty($project->repo_url))
                        <div class="card-links">
                            @if(!empty($project->live_url))
                                <a href="{{ $project->live_url }}" target="_blank" rel="noopener">Live site {!! $icon('arrow', 14) !!}</a>
                            @endif
                            @if(!empty($project->repo_url))
                                <a href="{{ $project->repo_url }}" target="_blank" rel="noopener">Source {!! $icon('arrow', 14) !!}</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== Experience + Education ===== --}}
    @if($hasExp || $hasEdu)
    <section class="page" id="background">
        <div class="wrap two-col">
            @if($hasExp)
            <div>
                <div class="sec-head"><p class="label">Where I've worked</p><h2>Experience</h2></div>
                <div class="timeline">
                    @foreach($experiences as $exp)
                    <div class="t-item">
                        <p class="t-date">
                            {{ $fmtDate($exp->start_date) }}@if(trim((string) $exp->start_date) !== '') &ndash; @endif{{ trim((string) $exp->end_date) !== '' ? $fmtDate($exp->end_date) : 'Present' }}
                        </p>
                        <p class="t-title">{{ $exp->role ?: 'Role' }}@if($exp->is_internship)<span class="tag">Internship</span>@endif</p>
                        <p class="t-sub">{{ $exp->company }}</p>
                        @if(!empty($exp->description))<p class="t-body">{{ $exp->description }}</p>@endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($hasEdu)
            <div>
                <div class="sec-head"><p class="label">Where I studied</p><h2>Education</h2></div>
                <div class="timeline">
                    @foreach($education as $edu)
                    <div class="t-item">
                        <p class="t-date">
                            {{ $edu->start_year }}@if($edu->start_year) &ndash; @endif{{ $edu->end_year ?: 'Present' }}
                        </p>
                        <p class="t-title">{{ $edu->institution }}</p>
                        @if(!empty($edu->degree) || !empty($edu->field))
                            <p class="t-sub">{{ $edu->degree }}@if(!empty($edu->degree) && !empty($edu->field)), @endif{{ $edu->field }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ===== Contact ===== --}}
    <section class="page" id="contact">
        <div class="wrap">
            <div class="sec-head"><p class="label">Get in touch</p><h2>Contact</h2></div>
            <div class="contact-list">
                @if($email !== '')<a href="mailto:{{ $email }}">{!! $icon('mail', 18) !!}{{ $email }}</a>@endif
                @if($phone !== '')<a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{!! $icon('phone', 18) !!}{{ $phone }}</a>@endif
                @if($location !== '')<span>{!! $icon('pin', 18) !!}{{ $location }}</span>@endif
            </div>
            @if($hasLinks)
            <div class="social">
                @foreach($links as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener">{{ ucfirst($link->platform) }}</a>
                @endforeach
            </div>
            @endif
        </div>
    </section>
</main>

<footer>
    @if($email !== '')<a href="mailto:{{ $email }}">{!! $icon('mail', 14) !!}{{ $email }}</a>@else<span></span>@endif
    <span>&copy; {{ date('Y') }} {{ $name }}</span>
</footer>

@include('portfolio.partials.theme-toggle-script')
<script>
(function () {
    // Highlight the nav link of the section currently in view
    var links = document.querySelectorAll('.menu a[data-target]');
    var map = {};
    links.forEach(function (a) { map[a.dataset.target] = a; });

    var targets = Object.keys(map)
        .map(function (id) { return document.getElementById(id); })
        .filter(Boolean);

    function setActive(id) {
        links.forEach(function (a) {
            var isCurrent = a.dataset.target === id;
            a.classList.toggle('active', isCurrent);
            if (isCurrent) { a.setAttribute('aria-current', 'location'); }
            else { a.removeAttribute('aria-current'); }
        });
    }

    function onScroll() {
        var previewOffset = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--pf-preview-bar-height')) || 0;
        var headerOffset = document.querySelector('.topbar').getBoundingClientRect().height;
        var y = window.scrollY + previewOffset + headerOffset + 24, current = targets[0] ? targets[0].id : 'home';
        targets.forEach(function (t) { if (t.getBoundingClientRect().top + window.scrollY <= y) { current = t.id; } });
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 4 && targets.length) {
            current = targets[targets.length - 1].id;
        }
        setActive(current);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    onScroll();
})();
</script>
</body>
</html>
