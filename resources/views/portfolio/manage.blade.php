@extends('layouts.app')
@section('title', 'Manage Portfolios Portfold')

@section('styles')
<style>
.manage-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
.manage-header h1 { margin: 0; }

.portfolio-list { display: flex; flex-direction: column; gap: 12px; }

.portfolio-item {
    background: #141414;
    border: 1px solid #222;
    border-radius: 12px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.portfolio-item-info h3 { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px; }
.portfolio-item-info p  { font-size: 13px; color: #666; }

.portfolio-item-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.status-badge {
    font-size: 11px;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 20px;
}
.status-published { background: #1a3a2a; color: #74c69d; border: 1px solid #2d6a4f; }
.status-draft     { background: #2a2a1a; color: #d4a017; border: 1px solid #4a3a00; }

.tpl-chip {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    background: #1e1b4b;
    color: #a5b4fc;
}

.item-actions { display: flex; gap: 8px; }

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #555;
}
.empty-state h2 { font-size: 20px; color: #777; margin-bottom: 8px; }
.empty-state p  { margin-bottom: 24px; }
</style>
@endsection

@section('content')
<div class="manage-header">
    <h1 class="page-title" style="margin:0">My Portfolios</h1>
    <a href="{{ route('portfolio.create') }}" class="btn btn-primary">+ Create New</a>
</div>

@if($portfolios->isEmpty())
    <div class="empty-state">
        <h2>No portfolios yet</h2>
        <p>Create your first portfolio and go live in minutes.</p>
        <a href="{{ route('portfolio.create') }}" class="btn btn-primary">Create Portfolio</a>
    </div>
@else
    <div class="portfolio-list">
        @foreach($portfolios as $p)
        <div class="portfolio-item">
            <div class="portfolio-item-info">
                <h3>{{ $p->headline ?? 'Untitled Portfolio' }}</h3>
                <p>Slug: /{{ $p->slug }} &nbsp;·&nbsp; Updated: {{ \Carbon\Carbon::parse($p->updated_at)->diffForHumans() }}</p>
            </div>
            <div class="portfolio-item-meta">
                <span class="tpl-chip">{{ $p->template_name }}</span>
                <span class="status-badge status-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
            </div>
            <div class="item-actions">
                <a href="{{ route('portfolio.preview', $p->id) }}" class="btn btn-secondary" style="font-size:13px; padding:7px 14px;">View</a>
                <a href="{{ route('portfolio.edit', $p->id) }}" class="btn btn-secondary" style="font-size:13px; padding:7px 14px;">Edit</a>
                <form action="{{ route('portfolio.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this portfolio?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="font-size:13px; padding:7px 14px;">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @if($portfolios->hasPages())
        <nav aria-label="Portfolio pages" style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:20px">
            @if($portfolios->previousPageUrl())<a class="btn btn-secondary" href="{{ $portfolios->previousPageUrl() }}">Previous</a>@else<span></span>@endif
            <span class="muted">Page {{ $portfolios->currentPage() }} of {{ $portfolios->lastPage() }}</span>
            @if($portfolios->nextPageUrl())<a class="btn btn-secondary" href="{{ $portfolios->nextPageUrl() }}">Next</a>@else<span></span>@endif
        </nav>
    @endif
@endif
@endsection
