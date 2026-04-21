<?php

namespace App\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Http\Requests\LiteracyLevelRequest;
use App\Http\Requests\SubjectAreaRequest;
use App\Http\Requests\ResourceTypeRequest;
use App\Models\TaxonomyHierarchy;
use App\Models\TaxonomyTerm;
use App\Models\TaxonomyVocabulary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TaxonomyController extends Controller
{
    public function index(Request $request): View
    {
        $terms = TaxonomyTerm::orderBy('vid', 'desc')->orderBy('weight')
            ->name(request('term'))
            ->vocabulary(request('vocabulary'))
            ->language(request('language'))
            ->paginate(10);

        $vocabulary = TaxonomyVocabulary::all('vid AS val', 'name');

        $args = [
            'route' => 'gettaxonomylist',
            'filters' => $request,
            'vocabulary' => $vocabulary,
        ];
        // creating search bar
        $createSearchBar = new SearchController;
        $searchBar = $createSearchBar->searchBar($args);

        return view('admin.taxonomy.taxonomy_list', compact('terms', 'searchBar'));
    }

    public function edit($vid, $tid): View
    {
        $term = TaxonomyTerm::find($tid);
        $vocabulary = TaxonomyVocabulary::all();
        $parents = TaxonomyTerm::where('vid', $vid)->get();
        $theParent = TaxonomyHierarchy::where('tid', $tid)->first();
        if (isset($theParent->parent)) {
            $theParent = $theParent->parent;
        } else {
            $theParent = 0;
        }

        return view('admin.taxonomy.taxonomy_edit', compact('term', 'vocabulary', 'parents', 'theParent'));
    }

    public function update(Request $request, $vid, $tid): RedirectResponse
    {
        $request->validate([
            'vid' => 'required',
            'name' => 'required',
            'weight' => 'required',
            'language' => 'required',
        ]);

        // Saving contact info to the database
        $term = TaxonomyTerm::find($tid);
        $term->vid = $request->input('vid');
        $term->name = $request->input('name');
        $term->weight = $request->input('weight');
        $term->language = $request->input('language');

        if ($term->tnid == 0) {
            $term->tnid = $tid;
        }

        $term->save();

        $parentid = $request->input('parent');

        $parent = TaxonomyHierarchy::firstOrNew(['tid' => $tid], ['parent' => $parentid]);

        if (! isset($parent->id)) {
            $latestId = DB::table('taxonomy_term_hierarchy')->max('aux_id');
            $THID = $latestId ? $latestId + 1 : 1;
        } else {
            $THID = $parent->id; // taxonomy_term_hierarchy.id
        }

        $parent->id = $THID;
        $parent->tid = $tid;
        $parent->parent = $parentid;
        $parent->save();

        return redirect('/admin/taxonomy')->with('success', 'Taxonomy item updated successfully!');
    }

    public function translate($tid): View
    {
        $tnid = TaxonomyTerm::find($tid)->tnid;
        if ($tnid) {
            $translations = TaxonomyTerm::where('tnid', $tnid)->get();
        } else {
            $translations = null;
        }

        $locals = \LaravelLocalization::getSupportedLocales();
        $supportedLocals = [];

        foreach ($locals as $key => $value) {
            array_push($supportedLocals, $key);
        }

        return view('admin.taxonomy.taxonomy_translate', compact('translations', 'supportedLocals', 'tnid', 'tid'));
    }

    public function create(): View
    {
        $vocabulary = TaxonomyVocabulary::all();

        return view('admin.taxonomy.taxonomy_create', compact('vocabulary'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'vid' => 'required',
            'name' => 'required',
            'weight' => 'required',
            'language' => 'required',
        ]);

        // Saving contact info to the database
        $term = new TaxonomyTerm;
        $term->vid = $request->input('vid');
        $term->name = $request->input('name');
        $term->weight = $request->input('weight');
        $term->language = $request->input('language');

        $term->save();

        $term->tnid = $term->id;
        // updating with tnid
        $term->save();

        return redirect('/admin/taxonomy')->with('success', 'Taxonomy item created successfully!');
    }

    public function createTranslate($tid, $tnid, $lang)
    {
        $vocabulary = TaxonomyVocabulary::all();
        $vid = TaxonomyTerm::where('tnid', $tnid)->first()->vid;
        $weight = TaxonomyTerm::where('tnid', $tnid)->first()->weight;
        $parents = TaxonomyTerm::where('vid', $vid)->get();
        $sourceParent = TaxonomyHierarchy::where('tid', $tid)->first()->parent;
        if ($sourceParent) {
            $parentTermTnid = TaxonomyTerm::where('id', $sourceParent)->first()->tnid;
            $parentTranslation = TaxonomyTerm::where('tnid', $parentTermTnid)->where('language', $lang)->first();
            // If the parent is translated in current language
            if ($parentTranslation) {
                $theParent = $parentTranslation->id;
            } else {
                return 'First translate the parent';
            }
        } else {
            $theParent = 0;
        }

        return view('admin.taxonomy.taxonomy_create_translate', compact(
            'vocabulary',
            'tnid',
            'vid',
            'lang',
            'weight',
            'parents',
            'theParent'
        ));
    }

    public function storeTranslate(Request $request, $tnid): RedirectResponse
    {
        $request->validate([
            'vid' => 'required',
            'name' => 'required',
            'weight' => 'required',
            'language' => 'required',
        ]);

        // Saving contact info to the database
        $term = TaxonomyTerm::create([
            'vid' => $request->input('vid'),
            'name' => $request->input('name'),
            'weight' => $request->input('weight'),
            'language' => $request->input('language'),
            'tnid' => $tnid,
        ]);

        TaxonomyHierarchy::insert([
            'id' => (int) (TaxonomyHierarchy::orderByDesc('id')->value('id') + 1),
            'tid' => $term->id,
            'parent' => $request->input('parent') ? $request->input('parent') : 0,
            'aux_id' => $request->input('aux_id') ? $request->input('aux_id') : $term->id,
        ]);

        return redirect('/admin/taxonomy')->with('success', 'Taxonomy item added successfully!');
    }

    public function subjectAreas(): View
    {
        $terms = TaxonomyTerm::where('vid', TaxonomyVocabularyEnum::ResourceSubject->value)->get();
        $languages = LaravelLocalization::getSupportedLocales();

        $subjectAreas = $terms->groupBy('tnid')->map(function ($translations) {
            return $translations->keyBy('language')->map(fn ($t) => $t->name);
        });

        return view('admin.taxonomy.subject-area.index', compact('subjectAreas', 'languages'));
    }

    public function editOrCreateSubjectArea($tnid = null)
    {
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        if ($tnid !== null && $tnid > 0 && TaxonomyTerm::where('vid', $vid)->where('tnid', $tnid)->doesntExist()) {
            abort(404);
        }

        $terms = TaxonomyTerm::with('taxonomyHierarchy')->where(['vid' => $vid, 'tnid' => $tnid])->get();
        $languages = LaravelLocalization::getSupportedLocales();
        $parents = TaxonomyTerm::where('vid', $vid)->where('tnid', '!=', $tnid)->get();

        $terms = $terms->keyBy('language')->map(function ($term) {
            return ['term' => $term];
        });

        return view('admin.taxonomy.subject-area.edit', compact('parents', 'terms', 'languages', 'tnid'));
    }

    public function storeOrUpdateSubjectArea(SubjectAreaRequest $request): RedirectResponse
    {
        $operation = $request->tnid ? 'updated' : 'created';
        DB::beginTransaction();

        try {
            $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
            $this->storeOrUpdateTranslation($request, $vid);

            DB::commit();

            return redirect()->route('subject_areas.index')->with('success', "Subject Area $operation successfully!");
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Subject Area was not '.$operation.'!'.$e->getMessage());
        }
    }

    private function storeOrUpdateTranslation($request, $vid){
        $parents = $request->input('parent', []);
        $weights = $request->input('weight', []);
        $names = $request->input('name', []);
        $termIds = $request->input('id', []);
        $tnid = $request->tnid ?? 0;

        foreach ($names as $language => $name) {
            $name = trim($name);
            if (! empty($name)) {
                $weight = $weights[$language] ?? null;
                $termId = $termIds[$language] ?? null;
                $parentId = $parents[$language] ?? 0;

                $term = $this->saveSubjectAreaTranslation($vid, $name, $weight, $language, $tnid, $parentId, $termId);

                if ($tnid == 0) {
                    $tnid = $term->id;

                    $term->tnid = $tnid;
                    $term->save();
                }
            }
        }
    }

    private function saveSubjectAreaTranslation($vid, $name, $weight, $language, $tnid, $parentId, $termId = null): TaxonomyTerm
    {
        $term = $termId ? TaxonomyTerm::find($termId) : new TaxonomyTerm;
        $term->vid = $vid;
        $term->name = $name;
        $term->weight = $weight;
        $term->language = $language;
        $term->tnid = $tnid;
        $term->save();

        $hierarchy = TaxonomyHierarchy::where('tid', $term->id)->first();

        if ($hierarchy) {
            $hierarchy->parent = $parentId;
            $hierarchy->save();
        } else {
            TaxonomyHierarchy::create([
                'id' => 0,
                'tid' => $term->id,
                'parent' => $parentId,
            ]);
            TaxonomyHierarchy::where(['tid' => $term->id, 'parent' => $parentId])->update(['id' => DB::raw('aux_id')]);
        }

        return $term;
    }

    public function resourceTypes(): View
    {
        $terms = TaxonomyTerm::where('vid', TaxonomyVocabularyEnum::ResourceType->value)->get();
        $languages = LaravelLocalization::getSupportedLocales();

        $resourceTypes = $terms->groupBy('tnid')->map(function ($translations) {
            return $translations->keyBy('language')->map(fn ($t) => $t->name);
        });

        return view('admin.taxonomy.resource-type.index', compact('resourceTypes', 'languages'));
    }

    public function editOrCreateResourceType($tnid = null): View
    {
        $vid = TaxonomyVocabularyEnum::ResourceType->value;

        if ($tnid !== null && $tnid > 0 && TaxonomyTerm::where('vid', $vid)->where('tnid', $tnid)->doesntExist()) {
            abort(404);
        }

        $terms = TaxonomyTerm::with('taxonomyHierarchy')->where(['vid' => $vid, 'tnid' => $tnid])->get();
        $languages = LaravelLocalization::getSupportedLocales();
        $parents = TaxonomyTerm::where('vid', $vid)->where('tnid', '!=', $tnid)->get();

        $terms = $terms->keyBy('language')->map(function ($term) {
            return ['term' => $term];
        });

        return view('admin.taxonomy.resource-type.edit', compact('parents', 'terms', 'languages', 'tnid'));
    }

    public function storeOrUpdateResourceType(ResourceTypeRequest $request): RedirectResponse
    {
        $operation = $request->tnid ? 'updated' : 'created';
        DB::beginTransaction();

        try {
            $vid = TaxonomyVocabularyEnum::ResourceType->value;

            $this->storeOrUpdateTranslation($request, $vid);

            DB::commit();

            return redirect()->route('resource_types.index')->with('success', "Resource Type $operation successfully!");
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Resource Type was not '.$operation.'!'.$e->getMessage());
        }
    }

    public function literacyLevels(): View
    {
        $terms = TaxonomyTerm::where('vid', TaxonomyVocabularyEnum::ResourceLevels->value)->get();
        $languages = LaravelLocalization::getSupportedLocales();

        $literacyLevels = $terms->groupBy('tnid')->map(function ($translations) {
            return $translations->keyBy('language')->map(fn ($t) => $t->name);
        });

        return view('admin.taxonomy.literacy-level.index', compact('literacyLevels', 'languages'));
    }

    public function editOrCreateLiteracyLevel($tnid = null): View
    {
        $vid = TaxonomyVocabularyEnum::ResourceLevels->value;

        if ($tnid !== null && $tnid > 0 && TaxonomyTerm::where('vid', $vid)->where('tnid', $tnid)->doesntExist()) {
            abort(404);
        }

        $terms = TaxonomyTerm::with('taxonomyHierarchy')->where(['vid' => $vid, 'tnid' => $tnid])->get();
        $languages = LaravelLocalization::getSupportedLocales();
        $parents = TaxonomyTerm::where('vid', $vid)->where('tnid', '!=', $tnid)->get();

        $terms = $terms->keyBy('language')->map(function ($term) {
            return ['term' => $term];
        });

        return view('admin.taxonomy.literacy-level.edit', compact('parents', 'terms', 'languages', 'tnid'));
    }

    public function storeOrUpdateLiteracyLevel(LiteracyLevelRequest $request): RedirectResponse
    {
        $operation = $request->tnid ? 'updated' : 'created';
        DB::beginTransaction();

        try {
            $vid = TaxonomyVocabularyEnum::ResourceLevels->value;

            $this->storeOrUpdateTranslation($request, $vid);

            DB::commit();

            return redirect()->route('literacy_levels.index')->with('success', "Resource Literacy Level $operation successfully!");
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Resource Literacy Level was not '.$operation.'!'.$e->getMessage());
        }
    }
}
