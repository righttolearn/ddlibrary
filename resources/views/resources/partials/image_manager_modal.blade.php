<!-- File Manager Modal -->
<div class="modal fade" id="imageManagerModal" tabindex="-1" aria-labelledby="fileManagerLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileManagerLabel">@lang('Image manager')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column flex-lg-row">
                <div class="image-manager-options d-flex flex-column p-2 bg-light border-end">
                    <div class="nav flex-column nav-pills" id="imageManagerTabs" role="tablist">
                        <button class="nav-link active text-nowrap mb-2" data-bs-toggle="pill" data-bs-target="#select-image-content" type="button">
                            <i class="ph-light ph-images"></i> @lang('Select image')
                        </button>
                        <button class="nav-link text-nowrap mb-2" data-bs-toggle="pill" data-bs-target="#upload-image-content" type="button">
                            <i class="ph-light ph-upload-simple"></i> @lang('Upload image')
                        </button>
                        <button class="nav-link text-nowrap" data-bs-toggle="pill" data-bs-target="#cropper-image-content" type="button">
                            <i class="ph-light ph-crop"></i> @lang('Crop your image')
                        </button>
                    </div>
                </div>
                <!-- Select Image Content -->
                <div class="tab-content flex-grow-1 p-3 overflow-auto">
                    <div class="tab-pane fade show active" id="select-image-content">
                        <h4>@lang('Select image from file manager')</h4>
                        <div class="row mb-4">
                            <div class="col-md-12 col-xl-6">
                                <label for="subject_areas" class="form-label">
                                    <strong>@lang('Subject Areas')</strong>
                                    @if(Lang::locale() != 'en')
                                        <i class="ph-light ph-info"
                                           data-bs-toggle="tooltip"
                                           title="Subject Areas">
                                        </i>
                                    @endif
                                </label>
                                <select class="form-select"
                                        id="subject_areas"
                                        name="subject_areas"
                                        data-action="search-images">
                                    <option value="">...</option>
                                    @foreach ($subjects as $item)
                                        @if ($item->parent == 0)
                                            <optgroup label="{{ $item->name }}">
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @foreach ($item->children as $pitem)
                                                    <option value="{{ $pitem->id }}">
                                                        {{ $pitem->name . termEn($pitem->id) }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6">
                                <label for="search-input" class="form-label">
                                    <strong>@lang('Search by image name')</strong>
                                </label>
                                <input type="text"
                                       id="search-input"
                                       data-action="search-images"
                                       placeholder="@lang('Search by image name')"
                                       class="form-control">
                            </div>
                        </div>
                        <div id="file-list" class="w-100">
                            <!-- File items will be populated dynamically -->
                        </div>
                        <div id="loading-message" class="d-none text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">@lang('Loading...')</span>
                            </div>
                        </div>
                    </div>
                    <!-- Upload Image Content -->
                    <div class="tab-pane fade" id="upload-image-content">
                        <h4>@lang('Upload New Image')</h4>
                        <form id="upload-form">
                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    <strong>@lang('Image')</strong>
                                </label>
                                <input type="file"
                                       data-action="select-new-image"
                                       name="image"
                                       class="form-control"
                                       accept="image/*"
                                       id="image">
                                <div class="w-100">
                                    <img id="preview" alt="Image Preview" class="mt-2 d-none" style="max-height: 300px;">
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
                    <div class="tab-pane fade" id="cropper-image-content">
                        <h4>@lang('Crop your image')</h4>
                        <form id="cropper-form">
                            <div class="mb-3">
                                <label for="cropper-image" class="form-label">
                                    <strong>@lang('Image')</strong>
                                    <span class="text-danger" title="This field is required.">*</span>
                                </label>
                                <input type="file" id="cropper-image" name="cropper_image" class="form-control"
                                    accept="image/*">
                                <div id="cropper" class="mt-3"></div>
                                <button type="button" id="download-cropped-image" class="btn btn-primary mt-2 d-none">
                                    @lang('Use this image')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
