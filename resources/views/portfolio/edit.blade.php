@extends('layouts.app')
@section('title', 'Edit Portfolio · Portfold')
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
    .editor-heading-actions {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        gap: 10px;
    }
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
    .add-btn:disabled { opacity: .55; cursor: not-allowed; }

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
        .editor-heading-actions { justify-self: start; }
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
    }    .editor-section .phone-field {
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
    $linkMap = $links->pluck('url', 'platform');
    $submittedProjects = old('projects');
    if (is_array($submittedProjects)) {
        $projectFormRows = collect($submittedProjects)->map(function ($submittedProject, $index) use ($projects) {
            $savedProject = $projects->firstWhere('display_order', (int) $index);
            $values = is_array($submittedProject) ? $submittedProject : [];

            return (object) array_merge((array) $savedProject, $values, ['display_order' => (int) $index]);
        });
        if ($projectFormRows->isEmpty()) {
            $projectFormRows = collect([(object) []]);
        }
    } else {
        $projectFormRows = $projects->count() ? $projects : collect([(object) []]);
    }
@endphp

<div class="portfolio-editor">
    <header class="editor-heading">
        <div class="editor-heading-copy">
            <div class="editor-eyebrow">Your workspace / Portfolio</div>
            <h1 class="editor-title">Refine your story<span>.</span></h1>
            <p class="editor-intro">Keep your work and experience in one place. Update any section, then save when you are ready.</p>
        </div>
        <div class="editor-heading-actions" aria-label="Portfolio actions">
            <a href="{{ route('portfolio.export', $portfolio->id) }}" class="editor-preview-link" aria-label="Download portfolio data as JSON">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 14.5v4A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5v-4"/></svg>
                Export JSON
            </a>
            <a href="{{ route('portfolio.export.html', $portfolio->id) }}" class="editor-preview-link" aria-label="Download portfolio as HTML">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3.5h8l4 4v13H6z"/><path d="M14 3.5v4h4M9 12h6M9 15h6M9 18h4"/></svg>
                Export HTML
            </a>
            <a href="{{ route('portfolio.preview', $portfolio->id) }}" class="editor-preview-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.7"/></svg>
                Preview portfolio
            </a>
        </div>
    </header>

    <form id="portfolio-edit-form" class="editor-form" action="{{ route('portfolio.update', $portfolio->id) }}" method="POST" enctype="multipart/form-data" data-loading data-warn-unsaved>
        @csrf
        @method('PUT')
                <div class="photo-card">
                    <div class="photo-preview-box" id="photo-preview-box">
                        @if(!empty($info->photo_url))
                            <img src="{{ $info->photo_url }}" alt="Current profile photo">
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.2"/><path d="M5.5 20c.5-3.2 2.8-5 6.5-5s6 1.8 6.5 5"/></svg>
                        @endif
                    </div>
                    <div class="photo-upload-controls">
                        <label for="profile_photo">Profile photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" onchange="previewPhoto(this)">
                        <span class="form-help">JPG, PNG or WebP · Up to 4 MB · Leave empty to keep the current photo.</span>
                        @error('profile_photo')
                            <span class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full name <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $info->full_name ?? '') }}" required autocomplete="name" placeholder="Juan Dela Cruz">
                    </div>
                    <div class="form-group">
                        <label for="headline">Headline</label>
                        <input type="text" id="headline" name="headline" value="{{ old('headline', $info->headline ?? '') }}" placeholder="e.g. Full-stack developer">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_email">Email <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $info->contact_email ?? '') }}" required autocomplete="email" placeholder="juan@email.com">
                    </div>
                                        @include('portfolio.partials.phone-field')
                </div>
                <div class="form-group">
                    <label for="location">Address or location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $info->location ?? '') }}" autocomplete="address-level2" placeholder="Cebu City, Philippines">
                </div>
                <div class="form-group">
                    <label for="bio">About me</label>
                    <textarea id="bio" name="bio" placeholder="Write a short introduction about yourself...">{{ old('bio', $info->bio ?? '') }}</textarea>
                </div>
            </section>

            <section class="editor-section" id="social-links" aria-labelledby="links-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">02</span>
                    <div><h2 id="links-heading">Social links</h2><p>Give visitors a few ways to find your work and connect with you.</p></div>
                </header>
                <div class="form-row">
                    <div class="form-group">
                        <label for="link-github">GitHub</label>
                        <input type="url" id="link-github" name="links[github]" value="{{ old('links.github', $linkMap['github'] ?? '') }}" placeholder="https://github.com/username">
                    </div>
                    <div class="form-group">
                        <label for="link-linkedin">LinkedIn</label>
                        <input type="url" id="link-linkedin" name="links[linkedin]" value="{{ old('links.linkedin', $linkMap['linkedin'] ?? '') }}" placeholder="https://linkedin.com/in/username">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="link-website">Personal website</label>
                        <input type="url" id="link-website" name="links[website]" value="{{ old('links.website', $linkMap['website'] ?? '') }}" placeholder="https://yoursite.com">
                    </div>
                    <div class="form-group">
                        <label for="link-facebook">Facebook</label>
                        <input type="url" id="link-facebook" name="links[facebook]" value="{{ old('links.facebook', $linkMap['facebook'] ?? '') }}" placeholder="https://facebook.com/username">
                    </div>
                </div>
            </section>

            <section class="editor-section" id="skills" aria-labelledby="skills-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">03</span>
                    <div><h2 id="skills-heading">Skills</h2><p>List the tools and strengths you want people to notice first.</p></div>
                </header>
                <div class="form-group">
                    <label for="skills-list">Your skills</label>
                    <input type="text" id="skills-list" name="skills" value="{{ old('skills', $skills->pluck('name')->join(', ')) }}" placeholder="HTML, CSS, JavaScript, Laravel, PHP">
                    <span class="form-help">Separate each skill with a comma.</span>
                </div>
            </section>

            <section class="editor-section" id="education" aria-labelledby="education-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">04</span>
                    <div><h2 id="education-heading">Education</h2><p>Add the schools, programs or courses that shaped your path.</p></div>
                </header>
                <div id="education-list">
                    @foreach(($education->count() ? $education : collect([(object) []])) as $edu)
                    @php $i = $loop->index; @endphp
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove education entry {{ $loop->iteration }}" title="Remove entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="education-{{ $i }}-institution">School or university</label>
                                <input type="text" id="education-{{ $i }}-institution" name="education[{{ $i }}][institution]" value="{{ $edu->institution ?? '' }}" placeholder="University of Cebu">
                            </div>
                            <div class="form-group">
                                <label for="education-{{ $i }}-degree">Degree</label>
                                <input type="text" id="education-{{ $i }}-degree" name="education[{{ $i }}][degree]" value="{{ $edu->degree ?? '' }}" placeholder="Bachelor of Science">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="education-{{ $i }}-field">Field of study</label>
                                <input type="text" id="education-{{ $i }}-field" name="education[{{ $i }}][field]" value="{{ $edu->field ?? '' }}" placeholder="Information Management">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="education-{{ $i }}-start">Start year</label>
                                    <input type="text" id="education-{{ $i }}-start" name="education[{{ $i }}][start_year]" value="{{ $edu->start_year ?? '' }}" placeholder="2022">
                                </div>
                                <div class="form-group">
                                    <label for="education-{{ $i }}-end">End year</label>
                                    <input type="text" id="education-{{ $i }}-end" name="education[{{ $i }}][end_year]" value="{{ $edu->end_year ?? '' }}" placeholder="2026">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" onclick="addEducation()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add education
                </button>
            </section>

            <section class="editor-section" id="experience" aria-labelledby="experience-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">05</span>
                    <div><h2 id="experience-heading">Work experience</h2><p>Show the roles and projects that built your professional story.</p></div>
                </header>
                <div id="experience-list">
                    @foreach(($experiences->count() ? $experiences : collect([(object) []])) as $exp)
                    @php $i = $loop->index; @endphp
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove work experience entry {{ $loop->iteration }}" title="Remove entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="experience-{{ $i }}-company">Company</label>
                                <input type="text" id="experience-{{ $i }}-company" name="experiences[{{ $i }}][company]" value="{{ $exp->company ?? '' }}" placeholder="Company name">
                            </div>
                            <div class="form-group">
                                <label for="experience-{{ $i }}-role">Role or position</label>
                                <input type="text" id="experience-{{ $i }}-role" name="experiences[{{ $i }}][role]" value="{{ $exp->role ?? '' }}" placeholder="Web developer">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="experience-{{ $i }}-start">Start date</label>
                                <input type="text" id="experience-{{ $i }}-start" name="experiences[{{ $i }}][start_date]" value="{{ $exp->start_date ?? '' }}" placeholder="Jun 2023">
                            </div>
                            <div class="form-group">
                                <label for="experience-{{ $i }}-end">End date</label>
                                <input type="text" id="experience-{{ $i }}-end" name="experiences[{{ $i }}][end_date]" value="{{ $exp->end_date ?? '' }}" placeholder="Present">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="experience-{{ $i }}-description">Description</label>
                            <textarea id="experience-{{ $i }}-description" name="experiences[{{ $i }}][description]" placeholder="Describe your responsibilities...">{{ $exp->description ?? '' }}</textarea>
                        </div>
                        <div class="form-group check">
                            <input type="checkbox" name="experiences[{{ $i }}][is_internship]" id="intern{{ $i }}" {{ !empty($exp->is_internship) ? 'checked' : '' }}>
                            <label for="intern{{ $i }}">This was an internship</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" onclick="addExperience()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add experience
                </button>
            </section>

            <section class="editor-section" id="projects" aria-labelledby="projects-heading">
                <header class="section-heading">
                    <span class="section-index" aria-hidden="true">06</span>
                    <div><h2 id="projects-heading">Projects</h2><p>Feature the work you are proud of and link visitors to the details.</p></div>
                </header>
                <div id="project-list">
                    @foreach($projectFormRows as $project)
                    {{-- The index must equal display_order: the controller uses it to keep the old screenshot. --}}
                    @php $i = $project->display_order ?? $loop->index; @endphp
                    <div class="repeatable-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove project entry {{ $loop->iteration }}" title="Remove entry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>
                        </button>
                        <div class="form-group">
                            <label for="project-{{ $i }}-title">Project title</label>
                            <input type="text" id="project-{{ $i }}-title" name="projects[{{ $i }}][title]" value="{{ $project->title ?? '' }}" placeholder="My awesome project" @if($errors->has("projects.$i.title")) aria-invalid="true" aria-describedby="project-{{ $i }}-title-error" @endif>
                            @if($errors->has("projects.$i.title"))
                                <span class="field-error" id="project-{{ $i }}-title-error" role="alert">{{ $errors->first("projects.$i.title") }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="project-{{ $i }}-description">Description</label>
                            <textarea id="project-{{ $i }}-description" name="projects[{{ $i }}][description]" placeholder="What does this project do?" @if($errors->has("projects.$i.description")) aria-invalid="true" aria-describedby="project-{{ $i }}-description-error" @endif>{{ $project->description ?? '' }}</textarea>
                            @if($errors->has("projects.$i.description"))
                                <span class="field-error" id="project-{{ $i }}-description-error" role="alert">{{ $errors->first("projects.$i.description") }}</span>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="project-{{ $i }}-live">Live URL</label>
                                <input type="url" id="project-{{ $i }}-live" name="projects[{{ $i }}][live_url]" value="{{ $project->live_url ?? '' }}" placeholder="https://myproject.com" @if($errors->has("projects.$i.live_url")) aria-invalid="true" aria-describedby="project-{{ $i }}-live-error" @endif>
                                @if($errors->has("projects.$i.live_url"))
                                    <span class="field-error" id="project-{{ $i }}-live-error" role="alert">{{ $errors->first("projects.$i.live_url") }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="project-{{ $i }}-repo">GitHub or repo URL</label>
                                <input type="url" id="project-{{ $i }}-repo" name="projects[{{ $i }}][repo_url]" value="{{ $project->repo_url ?? '' }}" placeholder="https://github.com/me/project" @if($errors->has("projects.$i.repo_url")) aria-invalid="true" aria-describedby="project-{{ $i }}-repo-error" @endif>
                                @if($errors->has("projects.$i.repo_url"))
                                    <span class="field-error" id="project-{{ $i }}-repo-error" role="alert">{{ $errors->first("projects.$i.repo_url") }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="project-{{ $i }}-screenshot">Project screenshot</label>
                            <div class="screenshot-preview-box" id="screenshot-preview-{{ $i }}">
                                @if(!empty($project->screenshot_url))
                                    <img src="{{ $project->screenshot_url }}" alt="Current project screenshot">
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m5 17 4.5-4 3.5 3 2.5-2 3.5 3"/></svg>
                                @endif
                            </div>
                            <input type="file" id="project-{{ $i }}-screenshot" name="projects[{{ $i }}][screenshot]" accept="image/jpeg,image/png,image/webp" onchange="previewScreenshot(this, 'screenshot-preview-{{ $i }}')" @if($errors->has("projects.$i.screenshot")) aria-invalid="true" aria-describedby="project-{{ $i }}-screenshot-error" @endif>
                            <span class="form-help">JPG, PNG or WebP · Up to 4 MB · Leave empty to keep the current screenshot.</span>
                            @if($errors->has("projects.$i.screenshot"))
                                <span class="field-error" id="project-{{ $i }}-screenshot-error" role="alert">{{ $errors->first("projects.$i.screenshot") }} Please choose the screenshot again after correcting it.</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-btn" onclick="addProject()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add project
                </button>
                <span class="form-help" id="project-limit-status" role="status" aria-live="polite"></span>
            </section>

            <div class="editor-actions">
                <div class="action-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3.5 5 6v5.2c0 4.4 2.8 7.5 7 9.3 4.2-1.8 7-4.9 7-9.3V6l-7-2.5Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Your updates will be applied when you save.
                </div>
                <div class="action-buttons">
                    <a href="{{ route('portfolio.preview', $portfolio->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" data-busy-text="Saving changes...">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4.5h12l2.5 2.5v12.5H4.5V4.5H5Z"/><path d="M8 4.5v5h8v-5M8 19.5v-6h8v6"/></svg>
                        Save changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let eduCount = {{ max($education->count(), 1) }};
let expCount = {{ max($experiences->count(), 1) }};
let projCount = {{ max((int) ($projectFormRows->keys()->max() ?? -1) + 1, 1) }};

const removeIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6m4-6v6M6.5 7l.7 13h9.6l.7-13M9 7V4h6v3"/></svg>';
const addIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
const screenshotPlaceholder = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m5 17 4.5-4 3.5 3 2.5-2 3.5 3"/></svg>';

function removeItem(button) {
    const projectList = button.closest('#project-list');
    button.closest('.repeatable-item').remove();
    if (projectList) updateProjectAddControl();
}

function previewPhoto(input) {
    const box = document.getElementById('photo-preview-box');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = event => { box.innerHTML = `<img src="${event.target.result}" alt="Selected profile photo preview">`; };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewScreenshot(input, previewId) {
    const box = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = event => { box.innerHTML = `<img src="${event.target.result}" alt="Selected project screenshot preview">`; };
        reader.readAsDataURL(input.files[0]);
    }
}

function addEducation() {
    const index = eduCount++;
    const item = `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove education entry" title="Remove entry">${removeIcon}</button>
            <div class="form-row">
                <div class="form-group"><label for="education-${index}-institution">School or university</label><input type="text" id="education-${index}-institution" name="education[${index}][institution]" placeholder="University of Cebu"></div>
                <div class="form-group"><label for="education-${index}-degree">Degree</label><input type="text" id="education-${index}-degree" name="education[${index}][degree]" placeholder="Bachelor of Science"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="education-${index}-field">Field of study</label><input type="text" id="education-${index}-field" name="education[${index}][field]" placeholder="Information Management"></div>
                <div class="form-row">
                    <div class="form-group"><label for="education-${index}-start">Start year</label><input type="text" id="education-${index}-start" name="education[${index}][start_year]" placeholder="2022"></div>
                    <div class="form-group"><label for="education-${index}-end">End year</label><input type="text" id="education-${index}-end" name="education[${index}][end_year]" placeholder="2026"></div>
                </div>
            </div>
        </div>`;
    document.getElementById('education-list').insertAdjacentHTML('beforeend', item);
}

function addExperience() {
    const index = expCount++;
    const item = `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove work experience entry" title="Remove entry">${removeIcon}</button>
            <div class="form-row">
                <div class="form-group"><label for="experience-${index}-company">Company</label><input type="text" id="experience-${index}-company" name="experiences[${index}][company]" placeholder="Company name"></div>
                <div class="form-group"><label for="experience-${index}-role">Role or position</label><input type="text" id="experience-${index}-role" name="experiences[${index}][role]" placeholder="Web developer"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="experience-${index}-start">Start date</label><input type="text" id="experience-${index}-start" name="experiences[${index}][start_date]" placeholder="Jun 2023"></div>
                <div class="form-group"><label for="experience-${index}-end">End date</label><input type="text" id="experience-${index}-end" name="experiences[${index}][end_date]" placeholder="Present"></div>
            </div>
            <div class="form-group"><label for="experience-${index}-description">Description</label><textarea id="experience-${index}-description" name="experiences[${index}][description]" placeholder="Describe your responsibilities..."></textarea></div>
            <div class="form-group check"><input type="checkbox" name="experiences[${index}][is_internship]" id="intern${index}"><label for="intern${index}">This was an internship</label></div>
        </div>`;
    document.getElementById('experience-list').insertAdjacentHTML('beforeend', item);
}

function addProject() {
    if (document.querySelectorAll('#project-list .repeatable-item').length >= 8) return;

    const index = projCount++;
    const previewId = `screenshot-preview-${index}`;
    const item = `
        <div class="repeatable-item">
            <button type="button" class="remove-btn" onclick="removeItem(this)" aria-label="Remove project entry" title="Remove entry">${removeIcon}</button>
            <div class="form-group"><label for="project-${index}-title">Project title</label><input type="text" id="project-${index}-title" name="projects[${index}][title]" placeholder="My awesome project"></div>
            <div class="form-group"><label for="project-${index}-description">Description</label><textarea id="project-${index}-description" name="projects[${index}][description]" placeholder="What does this project do?"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label for="project-${index}-live">Live URL</label><input type="url" id="project-${index}-live" name="projects[${index}][live_url]" placeholder="https://myproject.com"></div>
                <div class="form-group"><label for="project-${index}-repo">GitHub or repo URL</label><input type="url" id="project-${index}-repo" name="projects[${index}][repo_url]" placeholder="https://github.com/me/project"></div>
            </div>
            <div class="form-group">
                <label for="project-${index}-screenshot">Project screenshot</label>
                <div class="screenshot-preview-box" id="${previewId}">${screenshotPlaceholder}</div>
                <input type="file" id="project-${index}-screenshot" name="projects[${index}][screenshot]" accept="image/jpeg,image/png,image/webp" onchange="previewScreenshot(this, '${previewId}')">
                <span class="form-help">JPG, PNG or WebP · Up to 4 MB.</span>
            </div>
        </div>`;
    document.getElementById('project-list').insertAdjacentHTML('beforeend', item);
    updateProjectAddControl();
}

function updateProjectAddControl() {
    const addButton = document.querySelector('#projects .add-btn');
    const limitStatus = document.getElementById('project-limit-status');
    const count = document.querySelectorAll('#project-list .repeatable-item').length;
    if (!addButton) return;

    const limitReached = count >= 8;
    addButton.disabled = limitReached;
    addButton.setAttribute('aria-disabled', String(limitReached));
    addButton.title = limitReached ? 'The limit is 8 projects. Remove one to add another.' : '';
    if (limitStatus) {
        limitStatus.textContent = limitReached
            ? 'You have reached the 8-project limit. Remove an entry before adding another.'
            : `Project entries: ${count} of 8. You can add ${8 - count} more.`;
    }
}

const editorSections = document.querySelectorAll('.editor-section');
const sectionLinks = document.querySelectorAll('.section-nav a');
if ('IntersectionObserver' in window) {
    const sectionObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            sectionLinks.forEach(link => {
                if (link.getAttribute('href') === `#${entry.target.id}`) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        });
    }, { rootMargin: '-18% 0px -68% 0px' });
    editorSections.forEach(section => sectionObserver.observe(section));
}

updateProjectAddControl();
</script>
@endsection


