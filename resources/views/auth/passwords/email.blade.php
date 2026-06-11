@extends('layouts.main')

@section('title')
    @lang('Reset your password') - @lang('Darakht-e Danesh Library')
    @endsection

    @section('content')
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="mb-1">@lang('Reset your password')</h4>
                            <p class="text-muted small mb-4">@lang('Enter your email and we will send you a password reset link.')</p>

                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            @include('layouts.messages')

                            <form method="POST" action="{{ route('password.email') }}" id="reset-password-form">
                                @honeypot
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label">@lang('Email')</label>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           id="email" name="email" type="email"
                                           value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                @if(config('app.captcha') == 'yes')
                                    <button class="btn btn-primary w-100 g-recaptcha"
                                            data-sitekey="{{ config('services.recaptcha_v3.site_key') }}"
                                            data-callback='onSubmit'
                                            data-action='register'>
                                        @lang('Send password reset link')
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary w-100">
                                        @lang('Send password reset link')
                                    </button>
                                @endif
                            </form>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    @lang('If you registered using a phone number, please contact us.')
                                </small>
                            </div>

                            <div class="text-center mt-2">
                                <small>
                                    <a href="{{ route('login') }}">@lang('Back to login')</a>
                                </small>
                            </div>
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
                    document.getElementById("reset-password-form").submit();
                }
            </script>
        @endif
    @endpush
