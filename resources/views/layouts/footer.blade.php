<footer id="main-footer" class="py-4 px-3">
    <div class="container pt-2 mt-3">
        <div class="row">
            <div class="col-md-4">
                <h4>@lang('Latest news')</h4><br>
                @foreach($latestNews AS $news)
                    <a href="{{ URL::to('news/'.$news->id) }}" title="{{ $news->title }}">
                        <p>{{ $news->title }}<br><span class="badge text-bg-secondary text-white fw-normal">{{ __($news->created_at->diffForHumans()) }}</span></p>
                    </a>
                @endforeach
            </div>
            <div class="col-md-4">
                <h4>@lang('Latest resources')</h4><br>
                @foreach($latestResources AS $resource)
                    <a href="{{ URL::to('resource/'.$resource->id) }}" title="{{ $resource->title }}">
                        <p>{{ $resource->title }}<br><span class="badge text-bg-secondary text-white fw-normal">{{ __($resource->updated_at->diffForHumans()) }}</span></p>
                    </a>
                @endforeach
            </div>
            <div class="col-md-4">
                <h4>@lang('Useful links')</h4><br>
                @if($menu)
                    @foreach ($menu->where('location', 'bottom-menu')->where('language', app()->getLocale()) as $bmenu)
                        <a href="{{ URL::to($bmenu->path) }}" title="{{ $bmenu->title }}">{{ $bmenu->title }}</a><br>
                    @endforeach
                @endif
            </div>
        </div>
        <hr class="mx-n4">
    </div>
    <div class="container">
        <div class="row align-items-center">
            @if($menu)
                <div class="col-sm-12 col-md-6 col-lg-8 mb-3 mb-md-0">
                    <ul class="footer-nav list-unstyled d-flex flex-wrap flex-column flex-md-row px-0 gap-3">
                        @foreach ($menu->where('location', 'footer-menu')->where('language', app()->getLocale()) as $fmenu)
                            <li>
                                <a href="{{ URL::to($fmenu->path) }}" title="{{ $fmenu->title }}">{{ $fmenu->title }}</a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('privacy-policy') }}" title="@lang('Privacy Policy')">@lang('Privacy Policy')</a>
                        </li>
                    </ul>
                </div>
            @endif
            <div class="col-sm-12 col-md-6 col-lg-4 text-md-end">
                <div class="d-flex flex-column flex-md-row justify-content-md-end align-items-end align-items-md-center gap-3">
                    <div class="app-badges d-flex flex-row gap-2 mb-3 mb-md-0">
                        <a href="https://play.google.com/store/apps/details?id=com.ddacademi.library" target="_blank" title="@lang('Get it on Google Play')">
                            <img src="{{ (Lang::locale() != 'en') ?  getFile('files/google-play-badge-fa.png') : getFile('files/google-play-badge-en.png') }}" alt="@lang('Google Play')" class="app-badge">
                        </a>
                        <a href="https://apps.apple.com/us/app/darakht-e-danesh-library/id6745165605" target="_blank" title="@lang('Download on the App Store')">
                            <img src="{{ getFile('files/app-store-badge-en.svg') }}" alt="@lang('App Store')" class="app-badge">
                        </a>
                    </div>

                    <div class="social-icons d-flex justify-content-end align-items-center gap-3">
                        <a href="https://www.instagram.com/darakhtedanesh" target="_blank" title="DDL Instagram" class="social-icon">
                            <i class="ph-light ph-instagram-logo"></i>
                        </a>
                        <a href="https://www.facebook.com/darakhtedanesh/" target="_blank" title="DDL Facebook" class="social-icon">
                            <i class="ph-light ph-facebook-logo"></i>
                        </a>
                        <a href="https://www.youtube.com/c/DarakhteDaneshLibrary" target="_blank" title="DDL Youtube" class="social-icon">
                            <i class="ph-light ph-youtube-logo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- Copyright --}}
        <div class="row mt-3">
            <div class="col text-center small copyright-muted-text">
                &copy; {{ date('Y') }} @lang('Darakht-e Danesh Library. All rights reserved.')
            </div>
        </div>
    </div>
</footer>
