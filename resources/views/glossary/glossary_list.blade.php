@extends('layouts.main')
@section('title')
DDL Glossary
@endsection
@section('description')
DDL Glossary
@endsection
@section('page_image')
{{ asset('storage/files/logo-dd.png') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('glossary') }}" id="gform">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">@lang('Text')</label>
                            <input class="form-control" type="text" name="text" value="{{ $filters['text'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Subject')</label>
                            <select name="subject" class="form-select">
                                <option value="">@lang('All')</option>
                                @foreach($glossary_subjects as $id => $subject)
                                    <option value="{{ $id }}" {{ (isset($filters['subject']) && $filters['subject'] == $id) ? 'selected' : '' }}>{{ $subject }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (isLibraryManager() || isAdmin())
                            <div class="col-md-2">
                                <label class="form-label">@lang('Flagged')</label>
                                <select name="flagged" class="form-select">
                                    <option value="show" {{ (isset($filters['flagged']) && $filters['flagged'] == 'show') ? 'selected' : '' }}>@lang('Show')</option>
                                    <option value="hide" {{ (isset($filters['flagged']) && $filters['flagged'] == 'hide') ? 'selected' : '' }}>@lang('Hide')</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ph-light ph-magnifying-glass"></i> @lang('Filter')
                            </button>
                            @if (isLibraryManager() || isAdmin())
                                <a href="{{ route('glossary_create') }}" class="btn btn-secondary">
                                    <i class="ph-light ph-plus"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="success_msg" class="alert alert-success text-center d-none"></div>
        @if (isLibraryManager() || isAdmin())
            <div class="alert alert-info small">
                <i class="ph-light ph-info"></i> @lang('Click and edit the text you would like to change...')
            </div>
        @endif
        @if ($glossary_flagged)
            <h3>@lang('Flagged for review')</h3>
            @include('glossary.table', ['glossary' => $glossary_flagged, 'glossary_subjects' => $glossary_subjects, 'flagged_queue' => true])
            <br>
            <div class="d-flex justify-content-center mt-4">
                {{ $glossary_flagged->appends(request()->input())->links() }}
            </div>
            <br>
        @endif
        @include('glossary.table', ['glossary' => $glossary, 'glossary_subjects' => $glossary_subjects, 'flagged_queue' => false])
        <br>
        <div class="d-flex justify-content-center mt-4">
            {{ $glossary->appends(request()->input())->links() }}
        </div>
    </div>
@endsection
@push('scripts')
    @vite('resources/assets/js/modules/glossary.jsx')
@endpush

