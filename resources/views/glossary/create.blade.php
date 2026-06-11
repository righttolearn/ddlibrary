@extends('layouts.main')
@section('title')
    @lang('Create new glossary item') - @lang('Darakht-e Danesh Library')
@endsection
@section('description')
    @lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">@lang('Add a new glossary item')</h4>
            </div>
            <div class="card-body">
                @include('layouts.messages')
                <div class="alert alert-info small">
                    <i class="ph-light ph-info"></i> @lang('One of the three fields (English, Farsi or Pashto) is required.')
                </div>
                <form method="POST" action="{{ route('glossary_store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="english" class="form-label fw-semibold">@lang('English')</label>
                        <textarea class="form-control" rows="3" name="english" id="english"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="farsi" class="form-label fw-semibold">@lang('Farsi')</label>
                        <textarea class="form-control" rows="3" name="farsi" id="farsi"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pashto" class="form-label fw-semibold">@lang('Pashto')</label>
                        <textarea class="form-control" rows="3" name="pashto" id="pashto"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">
                            @lang('Subject') <span class="text-danger">*</span>
                        </label>
                        <select name="subject" class="form-select" id="subject" required>
                            @foreach($glossary_subjects as $id => $subject)
                                <option value="{{ $id }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
