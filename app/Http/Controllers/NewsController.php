<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Traits\SitewidePageViewTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Yajra\Datatables\Datatables;

class NewsController extends Controller
{
    use SitewidePageViewTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function index(): View
    {
        return view('admin.news.news_list');
    }

    // Ajax get News Function
    public function getNews()
    {
        $news = News::select(['id', 'title', 'language', 'created_at', 'updated_at']);

        return Datatables::of($news)
            ->addColumn('action', function ($news) {
                return '<a href="'.URL($news->language.'/news/edit/'.$news->id).'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>';
            })
            ->editColumn('language', '{{fixLanguage($language)}}')
            ->addColumn('created_at', function ($page) {
                return $page->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($page) {
                return $page->updated_at->diffForHumans();
            })
            ->orderColumn('id', '-id $1')
            ->make(true);
    }

    public function view(Request $request, $newsId): View
    {
        // setting the search session empty
        DDLClearSession();

        $myNews = new News;

        $news = News::find($newsId);
        if (! $news) {
            abort(404);
        }
        $this->pageView($request, $news->title);

        $translation_id = $news->tnid;
        if ($translation_id) {
            $translations = News::where('tnid', $translation_id)->get();
        } else {
            $translations = [];
        }

        return view('news.news_view', compact('news', 'translations'));
    }

    public function create(): View
    {
        // setting the search session empty
        DDLClearSession();

        return view('news.news_create');
    }

    public function store(Request $request, News $news): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $news->title = $request->input('title');
        $news->summary = fixImage($request->input('summary'), '', false, true);
        $news->body = fixImage($request->input('body'), '', false, true);
        $news->language = $request->input('language');
        $news->user_id = Auth::id();
        $news->status = $request->input('published');
        // inserting
        $news->save();

        $news = News::find($news->id);
        $news->tnid = $news->id;
        // updating with tnid
        $news->save();

        return redirect('news/'.$news->id)->with('success', 'Item successfully created!');
    }

    public function edit(News $news, $id): View
    {
        $news = $news->find($id);

        return view('news.news_edit', compact('news'));
    }

    public function update(Request $request, News $news, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $news = News::find($id);
        $news->title = $request->input('title');
        $news->summary = fixImage($request->input('summary'), '', false, true);
        $news->body = fixImage($request->input('body'), '', false, true);
        $news->language = $request->input('language');
        $news->user_id = Auth::id();
        $news->status = $request->input('published');
        // inserting
        $news->save();

        return redirect('news/'.$id)->with('success', 'Item successfully updated!');
    }

    public function translate(News $news, $id, $tnid): View
    {
        $news = $news->where('tnid', $tnid)->get();
        $news_self = $news->find($id);

        return view('news.news_translate', compact('news', 'news_self'));
    }

    public function addTranslate($tnid, $lang): View
    {
        return view('news.news_add_translate', compact('tnid', 'lang'));
    }

    public function addPostTranslate(Request $request, News $news, $tnid, $lang): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'nullable',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $news->title = $request->input('title');
        $news->summary = $request->input('summary');
        $news->body = $request->input('body');
        $news->language = $lang;
        $news->user_id = Auth::id();
        $news->tnid = $tnid;
        $news->status = $request->input('published');
        // inserting
        $news->save();

        return redirect('news/'.$news->id)->with('success', 'Item successfully updated!');
    }
}
