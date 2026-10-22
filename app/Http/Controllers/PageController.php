<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function index(): \Illuminate\View\View
    {
        return view('admin.pages.pages_list');
    }

    // Ajax get pages Function
    public function getPages()
    {
        $page = Page::select(['id', 'title', 'language', 'created_at', 'updated_at']);

        return Datatables::of($page)
            ->addColumn('action', function ($page) {
                return '<a href="'.URL($page->language.'/page/edit/'.$page->id).'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>';
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

    public function view(Request $request, $pageId): Factory|View|Application
    {
        // setting the search session empty
        DDLClearSession();

        $page = Page::findOrFail($pageId);
        // We don't want anyone else to access unpublished pages
        abort_if($page->status == 0 && ! (isAdmin() || isLibraryManager()), 403);

        $translation_id = $page->tnid;
        if ($translation_id) {
            $translations = Page::where('tnid', $translation_id)->get();
        } else {
            $translations = [];
        }

        return view('pages.pages_view', compact('page', 'translations'));
    }

    public function create(): \Illuminate\View\View
    {
        // setting the search session empty
        DDLClearSession();

        return view('pages.page_create');
    }

    public function store(Request $request, Page $page): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $page->title = $request->input('title');
        $page->summary = fixImage($request->input('summary'), '', false, true);
        $page->body = fixImage($request->input('body'), '', false, true);
        $page->language = $request->input('language');
        $page->user_id = Auth::id();
        $page->status = $request->input('published');
        // inserting
        $page->save();

        $page = Page::find($page->id);
        $page->tnid = $page->id;
        // updating with tnid
        $page->save();

        return redirect('page/'.$page->id)->with('success', 'Item successfully created!');
    }

    public function edit(Page $page, $id): \Illuminate\View\View
    {
        $page = $page->find($id);

        return view('pages.page_edit', compact('page'));
    }

    public function update(Request $request, Page $page, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $page = Page::find($id);
        $page->title = $request->input('title');
        $page->summary = fixImage($request->input('summary'), '', false, true);
        $page->body = fixImage($request->input('body'), '', false, true);
        $page->language = $request->input('language');
        $page->user_id = Auth::id();
        $page->status = $request->input('published');
        // inserting
        $page->save();

        return redirect('page/'.$id)->with('success', 'Item successfully updated!');
    }

    public function translate(Page $page, $id, $tnid): \Illuminate\View\View
    {
        $page = $page->where('tnid', $tnid)->get();
        $page_self = $page->find($id);

        return view('pages.page_translate', compact('page', 'page_self'));
    }

    public function addTranslate($tnid, $lang): \Illuminate\View\View
    {
        return view('pages.page_add_translate', compact('tnid', 'lang'));
    }

    public function addPostTranslate(Request $request, Page $page, $tnid, $lang): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'language' => 'nullable',
            'summary' => 'required',
            'body' => 'required',
            'published' => 'integer',
        ]);

        $page->title = $request->input('title');
        $page->summary = $request->input('summary');
        $page->body = $request->input('body');
        $page->language = $lang;
        $page->user_id = Auth::id();
        $page->tnid = $tnid;
        $page->status = $request->input('published');
        // inserting
        $page->save();

        return redirect('page/'.$page->id)->with('success', 'Item successfully updated!');
    }
}
