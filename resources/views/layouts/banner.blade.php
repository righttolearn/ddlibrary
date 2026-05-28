<nav class="navbar navbar-expand-lg px-2 navbar-light border-bottom">
    <a href="{{ URL::to('/') }}" class="navbar-brand" title="Darakht-e Danesh Library logo">
        <img src="{{ Storage::url('logo-dd.png') }}" alt="DD Library logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="container-fluid">
            <div class="d-flex flex-column align-items-lg-end w-100">
                <ul class="navbar-nav align-items-lg-center justify-content-end">
                    @php
                        $currentLocale = LaravelLocalization::getCurrentLocale();
                        $currentPath = request()->path();
                        $redirectPath = null;
                        if ($pos = str_contains($currentPath, $currentLocale . '/')) {
                            $redirectPath = substr($currentPath, $pos + 1);
                        }
                    @endphp
                    <li class="nav-item @if(LaravelLocalization::getCurrentLocaleDirection() == 'ltr') border-end @else border-start @endif border-white lh-1">
                        <a rel="alternate"
                           href="{{ URL::to('/en'.$redirectPath) }}"
                           hreflang="en"
                           title="Language"
                           class="nav-link"
                        >
                            English
                        </a>
                    </li>
                    <li class="nav-item @if(LaravelLocalization::getCurrentLocaleDirection() == 'ltr') border-end @else border-start @endif border-white lh-1">
                        <a rel="alternate"
                           href="{{ URL::to('/fa'.$redirectPath) }}"
                           hreflang="fa"
                           title="Language"
                           class="nav-link"
                        >
                            فارسی
                        </a>
                    </li>
                    <li class="nav-item">
                        <a rel="alternate"
                           href="{{ URL::to('/ps'.$redirectPath) }}"
                           hreflang="ps"
                           title="Language"
                           class="nav-link"
                        >
                            پښتو
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-language"></i> @lang('Other languages')
                        </a>
                        <div class="dropdown-menu @if(LaravelLocalization::getCurrentLocaleDirection() == 'ltr') dropdown-menu-right @else dropdown-menu-left @endif" aria-labelledby="navbarDropdown">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @unless($localeCode == LaravelLocalization::getCurrentLocale())
                                    @if ($localeCode != 'en' and $localeCode != 'fa' and $localeCode != 'ps')
                                        <a rel="alternate"
                                           href="{{ URL::to('/'.$localeCode.$redirectPath) }}"
                                           hreflang="{{ $localeCode }}"
                                           title="Language"
                                           class="dropdown-item"
                                        >
                                            {{ $properties['native'] }}
                                        </a>
                                    @endif
                                @endunless
                            @endforeach
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::to('/resources') }}"><i class="fas fa-book"></i> @lang('Library')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_default']) }}" class="nav-link" title="StoryWeaver">
                            <img src="{{ getFile('public/img/storyweaver-logo.svg') }}"
                                 class="storyweaver-logo"
                                 alt="SW logo"
                            >
                            @lang('StoryWeaver')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('resource.form') }}"><i class="fas fa-upload"></i> @lang('Submit')</a>
                    </li>
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-circle-user fa-2xl"></i>
                            </a>
                            <div class="dropdown-menu @if(LaravelLocalization::getCurrentLocaleDirection() == 'ltr') dropdown-menu-end @else dropdown-menu-start @endif" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ URL::to('/user/profile') }}" title="@lang('Account')">@lang('Account')</a>
                                @if (isAdmin())
                                    <a class="dropdown-item" href="{{ URL::to('/admin') }}" title="@lang('Admin panel')">@lang('Admin panel')</a>
                                @endif
                                <hr class="dropdown-divider">
                                <a href="{{ URL::to('logout') }}" class="dropdown-item" title="@lang('Log out')">@lang('Log out')</a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ URL::to('/login') }}" class="btn btn-primary" title="@lang('Log in or Register')"><i class="fas fa-sign-in-alt"></i> @lang('Log in / Register')</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav>
