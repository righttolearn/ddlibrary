<div class="row">
    <div class="col-sm-12 col-md-6 mb-3">
        <label for="title" class="form-label">@lang('Title') <span class="text-danger">*</span></label>
        <input class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
               id="title"
               name="title"
               type="text"
               value="{{ old('title', $resource?->title ?? '') }}"
               required autofocus placeholder="@lang('Title')">
        @error('title')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6 mb-3">
        <label for="author" class="form-label">@lang('Author')</label>
        <input class="form-control{{ $errors->has('author') ? ' is-invalid' : '' }}"
               id="author" name="author" type="text"
               value="{{ old('author', $resource?->authors?->pluck('name')->implode(', ') ?? '') }}"
               aria-describedby="authorOptional"
               data-action="autocomplete"
               data-url="{{ URL::to('resources/attributes/authors') }}">
        <small id="authorOptional" class="form-text text-muted">@lang('Optional')</small>
        @error('author')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6 mb-3">
        <label for="publisher" class="form-label">@lang('Publisher')</label>
        <input class="form-control{{ $errors->has('publisher') ? ' is-invalid' : '' }}"
               id="publisher" name="publisher" type="text"
               value="{{ old('publisher', $resource?->publishers?->pluck('name')->implode(', ') ?? '') }}"
               aria-describedby="publisherOptional"
               data-action="autocomplete"
               data-url="{{ URL::to('resources/attributes/publishers') }}">
        <small id="publisherOptional" class="form-text text-muted">@lang('Optional')</small>
        @error('publisher')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6 mb-3">
        <label for="language" class="form-label">@lang('Language') <span class="text-danger">*</span></label>
        <select class="form-select{{ $errors->has('language') ? ' is-invalid' : '' }}"
                name="language" id="language" required>
            <option value="">...</option>
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <option value="{{ $localeCode }}" {{ old('language', $resource?->language ?? '') == $localeCode ? 'selected' : '' }}>
                    {{ $properties['native'] }}
                </option>
            @endforeach
        </select>
        @error('language')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6 mb-3">
        <label class="form-label">@lang('This is a work of translation')</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox"
                   id="toggle-translation" name="has_translator" value="1"
                   data-action="toggle-translation"
                    {{ old('translator', $resource?->translators?->pluck('name')->implode(', ')) ? 'checked' : '' }}>
            <label class="form-check-label" for="toggle-translation">@lang('Yes')</label>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 mb-3 translation {{ old('translator', $resource?->translators?->pluck('name')->implode(', ')) ? '' : 'd-none' }}">
        <label for="translator" class="form-label">@lang('Translator')</label>
        <input class="form-control{{ $errors->has('translator') ? ' is-invalid' : '' }}"
               id="translator" name="translator" type="text"
               value="{{ old('translator', $resource?->translators?->pluck('name')->implode(', ') ?? '') }}"
               placeholder="@lang('Translator')"
               data-action="autocomplete"
               data-url="{{ URL::to('resources/attributes/translators') }}">
        @error('translator')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6 mb-3">
        <label class="form-label">@lang('Image') <span class="text-danger">*</span></label>
        <div>
            <button type="button" class="d-flex btn btn-primary align-items-center gap-1" data-bs-toggle="modal"
                    data-bs-target="#imageManagerModal" id="open-file-managers">
                <i class="ph-light ph-image"></i> @lang('Select or upload your image')
            </button>
            <input type="hidden" value="{{ old('resource_file_id', $resource->resourceFile->id ?? '') }}"
                   id="resource_file_id" name="resource_file_id">
        </div>
        @error('image')
            <span class="text-danger small"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    {{-- Selected Image Preview --}}
    <div class="col-sm-12 col-md-6 mb-3">
        <div id="selected-image-preview" class="{{ $resource?->resourceFile?->name ? '' : 'd-none' }}">
            <img id="preview-image"
                 src="{{ $resource?->resourceFile ? getResourceImage($resource->resourceFile->name, true) : '' }}"
                 class="img-fluid rounded" style="max-height: 250px;" alt="@lang('Selected Image')">
        </div>
    </div>

    <div class="col-12 mb-3">
        <label for="abstract" class="form-label">@lang('Abstract') <span class="text-danger">*</span></label>
        <textarea class="form-control{{ $errors->has('abstract') ? ' is-invalid' : '' }}"
                  name="abstract" id="abstract" rows="6" required>{{ old('abstract', $resource?->abstract ?? '') }}</textarea>
        @error('abstract')
        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
