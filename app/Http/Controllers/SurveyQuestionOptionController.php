<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyQuestionOptionController extends Controller
{
    public function index($survey_id, $id): View
    {
        $lang = config('app.locale');
        $question_self = SurveyQuestion::find($id);

        if (!$question_self) {
            abort(404);
        }
        $all_questions = SurveyQuestion::where(['tnid' => $question_self?->tnid, 'language' => $lang])->get();
        $all_question_ids = [];
        foreach ($all_questions as $question) {
            $all_question_ids[] = $question->id;
        }
        $questin_options = SurveyQuestionOption::whereIn('question_id', $all_question_ids)->get();
        $survey = $question_self->survey;

        return view('admin.surveys.option.list', compact('question_self', 'questin_options', 'survey'));
    }

    public function view(SurveyQuestionOption $options, $questionid, $id, $tnid): View
    {
        $question = SurveyQuestion::find($questionid);
        $survey = Survey::find($question->survey_id);
        $options = $options->where('tnid', $tnid)->get();
        $option_self = $options->find($id);

        return view('admin.surveys.option.view', compact('options', 'option_self', 'question', 'survey'));
    }

    public function create($survey_id, $question_id): View
    {
        $survey = Survey::find($survey_id);
        $question = SurveyQuestion::find($question_id);

        return view('admin.surveys.option.create', compact('survey', 'question'));
    }

    public function store(Request $request): RedirectResponse
    {
        $option = new SurveyQuestionOption;
        $option->question_id = $request['question_id'];
        $option->text = $request['text'];
        $option->language = $request['language'];
        $option->save();

        // update the tnid
        $created_option = SurveyQuestionOption::find($option->id);
        if ($request['tnid']) {
            $created_option->tnid = $request['tnid'];
        } else {
            $created_option->tnid = $option->id;
        }
        $created_option->save();

        return back()->with('status', 'Question Option Created!');
    }

    public function delete($id): RedirectResponse
    {
        $option = SurveyQuestionOption::find($id);
        $option->delete();

        return back()->with('status', 'Question\'s Option Deleted!');
    }

    public function addTranslate($tnid, $lang): View
    {
        $option = SurveyQuestionOption::where('id', $tnid)->first();
        $question = SurveyQuestion::where(['tnid' => $option->question_id, 'language' => $lang])->first();
        $survey = Survey::find($question?->survey_id);

        return view('admin.surveys.option.add_translation', compact('tnid', 'lang', 'question', 'survey'));
    }
}
