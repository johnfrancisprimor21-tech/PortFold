@extends('layouts.app')
@section('title', 'Create Portfolio · Portfold')
@section('container_class', 'editor-shell')

@section('styles')
<style>
    .editor-shell {
        --editor-ink: #f4f4ef;
        --editor-muted: #9ba6a5;
        --editor-line: rgba(195, 213, 208, .12);
        --editor-panel: rgba(20, 29, 30, .88);
        --editor-panel-soft: #172122;
        --editor-accent: #9ce1c0;
        --editor-accent-ink: #10221b;
        max-width: 1320px !important;
        padding: 42px clamp(18px, 4vw, 54px) 110px !important;
    }

    .portfolio-editor { color: var(--editor-ink); }
    .editor-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 28px;
        margin: 6px 0 34px;
    }
    .editor-heading-copy { max-width: 680px; }
    .editor-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px;
        color: var(--editor-accent);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .17em;
        text-transform: uppercase;
    }
    .editor-eyebrow::before {
        content: "";
        width: 22px;
        height: 1px;
        background: currentColor;
        opacity: .85;
    }
    .editor-title {
        margin: 0;
        color: var(--editor-ink);
        font-size: clamp(34px, 5vw, 58px);
        font-weight: 620;
        letter-spacing: -.055em;
        line-height: 1.04;
    }
    .editor-title span { color: var(--editor-accent); }
    .editor-intro {
        max-width: 540px;
        margin: 15px 0 0;
        color: var(--editor-muted);
        font-size: 16px;
        line-height: 1.7;
    }
    .editor-preview-link {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 10px;
        min-height: 46px;
        padding: 0 16px;
        border: 1px solid var(--editor-line);
        border-radius: 8px;
        background: rgba(255,255,255,.025);
        color: var(--editor-ink);
        font-size: 14px;
        font-weight: 550;
        text-decoration: none;
        transition: border-color .2s, background .2s, transform .2s;
    }
    .editor-preview-link:hover {
        transform: translateY(-1px);
        border-color: rgba(156,225,192,.5);
        background: rgba(156,225,192,.07);
    }
    .editor-preview-link svg { width: 17px; height: 17px; color: var(--editor-accent); }

    .editor-layout {
        display: grid;
        grid-template-columns: 218px minmax(0, 1fr);
        align-items: start;
        gap: clamp(24px, 4vw, 54px);
    }
    .editor-sidebar {
        position: sticky;
        top: 88px;
        padding: 18px 10px 14px;
        border: 1px solid var(--editor-line);
        border-radius: 12px;
        background: rgba(18, 26, 27, .7);
        box-shadow: 0 18px 48px rgba(0,0,0,.12);
    }
    .sidebar-label {
        padding: 0 11px;
        color: #738280;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .section-nav { display: grid; gap: 4px; margin-top: 13px; }
    .section-nav a {
        display: grid;
        grid-template-columns: 28px minmax(0, 1fr);
        align-items: center;
        gap: 9px;
        min-height: 43px;
        padding: 6px 10px;
        border-radius: 7px;
        color: var(--editor-muted);
        font-size: 13px;
        text-decoration: none;
        transition: color .2s, background .2s;
    }
    .section-nav a:hover, .section-nav a[aria-current="location"] {
        background: rgba(156,225,192,.085);
        color: var(--editor-ink);
    }
    .section-nav .nav-number {
        color: #75817f;
        font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
        font-size: 11px;
    }
    .section-nav a[aria-current="location"] .nav-number { color: var(--editor-accent); }
    .sidebar-note {
        margin: 18px 10px 1px;
        padding-top: 14px;
        border-top: 1px solid var(--editor-line);
        color: #7d8988;
        font-size: 11px;
        line-height: 1.6;
    }

    .editor-form { min-width: 0; }
    .editor-section {
        scroll-margin-top: 92px;
        margin: 0 0 18px;
        padding: clamp(20px, 3vw, 30px);
        border: 1px solid var(--editor-line);
        border-radius: 12px;
        background: var(--editor-panel);
        box-shadow: 0 22px 64px rgba(0,0,0,.11);
    }
    .section-heading {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr);
        align-items: start;
        gap: 14px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--editor-line);
    }
    .section-index {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(156,225,192,.24);
        border-radius: 9px;
        background: rgba(156,225,192,.065);
        color: var(--editor-accent);
        font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
        font-size: 12px;
        font-weight: 650;
    }
    .section-heading h2 {
        margin: 0;
        color: var(--editor-ink);
        font-size: 19px;
        font-weight: 600;
        letter-spacing: -.02em;
        line-height: 1.35;
    }
    .section-heading p {
        margin: 5px 0 0;
        color: var(--editor-muted);
        font-size: 13px;
        line-height: 1.55;
    }
    .editor-section .form-group { margin-bottom: 18px; }
    .editor-section .form-group:last-child { margin-bottom: 0; }
    .editor-section .form-group > label {
        margin-bottom: 7px;
        color: #d9e0dc;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .025em;
    }
    .editor-section .form-group input[type="text"],
    .editor-section .form-group input[type="email"],
    .editor-section .form-group input[type="url"],
    .editor-section .form-group textarea {
        min-height: 46px;
        padding: 11px 13px;
        border-color: rgba(195,213,208,.15);
        border-radius: 7px;
        background: #11191a;
        color: var(--editor-ink);
        font-size: 14px;
    }
    .editor-section .form-group textarea { min-height: 116px; line-height: 1.65; }
    .editor-section .form-group input::placeholder,
    .editor-section .form-group textarea::placeholder { color: #697574; }
    .editor-section .form-group input:hover,
    .editor-section .form-group textarea:hover { border-color: rgba(195,213,208,.3); }
    .editor-section .form-group input:focus,
    .editor-section .form-group textarea:focus {
        border-color: rgba(156,225,192,.72);
        outline: 3px solid rgba(156,225,192,.12);
        outline-offset: 0;
    }
    .editor-section .form-row { gap: 14px; }
    .editor-section .form-help {
        display: block;
        margin-top: 8px;
        color: #83908e;
        font-size: 12px;
        line-height: 1.55;
    }
    .editor-section .field-error { color: #ffaaa7; }
    .required-mark { color: var(--editor-accent); }

    .photo-card {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 22px;
        padding: 16px;
        border: 1px solid var(--editor-line);
        border-radius: 9px;
        background: linear-gradient(115deg, rgba(156,225,192,.055), rgba(255,255,255,.012));
    }
    .photo-preview-box {
        display: grid;
        flex: 0 0 auto;
        place-items: center;
        width: 86px;
        height: 86px;
        overflow: hidden;
        border: 1px solid rgba(195,213,208,.2);
        border-radius: 9px;
        background: #101818;
        color: #7d8988;
        font-size: 11px;
        text-align: center;
    }
    .photo-preview-box img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .photo-preview-box svg { width: 24px; height: 24px; color: #8ca19a; }
    .photo-upload-controls { min-width: 0; flex: 1; }
    .photo-upload-controls > label {
        display: block;
        margin-bottom: 8px;
        color: #e2e8e4;
        font-size: 13px;
        font-weight: 600;
    }
    .photo-upload-controls input[type="file"], .editor-section input[type="file"] {
        max-width: 100%;
        color: var(--editor-muted);
        font: inherit;
        font-size: 12px;
    }
    .photo-upload-controls input[type="file"]::file-selector-button,
    .editor-section input[type="file"]::file-selector-button {
        min-height: 36px;
        margin-right: 10px;
        padding: 7px 11px;
        border: 1px solid rgba(195,213,208,.2);
        border-radius: 6px;
        background: #202b2c;
        color: #edf2ee;
        font: inherit;
        font-size: 12px;
        cursor: pointer;
    }
    .photo-upload-controls input[type="file"]::file-selector-button:hover,
    .editor-section input[type="file"]::file-selector-button:hover { border-color: var(--editor-accent); }

    .repeatable-item {
        position: relative;
        margin: 0 0 12px;
        padding: 19px 18px 2px;
        border: 1px solid var(--editor-line);
        border-radius: 9px;
        background: rgba(7,13,14,.28);
    }
    .repeatable-item > .form-group:nth-child(2),
    .repeatable-item > .form-row:first-of-type { padding-right: 38px; }
    .repeatable-item .form-row:last-of-type .form-group { min-width: 0; }
    .remove-btn {
        position: absolute;
        z-index: 1;
        top: 10px;
        right: 10px;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border: 1px solid transparent;
        border-radius: 6px;
        background: transparent;
        color: #899391;
        cursor: pointer;
        transition: color .2s, border-color .2s, background .2s;
    }
    .remove-btn svg { width: 15px; height: 15px; }
    .remove-btn:hover { border-color: rgba(255,142,137,.2); background: rgba(255,142,137,.08); color: #ffaaa7; }
    .form-group.check { display: flex; align-items: center; gap: 10px; }
    .form-group.check input { width: 17px; height: 17px; margin: 0; accent-color: var(--editor-accent); }
    .form-group.check label { margin: 0; color: var(--editor-muted); cursor: pointer; font-size: 13px; }
    .screenshot-preview-box {
        display: grid;
        place-items: center;
        width: min(100%, 280px);
        aspect-ratio: 16 / 9;
        overflow: hidden;
        margin-bottom: 10px;
        border: 1px solid var(--editor-line);
        border-radius: 7px;
        background: #101718;
        color: #87918f;
        font-size: 12px;
    }
    .screenshot-preview-box img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .screenshot-preview-box svg { width: 27px; height: 27px; color: #7d908a; }
    .add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 44px;
        margin-top: 2px;
        border: 1px dashed rgba(195,213,208,.24);
        border-radius: 7px;
        background: transparent;
        color: #bdc9c5;
        font: inherit;
        font-size: 13px;
        font-weight: 550;
        cursor: pointer;
        transition: color .2s, border-color .2s, background .2s;
    }
    .add-btn svg { width: 15px; height: 15px; color: var(--editor-accent); }
    .add-btn:hover { border-color: rgba(156,225,192,.62); background: rgba(156,225,192,.045); color: #fff; }

    .editor-actions {
        position: sticky;
        bottom: 0;
        z-index: 8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin: 14px 0 0;
        padding: 15px 18px;
        border: 1px solid rgba(195,213,208,.15);
        border-radius: 10px;
        background: rgba(12,18,19,.93);
        box-shadow: 0 16px 40px rgba(0,0,0,.3);
        backdrop-filter: blur(14px);
    }
    .action-note {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--editor-muted);
        font-size: 12px;
    }
    .action-note svg { width: 17px; height: 17px; flex: 0 0 auto; color: var(--editor-accent); }
    .action-buttons { display: flex; align-items: center; gap: 9px; }
    .editor-actions .btn { min-height: 42px; padding: 9px 16px; border-radius: 7px; font-size: 13px; }
    .editor-actions .btn-secondary { border-color: rgba(195,213,208,.18); background: #1a2425; color: #d4ddda; }
    .editor-actions .btn-primary {
        gap: 9px;
        border-color: var(--editor-accent);
        background: var(--editor-accent);
        color: var(--editor-accent-ink);
        font-weight: 700;
    }
    .editor-actions .btn-primary:hover { border-color: #b2efd0; background: #b2efd0; }
    .editor-actions .btn-primary svg { width: 16px; height: 16px; }

    @media (max-width: 860px) {
        .editor-heading { align-items: flex-start; }
        .editor-layout { grid-template-columns: 1fr; gap: 18px; }
        .editor-sidebar { position: static; padding: 13px; }
        .sidebar-label, .sidebar-note { display: none; }
        .section-nav { display: flex; overflow-x: auto; gap: 5px; margin: 0; padding-bottom: 1px; }
        .section-nav a { flex: 0 0 auto; grid-template-columns: auto auto; min-height: 40px; padding: 6px 10px; }
    }
    @media (max-width: 600px) {
        .editor-shell { padding-top: 26px !important; }
        .editor-heading { display: grid; gap: 20px; margin-bottom: 24px; }
        .editor-title { font-size: 39px; }
        .editor-preview-link { justify-self: start; }
        .section-nav a { grid-template-columns: 1fr; text-align: center; }
        .section-nav .nav-number { display: none; }
        .editor-section { padding: 19px 15px; }
        .section-heading { grid-template-columns: 35px minmax(0, 1fr); gap: 11px; margin-bottom: 19px; padding-bottom: 16px; }
        .section-index { width: 34px; height: 34px; }
        .editor-section .form-row { grid-template-columns: 1fr; gap: 0; }
        .repeatable-item { padding: 17px 12px 1px; }
        .repeatable-item .form-row:first-of-type { padding-right: 38px; }
        .photo-card { align-items: flex-start; gap: 12px; padding: 12px; }
        .photo-preview-box { width: 68px; height: 68px; }
        .editor-actions { align-items: stretch; gap: 12px; padding: 12px; }
        .action-note { display: none; }
        .action-buttons { width: 100%; }
        .action-buttons .btn { flex: 1; }
    }
    .editor-form :focus-visible,
    .section-nav a:focus-visible,
    .editor-preview-link:focus-visible,
    .editor-actions .btn:focus-visible,
    .add-btn:focus-visible,
    .remove-btn:focus-visible {
        outline: 3px solid rgba(156,225,192,.75);
        outline-offset: 3px;
    }
    .error-summary {
        margin: 0 0 18px;
        padding: 16px 18px;
        border: 1px solid rgba(255,142,137,.42);
        border-radius: 9px;
        background: rgba(96,31,31,.2);
        color: #ffd8d5;
    }
    .error-summary h2 { margin: 0 0 6px; font-size: 15px; }
    .error-summary p { margin: 0; color: #e8b7b3; font-size: 13px; }
    .field-error { display: block; margin-top: 6px; font-size: 12px; }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    .editor-section [aria-invalid="true"] { border-color: #ff8e89 !important; }
    .editor-section input[type="checkbox"]:focus-visible { outline: 3px solid rgba(156,225,192,.75); outline-offset: 3px; }
    .editor-actions .btn-primary svg { flex: 0 0 auto; }
    .editor-section .phone-field {
        min-width: 0;
        margin: 0 0 18px;
        padding: 0;
        border: 0;
    }
    .editor-section .phone-field legend {
        margin-bottom: 7px;
        color: #d9e0dc;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .025em;
    }
    .phone-control {
        display: grid;
        grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
        gap: 9px;
    }
    .phone-control select,
    .phone-control input {
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        min-height: 46px;
        padding: 10px 11px;
        border: 1px solid rgba(195,213,208,.15);
        border-radius: 7px;
        background: #11191a;
        color: var(--editor-ink);
        font: inherit;
        font-size: 13px;
    }
    .phone-control select { cursor: pointer; }
    .phone-control select:focus,
    .phone-control input:focus {
        border-color: rgba(156,225,192,.72);
        outline: 3px solid rgba(156,225,192,.12);
        outline-offset: 0;
    }
    @media (max-width: 600px) {
        .phone-control { grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: 7px; }
        .phone-control select { padding-right: 7px; padding-left: 8px; font-size: 12px; }
    }
</style>
@endsection

@section('content')
@php
    $educationRows = old('education', [[]]);
    $experienceRows = old('experiences', [[]]);
    $projectRows = old('projects', [[]]);
    $educationRows = is_array($educationRows) ? $educationRows : [[]];
    $experienceRows = is_array($experienceRows) ? $experienceRows : [[]];
    $projectRows = is_array($projectRows) ? $projectRows : [[]];
    $educationNextIndex = count($educationRows) ? max(array_keys($educationRows)) + 1 : 0;
    $experienceNextIndex = count($experienceRows) ? max(array_keys($experienceRows)) + 1 : 0;
    $projectNextIndex = count($projectRows) ? max(array_keys($projectRows)) + 1 : 0;
@endphp

<div class="portfolio-editor">
    <header class="editor-heading">
        <div class="editor-heading-copy">
            <div class="editor-eyebrow">Your workspace / New portfolio</div>
            <h1 class="editor-title">Build your portfolio<span>.</span></h1>
            <p class="editor-intro">Add the details you want to share. Your work is saved as a draft first, then you can choose a template.</p>
        </div>
    </header>

    <div class="editor-layout">
        <aside class="editor-sidebar" aria-label="Create portfolio navigation">
            <div class="sidebar-label">Your sections</div>
            <nav class="section-nav" aria-label="Portfolio sections">
                <a href="#personal-information" aria-current="location"><span class="nav-number">01</span><span>Personal</span></a>
                <a href="#social-links"><span class="nav-number">02</span><span>Links</span></a>
                <a href="#skills"><span class="nav-number">03</span><span>Skills</span></a>
                <a href="#education"><span class="nav-number">04</span><span>Education</span></a>
                <a href="#experience"><span class="nav-number">05</span><span>Experience</span></a>
                <a href="#projects"><span class="nav-number">06</span><span>Projects</span></a>
            </nav>
            <p class="sidebar-note">Complete the sections that matter to you. Education, experience, links and projects are optional.</p>
        </aside>

        <form class="editor-form" action="{{ route('portfolio.store') }}" method="POST" enctype="multipart/form-data" data-warn-unsaved data-loading>
            @csrf

            @if($errors->any())
                <div class="error-summary" id="error-summary" role="alert" tabindex="-1" aria-labelledby="error-summary-title">
                    <h2 id="error-summary-title">A few details need your attention</h2>
                    <p>Review the highlighted information below, then save again.</p>
                </div>
            @endif
            <p class="sr-only" id="form-status" aria-live="polite" aria-atomic="true"></p>

            <section class="editor-section" id="personal-information" aria-labelledby="personal-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">01</span>
                    <div><h2 id="personal-heading">Personal information</h2><p>Introduce yourself and make it easy for people to get in touch.</p></div>
                </header>

                <div class="photo-card">
                    <div class="photo-preview-box" id="photo-preview-box" aria-label="Profile photo preview">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.2"/><path d="M5.5 20c.5-3.2 2.8-5 6.5-5s6 1.8 6.5 5"/></svg>
                    </div>
                    <div class="photo-upload-controls">
                        <label for="profile_photo">Profile photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" aria-describedby="profile-photo-help" onchange="previewPhoto(this)">
                        <span class="form-help" id="profile-photo-help">JPG, PNG or WebP · Up to 4 MB · This can appear on your portfolio.</span>
                        @error('profile_photo')
                            <span class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full name <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autocomplete="name" placeholder="Juan Dela Cruz" @if($errors->has('full_name')) aria-invalid="true" aria-describedby="full-name-error" @endif>
                        @error('full_name')<span class="field-error" id="full-name-error" role="alert">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="headline">Headline</label>
                        <input type="text" id="headline" name="headline" value="{{ old('headline') }}" placeholder="e.g. Full-stack developer">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_email">Email <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required autocomplete="email" placeholder="juan@email.com" @if($errors->has('contact_email')) aria-invalid="true" aria-describedby="contact-email-error" @endif>
                        @error('contact_email')<span class="field-error" id="contact-email-error" role="alert">{{ $message }}</span>@enderror
                    </div>
                                        @include('portfolio.partials.phone-field')
                </div>
                <div class="form-group">
                    <label for="location">Address or location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" autocomplete="address-level2" placeholder="Cebu City, Philippines">
                </div>
                <div class="form-group">
                    <label for="bio">About me</label>
                    <textarea id="bio" name="bio" maxlength="500" placeholder="Write a short introduction about yourself...">{{ old('bio') }}</textarea>
                    <span class="form-help">A short introduction works best · Up to 500 characters.</span>
                </div>
            </section>

            <section class="editor-section" id="social-links" aria-labelledby="links-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">02</span>
                    <div><h2 id="links-heading">Social links</h2><p>Give visitors a few ways to find your work and connect with you.</p></div>
                </header>
                <div class="form-row">
                    <div class="form-group"><label for="link-github">GitHub</label><input type="url" id="link-github" name="links[github]" value="{{ old('links.github') }}" placeholder="https://github.com/username"></div>
                    <div class="form-group"><label for="link-linkedin">LinkedIn</label><input type="url" id="link-linkedin" name="links[linkedin]" value="{{ old('links.linkedin') }}" placeholder="https://linkedin.com/in/username"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label for="link-website">Personal website</label><input type="url" id="link-website" name="links[website]" value="{{ old('links.website') }}" placeholder="https://yoursite.com"></div>
                    <div class="form-group"><label for="link-facebook">Facebook</label><input type="url" id="link-facebook" name="links[facebook]" value="{{ old('links.facebook') }}" placeholder="https://facebook.com/username"></div>
                </div>
                <span class="form-help">Use the full link, including https://. You can leave any of these blank.</span>
            </section>

            <section class="editor-section" id="skills" aria-labelledby="skills-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">03</span>
                    <div><h2 id="skills-heading">Skills</h2><p>List the tools and strengths you want people to notice first.</p></div>
                </header>
                <div class="form-group">
                    <label for="skills-list">Your skills</label>
                    <input type="text" id="skills-list" name="skills" value="{{ old('skills') }}" placeholder="HTML, CSS, JavaScript, Laravel, PHP">
                    <span class="form-help">Separate each skill with a comma.</span>
                </div>
            </section>

            <section class="editor-section" id="education" aria-labelledby="education-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">04</span>
                    <div><h2 id="education-heading">Education</h2><p>Add schools, programs or courses that shaped your path. This section is optional.</p></div>
                </header>
                <div id="education-list">
                    @foreach($educationRows as $i => $edu)
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove education entry {{ $loop->iteration }}" title="Remove education entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-row">
                            <div class="form-group"><label for="education-{{ $i }}-institution">School or university</label><input type="text" id="education-{{ $i }}-institution" name="education[{{ $i }}][institution]" value="{{ $edu['institution'] ?? '' }}" placeholder="University of Cebu"></div>
                            <div class="form-group"><label for="education-{{ $i }}-degree">Degree</label><input type="text" id="education-{{ $i }}-degree" name="education[{{ $i }}][degree]" value="{{ $edu['degree'] ?? '' }}" placeholder="Bachelor of Science"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label for="education-{{ $i }}-field">Field of study</label><input type="text" id="education-{{ $i }}-field" name="education[{{ $i }}][field]" value="{{ $edu['field'] ?? '' }}" placeholder="Information Management"></div>
                            <div class="form-row">
                                <div class="form-group"><label for="education-{{ $i }}-start">Start year</label><input type="text" inputmode="numeric" id="education-{{ $i }}-start" name="education[{{ $i }}][start_year]" value="{{ $edu['start_year'] ?? '' }}" placeholder="2022"></div>
                                <div class="form-group"><label for="education-{{ $i }}-end">End year</label><input type="text" inputmode="numeric" id="education-{{ $i }}-end" name="education[{{ $i }}][end_year]" value="{{ $edu['end_year'] ?? '' }}" placeholder="2026"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" id="add-education" onclick="addEducation()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add education</button>
            </section>

            <section class="editor-section" id="experience" aria-labelledby="experience-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">05</span>
                    <div><h2 id="experience-heading">Work experience</h2><p>Show the roles and projects that built your professional story. This section is optional.</p></div>
                </header>
                <div id="experience-list">
                    @foreach($experienceRows as $i => $exp)
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove work experience entry {{ $loop->iteration }}" title="Remove work experience entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-row">
                            <div class="form-group"><label for="experience-{{ $i }}-company">Company</label><input type="text" id="experience-{{ $i }}-company" name="experiences[{{ $i }}][company]" value="{{ $exp['company'] ?? '' }}" placeholder="Company name"></div>
                            <div class="form-group"><label for="experience-{{ $i }}-role">Role or position</label><input type="text" id="experience-{{ $i }}-role" name="experiences[{{ $i }}][role]" value="{{ $exp['role'] ?? '' }}" placeholder="Web developer"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label for="experience-{{ $i }}-start">Start date</label><input type="text" id="experience-{{ $i }}-start" name="experiences[{{ $i }}][start_date]" value="{{ $exp['start_date'] ?? '' }}" placeholder="Jun 2023"></div>
                            <div class="form-group"><label for="experience-{{ $i }}-end">End date</label><input type="text" id="experience-{{ $i }}-end" name="experiences[{{ $i }}][end_date]" value="{{ $exp['end_date'] ?? '' }}" placeholder="Present"></div>
                        </div>
                        <div class="form-group"><label for="experience-{{ $i }}-description">Description</label><textarea id="experience-{{ $i }}-description" name="experiences[{{ $i }}][description]" placeholder="Describe your responsibilities...">{{ $exp['description'] ?? '' }}</textarea></div>
                        <div class="form-group check"><input type="checkbox" name="experiences[{{ $i }}][is_internship]" id="intern{{ $i }}" value="1" {{ !empty($exp['is_internship'] ?? null) ? 'checked' : '' }}><label for="intern{{ $i }}">This was an internship</label></div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" id="add-experience" onclick="addExperience()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add experience</button>
            </section>

            <section class="editor-section" id="projects" aria-labelledby="projects-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">06</span>
                    <div><h2 id="projects-heading">Projects</h2><p>Feature work you are proud of and link visitors to the details. This section is optional.</p></div>
                </header>
                <div id="project-list">
                    @foreach($projectRows as $i => $project)
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove project entry {{ $loop->iteration }}" title="Remove project entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-group"><label for="project-{{ $i }}-title">Project title</label><input type="text" id="project-{{ $i }}-title" name="projects[{{ $i }}][title]" value="{{ $project['title'] ?? '' }}" placeholder="My awesome project"></div>
                        <div class="form-group"><label for="project-{{ $i }}-description">Description</label><textarea id="project-{{ $i }}-description" name="projects[{{ $i }}][description]" placeholder="What does this project do?">{{ $project['description'] ?? '' }}</textarea></div>
                        <div class="form-row">
                            <div class="form-group"><label for="project-{{ $i }}-live">Live URL</label><input type="url" id="project-{{ $i }}-live" name="projects[{{ $i }}][live_url]" value="{{ $project['live_url'] ?? '' }}" placeholder="https://myproject.com"></div>
                            <div class="form-group"><label for="project-{{ $i }}-repo">GitHub or repo URL</label><input type="url" id="project-{{ $i }}-repo" name="projects[{{ $i }}][repo_url]" value="{{ $project['repo_url'] ?? '' }}" placeholder="https://github.com/me/project"></div>
                        </div>
                        <div class="form-group">
                            <label for="project-{{ $i }}-screenshot">Project screenshot</label>
                            <div class="screenshot-preview-box" id="screenshot-preview-{{ $i }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m5 17 4.5-4 3.5 3 2.5-2 3.5 3"/></svg></div>
                            <input type="file" id="project-{{ $i }}-screenshot" name="projects[{{ $i }}][screenshot]" accept="image/jpeg,image/png,image/webp" aria-describedby="project-{{ $i }}-screenshot-help" onchange="previewScreenshot(this, 'screenshot-preview-{{ $i }}')">
                            <span class="form-help" id="project-{{ $i }}-screenshot-help">JPG, PNG or WebP · Up to 4 MB.</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" id="add-project" onclick="addProject()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add project</button>
            </section>

            <div class="editor-actions">
                <div class="action-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3.5 5 6v5.2c0 4.4 2.8 7.5 7 9.3 4.2-1.8 7-4.9 7-9.3V6l-7-2.5Z"/><path d="m9 12 2 2 4-4"/></svg>
                    After saving, choose a template for your portfolio.
                </div>
                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" data-busy-text="Saving portfolio...">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4.5h12l2.5 2.5v12.5H4.5V4.5H5Z"/><path d="M8 4.5v5h8v-5M8 19.5v-6h8v6"/></svg>
                        Save and choose template
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let eduCount = {{ $educationNextIndex }};
let expCount = {{ $experienceNextIndex }};
let projCount = {{ $projectNextIndex }};

const removeIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>';
const addIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
const screenshotIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m5 17 4.5-4 3.5 3 2.5-2 3.5 3"/></svg>';
const status = document.getElementById('form-status');

function announce(message) {
    if (status) status.textContent = message;
}

function removeItem(button) {
    const item = button.closest('.repeatable-item');
    const list = item.parentElement;
    const section = item.closest('.editor-section');
    const next = item.nextElementSibling || item.previousElementSibling;
    item.remove();
    const focusTarget = next?.querySelector('input:not([type="file"]), textarea, button')
        || section.querySelector('.add-btn');
    if (focusTarget) focusTarget.focus();
    announce('Entry removed.');
}

function showImagePreview(input, box) {
    if (!input.files || !input.files[0] || !box) return;
    const reader = new FileReader();
    reader.onload = event => {
        const image = document.createElement('img');
        image.src = event.target.result;
        image.alt = 'Selected image preview';
        box.replaceChildren(image);
    };
    reader.readAsDataURL(input.files[0]);
}

function previewPhoto(input) {
    showImagePreview(input, document.getElementById('photo-preview-box'));
}

function previewScreenshot(input, previewId) {
    showImagePreview(input, document.getElementById(previewId));
}

function addEntry(listId, markup, firstField, label) {
    const list = document.getElementById(listId);
    list.insertAdjacentHTML('beforeend', markup);
    list.lastElementChild.querySelector(firstField)?.focus();
    announce(`${label} entry added.`);
}

function addEducation() {
    const i = eduCount++;
    addEntry('education-list', `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove education entry" title="Remove education entry">${removeIcon}</button>
            <div class="form-row">
                <div class="form-group"><label for="education-${i}-institution">School or university</label><input type="text" id="education-${i}-institution" name="education[${i}][institution]" placeholder="University of Cebu"></div>
                <div class="form-group"><label for="education-${i}-degree">Degree</label><input type="text" id="education-${i}-degree" name="education[${i}][degree]" placeholder="Bachelor of Science"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="education-${i}-field">Field of study</label><input type="text" id="education-${i}-field" name="education[${i}][field]" placeholder="Information Management"></div>
                <div class="form-row">
                    <div class="form-group"><label for="education-${i}-start">Start year</label><input type="text" inputmode="numeric" id="education-${i}-start" name="education[${i}][start_year]" placeholder="2022"></div>
                    <div class="form-group"><label for="education-${i}-end">End year</label><input type="text" inputmode="numeric" id="education-${i}-end" name="education[${i}][end_year]" placeholder="2026"></div>
                </div>
            </div>
        </div>`, 'input', 'Education');
}

function addExperience() {
    const i = expCount++;
    addEntry('experience-list', `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove work experience entry" title="Remove work experience entry">${removeIcon}</button>
            <div class="form-row">
                <div class="form-group"><label for="experience-${i}-company">Company</label><input type="text" id="experience-${i}-company" name="experiences[${i}][company]" placeholder="Company name"></div>
                <div class="form-group"><label for="experience-${i}-role">Role or position</label><input type="text" id="experience-${i}-role" name="experiences[${i}][role]" placeholder="Web developer"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="experience-${i}-start">Start date</label><input type="text" id="experience-${i}-start" name="experiences[${i}][start_date]" placeholder="Jun 2023"></div>
                <div class="form-group"><label for="experience-${i}-end">End date</label><input type="text" id="experience-${i}-end" name="experiences[${i}][end_date]" placeholder="Present"></div>
            </div>
            <div class="form-group"><label for="experience-${i}-description">Description</label><textarea id="experience-${i}-description" name="experiences[${i}][description]" placeholder="Describe your responsibilities..."></textarea></div>
            <div class="form-group check"><input type="checkbox" name="experiences[${i}][is_internship]" id="intern${i}" value="1"><label for="intern${i}">This was an internship</label></div>
        </div>`, 'input', 'Work experience');
}

function addProject() {
    const i = projCount++;
    const previewId = `screenshot-preview-${i}`;
    addEntry('project-list', `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove project entry" title="Remove project entry">${removeIcon}</button>
            <div class="form-group"><label for="project-${i}-title">Project title</label><input type="text" id="project-${i}-title" name="projects[${i}][title]" placeholder="My awesome project"></div>
            <div class="form-group"><label for="project-${i}-description">Description</label><textarea id="project-${i}-description" name="projects[${i}][description]" placeholder="What does this project do?"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label for="project-${i}-live">Live URL</label><input type="url" id="project-${i}-live" name="projects[${i}][live_url]" placeholder="https://myproject.com"></div>
                <div class="form-group"><label for="project-${i}-repo">GitHub or repo URL</label><input type="url" id="project-${i}-repo" name="projects[${i}][repo_url]" placeholder="https://github.com/me/project"></div>
            </div>
            <div class="form-group">
                <label for="project-${i}-screenshot">Project screenshot</label>
                <div class="screenshot-preview-box" id="${previewId}">${screenshotIcon}</div>
                <input type="file" id="project-${i}-screenshot" name="projects[${i}][screenshot]" accept="image/jpeg,image/png,image/webp" aria-describedby="project-${i}-screenshot-help" onchange="previewScreenshot(this, '${previewId}')">
                <span class="form-help" id="project-${i}-screenshot-help">JPG, PNG or WebP · Up to 4 MB.</span>
            </div>
        </div>`, 'input', 'Project');
}

const editorSections = document.querySelectorAll('.editor-section');
const sectionLinks = document.querySelectorAll('.section-nav a');
if ('IntersectionObserver' in window) {
    const sectionObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            sectionLinks.forEach(link => {
                if (link.getAttribute('href') === `#${entry.target.id}`) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
        });
    }, { rootMargin: '-18% 0px -68% 0px' });
    editorSections.forEach(section => sectionObserver.observe(section));
}
</script>
@endsection


