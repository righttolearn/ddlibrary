<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description')">
    <!-- Twitter Card data -->
    <meta name="twitter:card" value="@yield('description')">

    <!-- Open Graph data -->
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ URL::current() }}" />
    <meta property="og:image" content="@yield('page_image')" />
    <meta property="og:description" content="@yield('description')" />

    <link rel="shortcut icon" href="{{ getFile('files/favicon.ico', now()->addMinutes(5)) }}">

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.jsx'])

    <script>
        let baseUrl = "{{ url('/') }}";
        let localLanguage = "{{ config('app.locale') }}";
    </script>

    @if (App::environment('production'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-QZK5S8JQJN"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-QZK5S8JQJN');
        </script>
    @endif

    <!-- Matomo -->
    <script>
      var _paq = window._paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      @auth
      _paq.push(['setUserId', '{{ hash('sha256', auth()->id() . config('app.key')) }}']);
      @endauth
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//{{ config('services.matomo.url') }}/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '{{ config('services.matomo.site_id') }}']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->
    @stack('styles')
</head>

<body>
    <div id="page-container">
        <!-- Facebook Chat Integration - Start -->
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v19.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.async = true;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <!-- Your customer chat code -->
        <div class="fb-customerchat" attribution=setup_tool page_id="1540661852875335" theme_color="#ffa800"
            logged_in_greeting="به کتابخانه درخت دانش خوش آمدید. چگونه می توانیم به شما کمک کنیم؟"
            logged_out_greeting="به کتابخانه درخت دانش خوش آمدید. چگونه می توانیم به شما کمک کنیم؟">
        </div>
        <!-- Facebook Chat Integration - End -->
        @include('layouts.banner')
        @yield('search')
        <main>
{{--            @php--}}
{{--            $lang = config('app.locale');--}}
{{--            $questions_count = \App\Models\SurveyQuestion::getPublishedQuestions($lang)->count();--}}
{{--            @endphp--}}
{{--            @if ($questions_count != 0)--}}
{{--                @if (Request::is(Lang::locale() . '/home'))--}}
{{--                    @include('../survey/survey_view')--}}
{{--                @elseif (Request::is(Lang::locale()))--}}
{{--                    @include('../survey/survey_view')--}}
{{--                @elseif (Request::is(Lang::locale() . '/resource/*'))--}}
{{--                    @include('../survey/survey_view')--}}
{{--                @elseif (Request::is(Lang::locale() . '/resources/*'))--}}
{{--                    @include('../survey/survey_view')--}}
{{--                @endif--}}
{{--            @endif--}}

            @yield('content')
            @if (session()->has('alert'))
                <x-alert :message="Session::get('alert.message')" :level="Session::get('alert.level')" />
            @endif

        </main>
        @yield('offcanvas')
        @include('layouts.footer')
    </div>
    @stack('scripts')

    @if (Auth::check() && Auth::user()->profile->gender == null)
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" 
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">@lang('Please select your gender')</h1>
                    </div>
                    <form action="{{ route('update.gender') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="gender">@lang('Gender')</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled selected>@lang('Select one of these')</option>
                                    <option value="Male">@lang('Male')</option>
                                    <option value="Female">@lang('Female')</option>
                                    <option value="None">@lang('Prefer not to say')</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary open-user-gender-modal d-none" data-bs-toggle="modal" data-bs-target="#staticBackdrop"></button>
        <script>
            setTimeout(() => {
                $(".open-user-gender-modal").trigger('click')
            }, 100);
        </script>
    @endif
</body>

</html>
