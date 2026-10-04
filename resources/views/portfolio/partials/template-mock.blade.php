{{-- Variable: $slug (simple | modern | creative). Decorative preview drawn with CSS. --}}
<div class="mock {{ $slug }}" aria-hidden="true">
    @if($slug === 'simple')
        <div class="bar dark"></div><div class="bar"></div><div class="bar w60"></div><div class="bar w70"></div>
        <div class="pills"><i></i><i></i><i></i><i></i></div>
    @elseif($slug === 'modern')
        <div class="m-nav"><i></i><i></i><i></i></div>
        <div class="m-body"><div class="m-side"><i></i><i></i><i></i><i></i></div><div class="m-main"><div class="m-hero"></div><div class="m-card"></div><div class="m-card"></div></div></div>
    @else
        <div class="c-wrap"><div class="c-side"><div class="c-avatar"></div><i></i><i></i><i></i></div><div class="c-main"><div class="big"></div><i></i><i></i><i></i><div class="tile"></div></div></div>
    @endif
</div>
