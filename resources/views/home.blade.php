@extends('layouts.app')
@section('title', 'Portfold - Free Portfolio Generator')
@section('container_class', 'wide')

@section('styles')
<style>
.hero { text-align: center; padding: 56px 16px 40px; }
.hero .badge { display: inline-block; background: #1e3a5f; color: #93c5fd; font-size: 13px; font-weight: 500; padding: 4px 14px; border-radius: 20px; margin-bottom: 20px; border: 1px solid #2563eb55; }
.hero h1 { font-size: clamp(30px, 5vw, 44px); font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 16px; }
.hero p { font-size: 18px; color: var(--muted); margin: 0 auto 32px; max-width: 480px; }
.hero-buttons { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-lg { min-height: 52px; padding: 12px 28px; font-size: 16px; }

.section-title { font-size: 13px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin: 56px 0 18px; }
.how { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; list-style: none; }
.how li { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 22px; }
.how .n { width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; }
.how h3 { font-size: 17px; color: #fff; margin-bottom: 4px; }
.how p { font-size: 15px; color: var(--muted); }

.template-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; list-style: none; }
.template-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.template-info { padding: 18px; }
.template-info h3 { font-size: 17px; color: #fff; margin-bottom: 4px; }
.template-info p { font-size: 15px; color: var(--muted); }
.cta-end { text-align: center; margin-top: 40px; }
</style>
@endsection

@section('content')
<section class="hero" aria-labelledby="hero-title">
    <span class="badge">Free portfolio templates</span>
    <h1 id="hero-title">Launch your portfolio<br>in minutes</h1>
    <p>Fill in your details, pick a design, and see your portfolio instantly. No coding needed.</p>
    <div class="hero-buttons">
        <a href="{{ route('portfolio.create') }}" class="btn btn-primary btn-lg">Create your portfolio</a>
        <a href="{{ route('portfolio.manage') }}" class="btn btn-secondary btn-lg">Manage portfolios</a>
    </div>
</section>

<h2 class="section-title">How it works</h2>
<ol class="how">
    <li><span class="n" aria-hidden="true">1</span><h3>Enter your info</h3><p>Add your details, skills, projects and photos. It takes about 5 minutes.</p></li>
    <li><span class="n" aria-hidden="true">2</span><h3>Choose a template</h3><p>Pick the look that fits you. You can switch any time.</p></li>
    <li><span class="n" aria-hidden="true">3</span><h3>Preview &amp; edit</h3><p>See the result right away, then edit or delete whenever you like.</p></li>
</ol>

<h2 class="section-title">Template styles</h2>
<ul class="template-cards">
    <li class="template-card">
        @include('portfolio.partials.template-mock', ['slug' => 'simple'])
        <div class="template-info"><span class="tpl-badge badge-simple">Clean &amp; professional</span><h3>Simple</h3><p>Light and easy to read. Great for any field.</p></div>
    </li>
    <li class="template-card">
        @include('portfolio.partials.template-mock', ['slug' => 'modern'])
        <div class="template-info"><span class="tpl-badge badge-modern">Visual &amp; dynamic</span><h3>Modern</h3><p>Cards, sections and project screenshots.</p></div>
    </li>
    <li class="template-card">
        @include('portfolio.partials.template-mock', ['slug' => 'creative'])
        <div class="template-info"><span class="tpl-badge badge-creative">Bold &amp; unique</span><h3>Creative</h3><p>An expressive layout built to stand out.</p></div>
    </li>
</ul>
<div class="cta-end"><a href="{{ route('portfolio.create') }}" class="btn btn-primary btn-lg">Get started</a></div>
@endsection
