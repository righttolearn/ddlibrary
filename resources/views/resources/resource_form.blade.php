@extends('layouts.main')
@section('content')
    <div class="container mt-3">
        <h3>{{ $edit ? __('Edit Resource') : __('Add Resource') }}</h3>
        <hr>
        <form method="POST"
              action="{{ $edit ? route('resource.save.edit', $resource->id) : route('resource.save') }}"
              enctype="multipart/form-data">
            @csrf
            <div class="accordion" id="resourceAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#step1">
                            @lang('Basic Information')
                        </button>
                    </h2>
                    <div id="step1" class="accordion-collapse collapse show" data-bs-parent="#resourceAccordion">
                        <div class="accordion-body">
                            @include('resources.partials.form_step1')
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step2">
                            @lang('Classification')
                        </button>
                    </h2>
                    <div id="step2" class="accordion-collapse collapse" data-bs-parent="#resourceAccordion">
                        <div class="accordion-body">
                            @include('resources.partials.form_step2')
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step3">
                            @lang('Rights & License')
                        </button>
                    </h2>
                    <div id="step3" class="accordion-collapse collapse" data-bs-parent="#resourceAccordion">
                        <div class="accordion-body">
                            @include('resources.partials.form_step3')
                        </div>
                    </div>
                </div>

            </div>

            @include('layouts.messages')
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary btn-lg">@lang('Submit')</button>
            </div>
        </form>
    </div>
    @include('resources.partials.image_manager_modal')
@endsection
@push('scripts')
    @vite([
        'resources/assets/js/modules/resource_form.jsx',
        'resources/assets/js/modules/image_manager.jsx',
    ])
@endpush
