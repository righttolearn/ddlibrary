<div class="row">
    @php
        $cc_common = $resource?->creativeCommons?->first()?->name ?? '';
    @endphp

    <div class="col-12 mb-4">
        <label class="form-label fw-semibold">@lang('Please select one of the following')</label>
        <div class="card border mb-2">
            <div class="card-body">
                <div class="form-check {{ Lang::locale() != 'en' ? 'form-check-reverse' : '' }}">
                    <input class="form-check-input" type="radio" value="translation"
                           name="resource_rights" id="translation_rights"
                            {{ $resource?->TranslationRights ? 'checked' : '' }}>
                    <label class="form-check-label" for="translation_rights">
                        <strong>@lang('Translation Rights')</strong><br>
                        @lang('I am providing a new translation. I have selected the license that appears on the original resource.')
                        @lang('If this is not translation, please skip this question and go to #2.')
                        @if(Lang::locale() != 'en')
                            <small class="d-block text-muted">
                                {{ en('I am providing a new translation. I have selected the license that appears on the original resource If this is not translation, please skip this question and go to #2') }}
                            </small>
                        @endif
                    </label>
                </div>
                <hr>
                <div class="form-check {{ Lang::locale() != 'en' ? 'form-check-reverse' : '' }}">
                    <input class="form-check-input" type="radio" value="educational"
                           name="resource_rights" id="educational_resource"
                            {{ $resourceRights === 'translation' ? 'checked' : '' }}>
                    <label class="form-check-label" for="educational_resource">
                        <strong>@lang('Educational Resource')</strong><br>
                        @lang('I am submitting a resource to DDL that is already published. I have selected the license that is on the original resource.')
                        @lang('If you are the original author, please skip this question and go to #3.')
                        @if(Lang::locale() != 'en')
                            <small class="d-block text-muted">
                                {{ en('I am submitting a resource to DDL that is already published. I have selected the license that is on the original resource. If you are the original author, please skip this question and go to #3.') }}
                            </small>
                        @endif
                    </label>
                </div>
                <hr>
                <div class="form-check {{ Lang::locale() != 'en' ? 'form-check-reverse' : '' }}">
                    <input class="form-check-input" type="radio" value="author"
                           name="resource_rights" id="iam_author"
                            {{ $resource?->iamAuthors ? 'checked' : '' }}>
                    <label class="form-check-label" for="iam_author">
                        <strong>@lang('I am the author')</strong><br>
                        @lang('I am the author and I am submitting my resource to DDL. I am selecting a creative commons license for my resource below.')
                        @if(Lang::locale() != 'en')
                            <small class="d-block text-muted">
                                {{ en('I am the author and I am submitting my resource to DDL. I am selecting a creative commons license for my resource below') }}
                            </small>
                        @endif
                    </label>
                </div>
            </div>
        </div>
        @error('resource_rights')
        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 mb-4">
        <label for="copyright_holder" class="form-label">
            <strong>@lang('License/Copyright Holder')</strong>
            @if(Lang::locale() != 'en')
                <i class="ph-light ph-info" data-bs-toggle="tooltip"
                   title="{{ en('License/Copyright Holder') }}"></i>
            @endif
        </label>
        <input class="form-control{{ $errors->has('copyright_holder') ? ' is-invalid' : '' }}"
               id="copyright_holder" name="copyright_holder" type="text"
               aria-describedby="licenseHelp"
               value="{{ old('copyright_holder', $resourceCopyrightHolder) }}">
        <small id="licenseHelp" class="form-text text-muted">
            @lang('Please enter the name of the person or organization owning or managing rights over the resource.')
        </small>
        @error('copyright_holder')
        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <h5>@lang('Select one of these')</h5>
    </div>

    <div class="col-sm-12 col-md-6 mb-4">
        <label class="form-label">
            <strong>@lang('If there is Creative Commons License on the resource, select one of these')</strong>
        </label>
        @foreach($creativeCommons as $cc)
            @if(in_array($cc->tnid, [535, 536, 537, 159, 6187]))
                <div class="form-check {{ Lang::locale() != 'en' ? 'form-check-reverse' : '' }}">
                    <input class="form-check-input" type="radio" value="{{ $cc->id }}"
                           name="creative_commons" id="radio_for_{{ $cc->id }}"
                           data-action="cc-select"
                            {{ $resourceCreativeCommons == $cc->id ? 'checked' : '' }}>
                    <label class="form-check-label" for="radio_for_{{ $cc->id }}">
                        {{ $cc->name }}
                    </label>
                </div>
            @endif
        @endforeach
        <a href="#" class="small text-decoration-none" style="display:none;" data-action="cc-reset" data-target="creative_commons">
            @lang('Clear selection')
        </a>
        <small class="form-text text-muted mt-2 d-block">
            @lang('Unsure of which option to select?')
            <a href="{{ URL::to('/page/2252') }}" title="@lang('Copyright help')">@lang('Click here')</a>
            @lang('for guidance on licensing this resource.')
        </small>
    </div>

    <div class="col-sm-12 col-md-6 mb-4">
        <label class="form-label">
            <strong>@lang('If there is no Creative Commons License on the resource, select one these:')</strong>
        </label>
        @foreach($creativeCommons as $other)
            @if(!in_array($other->tnid, [535, 536, 537, 159, 6187]))
                <div class="form-check {{ Lang::locale() != 'en' ? 'form-check-reverse' : '' }}">
                    <input class="form-check-input" type="radio" value="{{ $other->id }}"
                           name="creative_commons_other" id="radio_for_{{ $other->id }}"
                           data-action="cc-other-select"
                            {{ $resourceSharePermissions == $other->id ? 'checked' : '' }}>
                    <label class="form-check-label" for="radio_for_{{ $other->id }}">
                        {{ $other->name . termEn($other->id) }}
                    </label>
                </div>
            @endif
        @endforeach
        <a href="#" class="small text-decoration-none" style="display:none;" data-action="cc-reset" data-target="creative_commons_other">
            @lang('Clear selection')
        </a>
    </div>

    @if (isAdmin() || isLibraryManager())
        <div class="col-sm-12 col-md-6 mb-4">
            <label class="form-label"><strong>@lang('Published?')</strong></label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="published"
                           id="no-pub" value="0"
                            {{ ($resource['status'] ?? 0) == 0 ? 'checked' : '' }}>
                    <label class="form-check-label" for="no-pub">@lang('No')</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="published"
                           id="yes-pub" value="1"
                            {{ ($resource['status'] ?? 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes-pub">@lang('Yes')</label>
                </div>
            </div>
        </div>
    @endif
</div>
