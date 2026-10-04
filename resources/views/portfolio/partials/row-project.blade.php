{{-- Variables: $i (row index; equals display_order so the old screenshot is kept), $r (values), $shot (saved screenshot URL or null) --}}
<div class="repeatable-item" data-row>
    <button type="button" class="remove-btn" aria-label="Remove this project" onclick="removeRow(this)">&times;</button>
    <div class="form-group">
        <label for="proj-title-{{ $i }}">Project title</label>
        <input type="text" id="proj-title-{{ $i }}" name="projects[{{ $i }}][title]" value="{{ $r['title'] ?? '' }}" placeholder="My Awesome Project">
    </div>
    <div class="form-group">
        <label for="proj-desc-{{ $i }}">Description</label>
        <textarea id="proj-desc-{{ $i }}" name="projects[{{ $i }}][description]" placeholder="What does this project do, and what did you build?">{{ $r['description'] ?? '' }}</textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="proj-live-{{ $i }}">Live URL</label>
            <input type="url" id="proj-live-{{ $i }}" name="projects[{{ $i }}][live_url]" value="{{ $r['live_url'] ?? '' }}" placeholder="https://myproject.com">
        </div>
        <div class="form-group">
            <label for="proj-repo-{{ $i }}">GitHub / repo URL</label>
            <input type="url" id="proj-repo-{{ $i }}" name="projects[{{ $i }}][repo_url]" value="{{ $r['repo_url'] ?? '' }}" placeholder="https://github.com/me/project">
        </div>
    </div>
    <div class="form-group">
        <label for="proj-shot-{{ $i }}">Project screenshot</label>
        <div class="shot-box" id="shot-preview-{{ $i }}">
            @if(!empty($shot))<img src="{{ $shot }}" alt="Current screenshot">@else<span>No screenshot</span>@endif
        </div>
        <input type="file" id="proj-shot-{{ $i }}" name="projects[{{ $i }}][screenshot]" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this, 'shot-preview-{{ $i }}')">
        <span class="help">JPG, PNG or WebP, up to 4 MB.@if(!empty($shot)) Leave empty to keep the current one.@endif</span>
        <span class="field-error" data-file-error hidden></span>
    </div>
</div>
