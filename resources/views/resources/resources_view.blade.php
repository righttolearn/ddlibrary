@extends('layouts.main')
@section('title')
    {{ trim(strip_tags($resource->title)) }}
@endsection
@section('description')
    {{ html_entity_decode(trim(strip_tags(fixImage($resource->abstract, $resource->id)))) }}
@endsection
@section('page_image')
    {{  $resource->resourceFile ?  getResourceImage($resource->resourceFile->name, true)  : getImagefromResource($resource->abstract, '282x254') }}
@endsection
@push('style')
    <style>
        .epub-container {
            overflow: visible !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid resource-view">
        @include('layouts.messages')
        <div class="row m-2 mt-md-4">
            <div class="col-md-8">
                @if ($resource->attachments)
                    @foreach ($resource->attachments as $file)
                        @if ($file->file_mime == 'application/pdf')
                            <div class="ratio ratio-16x9">
                                <iframe
                                    src="{{ getFile('/resources/' . $file->file_name) }}#toolbar=0"
                                    title="@lang('Resource preview')"
                                    loading="lazy"></iframe>
                            </div>
                        @elseif(
                            $file->file_mime == 'application/msword' ||
                                $file->file_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ config('constants.google_doc_viewer_url') . rawurlencode(getFile('/resources/' . $file->file_name)) . '&embedded=true' }}"></iframe>
                            </div>
                        @elseif($file->file_mime == 'audio/mpeg')
                            <audio controls class="w-100 mb-3">
                                <source src="{{ getFile('/resources/' . $file->file_name) }}" type="audio/mpeg">
                                @lang('Your browser does not support audio, please download the file.')
                            </audio>
                        @elseif($ePub)
                            <div class="container-fluid p-2 d-none" id="epubViewer">
                                <div class="text-center mb-4">
                                    <h1 class="display-4 fw-light mb-2" id="epubTitle">@lang('Loading, please wait')</h1>
                                    <p class="fs-5 text-muted" id="epubAuthor">@lang('Author')</p>
                                </div>

                                <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                                    <button class="btn btn-primary px-4 py-2 rounded-pill" id="tocBtn"
                                        onclick="showTableOfContents()">@lang('Table of Contents')</button>
                                    <button class="btn btn-primary px-4 py-2 rounded-pill" id="fontSizeBtn"
                                        onclick="toggleFontSize()">@lang('Font Size')</button>
                                </div>

                                 <div class="bg-white rounded-3 shadow-lg p-3 epub-content-wrapper">
                                     <div class="fs-5 text-justify w-100" id="epubContent">
                                         <!-- EPUB content will be rendered here by epubjs -->
                                     </div>
                                 </div>

                                <div class="d-flex align-items-center mt-4 p-3 bg-light rounded-3">
                                    <button class="btn btn-primary me-3" onclick="previousPage()">{{ $prevArrow }} @lang('Previous')</button>
                                    <button class="btn btn-primary ms-3" onclick="nextPage()">@lang('Next') {{ $nextArrow }}</button>
                                </div>
                            </div>
                        @else
                            <span class="download-item no-preview">@lang('No preview available.')</span>
                        @endif
                        <h6>@lang('File :id', ['id' => $loop->iteration])</h6>
                        <div class="mb-2">
                            @if (Auth::check())
                                @if(!$ePub)
                                    <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                                       href="{{ getFile('/resources/' . $file->file_name) }}"
                                       target="_blank"
                                       data-file="{{ $file->id }}"
                                       data-resource="{{ $resource->id }}"
                                       data-action="download-counter"
                                    >
                                        <i class="ph-fill ph-download-simple"></i>
                                        <span>
                                            @lang('Download')
                                            <small class="d-block opacity-75">{{ formatBytes($file->file_size) }} · {{ $file->extension }}</small>
                                        </span>
                                    </a>
                                @endif
                            @else
                                <div class="alert alert-info py-1 px-2 d-inline-flex align-items-center gap-1 small">
                                    <i class="ph-light ph-lock"></i> @lang('Please login to download this file.')
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
                <div class="bg-light py-4 mb-4">
                    <div class="container">
                        <div class="row align-items-center gap-3">
                            <div class="col-md-3">
                                <img class="img-fluid rounded shadow-sm w-100 object-fit-cover resource-hero-img"
                                     style="max-height: 250px; font-size: 0;"
                                     src="{{ $resource->resourceFile ? getResourceImage($resource->resourceFile->name, true) : getImagefromResource($resource->abstract, '282x254') }}"
                                     alt="{{ $resource->title }}">
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h3 class="mb-2">{{ $resource->title }}</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        @if (isLibraryManager() || isAdmin())
                                            <a href="{{ route('resource.form.edit', $resource->id) }}"
                                               title="@lang('Edit')"
                                               class="text-decoration-none">
                                                <i class="ph-light ph-pencil-simple"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-link p-0 text-decoration-none" title="@lang('Mark this resource as your favorite')"
                                                id="resourceFavorite"
                                                data-action="favorite"
                                                data-resource="{{ $resource->id }}"
                                                data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                                            <i class="ph-{{ $resource->favorites->contains('id', auth()->id()) ? 'fill' : 'light' }} ph-star"></i>
                                        </button>
                                        <button class="btn btn-link p-0 text-decoration-none"
                                                data-bs-toggle="modal"
                                                data-bs-target="#shareModal"
                                                title="@lang('Share this resource')">
                                            <i class="ph-light ph-share-network"></i>
                                        </button>
                                        <button class="btn btn-link p-0 text-decoration-none"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reportModal"
                                                title="@lang('Report this resource')">
                                            <i class="ph-light ph-flag"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @foreach ($resource->subjects as $subject)
                                        <a class="badge text-bg-primary text-decoration-none fs-6"
                                           href="{{ URL::to('resources/list?subject_area=' . $subject->id) }}">
                                            {{ $subject->name }}
                                        </a>
                                    @endforeach
                                    @foreach ($resource->levels as $level)
                                        <a class="badge text-bg-secondary text-decoration-none fs-6"
                                           href="{{ URL::to('resources/list?level=' . $level->id) }}">
                                            {{ $level->name }}
                                        </a>
                                    @endforeach
                                    @foreach ($resource->LearningResourceTypes as $ltype)
                                        <a class="badge border border-primary text-primary text-decoration-none fs-6"
                                           href="{{ URL::to('resources/list?type=' . $ltype->id) }}">
                                            {{ $ltype->name }}
                                        </a>
                                    @endforeach
                                </div>
                                <div class="d-flex gap-3 text-muted small">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="ph-light ph-eye text-primary"></i> {{ $resource->views_count }} @lang('views')
                                    </span>
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="ph-light ph-star"></i> <span class="resource-favorites">{{ $resource->favorites_count }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="resource-view-title-box" class="mb-3">
                    {!! fixImage($resource->abstract, $resource->id) !!}
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="py-1 ps-1" id="meta-box-title">@lang('About this resource')</h5>
                    </div>

                    @if ($resource->authors->isNotEmpty())
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ph-light ph-user text-primary"></i>
                                <span class="fw-semibold small text-muted">@lang('Author')</span>
                            </div>
                            @foreach ($resource->authors as $author)
                                <span class="d-block">{{ $author->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if ($resource->publishers->isNotEmpty())
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ph-light ph-buildings text-primary"></i>
                                <span class="fw-semibold small text-muted">@lang('Publisher')</span>
                            </div>
                            @foreach ($resource->publishers as $publisher)
                                <a class="d-block text-decoration-none" href="{{ URL::to('resources/list?publisher=' . $publisher->id) }}">{{ $publisher->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if ($resource->translators->isNotEmpty())
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ph-light ph-translate text-primary"></i>
                                <span class="fw-semibold small text-muted">@lang('Translator')</span>
                            </div>
                            @foreach ($resource->translators as $translator)
                                <span class="d-block">{{ $translator->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if ($resource->creativeCommons->isNotEmpty())
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ph-light ph-copyright text-primary"></i>
                                <span class="fw-semibold small text-muted">@lang('License')</span>
                            </div>
                            <span class="badge text-bg-secondary">{{ $resource->creativeCommons->first()->name }}</span>
                        </div>
                    @endif

                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ph-light ph-globe text-primary"></i>
                            <span class="fw-semibold small text-muted">@lang('Available in')</span>
                        </div>
                        @foreach ($languages_available as $locale => $properties)
                            <a class="d-block text-decoration-none" rel="alternate" hreflang="{{ $locale }}"
                               href="{{ LaravelLocalization::getLocalizedURL($locale, $properties['url'], [], true) }}">
                                {{ $properties['native'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">@lang('Similar resources')</h6>
                    </div>
                    <div class="card-body p-2">
                        @foreach ($relatedItems as $item)
                            <a title="{{ $item->title }}" href="{{ URL::to('resource/' . $item->id) }}" class="text-decoration-none">
                                <div class="d-flex gap-2 p-2 rounded similar-resource-item mb-1">
                                    <div class="d-none d-lg-block flex-shrink-0">
                                        <img class="w-100 h-100 object-fit-cover"
                                             src="{{ getResourceImage($item->name, true) }}"
                                             alt="{{ $item->title }}">
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="d-block fw-semibold small text-dark text-truncate">{{ $item->title }}</span>
                                        <span class="d-block small text-muted">{{ Str::limit(strip_tags($item->abstract), 60) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body p-3">
                        @if (isAdmin() || isLibraryManager())
                            @if ($resource->user)
                                <p>@lang('Added by'):
                                    <a href="{{ route('user-view', $resource->user->id) }}">
                                        {{ $resource->user->username }}
                                    </a>
                                </p>
                            @endif

                            <form class="form-row" method="post" action="{{ route('updatetid', $resource->id) }}">
                                @honeypot
                                @csrf
                                <label for="link">
                                    {{ __('If this resource is translated, enter the translated resource id and click submit:') }}
                                </label>
                                <div class="row">
                                    <div class="col-sm-9">
                                        <input type="number" name="link" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="submit" class="btn btn-primary" value="@lang('Submit')">
                                    </div>
                                </div>
                            </form>

                            <div class="mt-2">
                                <strong>@lang('Linked resources:')</strong>
                            </div>
                            @foreach ($translations as $translation)
                                <div class="d-flex gap-1 mt-1 bg-secondary-subtle p-2">
                                    <div class="p-1">
                                        {{ $loop->iteration }}.
                                    </div>
                                    <div class="p-1">
                                        <a href="{{ URL::to($translation->language . '/resource/' . $translation->id) }}"
                                            target="_blank">
                                            {{ $translation->title }} ({{ $translation->language }})
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @include('resources.partials.comments')
        </div>
    </div>
    @include('resources.partials.share-modal')
    @include('resources.partials.report-modal')
@endsection
@if ($ePub)
    <div id="app" data-file-route="{{ $ePub }}"></div>
@endif

@push('scripts')
    @vite('resources/assets/js/modules/resource_view.jsx')
    @if ($ePub)
        @vite('resources/assets/js/epub.jsx')
    @endif
@endpush
