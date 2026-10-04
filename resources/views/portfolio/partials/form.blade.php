{{--
  Shared by Create and Edit so both behave identically.
  Needs: $action, $method ('POST'|'PUT'), $submitLabel, $busyText, $cancelUrl
  Optional (edit only): $info, $skills, $projects, $education, $experiences, $links
--}}
@php
    $info = $info ?? null;
    $linkMap = collect($links ?? [])->pluck('url', 'platform');

    // Keep what the user typed after a validation error; otherwise show saved data; otherwise one blank row.
    $eduRows = old('education') ?: (collect($education ?? [])->map(fn ($r) => (array) $r)->values()->all() ?: [[]]);
    $expRows = old('experiences') ?: (collect($experiences ?? [])->map(fn ($r) => (array) $r)->values()->all() ?: [[]]);
    $savedProjects = collect($projects ?? [])->keyBy(fn ($p) => (int) $p->display_order);
    $projRows = old('projects') ?: ($savedProjects->map(fn ($p) => (array) $p)->all() ?: [0 => []]);

    $nextEdu  = max(array_keys($eduRows)) + 1;
    $nextExp  = max(array_keys($expRows)) + 1;
    $nextProj = max(array_keys($projRows)) + 1;
@endphp

<nav class="jump" aria-label="Jump to a section">
    <a href="#sec-personal">Personal</a><a href="#sec-links">Links</a><a href="#sec-skills">Skills</a>
    <a href="#sec-education">Education</a><a href="#sec-experience">Experience</a><a href="#sec-projects">Projects</a>
</nav>
<p class="legend-note">Fields marked <span class="req" aria-hidden="true">*</span><span class="sr-only" style="position:absolute;left:-9999px;">with an asterisk</span> are required. Everything else is optional, so you can fill in the rest later.</p>

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" data-loading data-warn-unsaved>
    @csrf
    @if($method !== 'POST') @method($method) @endif

    {{-- Personal --}}
    <section class="form-section" id="sec-personal" aria-labelledby="h-personal">
        <h2 id="h-personal">👤 Personal information</h2>
        <p class="section-help">This appears at the top of your portfolio.</p>

        <div class="form-group">
            <label for="profile_photo">Profile photo</label>
            <div class="photo-upload-wrap">
                <div class="photo-preview-box" id="photo-preview-box">
                    @if(!empty($info->photo_url))<img src="{{ $info->photo_url }}" alt="Current profile photo">@else<span>No photo yet</span>@endif
                </div>
                <div class="photo-upload-controls">
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this, 'photo-preview-box')" @error('profile_photo') aria-invalid="true" aria-describedby="photo-error" @enderror>
                    <span class="help">JPG, PNG or WebP, up to 4 MB. A wide landscape photo looks best as the hero image.@if(!empty($info->photo_url)) Leave empty to keep your current photo.@endif</span>
                    <span class="field-error" data-file-error hidden></span>
                    @error('profile_photo')<span class="field-error" id="photo-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Full name<span class="req" aria-hidden="true">*</span></label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $info->full_name ?? '') }}" required aria-required="true" autocomplete="name" placeholder="Juan Dela Cruz" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
                @error('full_name')<span class="field-error" id="full_name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="headline">Headline</label>
                <input type="text" id="headline" name="headline" value="{{ old('headline', $info->headline ?? '') }}" maxlength="120" placeholder="e.g. Aspiring Full-stack Developer" aria-describedby="headline-help">
                <span class="help" id="headline-help">One short line about what you do.</span>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="contact_email">Email<span class="req" aria-hidden="true">*</span></label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $info->contact_email ?? '') }}" required aria-required="true" autocomplete="email" placeholder="juan@email.com" @error('contact_email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('contact_email')<span class="field-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="phone">Contact number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $info->phone ?? '') }}" autocomplete="tel" inputmode="tel" placeholder="+63 912 345 6789">
            </div>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="{{ old('location', $info->location ?? '') }}" autocomplete="address-level2" placeholder="Cebu City, Philippines">
        </div>
        <div class="form-group">
            <label for="bio">About me</label>
            <textarea id="bio" name="bio" maxlength="500" placeholder="Write 2 to 4 sentences about who you are and what you're working towards." aria-describedby="bio-count">{{ old('bio', $info->bio ?? '') }}</textarea>
            <div class="counter" id="bio-count" aria-live="polite"></div>
        </div>
    </section>

    {{-- Links --}}
    <section class="form-section" id="sec-links" aria-labelledby="h-links">
        <h2 id="h-links">🔗 Social media &amp; links</h2>
        <p class="section-help">Paste the full address, starting with https://</p>
        <div class="form-row">
            <div class="form-group"><label for="link-github">GitHub</label><input type="url" id="link-github" name="links[github]" value="{{ old('links.github', $linkMap['github'] ?? '') }}" placeholder="https://github.com/username"></div>
            <div class="form-group"><label for="link-linkedin">LinkedIn</label><input type="url" id="link-linkedin" name="links[linkedin]" value="{{ old('links.linkedin', $linkMap['linkedin'] ?? '') }}" placeholder="https://linkedin.com/in/username"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label for="link-website">Personal website</label><input type="url" id="link-website" name="links[website]" value="{{ old('links.website', $linkMap['website'] ?? '') }}" placeholder="https://yoursite.com"></div>
            <div class="form-group"><label for="link-facebook">Facebook</label><input type="url" id="link-facebook" name="links[facebook]" value="{{ old('links.facebook', $linkMap['facebook'] ?? '') }}" placeholder="https://facebook.com/username"></div>
        </div>
    </section>

    {{-- Skills --}}
    <section class="form-section" id="sec-skills" aria-labelledby="h-skills">
        <h2 id="h-skills">🛠 Skills</h2>
        <p class="section-help">List the tools and languages you use. Logos are added automatically.</p>
        <div class="form-group">
            <label for="skills">Skills</label>
            <input type="text" id="skills" name="skills" value="{{ old('skills', collect($skills ?? [])->pluck('name')->join(', ')) }}" placeholder="HTML, CSS, JavaScript, Laravel, PHP" aria-describedby="skills-help">
            <span class="help" id="skills-help">Separate each skill with a comma.</span>
        </div>
    </section>

    {{-- Education --}}
    <section class="form-section" id="sec-education" aria-labelledby="h-edu">
        <h2 id="h-edu">🎓 Education</h2>
        <p class="section-help">Add your school, newest first. Empty entries are ignored.</p>
        <div id="education-list">
            @foreach($eduRows as $i => $r) @include('portfolio.partials.row-education', ['i' => $i, 'r' => $r]) @endforeach
        </div>
        <button type="button" class="add-btn" onclick="addRow('education')">+ Add education</button>
    </section>

    {{-- Experience --}}
    <section class="form-section" id="sec-experience" aria-labelledby="h-exp">
        <h2 id="h-exp">💼 Work experience &amp; internships</h2>
        <p class="section-help">Optional. Type dates in any format, like "Jun 2023".</p>
        <div id="experience-list">
            @foreach($expRows as $i => $r) @include('portfolio.partials.row-experience', ['i' => $i, 'r' => $r]) @endforeach
        </div>
        <button type="button" class="add-btn" onclick="addRow('experience')">+ Add experience</button>
    </section>

    {{-- Projects --}}
    <section class="form-section" id="sec-projects" aria-labelledby="h-proj">
        <h2 id="h-proj">🚀 Projects</h2>
        <p class="section-help">Showcase your best work. A screenshot makes each project much easier to remember.</p>
        <div id="project-list">
            @foreach($projRows as $i => $r) @include('portfolio.partials.row-project', ['i' => $i, 'r' => $r, 'shot' => optional($savedProjects->get((int) $i))->screenshot_url]) @endforeach
        </div>
        <button type="button" class="add-btn" onclick="addRow('project')">+ Add project</button>
    </section>

    <div class="action-bar">
        <span class="hint">Your changes are saved when you press the button.</span>
        <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary" data-busy-text="{{ $busyText }}">{{ $submitLabel }}</button>
    </div>
