<div class="offcanvas offcanvas-start" tabindex="-1" id="filterPanel" aria-labelledby="filterPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="filterPanelLabel">@lang('Filter')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="@lang('Close')"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('resourceList') }}">
            <div class="mb-3">
                <label for="language" class="form-label">@lang('Language')</label>
                <select class="form-select" name="language" id="language">
                    @foreach ($languages as $key => $language)
                        <option value="{{ $key }}" @selected($key == config('app.locale'))>
                            {{ $language['native'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="search" class="form-label">@lang('Keywords')</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="@lang('Keywords to filter by')">
            </div>

            <div class="mb-3">
                <label for="selectSubjectAreaParent" class="form-label">@lang('Subjects')</label>
                <select class="form-select" name="subjectAreaParent[]" id="selectSubjectAreaParent" multiple>
                    @foreach ($parentSubjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ ucfirst(strtolower($subject->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="selectSubjectAreaChild" class="form-label">@lang('Subjects (sub-categories)')</label>
                <select class="form-select" name="subjectAreaChild[]" id="selectSubjectAreaChild"
                        multiple>
                </select>
            </div>

            <div class="mb-3">
                <label for="selectResourceType" class="form-label">@lang('Resource types')</label>
                <select class="form-select" name="type[]" id="selectResourceType" multiple>
                    @foreach ($resourceTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ ucfirst(strtolower($type->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="selectLiteracyLevel" class="form-label">@lang('Resource literacy levels')</label>
                <select class="form-select" name="level[]" id="selectLiteracyLevel" multiple>
                    @foreach ($literacyLevels as $level)
                        <option value="{{ $level->id }}">
                            {{ ucfirst(strtolower($level->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">@lang('Apply filters')</button>
                <a href="{{ route('resourceList') }}" class="btn btn-outline-secondary">@lang('Clear filters')</a>
            </div>
        </form>
    </div>
</div>
