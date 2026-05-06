@extends('layouts.main')
@section('title')
    @lang('Create or edit a resource - step 1')
@endsection
@section('content')
    <div class="container mt-3">
        <h3>@lang('Create or edit a resource - step 1 of 3')</h3>
        <hr>
        <form method="POST"
            action="@if ($edit) {{ route('edit1', $resource['id']) }}@else{{ route('step1') }} @endif">
            @csrf

            <div class="row">

                <div class="form-group col-sm-12 col-md-6 mb-3">
                    <label for="title">
                        @lang('Title')
                    </label>
                    <input class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" id="title" name="title"
                        type="text" value="{{ @$resource['title'] }}" required autofocus placeholder="@lang('Title')">
                    @if ($errors->has('title'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group ui-widget col-sm-12 col-md-6 mb-3">
                    <label for="author">
                        @lang('Author')
                    </label>
                    <input class="form-control{{ $errors->has('author') ? ' is-invalid' : '' }} col-md-6" id="author"
                        name="author" type="text" value="{{ $resource->authors?->pluck('name')->implode(', ') ?? '' }}"
                        aria-describedby="authorOptional"
                        onkeydown="bringMeAttr('author','{{ URL::to('resources/attributes/authors') }}')">
                    <small id="authorOptional" class="form-text text-muted">
                        @lang('Optional')
                    </small>
                    @if ($errors->has('author'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('author') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group ui-widget col-sm-12 col-md-6 mb-3">
                    <label for="publisher">
                        @lang('Publisher')
                    </label>
                    <input class="form-control{{ $errors->has('publisher') ? ' is-invalid' : '' }} col-md-6" id="publisher"
                        name="publisher" type="text"
                        value="{{ old('publisher') ? old('publisher') : $resource->publishers?->pluck('name')->implode(', ') }}"
                        aria-describedby="publisherOptional"
                        onkeydown="bringMeAttr('publisher','{{ URL::to('resources/attributes/publishers') }}')">
                    <small id="publisherOptional" class="form-text text-muted">
                        @lang('Optional')
                    </small>
                    @if ($errors->has('publisher'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('publisher') }}</strong>
                        </span>
                    @endif
                </div>
               


                <div class="form-group col-sm-12 col-md-6 mb-3">
                    <label for="language">
                        @lang('Language')
                    </label>
                    <select class="form-control{{ $errors->has('language') ? ' is-invalid' : '' }} col-md-6"
                        name="language" id="language" required>
                        <option value="">...</option>
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <option value="{{ $localeCode }}"
                                {{ @$resource['language'] == $localeCode ? 'selected' : '' }}>{{ $properties['native'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
               
                
                
                 <div class="form-group col-sm-12 col-md-6 mb-3">
                    <div>
                        <label for="toggle-translation">
                            @lang('This is a work of translation')
                        </label>
                        <div>
                            <input type="checkbox" onchange="toggleTranslation(this)" {{ old('translator') || $resource->translators?->pluck('name')->implode(', ') ? 'checked' : '' }}
                            id="toggle-translation" name="has_translator" value="1">
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-12 col-md-6 mb-3">
                    <div class="translation {{ old('translator') || $resource->translators?->pluck('name')->implode(', ') ? '' : 'd-none' }}">
                        <div>
                            <label for="translator" class="">
                                @lang('Translator')
                            </label>
                        </div>
                        <input class="form-control {{ $errors->has('translator') ? ' is-invalid' : '' }} col-md-6"
                            id="translator" name="translator" type="text"
                            value="{{ old('translator') ? old('translator') : $resource->translators?->pluck('name')->implode(', ') }}"
                            aria-describedby="translatorOptional" placeholder="@lang('Translator')"
                            onkeydown="bringMeAttr('translator','{{ URL::to('resources/attributes/translators') }}')">
                        @if ($errors->has('translator'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('translator') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-sm-12 col-md-6 mb-3">
                    <label for="image">
                        <strong>@lang('Image')</strong>
                        <span class="form-required" title="This field is required.">*</span>
                    </label>
                    <div>
                        <div class="flex-1">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="searchImages()"
                                data-bs-target="#exampleModal" id="open-file-managers">@lang('Select or upload your image')
                            </button>
                            <input type="hidden" value="{{ @$resource->resourceFile->id }}" id="resource_file_id"
                                name="resource_file_id" required>
                        </div>
                    </div>
                    @if ($errors->has('image'))
                        <span>
                            <strong>{{ $errors->first('image') }}</strong>
                        </span><br>
                    @endif
                </div>

                {{-- Selected Image Preview --}}
                <div class="form-group col-sm-12 col-md-6 mb-3">
                    <div id="selected-image-preview" class="flex-1 mt-1 border-radius-5 w-100 overflow-hidden"
                        style="display: {{ @$resource->resourceFile?->name ? 'block' : 'none' }};">
                        <img id="preview-image" src="{{ @$resource->resourceFile ? getResourceImage(@$resource->resourceFile->name, true) : '' }}" class="border-radius-5"
                            style="max-height: 250px;" alt="Selected Image">
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="abstract">
                        @lang('Abstract')
                    </label>
                    <div id="editor" class="mb-2">
                        <textarea class="form-control{{ $errors->has('abstract') ? ' is-invalid' : '' }}" name="abstract"
                            style="height: 200px" required>{{ @$resource['abstract'] }}</textarea>
                    </div>
                    @if ($errors->has('abstract'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('abstract') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            @include('layouts.messages')
            <input class="btn btn-primary" type="submit" value="@lang('Next')">
        </form>
    </div>


    <!-- File Manager Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="fileManagerLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileManagerLabel">@lang('Image manager')</h5>
                    <div class="text-{{ Lang::locale() == 'en' ? 'end' : 'start' }} flex-fill">

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body d-flex flex-column flex-lg-row">
                    <div class="image-manager-options d-flex flex-column p-1 bg-light border-end"
                        style="min-width: 250px;">
                        <button id="select-image-option" class="btn btn-outline-primary mb-2 active"
                            onclick="setActive('select')">@lang('Select image')</button>
                        <button id="upload-image-option" class="btn btn-outline-secondary mb-2"
                            onclick="setActive('upload')">@lang('Upload image')</button>
                        <button id="cropper-image-option" class="btn btn-outline-secondary"
                            onclick="setActive('crop')">@lang('Crop your image')</button>
                    </div>
                    <div class="image-manager-content flex-grow-1 p-3 overflow-auto">
                        <!-- Select Image Content -->
                        <div id="select-image-content">
                            <h3>@lang('Select image from file manager') <span id="result"></span></h3>
                            <div class="row mb-4">
                                <div class="col-md-12 col-xl-6">
                                    <label for="subject_areas" class="form-label">
                                        <strong>@lang('Subject Areas') {{ en('Subject Areas') }}</strong>
                                        <span class="text-danger" title="This field is required.">*</span>
                                    </label>
                                    <select class="form-select {{ $errors->has('subject_areas') ? 'is-invalid' : '' }}"
                                        id="subject_areas" name="subject_areas" onchange="searchImages()" required>
                                        <option value="">...</option>
                                        @foreach ($subjects as $item)
                                            @if ($item->parent == 0)
                                                <optgroup label="{{ $item->name }}">
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    <?php if (isset($subjects) && isset($item)) {
                                                        $parentItems = $subjects->where('parent', $item->id);
                                                    } ?>
                                                    @foreach ($parentItems as $pitem)
                                                        <option value="{{ $pitem->id }}">
                                                            {{ $pitem->name . termEn($pitem->id) }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subject_areas'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('subject_areas') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-12 col-xl-6">
                                    <label for="search-input" class="form-label">
                                        <strong>@lang('Search by image name')</strong>
                                        <span class="text-danger" title="This field is required.">*</span>
                                    </label>
                                    <input type="text" id="search-input" onkeyup="searchImages()"
                                        placeholder="@lang('Search by image name')" class="form-control">
                                </div>
                            </div>
                            <div id="file-list" class="w-100">
                                <!-- File items will be populated dynamically -->
                            </div>
                            <div id="loading-message" style="display: none;">@lang('Loading, please wait')</div>

                        </div>
                        <!-- Upload Image Content -->
                        <div id="upload-image-content" style="display: none;">
                            <h3>@lang('Upload New Image')</h3>
                            <form id="upload-form">
                                <div class="mb-3">
                                    <label for="image" class="form-label">
                                        <strong>@lang('Image')</strong>
                                        <span class="text-danger" title="This field is required.">*</span>
                                    </label>
                                    <input type="file" onchange="selectNewImage()" name="image"
                                        class="form-control" accept="image/*" id="image" required>
                                    <div class="w-100">
                                        <img id="preview" alt="Image Preview" style="max-height: 300px;display: none" class="mt-2">
                                    </div>
                                    <div id="dimensions"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="image-name" class="form-label">
                                        <strong>@lang('File name')</strong>
                                    </label>
                                    <input type="text" id="image-name" name="image_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="license" class="form-label">
                                        <strong>@lang('License')</strong>
                                    </label>
                                    <select class="form-select" name="taxonomy_term_data_id" id="license">
                                        <option value="">...</option>
                                        @foreach ($creativeCommons as $creativeCommon)
                                            <option value="{{ $creativeCommon->id }}">{{ $creativeCommon->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="language" value="{{ config('app.locale') }}">
                                <button type="submit" class="btn btn-primary">@lang('Upload')</button>
                            </form>
                        </div>
                        <div id="cropper-image-content" style="display: none;">
                            <h3>@lang('Crop your image')</h3>
                            <form id="cropper-form">
                                <div class="mb-3">
                                    <label for="cropper-image" class="form-label">
                                        <strong>@lang('Image')</strong>
                                        <span class="text-danger" title="This field is required.">*</span>
                                    </label>
                                    <input type="file" id="cropper-image" name="cropper_image" class="form-control"
                                        accept="image/*" required>
                                    <div id="cropper" class="mt-3" style="width: 100%; height: 300px;"></div>
                                    <button type="button" id="download-cropped-image" class="btn btn-primary mt-2"
                                        style="display: none;">@lang('Download')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @vite('resources/assets/js/resource.jsx')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"
        integrity="sha384-P65gU1u4/dZpqRQ0AVqW+DHPwXmNAR84Qk31dC95hjk0WatF1GsVF1zRm/0uB+o0" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css"
        integrity="sha384-1arqhTHsGLPVJdhZo8SAycbI+y5k+G7khi5bTZ4BxHJIpCfvWoeSDgXEXXRxB/9G" crossorigin="anonymous">
@endsection
