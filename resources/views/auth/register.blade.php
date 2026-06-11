@extends('layouts.main')
@section('title')
    @lang('Register an account') - @lang('Darakht-e Danesh Library')
@endsection
@section('description')
    @lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection
@section('page_image')
    {{ getFile('public/img/logo-ddl-new.png') }}
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-4">@lang('Sign up')</h4>
                        <div class="d-flex gap-2 mb-3">
                            @if(config('app.google_sso_enabled'))
                                <a href="{{ route('login.google') }}"
                                   class="btn border flex-grow-1 d-flex align-items-center justify-content-center gap-2 bg-white">
                                    <i class="ph-fill ph-google-logo btn-google"></i>
                                    @lang('Continue with Google')
                                </a>
                            @else
                                <button type="button"
                                        class="btn border flex-grow-1 d-flex align-items-center justify-content-center gap-2 bg-white"
                                        disabled>
                                    <i class="ph-fill ph-google-logo btn-google"></i>
                                    @lang('Continue with Google')
                                </button>
                            @endif

                            @if(config('app.facebook_sso_enabled'))
                                <a href="{{ route('login.facebook') }}"
                                   class="btn border flex-grow-1 d-flex align-items-center justify-content-center gap-2 bg-white">
                                    <i class="ph-fill ph-facebook-logo btn-facebook"></i>
                                    @lang('Continue with Facebook')
                                </a>
                            @else
                                <button type="button"
                                        class="btn border flex-grow-1 d-flex align-items-center justify-content-center gap-2 bg-white"
                                        disabled>
                                    <i class="ph-fill ph-facebook-logo btn-facebook"></i>
                                    @lang('Continue with Facebook')
                                </button>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <hr class="flex-grow-1">
                            <small class="text-muted">@lang('Or, sign up using your email')</small>
                            <hr class="flex-grow-1">
                        </div>

                        <form method="POST" action="{{ route('register') }}" id="register-form">
                            @honeypot
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('Email')</label>
                                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       id="email" name="email" type="email"
                                       value="{{ old('email') }}" required autofocus>
                                <small class="form-text text-muted">
                                    @lang('Your email will be treated as confidential information and will be used to reset your password and communicate to you. If you do not own an email address, <a href=":gmail_signup_url" target="_blank">click here</a> to create one.', ['gmail_signup_url' => $gmail_signup_url])
                                </small>
                                @error('email')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">@lang('Password')</label>
                                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       id="password" name="password" type="password" required>
                                <small class="form-text text-muted">
                                    @lang('Must be 8 characters, with at least 1 special character and 1 digit.')
                                </small>
                                @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">@lang('Password (confirm)')</label>
                                <input class="form-control" id="password_confirmation"
                                       name="password_confirmation" type="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">@lang('First name')</label>
                                <input class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name') }}" type="text" required>
                                @error('first_name')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">@lang('Last name')</label>
                                <input class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                       id="last_name" name="last_name"
                                       value="{{ old('last_name') }}" type="text">
                                <small class="form-text text-muted">@lang('Optional')</small>
                                @error('last_name')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">@lang('Gender')</label>
                                <select class="form-select{{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                        name="gender" id="gender" required>
                                    <option value="">@lang('Select an option')</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>@lang('Male')</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>@lang('Female')</option>
                                    <option value="None" {{ old('gender') == 'None' ? 'selected' : '' }}>@lang('Prefer not to say')</option>
                                </select>
                                @error('gender')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="country" class="form-label">@lang('Country')</label>
                                <select class="form-select{{ $errors->has('country') ? ' is-invalid' : '' }}"
                                        name="country" id="country"
                                        data-action="populate-city"
                                        data-provinces="{{ json_encode($provinces) }}"
                                        required>
                                    <option value="">@lang('Select an option')</option>
                                    @foreach($countries as $cn)
                                        <option value="{{ $cn->tnid }}" {{ old('country') == $cn->tnid ? 'selected' : '' }}>
                                            {{ $cn->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="city" class="form-label">@lang('City')</label>
                                <select class="form-select{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                        name="city" id="city">
                                    <option value="">@lang('Select an option')</option>
                                </select>
                                <input type="text" class="form-control d-none mt-2"
                                       name="city_other" id="js-text-city">
                                <small class="form-text text-muted">@lang('Optional')</small>
                                @error('city')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            @if(config('app.captcha') == 'yes')
                                <button type="submit" class="btn btn-primary w-100 g-recaptcha"
                                        data-sitekey="{{ config('services.recaptcha_v3.site_key') }}"
                                        data-callback='onSubmit'
                                        data-action='register'>
                                    @lang('Sign up')
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary w-100">
                                    @lang('Sign up')
                                </button>
                            @endif
                        </form>

                        <div class="text-center mt-3">
                            <small>@lang('Already have an account?')
                                <a href="{{ route('login') }}" class="text-decoration-none">@lang('Log in')</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/modules/auth.jsx')
    @if (config('app.captcha') == 'yes')
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script>
            function onSubmit(token) {
                document.getElementById("register-form").submit();
            }
        </script>
    @endif
@endpush