</form>

{{-- Blank rows used by the "+ Add" buttons --}}
<template id="tpl-education">@include('portfolio.partials.row-education', ['i' => '__I__', 'r' => []])</template>
<template id="tpl-experience">@include('portfolio.partials.row-experience', ['i' => '__I__', 'r' => []])</template>
<template id="tpl-project">@include('portfolio.partials.row-project', ['i' => '__I__', 'r' => [], 'shot' => null])</template>

<script>
(function () {
    var next = { education: {{ $nextEdu }}, experience: {{ $nextExp }}, project: {{ $nextProj }} };
    var lists = { education: 'education-list', experience: 'experience-list', project: 'project-list' };
    var MAX_BYTES = 4 * 1024 * 1024;

    window.addRow = function (kind) {
        var html = document.getElementById('tpl-' + kind).innerHTML.split('__I__').join(next[kind]++);
        var list = document.getElementById(lists[kind]);
        list.insertAdjacentHTML('beforeend', html);
        var first = list.lastElementChild.querySelector('input[type="text"], input[type="url"]');
        if (first) { first.focus(); }
    };

    window.removeRow = function (btn) {
        var row = btn.closest('[data-row]');
        var hasContent = Array.prototype.some.call(row.querySelectorAll('input[type="text"], input[type="url"], textarea'), function (el) { return el.value.trim() !== ''; });
        if (hasContent && !confirm('Remove this entry? What you typed in it will be lost.')) { return; }
        var list = row.parentElement;
        row.remove();
        var focusTarget = list.querySelector('input, textarea') || list.parentElement.querySelector('.add-btn');
        if (focusTarget) { focusTarget.focus(); }
    };

    // Live preview + size/type check before upload (error prevention)
    window.previewImage = function (input, boxId) {
        var box = document.getElementById(boxId);
        var err = input.parentElement.querySelector('[data-file-error]');
        if (err) { err.hidden = true; }
        var file = input.files && input.files[0];
        if (!file) { return; }
        var problem = null;
        if (!/^image\/(jpeg|png|webp)$/.test(file.type)) { problem = 'Please choose a JPG, PNG or WebP image.'; }
        else if (file.size > MAX_BYTES) { problem = 'That image is ' + (file.size / 1048576).toFixed(1) + ' MB. Please choose one under 4 MB.'; }
        if (problem) { input.value = ''; if (err) { err.textContent = problem; err.hidden = false; } return; }
        var reader = new FileReader();
        reader.onload = function (e) { box.innerHTML = '<img alt="Preview of the selected image">'; box.firstChild.src = e.target.result; };
        reader.readAsDataURL(file);
    };

    // Live character counter for the bio
    var bio = document.getElementById('bio'), count = document.getElementById('bio-count');
    function updateCount() {
        var n = bio.value.length, max = bio.maxLength;
        count.textContent = n + ' / ' + max + ' characters';
        count.classList.toggle('near', n > max * 0.9);
    }
    bio.addEventListener('input', updateCount); updateCount();
})();
</script>
