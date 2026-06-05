<div class="container-fluid bg-secondary text-center py-5">

    <h2 class="my-5 text-white">{!! __('Free and open educational<br>resources for Afghanistan') !!}</h2>

    <form class="justify-content-center row" method="GET" action="{{ route('resourceList') }}">
        <div class="col-md-7 col-12 my-2">
            <div class="input-group">
                <input type="text" id="search" name="search" class="form-control form-control-lg" placeholder="@lang('Search our growing library!')">
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request()->routeIs('resourceList'))
                    <button class="btn btn-primary ms-1 d-flex align-items-center"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#filterPanel"
                            title="@lang('Filter')">
                        <i class="ph-fill ph-sliders filter-icon"></i>
                    </button>
                @else
                    <a href="{{ route('resourceList') }}#filterPanel"
                       class="btn btn-primary ms-1 d-flex align-items-center"
                       title="@lang('Filter')">
                        <i class="ph-fill ph-sliders filter-icon"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
