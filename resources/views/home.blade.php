@extends('layouts.main')
@section('title')
    @lang('Darakht-e Danesh Library')
@endsection
@section('description')
    @lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection
@section('page_image')
    {{ asset('storage/files/logo-dd.png') }}
@endsection
@section('search')
    @include('layouts.search')
@endsection
@section('content')
    <div class="container pt-3 px-0 text-center">
        <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_default']) }}" class="text-decoration-none">
            <div class="row align-items-center justify-content-between py-5">
                <div class="col-md-6">
                    <h1 class="text-primary fw-bold text-start">{!! __("Access children's<br>storybooks through<br>StoryWeaver") !!}</h1>
                </div>
                <div class="col-md-5">
                    <div class="card border-0">
                        <img src="{{ Storage::url('files/Girls_studying.png') }}"
                             class="card-img-top rounded-0 w-100 object-fit-cover"
                             alt="Girls studying"
                             style="height: 300px;"
                        >
                        <div class="card-body rounded-bottom-4 bg-secondary text-center fw-bold py-2">
                            <h3 class="text-white">{!! __("StoryWeaver<br>Library") !!}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <hr class="border-secondary border-5 opacity-100 mx-n4">
        <h2 class="p-2 mb-4">
            @lang('Explore our subjects')
        </h2>
        <div class="row justify-content-center py-4">
            @foreach($subjectAreas as $subject)
                <a href="{{ URL::to('resources/list?=&subject_area[]='.$subject->subject_area) }}"
                   title="{{ $subject->name }}"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <i class="{{ $subject->phosphor_icon }}"></i>
                        <p>{{ ucfirst(strtolower($subject->name)) }}</p>
                        <p class="resource-count">{{ App\Models\Resource::countSubjectAreas($subject->id)->total }}
                            @lang('Resources')</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    <div class="bg-light w-100">
        <div class="container pt-3 px-0 text-center">
            <h2 class="p-2 mb-4">
                @lang('Featured resource collections')
            </h2>
            <div class="row justify-content-center py-4">
                @foreach ($featured as $item)
                    <?php
                    if (isset($item)) {
                        if($item->url){
                            $url = URL::to($item->url);
                        }elseif($item->type_id){
                            $url = URL::to('resources/list?type='.$item->type_id);
                        }elseif($item->subject_id){
                            $url = URL::to('resources/list?subject_area='.$item->subject_id);
                        }elseif($item->level_id){
                            $url = URL::to('resources/list?level='.$item->level_id);
                        }else{
                            $url = URL::to('/');
                        }
                    }
                    ?>
                    <a href="{{ URL::to($url) }}"
                       title="{{ $item->name }}"
                       class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                    >
                        <div class="home-subject-areas">
                            <div class="bg-secondary rounded-4 p-3 d-inline-flex">
                                <i class="{{ $item->phosphor_icon }} text-white"></i>
                            </div>
                            <p>{{ $item->name }}</p>
                        </div>
                    </a>
                @endforeach
                <a href="{{ URL::to('glossary') }}"
                   title="Glossary"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <div class="bg-secondary rounded-4 p-3 d-inline-flex">
                            <i class="ph-light ph-book-bookmark text-white"></i>
                        </div>
                        <p>@lang('Glossary')</p>
                    </div>
                </a>

                <?php
                $covid_url = 'page/4137';
                if (Lang::locale() == 'fa') {
                    $covid_url = 'page/4133';
                } elseif (Lang::locale() == 'ps') {
                    $covid_url = 'page/4134';
                } elseif (Lang::locale() == 'pa') {
                    $covid_url = 'page/4135';
                } elseif (Lang::locale() == 'uz') {
                    $covid_url = 'page/4136';
                }
                ?>
                <a href="{{ URL::to($covid_url) }}"
                   title="COVID-19"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <div class="bg-secondary rounded-4 p-3 d-inline-flex">
                            <i class="ph-light ph-virus text-white"></i>
                        </div>
                        <p>@lang('COVID-19')</p>
                    </div>
                </a>
                @php
                    $newcomers_support_url = 'page/4141';
                @endphp
                <a href="{{ URL::to($newcomers_support_url) }}"
                   title="Newcomers support URL"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <div class="bg-secondary rounded-4 p-3 d-inline-flex">
                            <i class="ph-light ph-hands-clapping text-white"></i>
                        </div>
                        <p>@lang('Resources For Afghan Newcomers')</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="w-100">
        <div class="container py-4 text-center">
            <h2 class="p-2 mb-4">
                @lang('StoryWeaver Library')
            </h2>

            <div class="row justify-content-center my-4">
                <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_family_and_friends']) }}"
                   title="Family & friends"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <i class="ph-light ph-users"></i>
                        <p>@lang('Family & Friends')</p>
                    </div>
                </a>
                <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_growing_up']) }}"
                   title="Growing up"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <i class="ph-light ph-person-simple-walk"></i>
                        <p>@lang('Growing Up')</p>
                    </div>
                </a>
                <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_funny']) }}"
                   title="Funny"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <i class="ph-light ph-smiley"></i>
                        <p>@lang('Funny')</p>
                    </div>
                </a>
                <a href="{{ route('storyweaver-confirm', ['landing_page' => 'storyweaver_stem']) }}"
                   title="STEM"
                   class="col-6 col-sm-4 col-lg-2 text-center text-decoration-none"
                >
                    <div class="home-subject-areas">
                        <i class="ph-light ph-atom"></i>
                        <p>@lang('STEM')</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="bg-light w-100">
        <div class="container pt-3 px-0 text-center">
            <h2 class="mb-4 p-2">
                @lang('Quickstart videos')
            </h2>
            <div class="row mt-4 pb-4">
                <div class="col-md-6">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube-nocookie.com/embed/bF5dpED9W64"
                                title="@lang('Our work in Afghanistan')"
                                allow="clipboard-write; encrypted-media; picture-in-picture"
                                allowfullscreen
                                loading="lazy"
                        ></iframe>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube-nocookie.com/embed/{{ $howToVideoId }}"
                                title="@lang('How to use our library')"
                                allow="clipboard-write; encrypted-media; picture-in-picture"
                                allowfullscreen
                                loading="lazy"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
