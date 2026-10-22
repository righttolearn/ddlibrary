<div class="report-section">
    <h5 class="mb-3">Partner-contributed resources</h5>
    <div class="report-card-list">
        <div class="report-card">
            <div class="report-card-label">Language</div>
            <div class="report-card-value">{{ $languageName }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Partner resources</div>
            <div class="report-card-value">{{ $partnerCount }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Partner utilization</div>
            <div class="report-card-value">{{ $partnerUtilization }}%</div>
        </div>
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Resources by partner</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($partners as $partner)
            <div class="report-card">
                <div class="report-card-label">{{ $partner->partner }}</div>
                <div class="report-card-value">{{ $partner->total }}</div>
                <div class="report-card-language">Utilization: {{ $partner->utilization }}%</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>
