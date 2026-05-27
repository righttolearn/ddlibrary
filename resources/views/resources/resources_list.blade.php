@extends('layouts.main')
@section('title')
@lang('Latest Resources') - @lang('Darakht-e Danesh Online Library')
@endsection
@section('description')
@lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection
@section('page_image')
{{ asset('storage/files/logo-dd.png') }}
@endsection
@section('search')
    @include('layouts.search')
@endsection

@section('content')
    <div class="container-fluid px-5 pt-4" id="resource_list">
        @if (!$resources->isEmpty())
            <div class="row justify-content-center">
                @foreach ($resources AS $resource)
                    <div class="col-xl-6 col-lg-12 mb-3">
                        <div class="card border-0 shadow-sm resource-card h-100">
                            <div class="row g-0 h-100">
                                <div class="col-12 col-sm-3 col-xl-4">
                                    <img class="img-fluid h-100 object-fit-cover rounded-start lazyload"
                                         data-src="{{ $resource->name ? getResourceImage($resource->name, true) : getImagefromResource($resource->abstract, '282x254') }}"
                                         alt="{{ $resource->title }}" src="">
                                </div>
                                <div class="col-12 col-sm-9 col-xl-8">
                                    <div class="card-body py-2 px-3">
                                        <h6 class="card-title fw-bold mb-1">{{ $resource->title }}</h6>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            @if($resource->type)
                                                <span class="badge bg-primary">{{ $resource->type }}</span>
                                            @endif
                                            @if($resource->level)
                                                <span class="badge bg-secondary">{{ $resource->level }}</span>
                                            @endif
                                            @if($resource->language)
                                                <span class="badge border border-primary text-primary">{{ $resource->language }}</span>
                                            @endif
                                        </div>
                                        <p class="card-text text-muted small mb-1">
                                            @if($resource->author) {{ $resource->author }} @endif
                                            @if($resource->author && $resource->publisher) · @endif
                                            @if($resource->publisher) {{ $resource->publisher }} @endif
                                        </p>
                                        @if($resource->abstract)
                                            <p class="card-text small text-truncate-2">{{ Str::limit($resource->abstract, 150) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0 px-3 pb-2">
                                    <small class="text-muted d-flex gap-1 justify-content-end align-items-center">
                                        <span class="d-flex align-items-center gap-1"><i class="ph-fill ph-eye text-primary"></i> {{ $resource->views_count }}</span>
                                        <span class="d-flex align-items-center gap-1"><i class="ph-fill ph-chat-circle text-secondary"></i> {{ $resource->comments_count }}</span>
                                        <span class="d-flex align-items-center gap-1"><i class="ph-fill ph-star" style="color: gold;"></i> {{ $resource->favorites_count }}</span>
                                    </small>
                                </div>
                            </div>
                            <a href="{{ URL::to('resource/'.$resource->id) }}" class="stretched-link"></a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h2 class="text-center">@lang('No records found!')</h2>
        @endif
        <div class="d-flex justify-content-center mt-4">
            {{ $resources->appends(request()->input())->links() }}
        </div>
    </div>
@endsection
