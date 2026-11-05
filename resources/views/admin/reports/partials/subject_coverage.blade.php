<div class="report-section">
    <h5 class="mb-3">Subject area coverage</h5>
    <div class="report-card-list">
        @forelse ($coverageRows as $coverage)
            <div class="report-card">
                <div class="report-card-label">{{ $coverage->language }}</div>
                <div class="report-card-value">{{ $coverage->coverage }}%</div>
                <div class="report-card-language">
                    {{ $coverage->coveredSubjects }} of {{ $coverage->totalSubjects }} subjects
                </div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>
