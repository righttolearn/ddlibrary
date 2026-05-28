{{-- resources/partials/form_step2.blade.php --}}
<div class="row">
    <div class="col-sm-12 col-md-6 mb-3">
        <label for="attachments" class="form-label">@lang('Attachments')</label>
        <div class="d-flex gap-3 mb-2 attachment-1">
            <div class="flex-grow-1">
                <input class="form-control{{ $errors->has('attachments') ? ' is-invalid' : '' }}"
                       id="attachments" name="attachments[]" type="file">
            </div>
            <div class="align-self-center">
                <button type="button"
                        class="btn btn-link p-0 text-danger remove-attachment text-decoration-none"
                        data-target="attachment-1"
                        data-confirm="@lang('Are you sure you want to remove this attachment?')">
                    <i class="ph-light ph-trash"></i>
                </button>
            </div>
        </div>
        <button type="button" class="add_more btn btn-link ps-0 text-decoration-none">
            <i class="ph-light ph-plus"></i> @lang('Add more files')
        </button>

        @if (isset($resource['attc']) && $edit)
            <div class="bg-light py-3 px-2 rounded my-2">
                @foreach ($resource['attc'] as $item)
                    <div class="d-flex gap-3 mb-2 file-{{ $loop->iteration }}">
                        <div class="align-self-center text-muted small">{{ $loop->iteration }}.</div>
                        <div class="flex-grow-1">
                            <a href="{{ asset('/storage/attachments/' . $item['file_name']) }}" target="_blank">
                                {{ $item['file_name'] }}
                            </a>
                        </div>
                        <div class="align-self-center">
                            <a href="{{ url('delete/file/' . $resource['id'] . '/' . $item['file_name']) }}"
                               class="text-danger text-decoration-none"
                               data-action="confirm-delete"
                               data-confirm="@lang('Are you sure you want to delete this file?')">
                                <i class="ph-light ph-trash"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @error('attachments')
        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 mb-3">
        <label for="subject_areas" class="form-label">
            <strong>@lang('Subject Areas')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip" title="Subject Areas"></i>
            @endif
        </label>
        <select class="form-select{{ $errors->has('subject_areas') ? ' is-invalid' : '' }}"
                id="subject_areas" name="subject_areas[]" required multiple>
            @foreach ($subjects as $item)
                @if ($item->parent == 0)
                    <optgroup label="{{ $item->name }}">
                        <option value="{{ $item->id }}"
                                {{ $resourceSubjectAreas != null ? (in_array($item->id, $resourceSubjectAreas) ? 'selected' : '') : '' }}>
                            {{ $item->name }}
                        </option>
                        @foreach ($item->children as $pitem)
                            <option value="{{ $pitem->id }}"
                                    {{ $resourceSubjectAreas != null ? (in_array($pitem->id, $resourceSubjectAreas) ? 'selected' : '') : '' }}>
                                {{ $pitem->name . termEn($pitem->id) }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            @endforeach
        </select>
        @error('subject_areas')
        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 mb-3">
        <label for="keywords" class="form-label">
            <strong>@lang('Keywords')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip" title="Keywords"></i>
            @endif
        </label>
        <input class="form-control{{ $errors->has('keywords') ? ' is-invalid' : '' }}"
               id="keywords" name="keywords" type="text"
               value="{{ old('keywords', $resourceKeywords) }}"
               data-action="autocomplete"
               data-url="{{ URL::to('resources/attributes/keywords') }}">
        <small class="form-text text-muted">@lang('Optional')</small>
        @error('keywords')
        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 mb-3">
        <label for="learning_resources_types" class="form-label">
            <strong>@lang('Learning Resources Types')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip" title="{{ en('Learning Resources Types') }}"></i>
            @endif
        </label>
        <select class="form-select{{ $errors->has('learning_resources_types') ? ' is-invalid' : '' }}"
                id="learning_resources_types" name="learning_resources_types[]" required multiple>
            @foreach ($learningResourceTypes as $item)
                <option value="{{ $item->id }}"
                        {{ $resourceLearningResourceTypes != null ? (in_array($item->id, $resourceLearningResourceTypes) ? 'selected' : '') : '' }}>
                    {{ $item->name . termEn($item->id) }}
                </option>
            @endforeach
        </select>
        @error('learning_resources_types')
        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 mb-3">
        <label for="educational_use" class="form-label">
            <strong>@lang('Educational Use')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip" title="{{ en('Educational Use') }}"></i>
            @endif
        </label>
        <select class="form-select{{ $errors->has('educational_use') ? ' is-invalid' : '' }}"
                id="educational_use" name="educational_use[]" required multiple>
            @foreach ($educationalUse as $item)
                <option value="{{ $item->id }}"
                        {{ $editEducationalUse != null ? (in_array($item->id, $editEducationalUse) ? 'selected' : '') : '' }}>
                    {{ $item->name . termEn($item->id) }}
                </option>
            @endforeach
        </select>
        @error('educational_use')
        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">
            <strong>@lang('Resource Levels')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip" title="{{ en('Resource Levels') }}"></i>
            @endif
        </label>
        <ul class="list-unstyled">
            @foreach ($levels as $level)
                @if ($level->parent == 0)
                    <li class="mb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="level[]"
                                   value="{{ $level->id }}"
                                   id="level_{{ $level->id }}"
                                   data-action="level-toggle"
                                   data-target="subLevel{{ $level->id }}"
                                    {{ $resourceLevels != null ? (in_array($level->id, $resourceLevels) ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="level_{{ $level->id }}">
                                {{ $level->name . termEn($level->id) }}
                            </label>
                        </div>
                        @if ($level->children->isNotEmpty())
                            <ul class="list-unstyled ms-4" id="subLevel{{ $level->id }}">
                                @foreach ($level->children as $child)
                                    <li class="mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input js-child" type="checkbox" name="level[]"
                                                   value="{{ $child->id }}"
                                                   id="level_{{ $child->id }}"
                                                   data-action="level-toggle"
                                                   data-target="subLevel{{ $child->id }}"
                                                    {{ $resourceLevels != null ? (in_array($child->id, $resourceLevels) ? 'checked' : '') : '' }}>
                                            <label class="form-check-label" for="level_{{ $child->id }}">
                                                {{ $child->name . termEn($child->id) }}
                                            </label>
                                        </div>
                                        @if ($child->children->isNotEmpty())
                                            <ul class="list-unstyled ms-4" id="subLevel{{ $child->id }}">
                                                @foreach ($child->children as $grandchild)
                                                    <li class="mb-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input js-child" type="checkbox"
                                                                   name="level[]"
                                                                   value="{{ $grandchild->id }}"
                                                                   id="level_{{ $grandchild->id }}"
                                                                    {{ $resourceLevels != null ? (in_array($grandchild->id, $resourceLevels) ? 'checked' : '') : '' }}>
                                                            <label class="form-check-label" for="level_{{ $grandchild->id }}">
                                                                {{ $grandchild->name . termEn($grandchild->id) }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
