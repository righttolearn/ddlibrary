<div class="container-fluid bg-secondary text-center py-5">

    <h2 class="my-5 text-white">{!! __('Free and open educational<br>resources for Afghanistan') !!}</h2>

    <form class="justify-content-center row" method="GET" action="{{ route('resourceList') }}">
        <div class="col-md-7 col-12 my-2">
            <div class="input-group">
                <input type="text" id="search" name="search" class="form-control form-control-lg" placeholder="@lang('Search our growing library!')">
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('resourceFilter') }}" class="btn btn-primary {{ (Lang::locale() != 'en') ? 'me-1' : 'ms-1' }} btn-lg" title="@lang('Filter')">
                    <i class="fas fa-sliders"></i>
                </a>
            </div>
        </div>
    </form>
</div>
