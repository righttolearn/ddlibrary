<?php

namespace App\Http\Controllers;

use App\Models\FeaturedSpotlight;
use App\Models\Resource;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
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
        $resources = new Resource;

        $spotlights = FeaturedSpotlight::active()->get();
        $subjectAreas = Cache::remember("subject_areas_{$languageCode}", 3600, function () use ($resources) {
            return $resources->subjectIconsAndTotal()->map(function ($subject) {
                $subject->total = Resource::countSubjectAreas($subject->id)?->total ?? 0;
                return $subject;
            });
        });
        $featured = Cache::remember("featured_collections_{$languageCode}", 3600, fn() => $resources->featuredCollections());

        // $surveys = Survey::find(1);
        // $surveyQuestions = SurveyQuestion::where('survey_id', 1)->first();
        // $surveyQuestionOptions = SurveyQuestionOption::where('question_id', 1)->get();
        $howToVideoId = match(config('app.locale')) {
            'en' => '-PgQmUX2vbs',
            'ps' => 'EhoGbreiCjo',
            default => '-JM5lzeDWrE',
        };

        return view('home', compact(
            'spotlights',
            'subjectAreas',
            'featured',
            'howToVideoId'
        ));
    }
}
