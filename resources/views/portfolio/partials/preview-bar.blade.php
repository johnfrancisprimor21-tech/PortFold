{{-- Fixed preview controls with page space reserved so they never cover portfolio content. --}}
<style>
html { scroll-padding-top: 78px; }
#pf-bar {
    position: fixed;
    inset: 0 0 auto;
    z-index: 2147483000;
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 62px;
    padding: 8px clamp(14px, 3vw, 42px);
    background: rgba(23, 10, 13, .97);
    border-bottom: 1px solid rgba(255, 255, 255, .12);
    box-shadow: 0 8px 24px rgba(16, 8, 10, .18);
    color: #f8f4f2;
    font: 500 14px/1.2 'Segoe UI', system-ui, sans-serif;
    backdrop-filter: blur(12px);
}
#pf-bar .pf-tag {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    margin-right: auto;
    color: #f8f4f2;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}
#pf-bar .pf-tag::before {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #f25347;
    box-shadow: 0 0 0 4px rgba(242, 83, 71, .16);
    content: '';
}
#pf-bar a, #pf-bar button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 10px 16px;
    border: 1px solid rgba(255, 255, 255, .2);
    border-radius: 8px;
    background: rgba(255, 255, 255, .07);
    color: #f8f4f2;
    font: inherit;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background .16s, border-color .16s;
}
#pf-bar a:hover, #pf-bar button:hover { background: rgba(255, 255, 255, .14); border-color: rgba(255, 255, 255, .38); }
#pf-bar a.pf-primary { background: #b72e28; border-color: #b72e28; color: #fff; }
#pf-bar a.pf-primary:hover { background: #9d241f; border-color: #9d241f; }
#pf-bar a:focus-visible, #pf-bar button:focus-visible { outline: 3px solid #ffd0ca; outline-offset: 3px; }
#pf-bar[hidden], #pf-show[hidden] { display: none !important; }
#pf-show {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 2147483002;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 48px;
    padding: 11px 16px;
    border: 1px solid rgba(255, 255, 255, .2);
    border-radius: 999px;
    background: #170a0d;
    color: #fff;
    box-shadow: 0 8px 24px rgba(16, 8, 10, .24);
    font: 600 14px/1.2 'Segoe UI', system-ui, sans-serif;
    cursor: pointer;
    transition: background .16s, transform .16s;
}
#pf-show:hover { background: #342025; transform: translateY(-1px); }
#pf-show:focus-visible { outline: 3px solid #b72e28; outline-offset: 3px; }
#pf-show svg { width: 18px; height: 18px; flex: none; }
#pf-toast {
    position: fixed;
    left: 50%;
    top: calc(var(--pf-preview-bar-height, 62px) + 12px);
    transform: translateX(-50%);
    z-index: 2147483001;
    padding: 11px 18px;
    border: 1px solid #2d6a4f;
    border-radius: 10px;
    background: #12281d;
    color: #b7efcf;
    font: 500 14px 'Segoe UI', system-ui, sans-serif;
    box-shadow: 0 8px 24px rgba(16, 8, 10, .16);
}
@media (max-width: 560px) {
    html { scroll-padding-top: 178px; }
    #pf-bar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        grid-template-areas: 'label hide' 'portfolios edit' 'template template';
        gap: 6px;
        padding: 8px 12px;
    }
    #pf-bar .pf-tag { grid-area: label; margin: 0; }
    #pf-bar a:nth-of-type(1) { grid-area: portfolios; }
    #pf-bar a:nth-of-type(2) { grid-area: edit; }
    #pf-bar a.pf-primary { grid-area: template; }
    #pf-bar button { grid-area: hide; justify-self: end; }
    #pf-bar a, #pf-bar button { width: 100%; padding-right: 10px; padding-left: 10px; }
    #pf-bar button { width: auto; min-width: 72px; }
}
@media print { #pf-bar, #pf-show, #pf-toast { display: none !important; } }
</style>
@auth
@if((string) ($portfolio->user_id ?? '') === (string) auth()->id())
@if(session('success'))<div id="pf-toast" role="status">{{ session('success') }}</div>@endif
<div id="pf-bar" role="region" aria-label="Portfolio preview controls">
    <span class="pf-tag">Preview mode</span>
    <a href="{{ route('portfolio.manage') }}">My portfolios</a>
    <a href="{{ route('portfolio.edit', $portfolio->id) }}">Edit info</a>
    <a href="{{ route('portfolio.template', $portfolio->id) }}" class="pf-primary">Change template</a>
    <button type="button" id="pf-hide" aria-label="Hide preview toolbar">Hide</button>
</div>
<button type="button" id="pf-show" aria-label="Show preview controls" hidden>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
        <path d="M4 6h7m4 0h5M4 18h3m4 0h9"/><circle cx="13" cy="6" r="2"/><circle cx="9" cy="18" r="2"/>
    </svg>
    <span>Preview controls</span>
</button>
<script>
(function () {
    var bar = document.getElementById('pf-bar');
    var hideButton = document.getElementById('pf-hide');
    var showButton = document.getElementById('pf-show');
    var toast = document.getElementById('pf-toast');

    function reserveToolbarSpace() {
        if (bar.hidden) { return; }
        var height = bar.offsetHeight;
        document.body.style.paddingTop = height + 'px';
        document.documentElement.style.setProperty('--pf-preview-bar-height', height + 'px');
        document.documentElement.style.scrollPaddingTop = (height + 12) + 'px';
    }

    function releaseToolbarSpace() {
        document.body.style.paddingTop = '';
        document.documentElement.style.scrollPaddingTop = '';
        document.documentElement.style.removeProperty('--pf-preview-bar-height');
    }

    reserveToolbarSpace();
    window.addEventListener('resize', reserveToolbarSpace);
    hideButton.addEventListener('click', function () {
        bar.hidden = true;
        releaseToolbarSpace();
        showButton.hidden = false;
        showButton.focus();
    });
    showButton.addEventListener('click', function () {
        showButton.hidden = true;
        bar.hidden = false;
        reserveToolbarSpace();
        hideButton.focus();
    });

    if (toast) {
        window.setTimeout(function () { toast.remove(); }, 4000);
    }
})();
</script>
@endif
@endauth
