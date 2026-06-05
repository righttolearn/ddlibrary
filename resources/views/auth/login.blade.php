@extends('layouts.main')
@section('title')
    @lang('Log in to Darakht-e Danesh Library') - @lang('Darakht-e Danesh Library')
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
            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">@lang('Log in')</h3>
                        <form method="POST" id="login-form" action="{{ route('login') }}">
                            @honeypot
                            @csrf
                            <div class="mb-2">
                                <input id="email"
                                       class="form-control"
                                       name="email"
                                       autocomplete="username"
                                       spellcheck="false"
                                       placeholder="@lang('Email')"
                                       type="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                >
                            </div>
                            <div class="mb-3">
                                <input id="password"
                                       class="form-control"
                                       name="password"
                                       autocomplete="current-password"
                                       spellcheck="false"
                                       placeholder="@lang('Password')"
                                       type="password"
                                       required
                                >
                            </div>
                            @error('email')
                            <span class="text-danger bg-danger-subtle p-2 rounded d-block mb-2">
                                {{ $message }}
                            </span>
                            @enderror
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">@lang('Remember me')</label>
                            </div>
                            @if (config('app.captcha') == 'yes')
                                <input class="g-recaptcha btn btn-primary btn-md btn-block w-100 my-2"
                                       type="submit"
                                       data-sitekey="{{ config('services.recaptcha_v3.site_key') }}"
                                       data-callback='onSubmit'
                                       data-action='register'
                                       value="@lang('Log in')"
                                >
                            @else
                                <input class="btn btn-primary btn-md w-100 my-2"
                                       type="submit"
                                       value="@lang('Log in')"
                                >
                            @endif
                            <div class="d-flex gap-2 my-3">
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
                                        <i class="ph-fill ph-facebook-log btn-facebook"></i>
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
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">@lang('Forgot your password?')</a>
                                <a href="{{ route('register') }}" class="text-decoration-none">@lang('Sign up')</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if (config('app.captcha') == 'yes')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endif
    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
@endpush
