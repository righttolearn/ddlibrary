@extends('layouts.main')

@section('title')
    @lang('Reset Password') - @lang('Darakht-e Danesh Library')
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-1">@lang('Reset Password')</h4>
                        <p class="text-muted small mb-4">@lang('Enter your email and a new password below.')</p>

                        <form method="POST" action="{{ route('password.request') }}">
                            @honeypot
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('Email')</label>
                                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       id="email" name="email" type="email"
                                       value="{{ $email ?? old('email') }}"
                                       required autofocus>
                                @error('email')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">@lang('New Password')</label>
                                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       id="password" name="password" type="password"
                                       aria-describedby="passwordHelp" required>
                                <small id="passwordHelp" class="form-text text-muted">
                                    @lang('Must be 8 characters, with at least 1 special character and 1 digit.')
                                </small>
                                @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label">@lang('Confirm New Password')</label>
                                <input class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                       id="password-confirm" name="password_confirmation"
                                       type="password" required>
                                @error('password_confirmation')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                @lang('Reset Password')
                            </button>
                        </form>

                        <div class="text-center mt-3">
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
