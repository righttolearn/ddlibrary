<div class="report-section">
    <h5 class="mb-3">Translated and adopted resources</h5>
    <div class="report-card-list">
        <div class="report-card">
            <div class="report-card-label">Language</div>
            <div class="report-card-value">{{ $languageName }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Translated resources</div>
            <div class="report-card-value">{{ $translatedCount }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Translated utilization</div>
            <div class="report-card-value">{{ $translatedUtilization }}%</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Adopted resources</div>
            <div class="report-card-value">{{ $adoptedCount }}</div>
        </div>
        <div class="report-card">
            <div class="report-card-label">Adopted utilization</div>
            <div class="report-card-value">{{ $adoptedUtilization }}%</div>
        </div>
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Translated resources by language pair</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($translatedLanguagePairs as $languagePair)
            <div class="report-card">
                <div class="report-card-label">
                    {{ $languagePair->from_language }} → {{ $languagePair->to_language }}
                </div>
                <div class="report-card-value">{{ $languagePair->total }}</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>

<div class="report-section">
    <h6 class="mb-3">Adopted resources by language pair</h6>
    <div class="report-card-list report-card-scroll">
        @forelse ($adoptedLanguagePairs as $languagePair)
            <div class="report-card">
                <div class="report-card-label">
                    {{ $languagePair->from_language }} → {{ $languagePair->to_language }}
                </div>
                <div class="report-card-value">{{ $languagePair->total }}</div>
            </div>
        @empty
            <div class="alert alert-light border mb-0">No data</div>
        @endforelse
    </div>
</div>
