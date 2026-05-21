@extends('layouts.main')
@section('title')
    @lang('Create or edit a resource - step 2')
@endsection
@section('content')
    <div class="container mt-3">
        <h3>@lang('Create or edit a resource - step 2 of 3')</h3>
        <hr>
        <form method="POST"
            action="@if ($edit) {{ route('edit2', $resource['id']) }}@else{{ route('step2') }} @endif"
            enctype="multipart/form-data">
            @csrf
            <div class=" col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="attachments">
                        @lang('Attachments')
                    </label>
                    <div class="d-flex gap-3 attachment-1">
                        <div class="flex-grow-1 align-items-center">
                            <input
                                class="form-control form-control-file {{ $errors->has('attachments') ? ' is-invalid' : '' }}"
                                id="attachments" name="attachments[]" type="file">
                        </div>
                        <div class="align-self-center">
                            <span class="fa fa-trash text-danger" onclick="removeAttachment('attachment-1')"></span>
                        </div>
                    </div>
                    <button type='button' class="add_more btn btn-link">@lang('Add more files')</button>
                    @if (isset($resource['attc']) and $edit)
                        <div class="bg-white py-3 px-2 rounded my-2">
                            @foreach ($resource['attc'] as $item)
                                <div class="d-flex gap-3 attachment-1 file-{{ $loop->iteration }}">
                                    <div class="align-self-center">{{ $loop->iteration }}.</div>
                                    <div class="flex-grow-1 align-items-center">
                                        <a
                                            href="{{ getFile('resources/' . $item['file_name']) }}" target="_blank">{{ $item['file_name'] }}</a>
                                    </div>
                                    <div class="align-self-center">
                                        <a href="{{ route('delete-file', ['resourceId' => $resource['id'], 'fileName' => $item['file_name']]) }}"
                                        onclick="return confirm(`{{ __('Are you sure to delete this file?')}}`)">
                                            <span class="fa fa-trash text-danger"></span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($errors->has('attachments'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('attachments') }}</strong>
                        </span><br>
                    @endif

                </div>
            </div>
            <div class="form-item col-sm-12 col-md-6">
                <label for="subject_areas">
                    <strong>@lang('Subject Areas') {{ en('Subject Areas') }}</strong>
                </label>
                <div class="searchable-select-wrapper" id="subject_areas_wrapper">
                    <input type="text" class="form-control form-control-sm mb-2" id="subject_areas_search" 
                           placeholder="@lang('Search')..." style="display: none;">
                    <select class="form-select{{ $errors->has('subject_areas') ? ' is-invalid' : '' }}" id="subject_areas"
                        name="subject_areas[]" size="10" required multiple>
                        @foreach ($subjects as $item)
                            @if ($item->parent == 0)
                                <optgroup label="{{ $item->name }}">
                                    <option value="{{ $item->id }}"
                                        {{ $resourceSubjectAreas != null ? (in_array($item->id, $resourceSubjectAreas) ? 'selected' : '') : '' }}>
                                        {{ $item->name }}</option>
                                    <?php if (isset($subjects) && isset($item)) {
                                        $parentItems = $subjects->where('parent', $item->id);
                                    }
                                    ?>
                                    @foreach ($parentItems as $pitem)
                                        <option value="{{ $pitem->id }}"
                                            {{ $resourceSubjectAreas != null ? (in_array($pitem->id, $resourceSubjectAreas) ? 'selected' : '') : '' }}>
                                            {{ $pitem->name . termEn($pitem->id) }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                @if ($errors->has('subject_areas'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('subject_areas') }}</strong>
                    </span><br>
                @endif
            </div>
            <div class="form-item col-sm-12 col-md-6">
                <label for="keywords">
                    <strong>@lang('Keywords') {{ en('Keywords') }}</strong>
                </label>
                <input class="form-control{{ $errors->has('keywords') ? ' is-invalid' : '' }}" id="keywords"
                    name="keywords" type="text" value="{{ isset($resource['keywords']) ? $resource['keywords'] : '' }}"
                    onkeydown="javascript:bringMeAttr('keywords','{{ URL::to('resources/attributes/keywords') }}')">
                <small id="authorOptional" class="form-text text-muted">
                    @lang('Optional')
                </small>
                @if ($errors->has('keywords'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('keywords') }}</strong>
                    </span><br>
                @endif
            </div>
            <div class="form-item col-sm-12 col-md-6">
                <label for="learning_resources_types">
                    <strong>@lang('Learning Resources Types') {{ en('Learning Resources Types') }}</strong>
                </label>
                <select class="form-control{{ $errors->has('learning_resources_types') ? ' is-invalid' : '' }}"
                    id="learning_resources_types" name="learning_resources_types[]" size="10" required multiple>
                    @foreach ($learningResourceTypes as $item)
                        <option value="{{ $item->id }}"
                            {{ $resourceLearningResourceTypes != null ? (in_array($item->id, $resourceLearningResourceTypes) ? 'selected' : '') : '' }}>
                            {{ $item->name . termEn($item->id) }}</option>
                    @endforeach
                </select>

                @if ($errors->has('learning_resources_types'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('learning_resources_types') }}</strong>
                    </span><br>
                @endif
            </div>
            <div class="form-item col-sm-12 col-md-6">
                <label for="educational_use">
                    <strong>@lang('Educational Use') {{ en('Educational Use') }}</strong>
                </label>
                <select class="form-control{{ $errors->has('educational_use') ? ' is-invalid' : '' }}" id="educational_use"
                    name="educational_use[]" size="5" required multiple>
                    @foreach ($educationalUse as $item)
                        <option value="{{ $item->id }}"
                            {{ $editEducationalUse != null ? (in_array($item->id, $editEducationalUse) ? 'selected' : '') : '' }}>
                            {{ $item->name . termEn($item->id) }}</option>
                    @endforeach
                </select>

                @if ($errors->has('educational_use'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('educational_use') }}</strong>
                    </span><br>
                @endif
            </div>

            <div class="form-item">
                <label for="resource_levels">
                    <strong>@lang('Resource Levels') {{ en('Resource Levels') }}</strong>
                </label>
                <ul style="list-style-type: none;">
                    <?php
                    if (!isset($resource['level'])) {
                        $resource['level'] = [];
                    }
                    ?>
                    @foreach ($levels as $level)
                        @if ($level->parent == 0)
                            <li><input type="checkbox" name="level[]"
                                    {{ $resourceLevels != null ? (in_array($level->id, $resourceLevels) ? 'checked' : '') : '' }}
                                    value="{{ $level->id }}" onchange="fnTest(this,'subLevel{{ $level->id }}');">
                                {{ $level->name . termEn($level->id) }}
                                <?php if (isset($levels) && isset($level)) {
                                    $levelParent = $levels->where('parent', $level->id);
                                } ?>
                                @if ($levelParent)
                                    <ul id="subLevel{{ $level->id }}" class="subItem" style="list-style-type: none;">
                                        @foreach ($levelParent as $item)
                                            <li><input type="checkbox" name="level[]"
                                                    onchange="fnTest(this,'subLevel{{ $item->id }}');"
                                                    {{ $resourceLevels != null ? (in_array($item->id, $resourceLevels) ? 'checked' : '') : '' }}
                                                    class="js-child" value="{{ $item->id }}">
                                                {{ $item->name . termEn($item->id) }}

                                                <?php $levelItemParent = $levels->where('parent', $item->id); ?>
                                                @if ($levelItemParent)
                                                    <ul id="subLevel{{ $item->id }}" class="subItem"
                                                        style="list-style-type: none;">
                                                        @foreach ($levelItemParent as $itemLevel)
                                                            <li><input type="checkbox" name="level[]"
                                                                    {{ $resourceLevels != null ? (in_array($itemLevel->id, $resourceLevels) ? 'checked' : '') : '' }}
                                                                    class="js-child" value="{{ $itemLevel->id }}">
                                                                {{ $itemLevel->name . termEn($itemLevel->id) }}</li>
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
            @include('layouts.messages')
            <div class="d-grid gap-2 d-md-block">
                <input class="btn btn-primary btn-md" type="button" value="@lang('Previous') {{ en('Previous') }}"
                    onclick="location.href='@if ($edit) {{ route('edit1', $resource['id']) }}@else{{ route('step1') }} @endif'">
                <input class="btn btn-primary btn-md" type="submit" value="@lang('Next') {{ en('Next') }}">
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        function removeAttachment(attachment) {
            if (confirm("{{ __('Are you sure to delete this file?')}}")) {
                $(`.${attachment}`).remove()
            }
        }
    </script>
@endpush
