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

.manage-section-title { margin: 0 0 14px; font-size: 18px; font-weight: 600; }
.trash-section { margin-top: 42px; padding-top: 28px; border-top: 1px solid #2b2b2b; }
.trash-section > p { margin: -4px 0 18px; color: #8e8e8e; font-size: 13px; line-height: 1.6; }
.restore-button { white-space: nowrap; }
.permanent-delete-button { white-space: nowrap; }

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

<h2 class="manage-section-title">Active portfolios</h2>
@if($portfolios->isEmpty())
    <div class="empty-state">
        <h2>No active portfolios</h2>
        <p>Create a portfolio or restore one from Trash.</p>
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
                <form action="{{ route('portfolio.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Move this portfolio to Trash? Its public link will stop working, but your content and images will be kept and can be restored any time.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="font-size:13px; padding:7px 14px;">Move to Trash</button>
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

@if($trashedPortfolios->isNotEmpty())
    <section class="trash-section" aria-labelledby="trash-heading">
        <h2 class="manage-section-title" id="trash-heading">Trash</h2>
        <p>Portfolios in Trash are hidden from their public links. Restore one to keep using it, or permanently delete it and its uploaded images. Permanent deletion cannot be undone.</p>
        <div class="portfolio-list">
            @foreach($trashedPortfolios as $p)
            <div class="portfolio-item">
                <div class="portfolio-item-info">
                    <h3>{{ $p->full_name ?: ($p->headline ?: 'Untitled Portfolio') }}</h3>
                    <p>Moved to Trash {{ \Carbon\Carbon::parse($p->deleted_at)->diffForHumans() }} &nbsp;·&nbsp; Slug: /{{ $p->slug }}</p>
                </div>
                <div class="portfolio-item-meta">
                    <span class="tpl-chip">{{ $p->template_name }}</span>
                    <span class="status-badge status-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                </div>
                <div class="item-actions">
                    <form action="{{ route('portfolio.restore', $p->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary restore-button" style="font-size:13px; padding:7px 14px;">Restore</button>
                    </form>
                    <form action="{{ route('portfolio.permanent-delete', $p->id) }}" method="POST" onsubmit="return confirm('Permanently delete this portfolio, all of its content, and its uploaded images? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger permanent-delete-button" style="font-size:13px; padding:7px 14px;">Delete permanently</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($trashedPortfolios->hasPages())
            <nav aria-label="Deleted portfolio pages" style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:20px">
                @if($trashedPortfolios->previousPageUrl())<a class="btn btn-secondary" href="{{ $trashedPortfolios->previousPageUrl() }}">Previous</a>@else<span></span>@endif
                <span class="muted">Page {{ $trashedPortfolios->currentPage() }} of {{ $trashedPortfolios->lastPage() }}</span>
                @if($trashedPortfolios->nextPageUrl())<a class="btn btn-secondary" href="{{ $trashedPortfolios->nextPageUrl() }}">Next</a>@else<span></span>@endif
            </nav>
        @endif
    </section>
@endif
@endsection
