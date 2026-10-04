@extends('layouts.app')
@section('title', 'Choose a Template - Portfold')

@section('styles')
<style>
/* Wider than the default 900px container so the cards can breathe */
.container { max-width: 1100px; }

.step-label { font-size: 12px; color: #6b7280; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 6px; }

.template-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }

/* Real radio inputs, visually hidden but keyboard/screen-reader accessible */
.tpl-option { position: relative; display: block; }
.tpl-option input { position: absolute; opacity: 0; inset: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2; margin: 0; }

.tpl-card {
    background: #141414;
    border: 2px solid #222;
    border-radius: 14px;
    overflow: hidden;
    transition: border-color .2s, transform .2s, box-shadow .2s;
    height: 100%;
}
.tpl-option:hover .tpl-card { border-color: #444; transform: translateY(-3px); }
.tpl-option input:focus-visible + .tpl-card { outline: 2px solid #93c5fd; outline-offset: 3px; }
.tpl-option input:checked + .tpl-card { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37,99,235,.18); }

/* Check badge */
.tpl-check {
    position: absolute; top: 12px; right: 12px; z-index: 1;
    width: 26px; height: 26px; border-radius: 50%;
    background: #2563eb; color: #fff; font-size: 14px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transform: scale(.6); transition: all .2s;
}
.tpl-option input:checked + .tpl-card .tpl-check { opacity: 1; transform: scale(1); }

/* Mini mock-ups drawn with CSS */
.tpl-preview { height: 190px; position: relative; overflow: hidden; }
.tpl-preview.simple   { background: #f8f9fa; padding: 22px 28px; }
.tpl-preview.modern   { background: #1e1b4b; }
.tpl-preview.creative { background: #0d1117; }

.bar  { background: #d1d5db; border-radius: 3px; height: 6px; margin-bottom: 8px; }
.bar.dark { background: #1f2937; height: 10px; width: 45%; margin-bottom: 14px; }
.bar.w80 { width: 80%; } .bar.w60 { width: 60%; } .bar.w70 { width: 70%; }
.rule { height: 1px; background: #e5e7eb; margin: 12px 0; }

/* Modern */
.m-nav { height: 22px; background: #2e2a6b; display: flex; align-items: center; gap: 6px; padding: 0 12px; }
.m-nav i { width: 26px; height: 5px; border-radius: 3px; background: #6d5fd6; display: block; }
.m-body { display: flex; height: calc(100% - 22px); }
.m-side { width: 52px; background: #17153a; padding: 10px 8px; }
.m-side i { display: block; height: 6px; border-radius: 3px; background: #4c46a8; margin-bottom: 8px; }
.m-main { flex: 1; padding: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; align-content: start; }
.m-hero { grid-column: 1 / -1; height: 52px; border-radius: 6px; background: linear-gradient(135deg, #7c3aed, #4f46e5); }
.m-card { height: 44px; border-radius: 6px; background: #2b2870; border: 1px solid #3d3a8c; }

/* Creative */
.c-wrap { display: flex; height: 100%; }
.c-side { width: 38%; background: #161b22; padding: 18px 14px; border-right: 3px solid #e11d48; }
.c-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #e11d48, #fb7185); margin-bottom: 12px; }
.c-side i { display: block; height: 5px; border-radius: 3px; background: #30363d; margin-bottom: 7px; }
.c-main { flex: 1; padding: 18px 16px; }
.c-main .big { height: 12px; width: 70%; background: #e11d48; border-radius: 3px; margin-bottom: 12px; }
.c-main i { display: block; height: 5px; border-radius: 3px; background: #30363d; margin-bottom: 7px; }
.c-main .tile { height: 38px; border-radius: 6px; background: #1c2129; border: 1px solid #30363d; margin-top: 12px; }

.tpl-info { padding: 18px; }
.tpl-info h3 { font-size: 17px; font-weight: 600; color: #fff; margin-bottom: 6px; }
.tpl-info p  { font-size: 14px; color: #9ca3af; line-height: 1.5; margin-bottom: 14px; }
.tpl-tags { font-size: 12px; color: #6b7280; }

.tpl-badge { font-size: 11px; font-weight: 500; padding: 2px 9px; border-radius: 10px; display: inline-block; margin-bottom: 10px; }
.badge-simple   { background: #1a3a2a; color: #74c69d; }
.badge-modern   { background: #1e1b4b; color: #a5b4fc; }
.badge-creative { background: #2d1a2a; color: #f9a8d4; }

.actions { margin-top: 32px; display: flex; gap: 12px; justify-content: flex-end; align-items: center; flex-wrap: wrap; }
.actions .hint { margin-right: auto; font-size: 13px; color: #6b7280; }
.btn[disabled] { opacity: .45; cursor: not-allowed; }

@media (max-width: 600px) {
    .actions { justify-content: stretch; }
    .actions .btn { flex: 1; text-align: center; }
    .actions .hint { width: 100%; }
}
</style>
@endsection

@section('content')
<div class="step-label">Step 2 of 3</div>
<h1 class="page-title">Choose Your Template</h1>
<p class="page-subtitle">Select the design that best represents you. You can change it later.</p>

<form action="{{ route('portfolio.applyTemplate', $portfolio->id) }}" method="POST">
    @csrf

    <div class="template-grid" role="radiogroup" aria-label="Portfolio templates">
        @foreach($templates as $template)
        <label class="tpl-option">
            <input type="radio"
                   name="template_id"
                   value="{{ $template->id }}"
                   data-name="{{ $template->name }}"
                   {{ $portfolio->template_id === $template->id ? 'checked' : '' }}>

            <div class="tpl-card">
                <span class="tpl-check" aria-hidden="true">&#10003;</span>

                <div class="tpl-preview {{ $template->slug }}" aria-hidden="true">
                    @if($template->slug === 'simple')
                        <div class="bar dark"></div>
                        <div class="bar w80"></div>
                        <div class="bar w60"></div>
                        <div class="rule"></div>
                        <div class="bar w70"></div>
                        <div class="bar w80"></div>
                        <div class="bar w60"></div>
                    @elseif($template->slug === 'modern')
                        <div class="m-nav"><i></i><i></i><i></i></div>
                        <div class="m-body">
                            <div class="m-side"><i></i><i></i><i></i><i></i></div>
                            <div class="m-main">
                                <div class="m-hero"></div>
                                <div class="m-card"></div>
                                <div class="m-card"></div>
                            </div>
                        </div>
                    @else
                        <div class="c-wrap">
                            <div class="c-side"><div class="c-avatar"></div><i></i><i></i><i></i></div>
                            <div class="c-main"><div class="big"></div><i></i><i></i><i></i><div class="tile"></div></div>
                        </div>
                    @endif
                </div>

                <div class="tpl-info">
                    <span class="tpl-badge badge-{{ $template->slug }}">{{ $template->category }}</span>
                    <h3>{{ $template->name }}</h3>
                    <p>
                        @if($template->slug === 'simple') Clean and professional design. Great for any field.
                        @elseif($template->slug === 'modern') Modern layout using cards, sections, and visual elements.
                        @else A unique and expressive arrangement for standing out.
                        @endif
                    </p>
                    <span class="tpl-tags">{{ $template->style_tags }}</span>
                </div>
            </div>
        </label>
        @endforeach
    </div>

    <div class="actions">
        <span class="hint" id="hint">Pick a template to continue.</span>
        <a href="{{ route('portfolio.edit', $portfolio->id) }}" class="btn btn-secondary">&larr; Edit Info</a>
        <button type="submit" class="btn btn-primary" id="apply-btn" disabled>Generate Portfolio &rarr;</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
(function () {
    const radios = document.querySelectorAll('input[name="template_id"]');
    const btn    = document.getElementById('apply-btn');
    const hint   = document.getElementById('hint');

    function sync() {
        const chosen = document.querySelector('input[name="template_id"]:checked');
        btn.disabled = !chosen;
        hint.textContent = chosen ? 'Selected: ' + chosen.dataset.name : 'Pick a template to continue.';
    }

    radios.forEach(r => r.addEventListener('change', sync));
    sync();
})();
</script>
@endsection