<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Traits\SitewidePageViewTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    use SitewidePageViewTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // setting the search session empty
        DDLClearSession();
        $languageCode = config('app.locale');
        $this->pageView($request, "Home Page $languageCode");

        $resources = new Resource;

        $subjectAreas = Cache::remember("subject_areas_{$languageCode}", 3600, fn() => $resources->subjectIconsAndTotal());
        $featured = Cache::remember("featured_collections_{$languageCode}", 3600, fn() => $resources->featuredCollections());

        $appLocale = app()->getLocale();
        Carbon::setLocale($appLocale);
        // $surveys = Survey::find(1);
        // $surveyQuestions = SurveyQuestion::where('survey_id', 1)->first();
        // $surveyQuestionOptions = SurveyQuestionOption::where('question_id', 1)->get();
        $howToVideoId = match($appLocale) {
            'en' => '-PgQmUX2vbs',
            'ps' => 'EhoGbreiCjo',
            default => '-JM5lzeDWrE',
        };

        return view('home', compact(
            'latestNews',
            'subjectAreas',
            'featured',
            'latestResources',
            'howToVideoId'
        ));
    }
}
