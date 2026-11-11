<div class="report-section">
    <h5 class="mb-3">Unique uploaded resources</h5>
    <div class="report-card-list">
        <div class="report-card">
            <div class="report-card-label">Language</div>
            <div class="report-card-value">{{ $languageName }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Total unique uploaded resources</div>
            <div class="report-card-value">{{ $totalResources }}</div>
        </div>
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Resources by language</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($resourcesByLanguage as $resource)
            <div class="report-card">
                <div class="report-card-label">{{ $resource->language }}</div>
                <div class="report-card-value">{{ $resource->total }}</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Resources by subject</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($resourcesBySubject as $resource)
            <div class="report-card">
                <div class="report-card-label">{{ $resource->name }}</div>
                <div class="report-card-language">{{ $resource->language }}</div>
                <div class="report-card-value">{{ $resource->total }}</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Resources by type</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($resourcesByType as $resource)
            <div class="report-card">
                <div class="report-card-label">{{ $resource->name }}</div>
                <div class="report-card-language">{{ $resource->language }}</div>
                <div class="report-card-value">{{ $resource->total }}</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>
