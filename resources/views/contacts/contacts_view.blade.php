@extends('layouts.main')

@section('title')
    @lang('Contact Us') - @lang('Darakht-e Danesh Online Library')
@endsection
@section('description')
    @lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection
@section('page_image')
    {{ getFile('public/img/logo-ddl-new.png') }}
@endsection

@section('content')
    <div class="container py-5">
        <div class="row gap-4">
            <div class="col-md-7">
                <h2 class="mb-4">@lang('Contact Us')</h2>

                @include('layouts.messages')

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('contact') }}" id="contact-form">
                            @honeypot
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">@lang('Full Name') <span class="text-danger">*</span></label>
                                <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                       id="name" name="name" type="text"
                                       value="{{ old('name', $fullname) }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('Email') <span class="text-danger">*</span></label>
                                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       id="email" name="email" type="email"
                                       value="{{ old('email', $email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">@lang('Subject') <span class="text-danger">*</span></label>
                                <input class="form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}"
                                       id="subject" name="subject" type="text"
                                       value="{{ old('subject') }}" required>
                                @error('subject')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label">@lang('Message') <span class="text-danger">*</span></label>
                                <textarea class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                          id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            @if(config('app.captcha') == 'yes')
                                <button type="submit" class="btn btn-primary w-100 g-recaptcha"
                                        data-sitekey="{{ config('services.recaptcha_v3.site_key') }}"
                                        data-callback='onSubmit'
                                        data-action='register'>
                                    @lang('Send')
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary w-100">
                                    @lang('Send')
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <h5 class="mb-2">
                            <i class="ph-light ph-presentation-chart me-2"></i>@lang('Schedule a Demo')
                        </h5>
                        <p class="text-muted mb-0">
                            @lang('Want to schedule a demo of the DD Library at your school, college or institution? Send us a request using the contact form on this page.')
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-2">
                            <i class="ph-light ph-envelope-simple me-2"></i>@lang('Newsletter')
                        </h5>
                        <p class="text-muted mb-3">
                            @lang('About three times a year we send out the DDL newsletter. If you are a registered user of the Library, you will automatically receive the newsletter.')
                        </p>
                        <a href="https://darakhtdanesh.us11.list-manage.com/subscribe?u=abbdaa95e801980b608399770&id=9bf90f679d"
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="ph-light ph-arrow-square-out me-1"></i>@lang('Subscribe to newsletter')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(config('app.captcha') == 'yes')
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script>
            function onSubmit(token) {
                document.getElementById("contact-form").submit();
            }
        </script>
    @endif
@endpush
