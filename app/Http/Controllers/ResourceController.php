<?php

namespace App\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Http\Requests\ResourceStepOneRequest;
use App\Http\Requests\UpdateResourceFilterOptionsRequest;
use App\Mail\NewComment;
use App\Models\DownloadCount;
use App\Models\Resource;
use App\Models\ResourceAttachment;
use App\Models\ResourceAuthor;
use App\Models\ResourceComment;
use App\Models\ResourceCopyrightHolder;
use App\Models\ResourceCreativeCommon;
use App\Models\ResourceEducationalResource;
use App\Models\ResourceEducationalUse;
use App\Models\ResourceFavorite;
use App\Models\ResourceFile;
use App\Models\ResourceFlag;
use App\Models\ResourceIamAuthor;
use App\Models\ResourceKeyword;
use App\Models\ResourceLearningResourceType;
use App\Models\ResourceLevel;
use App\Models\ResourcePublisher;
use App\Models\ResourceSharePermission;
use App\Models\ResourceSubjectArea;
use App\Models\ResourceTranslationRight;
use App\Models\ResourceTranslator;
use App\Models\ResourceView;
use App\Models\Setting;
use App\Models\TaxonomyTerm;
use App\Traits\LanguageTrait;
use App\Traits\SitewidePageViewTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Exceptions\SupportedLocalesNotDefined;
use Throwable;

class ResourceController extends Controller
{
    use LanguageTrait, SitewidePageViewTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function index(Request $request): Factory|View|Application
    {
        // setting the search session empty
        DDLClearSession();

        $myResources = new Resource;
        $languages = $this->getLanguages();

        $resources = $myResources->filterResources($request->all());

        $request->session()->put('filters', $request->all());

        $filters = $request->session()->get('filters');

        return view('admin.resources.resources', compact('resources', 'filters', 'languages'));
    }

    public function updateTid(Request $request, $resourceId): RedirectResponse
    {
        $translatedResource = Resource::findOrFail($request->input('link'));
        $resource = Resource::findOrFail($resourceId);

        // Check if both resources are self-referential
        if ($translatedResource->tnid == $translatedResource->id && $resource->tnid == $resource->id) {
            // Case 1: Both are not primary
            if (! $translatedResource->primary_tnid && ! $resource->primary_tnid) {
                $resource->primary_tnid = true;
                $resource->save();

                $translatedResource->tnid = $resource->id;
                $translatedResource->save();
            }
            // Case 2: If both have primary_tnid
            elseif ($translatedResource->primary_tnid && $resource->primary_tnid) {
                Session::flash('alert', [
                    'message' => __('Both resources are primary and cannot be linked.'),
                    'level' => 'danger',
                ]);

                return back();
            }
        }

        // Handle cases where one resource is primary
        if ($resource->primary_tnid) {
            if ($translatedResource->tnid != $resource->id) {
                $translatedResource->tnid = $resource->id;
                $translatedResource->save();
            }
        } elseif ($translatedResource->primary_tnid) {
            $resource->tnid = $translatedResource->id;
            $resource->save();
        }

        // Handle cases where tnid is not equal to id
        else {
            if ($resource->tnid != $resource->id) {
                $primaryResource = Resource::find($resource->tnid);
                if ($primaryResource) {
                    $translatedResource->tnid = $primaryResource->id;
                    $translatedResource->save();
                }
            } elseif ($translatedResource->tnid != $translatedResource->id) {
                $primaryResource = Resource::find($translatedResource->tnid);
                if ($primaryResource) {
                    $resource->tnid = $primaryResource->id;
                    $resource->save();
                }
            }
        }

        Session::flash('alert', [
            'message' => __('Resource linked successfully'),
            'level' => 'success',
        ]);

        return back();
    }

    public function list(Request $request): Factory|View|Application
    {
        // setting the search session empty
        DDLClearSession();
        $language = $request['language'] ?? null;
        $this->pageView($request, 'Resource List');

        $resource = new Resource;

        // Getting all whatever in the parameterBag
        $everything = $request->all();

        if (isset($everything['search'])) {
            session(['search' => $everything['search']]);
        }

        if (
            $request->filled('subjectAreaParent')
            or
            $request->filled('subjectAreaChild')
        ) {
            $subjectAreaParentIds = [];
            $parentIdsfromChildren = [];
            $subjectAreaChildIds = [];

            if ($request->filled('subjectAreaParent')) {
                $subjectAreaParentIds = $everything['subjectAreaParent'];
            }

            if ($request->filled('subjectAreaChild')) {
                $subjectAreaChildIds = $everything['subjectAreaChild'];
                $parentIdsfromChildren = (new Resource)
                    ->resourceAttributesList('taxonomy_term_data', 8, $language)  // 8 being subject areas
                    ->whereIn('id', $subjectAreaChildIds)
                    ->pluck('parent')
                    ->toArray();
            }

            $bothParentIds = array_merge($parentIdsfromChildren, $subjectAreaParentIds);
            $noDuplicateParentAreaIds = array_keys(  // return the array with all the keys
                array_intersect(
                    array_count_values(  // count how many times a particular value occurs
                        $bothParentIds
                    ),
                    [1]  // keep only the ones with that occurred exactly once
                )
            );

            $finalSubjectAreaIds = array_merge($noDuplicateParentAreaIds, $subjectAreaChildIds);
            $finalSubjectAreaIds = array_map('strval', $finalSubjectAreaIds);
            $request->query->remove('subjectAreaParent');
            $request->query->add(['subject_area' => $finalSubjectAreaIds]);
        }

        $resources = $resource->paginateResourcesBy($request);

        return view('resources.resources_list', compact('resources'));
    }

    public function resourceFilter(): Factory|View|Application
    {
        $resourceObject = new Resource;
        $parentSubjects = $resourceObject
            ->resourceAttributesList('taxonomy_term_data', 8)  // 8 being subject areas
            ->where('parent', 0);
        $resourceTypes = $resourceObject->resourceAttributesList('taxonomy_term_data', 7);  // 7 being resource types
        $literacyLevels = $resourceObject
            ->resourceAttributesList('taxonomy_term_data', 13)
            ->where('parent', 0); // 13 being resource literacy levels
        $languages = $this->getLanguages();

        return view('resources.resources_filter', compact('parentSubjects', 'languages', 'resourceTypes', 'literacyLevels'));
    }

    public function getSubjectChildren(Request $request): array
    {
        $subjectIds = explode(',', $request->input('IDs'));
        $language = $request->input('language');

        return (new Resource)
            ->resourceAttributesList('taxonomy_term_data', 8, $language)  // 8 being subject areas
            ->whereIn('parent', $subjectIds)
            ->pluck('id', 'name')
            ->toArray();
    }

    public function updateFilterOptions(UpdateResourceFilterOptionsRequest $request): JsonResponse
    {
        try {
            $language = $request->input('language');

            $subjectAreas = $this->getResourceAttributesList(TaxonomyVocabularyEnum::ResourceSubject->value, $language);

            $resourceTypes = $this->getResourceAttributesList(TaxonomyVocabularyEnum::ResourceType->value, $language);

            $literacyLevels = $this->getResourceAttributesList(TaxonomyVocabularyEnum::ResourceLevels->value, $language);

            return response()->json([
                'success' => true,
                'message' => __('Filter options updated successfully.'),
                'subjectAreas' => $subjectAreas,
                'resourceTypes' => $resourceTypes,
                'literacyLevels' => $literacyLevels,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong while updating filter options.')
            ], 500);
        }
    }

    private function getResourceAttributesList($vid, $language): array
    {
        return (new Resource)
            ->resourceAttributesList('taxonomy_term_data', $vid, $language)
            ->where('parent', 0)
            ->pluck('id', 'name')
            ->toArray();
    }

    /**
     * @throws SupportedLocalesNotDefined
     */
    public function viewPublicResource(Request $request, $resourceId): View|Factory|Redirector|RedirectResponse|Application
    {
        DDLClearSession();
        $myResources = new Resource;

        $resource = Resource::with(['attachments', 'resourceFile:id,name'])
            ->with('favorites', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->withCount('favorites')
            ->findOrFail($resourceId);

        if ($resource->status == 0 && ! (isAdmin() || isLibraryManager())) {  // We don't want anyone else to access unpublished resources
            abort(403);
        }

        $this->pageView($request, $resource->title);

        $relatedItems = $myResources->getRelatedResources($resourceId, $resource->subjects);
        $comments = ResourceComment::where('resource_id', $resourceId)->published()->get();
        $languages_available = [];

        $translation_id = $resource->tnid;
        $translations = null;
        if ($translation_id) {
            $translations = $myResources->getResourceTranslations($translation_id);
            $supportedLocals = [];
            $newId = [];
            foreach (config('laravellocalization.localesOrder') as $localeCode) {
                $supportedLocals[] = $localeCode;
            }

            if ($translations) {
                foreach ($translations as $tr) {
                    if (in_array($tr->language, $supportedLocals)) {
                        $newId[$tr->language] = $tr->id;
                    }
                }
            }

            foreach (\LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                if (isset($newId[$localeCode]) && $newId != 0) {
                    $currentUrl = explode('/', url()->current());
                    $index = count($currentUrl) - 1;
                    $currentUrl[$index] = $newId[$localeCode];
                    $newUrl = implode('/', $currentUrl);
                    $languages_available[$localeCode]['url'] = $newUrl;
                    $languages_available[$localeCode]['native'] = $properties['native'];
                }
            }
        }
        $this->resourceViewCounter($request, $resourceId);
        $views = new ResourceView;
        Carbon::setLocale(app()->getLocale());

        $ePub = null;
        $ePubFile = $resource->attachments->where('file_mime', 'application/epub+zip')->first();
        if ($ePubFile) {
            if (config('app.env') != 'production') {
                $ePub = asset('files/resources/'.$ePubFile->file_name);
            } else {
                $ePub = getFile("resources/$ePubFile->file_name");
            }
        }

        return view('resources.resources_view', compact(
            'resource',
            'relatedItems',
            'comments',
            'languages_available',
            'views',
            'translations',
            'ePub'
        ));
    }

    public function createStepOne(Request $request): Factory|View|Application
    {
        $resource = $request->session()->get('new_resource_step_1');
        $myResources = new Resource;
        $creativeCommons = $myResources->resourceAttributesList('taxonomy_term_data', 10, config('app.locale'), [168, 535]); // taxonomy_term_data.tnid [168=Unknown , 535=CC BY / CC BY-SA]
        $subjects = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceSubject);

        return view('resources.resources_modify_step1', compact('resource', 'creativeCommons', 'subjects'));
    }

    public function postStepOne(ResourceStepOneRequest $request): Redirector|Application|RedirectResponse
    {
        $validatedData = $request->validated();

        if (! $request->has_translator) {
            $validatedData['translator'] = null;
        }

        $request->session()->put('new_resource_step_1', $validatedData);

        return redirect('/resources/add/step2');
    }

    public function createStepTwo(Request $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $new_resource_step_1 = $request->session()->get('new_resource_step_1');

        if (! $new_resource_step_1) {
            return redirect('/resources/add/step1');
        }

        $resource = $request->session()->get('new_resource_step_2');

        $resourceSubjectAreas = $resource['subject_areas'];
        $resourceLearningResourceTypes = $resource['learning_resources_types'];
        $editEducationalUse = $resource['educational_use'];
        // $resourceKeywords = json_encode($resource['keywords']);

        $myResources = new Resource;

        $subjects = $myResources->resourceAttributesList('taxonomy_term_data', 8);
        $keywords = $myResources->resourceAttributesList('taxonomy_term_data', 23);
        $learningResourceTypes = $myResources->resourceAttributesList('taxonomy_term_data', 7);
        $educationalUse = $myResources->resourceAttributesList('taxonomy_term_data', 25);
        $types = $myResources->resourceAttributesList('taxonomy_term_data', 7);
        $levels = $myResources->resourceAttributesList('taxonomy_term_data', 13);

        $edit = false;

        return view('resources.resources_modify_step2', compact(
            'resource',
            'edit',
            'subjects',
            'keywords',
            'types',
            'levels',
            'learningResourceTypes',
            'educationalUse',
            'resourceSubjectAreas',
            'resourceLearningResourceTypes',
            'editEducationalUse'
        ));
    }

    public function postStepTwo(Request $request): Redirector|Application|RedirectResponse
    {
        $new_resource_step_1 = $request->session()->get('new_resource_step_1');

        $validatedData = $request->validate([
            'attachments.*' => 'file|mimes:xlsx,epub,xls,csv,jpg,jpeg,png,bmp,mpga,ppt,pptx,doc,docx,pdf,tif,tiff,mp3|max:131072', // Max file size is 128 MB
            'subject_areas' => 'required',
            'keywords' => 'string|nullable',
            'learning_resources_types' => 'required',
            'educational_use' => 'required',
            'level' => 'required',
        ]);

        if (isset($validatedData['attachments'])) {
            foreach ($validatedData['attachments'] as $attachments) {
                $fileMime = $attachments->getMimeType();
                $fileSize = $attachments->getSize();
                $fileName = $attachments->getClientOriginalName();
                $fileExtension = \File::extension($fileName);
                $diskType = 's3';
                if (config('app.env') != 'production') {
                    $diskType = 'public';
                }
                $uniqueId = uniqid(); // Generate a unique ID
                $fileName = auth()->user()->id.'_'.$uniqueId.'_'.time().'.'.$fileExtension;
                Storage::disk($diskType)->put('resources/'.$fileName, file_get_contents($attachments));
                $validatedData['attc'][] = [
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'file_mime' => $fileMime,
                ];
            }
            unset($validatedData['attachments']);
        }

        $validatedData = $this->getValidatedData($new_resource_step_1, $validatedData);

        $request->session()->put('new_resource_step_2', $validatedData);

        return redirect('/resources/add/step3');
    }

    public function createStepThree(Request $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $resource1 = $request->session()->get('new_resource_step_1');
        $resource2 = $request->session()->get('new_resource_step_2');

        if (! $resource1 || ! $resource2) {
            return redirect('/resources/add/step1');
        }

        $resource = $request->session()->get('new_resource_step_3');

        $myResources = new Resource;

        $creativeCommons = $myResources->resourceAttributesList('taxonomy_term_data', 10);
        $creativeCommonsOther = $myResources->resourceAttributesList('taxonomy_term_data', 26);
        $edit = false;

        return view('resources.resources_modify_step3', compact('resource', 'edit', 'creativeCommons', 'creativeCommonsOther'));
    }

    /**
     * Store resource
     *
     * @throws Throwable
     */
    public function postStepThree(Request $request)
    {
        $validatedData = $request->validate([
            'translation_rights' => 'integer',
            'educational_resource' => 'integer',
            'copyright_holder' => 'string|nullable',
            'iam_author' => 'integer',
            'creative_commons' => 'integer',
            'creative_commons_other' => 'integer',
        ]);

        $request->session()->put('new_resource_step_3', $validatedData);

        $resource1 = $request->session()->get('new_resource_step_1');
        $resource2 = $request->session()->get('new_resource_step_2');
        $resource3 = $request->session()->get('new_resource_step_3');
        if (isAdmin()) {
            $resource3['published'] = $request->input('published');
        } else {
            $resource3['published'] = 0;
        }

        $request->session()->forget('new_resource_step_1');
        $request->session()->forget('new_resource_step_2');
        $request->session()->forget('new_resource_step_3');
        $request->session()->save();

        $finalArray = array_merge($resource1, $resource2, $resource3);

        $result = DB::transaction(function () use ($finalArray) {
            $myResources = new Resource;

            $myResources->title = $finalArray['title'];
            $myResources->resource_file_id = $finalArray['resource_file_id'];
            $myResources->abstract = $finalArray['abstract'];
            $myResources->language = $finalArray['language'];
            $myResources->user_id = Auth::id();
            $myResources->status = $finalArray['published'];
            $myResources->published_at = date('Y-m-d H:i:s');
            // inserting to resource table
            $myResources->save();

            if ($myResources->resource_file_id) {

                ResourceFile::where(['id' => $myResources->resource_file_id, 'resource_id' => null])->update(['resource_id' => $myResources->id]);
            }
            $myResources = Resource::find($myResources->id);
            $myResources->tnid = $myResources->id;
            // updating resource table with tnid
            $myResources->save();

            if (isset($finalArray['attc'])) {
                foreach ($finalArray['attc'] as $attc) {
                    $myAttachments = new ResourceAttachment;
                    $myAttachments->resource_id = $myResources->id;
                    $myAttachments->file_name = $attc['file_name'];
                    $myAttachments->file_mime = $attc['file_mime'];
                    $myAttachments->file_size = $attc['file_size'];
                    $myAttachments->save();
                }
            }

            // Inserting Subject Areas
            foreach ($finalArray['subject_areas'] as $subject) {
                $mySubjects = new ResourceSubjectArea;
                $mySubjects->resource_id = $myResources->id;
                $mySubjects->tid = $subject;
                $mySubjects->save();
            }

            // Inserting Keywords
            $keywords = trim($finalArray['keywords'], ',');
            $keywords = explode(',', $keywords);
            foreach ($keywords as $kw) {
                $theTaxonomy = TaxonomyTerm::where('name', trim($kw))
                    ->where('vid', 23)
                    ->first();

                $myKeywords = new ResourceKeyword;
                $myKeywords->resource_id = $myResources->id;
                if ($theTaxonomy != null) {
                    $myKeywords->tid = $theTaxonomy->id;
                } else {
                    $myTaxonomy = new TaxonomyTerm;
                    $myTaxonomy->vid = 23;
                    $myTaxonomy->name = trim($kw);
                    $myTaxonomy->language = $finalArray['language'];
                    $myTaxonomy->save();
                    $myKeywords->tid = $myTaxonomy->id;
                }
                $myKeywords->save();
            }

            // Inserting Authors
            if (isset($finalArray['author'])) {
                $authors = trim($finalArray['author'], ',');
                $authors = explode(',', $authors);
                foreach ($authors as $author) {
                    $theTaxonomy = TaxonomyTerm::where('name', trim($author))
                        ->where('vid', 24)
                        ->first();

                    $myAuthor = new ResourceAuthor;
                    $myAuthor->resource_id = $myResources->id;

                    if ($theTaxonomy != null) {
                        $myAuthor->tid = $theTaxonomy->id;
                    } else {
                        $myTaxonomy = new TaxonomyTerm;
                        $myTaxonomy->vid = 24;
                        $myTaxonomy->name = $author;
                        $myTaxonomy->language = $finalArray['language'];
                        $myTaxonomy->save();

                        $myAuthor->tid = $myTaxonomy->id;
                    }
                    $myAuthor->save();
                }
            }

            // Inserting Publishers
            if (isset($finalArray['publisher'])) {
                $publisherName = trim($finalArray['publisher'], ',');
                $theTaxonomy = TaxonomyTerm::where('name', $publisherName)
                    ->where('vid', 9)
                    ->first();

                $myPublisher = new ResourcePublisher;
                $myPublisher->resource_id = $myResources->id;

                if ($theTaxonomy != null) {
                    $myPublisher->tid = $theTaxonomy->id;
                } else {
                    $myTaxonomy = new TaxonomyTerm;
                    $myTaxonomy->vid = 9;
                    $myTaxonomy->name = $publisherName;
                    $myTaxonomy->language = $finalArray['language'];
                    $myTaxonomy->save();

                    $myPublisher->tid = $myTaxonomy->id;
                }
                $myPublisher->save();
            }

            // Inserting Translators
            if (isset($finalArray['translator'])) {
                $translators = trim($finalArray['translator'], ',');
                $translators = explode(',', $translators);
                foreach ($translators as $translator) {
                    $theTaxonomy = TaxonomyTerm::where('name', trim($translator))
                        ->where('vid', 24)
                        ->first();

                    $myTranslator = new ResourceTranslator;
                    $myTranslator->resource_id = $myResources->id;

                    if ($theTaxonomy != null) {
                        $myTranslator->tid = $theTaxonomy->id;
                    } else {
                        $myTaxonomy = new TaxonomyTerm;
                        $myTaxonomy->vid = 24;
                        $myTaxonomy->name = $translator;
                        $myTaxonomy->language = $finalArray['language'];
                        $myTaxonomy->save();

                        $myTranslator->tid = $myTaxonomy->id;
                    }
                    $myTranslator->save();
                }
            }

            // Inserting Learning Resource Types
            foreach ($finalArray['learning_resources_types'] as $ltype) {
                $myLearningType = new ResourceLearningResourceType;
                $myLearningType->resource_id = $myResources->id;
                $myLearningType->tid = $ltype;
                $myLearningType->save();
            }

            // Inserting Educational Use
            foreach ($finalArray['educational_use'] as $edus) {
                $myEdu = new ResourceEducationalUse;
                $myEdu->resource_id = $myResources->id;
                $myEdu->tid = $edus;
                $myEdu->save();
            }

            // Inserting Resource Levels
            foreach ($finalArray['level'] as $level) {
                $myLevel = new ResourceLevel;
                $myLevel->resource_id = $myResources->id;
                $myLevel->tid = $level;
                $myLevel->save();
            }

            // Inserting Translation Rights
            if (isset($finalArray['translation_rights'])) {
                $myTranslationRight = new ResourceTranslationRight;
                $myTranslationRight->resource_id = $myResources->id;
                $myTranslationRight->value = $finalArray['translation_rights'];
                $myTranslationRight->save();
            }

            // Inserting Educational Resource
            if (isset($finalArray['educational_resource'])) {
                $myEduResource = new ResourceEducationalResource;
                $myEduResource->resource_id = $myResources->id;
                $myEduResource->value = $finalArray['educational_resource'];
                $myEduResource->save();
            }

            // Inserting ResourceIamAuthor
            if (isset($finalArray['iam_author'])) {
                $myIamAuthor = new ResourceIamAuthor;
                $myIamAuthor->resource_id = $myResources->id;
                $myIamAuthor->value = $finalArray['iam_author'];
                $myIamAuthor->save();
            }

            // Inserting Copyright Holder
            if (isset($finalArray['copyright_holder'])) {
                $myCopyrightHolder = new ResourceCopyrightHolder;
                $myCopyrightHolder->resource_id = $myResources->id;
                $myCopyrightHolder->value = $finalArray['copyright_holder'];
                $myCopyrightHolder->save();
            }

            // Inserting Creative Commons
            if (isset($finalArray['creative_commons'])) {
                $myCC = new ResourceCreativeCommon;
                $myCC->resource_id = $myResources->id;
                $myCC->tid = $finalArray['creative_commons'];
                $myCC->save();
            }

            // Inserting Sharing Permission
            if (isset($finalArray['creative_commons_other'])) {
                $mySharePermit = new ResourceSharePermission;
                $mySharePermit->resource_id = $myResources->id;
                $mySharePermit->tid = $finalArray['creative_commons_other'];
                $mySharePermit->save();
            }

            return true;
        });

        if ($result and isAdmin()) {
            Session::flash('alert', [
                'message' => __('Resource successfully added!'),
                'level' => 'success',
            ]);

            return redirect('/home');
        } elseif ($result) {
            Session::flash('alert', [
                'message' => __('Resource successfully added! It will be published after review.'),
                'level' => 'success',
            ]);

            return redirect('/home');
        }

        Session::flash('alert', [
            'message' => __('Resource couldn\'t be added.'),
            'level' => 'danger',
        ]);

        return redirect('/home');

    }

    public function attributes(string $entity, Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $myResources = new Resource;
        $keyword = $request->only('term');
        if (! $keyword) {
            return redirect('/home');
        }
        $vid = null;
        if ($entity == 'authors') {
            $vid = 24;
        } elseif ($entity == 'publishers') {
            $vid = 9;
        } elseif ($entity == 'translators') {
            $vid = 22;
        } elseif ($entity == 'keywords') {
            $vid = 23;
        }

        if ($vid) {
            $records = $myResources->searchResourceAttributes($keyword['term'], 'taxonomy_term_data', $vid);

            return response()->json($records->toArray());
        }

        return redirect('/home');
    }

    public function resourceFavorite(Request $request): JsonResponse
    {
        $resourceId = $request->input('resourceId');
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['status' => 'notloggedin']);
        }

        $favorite = ResourceFavorite::where(['resource_id' => $resourceId, 'user_id' => $userId])->first();

        if ($favorite) {
            $favorite->delete();
            $action = 'deleted';
        } else {
            ResourceFavorite::create([
                'resource_id' => $resourceId,
                'user_id' => $userId,
            ]);
            $action = 'added';
        }

        $favoriteCount = ResourceFavorite::where('resource_id', $resourceId)->count();

        return response()->json([
            'action' => $action,
            'favorite_count' => $favoriteCount,
        ]);
    }

    public function flag(Request $request): Redirector|Application|RedirectResponse
    {
        $userId = $request->input('userid');
        $resourceId = $request->input('resource_id');

        if (empty($userId)) {
            return redirect('login');
        } elseif (empty($resourceId)) {
            return redirect('home');
        }

        $flag = new ResourceFlag;
        $flag->resource_id = $resourceId;
        $flag->user_id = $userId;
        $flag->type = $request->input('type');
        $flag->details = $request->input('details');
        $flag->save();

        return redirect('resource/'.$resourceId)
            ->with('success', __('Thank you for your report. We will review and take action as soon as possible.'));
    }

    public function comment(Request $request): Redirector|Application|RedirectResponse
    {
        $userId = $request->input('userid');
        $resourceId = $request->input('resource_id');

        if (empty($userId)) {
            return redirect('login');
        }

        $comment = new ResourceComment;
        $comment->resource_id = $resourceId;
        $comment->user_id = $userId;
        $comment->comment = $request->input('comment');
        $comment->save();

        if (config('mail.send_email') == 'yes') {
            Mail::to(Setting::find(1)->website_email)->send(new NewComment($comment));
        }

        Session::flash('alert', [
            'message' => __('Your comment is recorded. It will be published after a review.'),
            'level' => 'success',
        ]);

        return redirect('resource/'.$resourceId);
    }

    public function resourceViewCounter(Request $request, $resourceId): void
    {
        $myResources = new Resource;

        $userAgentParser = parse_user_agent($request);
        $userAgent = [
            'resource_id' => $resourceId,
            'userid' => Auth::id() ?: 0,
            'ip' => $request->ip(),
            'browser_name' => $userAgentParser['browser'],
            'browser_version' => $userAgentParser['version'],
            'platform' => $userAgentParser['platform'],
        ];

        $myResources->updateResourceCounter($userAgent);
    }

    public function createStepOneEdit($resourceId, Request $request): Factory|View|Application
    {
        $myResources = new Resource;

        $resource = $request->session()->get('edit_resource_step_1');
        if ($resource and ($resourceId != $resource->id)) {
            $resource = null;
        }
        if ($resource == null) {
            $resource = Resource::with(['authors:id,name', 'translators:id,name', 'publishers:id,name', 'resourceFile:id,name'])->findOrFail($resourceId);
        }
        $edit = true;
        $subjects = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceSubject);

        return view('resources.resources_modify_step1', compact('resource', 'edit', 'subjects'));
    }

    public function postStepOneEdit($resourceId, ResourceStepOneRequest $request): Redirector|Application|RedirectResponse
    {
        $validatedData = $request->validated();

        if (! $request->has_translator) {
            $validatedData['translator'] = null;
        }
        $validatedData['id'] = $resourceId;

        $request->session()->put('edit_resource_step_1', $validatedData);

        return redirect('/resources/edit/step2/'.$resourceId);
    }

    public function createStepTwoEdit($resourceId, Request $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $resource1 = $request->session()->get('edit_resource_step_1');

        if (! $resource1) {
            return redirect('/resources/edit/step1');
        }

        $myResources = new Resource;

        $resourceSubjectAreas = [];
        $resourceLearningResourceTypes = [];
        $editEducationalUse = [];
        $resourceLevels = [];
        $resourceKeywords = [];
        $resourceAttachments = [];

        $resource = $request->session()->get('edit_resource_step_2');
        if ($resource and ($resourceId != $resource['id'])) {
            $resource = null;
        }

        if (isset($resource['subject_areas'])) {
            $resourceSubjectAreas = $resource['subject_areas'];
        } else {
            $dataSubjects = $myResources->resourceAttributes($resourceId, 'resource_subject_areas', 'tid', 'taxonomy_term_data');
            foreach ($dataSubjects as $item) {
                $resourceSubjectAreas[] = $item->id;
            }
        }

        if (isset($resource['learning_resources_types'])) {
            $resourceLearningResourceTypes = $resource['learning_resources_types'];
        } else {
            $dataResourceTypes = $myResources->resourceAttributes($resourceId, 'resource_learning_resource_types', 'tid', 'taxonomy_term_data');
            foreach ($dataResourceTypes as $item) {
                $resourceLearningResourceTypes[] = $item->id;
            }
        }

        if ($resource && isset($resource['keywords'])) {
            $resourceKeywords = explode(',', $resource['keywords']);
        } else {
            $dataKeywords = $myResources->resourceAttributes($resourceId, 'resource_keywords', 'tid', 'taxonomy_term_data');
            foreach ($dataKeywords as $item) {
                $resourceKeywords[] = $item->name;
            }
        }

        if (isset($resource['educational_use'])) {
            $editEducationalUse = $resource['educational_use'];
        } else {
            $dataEducationalUse = $myResources->resourceAttributes($resourceId, 'resource_educational_uses', 'tid', 'taxonomy_term_data');
            foreach ($dataEducationalUse as $item) {
                $editEducationalUse[] = $item->id;
            }
        }

        if (isset($resource['level'])) {
            $resourceLevels = $resource['level'];
        } else {
            $dataLevels = $myResources->resourceAttributes($resourceId, 'resource_levels', 'tid', 'taxonomy_term_data');
            foreach ($dataLevels as $item) {
                $resourceLevels[] = $item->id;
            }
        }

        if (isset($resource['attc'])) {
            foreach ($resource['attc'] as $item) {
                $resourceAttachments[] = [
                    'file_name' => $item['file_name'],
                    'file_size' => $item['file_size'],
                    'file_mime' => $item['file_mime'],
                ];
            }
        } else {
            $dataAttachments = $myResources->resourceAttachments($resourceId)->toArray();
            foreach ($dataAttachments as $item) {
                $resourceAttachments[] = [
                    'file_name' => $item->file_name,
                    'file_size' => $item->file_size,
                    'file_mime' => $item->file_mime,
                ];
            }
            $resource['attc'] = $resourceAttachments;
            $request->session()->put('edit_resource_step_2', $resource);
            $request->session()->save();
        }

        $resourceKeywords = $resourceKeywords ? implode(',', $resourceKeywords) : '';

        $subjects = $myResources->resourceAttributesList('taxonomy_term_data', 8);
        $keywords = $myResources->resourceAttributesList('taxonomy_term_data', 23);
        $learningResourceTypes = $myResources->resourceAttributesList('taxonomy_term_data', 7);
        $educationalUse = $myResources->resourceAttributesList('taxonomy_term_data', 25);
        $types = $myResources->resourceAttributesList('taxonomy_term_data', 7);
        $levels = $myResources->resourceAttributesList('taxonomy_term_data', 13);
        $resource['id'] = $resourceId;
        $edit = true;

        return view('resources.resources_modify_step2', compact(
            'resource',
            'edit',
            'subjects',
            'keywords',
            'types',
            'levels',
            'learningResourceTypes',
            'educationalUse',
            'resourceSubjectAreas',
            'resourceLearningResourceTypes',
            'editEducationalUse',
            'resourceAttachments',
            'resourceLevels',
            'resourceKeywords'
        ));
    }

    public function postStepTwoEdit($resourceId, Request $request): Redirector|Application|RedirectResponse
    {
        $resource = $request->session()->get('edit_resource_step_2');
        $validatedData = $request->validate([
            'attachments.*' => 'file|mimes:xlsx,xls,csv,epub,jpg,jpeg,png,bmp,mpga,ppt,pptx,doc,docx,pdf,tif,tiff,mp3',
            'subject_areas' => 'required',
            'keywords' => 'string|nullable',
            'learning_resources_types' => 'required',
            'educational_use' => 'required',
            'level' => 'required',
        ]);

        if (isset($validatedData['attachments'])) {
            foreach ($validatedData['attachments'] as $attachments) {
                $fileMime = $attachments->getMimeType();
                $fileSize = $attachments->getSize();
                $fileName = $attachments->getClientOriginalName();
                $fileExtension = \File::extension($fileName);
                $uniqueId = uniqid(); // Generate a unique ID
                $fileName = auth()->user()->id.'_'.$uniqueId.'_'.time().'.'.$fileExtension;
                $diskType = 's3';
                if (config('app.env') != 'production') {
                    $diskType = 'public';
                }
                unset($validatedData['attachments']);
                Storage::disk($diskType)->put('resources/'.$fileName, file_get_contents($attachments));
                $validatedData['attc'][] = [
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'file_mime' => $fileMime,
                ];
            }
        }

        $validatedData = $this->getValidatedData($resource, $validatedData);

        $validatedData['resourceid'] = $resourceId;
        $request->session()->put('edit_resource_step_2', $validatedData);
        $request->session()->save();

        return redirect('/resources/edit/step3/'.$resourceId);
    }

    public function createStepThreeEdit($resourceId, Request $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $resource1 = $request->session()->get('edit_resource_step_1');
        $resource2 = $request->session()->get('edit_resource_step_2');

        if (! $resource1 || ! $resource2) {
            return redirect('/resources/edit/step1');
        }

        $resource = $request->session()->get('edit_resource_step_3');
        if ($resource and ($resourceId != $resource['id'])) {
            $resource = null;
        }

        $dbRecords = Resource::find($resourceId);

        $myResources = new Resource;

        $creativeCommons = $myResources->resourceAttributesList('taxonomy_term_data', 10);
        $creativeCommonsOther = $myResources->resourceAttributesList('taxonomy_term_data', 26);

        $resource['id'] = $resourceId;
        $resource['status'] = $dbRecords->status;
        $edit = true;

        return view('resources.resources_modify_step3', compact('dbRecords', 'edit', 'resource', 'creativeCommons', 'creativeCommonsOther'));
    }

    /**
     * Store resource
     *
     * @throws Throwable
     */
    public function postStepThreeEdit($resourceId, Request $request): Redirector|Application|RedirectResponse
    {
        $validatedData = $request->validate([
            'translation_rights' => 'integer',
            'educational_resource' => 'integer',
            'iam_author' => 'integer',
            'copyright_holder' => 'string|nullable',
            'creative_commons' => 'integer',
            'creative_commons_other' => 'integer',
        ]);

        $request->session()->put('edit_resource_step_3', $validatedData);

        $resource1 = $request->session()->get('edit_resource_step_1');
        $resource2 = $request->session()->get('edit_resource_step_2');
        $resource3 = $request->session()->get('edit_resource_step_3');
        $resource3['published'] = $request->input('published');

        $request->session()->forget('edit_resource_step_1');
        $request->session()->forget('edit_resource_step_2');
        $request->session()->forget('edit_resource_step_3');
        $request->session()->save();

        $finalArray = array_merge($resource1, $resource2, $resource3);

        $result = DB::transaction(function () use ($resourceId, $finalArray) {
            $myResources = Resource::find($resourceId);

            $myResources->title = $finalArray['title'];
            $myResources->abstract = $finalArray['abstract'];
            $myResources->language = $finalArray['language'];
            $myResources->resource_file_id = $finalArray['resource_file_id'];
            $myResources->status = $finalArray['published'];
            $myResources->published_at = date('Y-m-d H:i:s');

            // inserting to resource table
            $myResources->save();

            if ($myResources->resource_file_id) {

                ResourceFile::where(['id' => $myResources->resource_file_id, 'resource_id' => null])->update(['resource_id' => $myResources->id]);
            }

            // Updating Attachments
            if (isset($finalArray['attc'])) {
                ResourceAttachment::where('resource_id', $resourceId)->delete();
                foreach ($finalArray['attc'] as $attc) {
                    $myAttachments = new ResourceAttachment;
                    $myAttachments->resource_id = $resourceId;
                    $myAttachments->file_name = $attc['file_name'];
                    $myAttachments->file_mime = $attc['file_mime'];
                    $myAttachments->file_size = $attc['file_size'];
                    $myAttachments->save();
                }
            }

            // Deleting Subject Areas
            $theSubjects = ResourceSubjectArea::where('resource_id', $resourceId);
            $theSubjects?->delete();

            // Inserting Subject Areas
            foreach ($finalArray['subject_areas'] as $subject) {
                $mySubjects = new ResourceSubjectArea;
                $mySubjects->resource_id = $myResources->id;
                $mySubjects->tid = $subject;
                $mySubjects->save();
            }

            // Delete Keywords
            $theKeyword = ResourceKeyword::where('resource_id', $resourceId);
            $theKeyword?->delete();

            if (isset($finalArray['keywords'])) {
                $keywords = trim($finalArray['keywords'], ',');
                $keywords = explode(',', $keywords);
                if ($keywords != null) {
                    foreach ($keywords as $kw) {
                        $theTaxonomy = TaxonomyTerm::where('name', trim($kw))
                            ->where('vid', 23)
                            ->first();

                        if ($theTaxonomy != null) {
                            $myKeywords = new ResourceKeyword;
                            $myKeywords->resource_id = $myResources->id;
                            $myKeywords->tid = $theTaxonomy->id;
                            $myKeywords->save();
                        } else {
                            $myTaxonomy = new TaxonomyTerm;
                            $myTaxonomy->vid = 23;
                            $myTaxonomy->name = trim($kw);
                            $myTaxonomy->language = $finalArray['language'];
                            $myTaxonomy->save();

                            $myKeywords = new ResourceKeyword;
                            $myKeywords->resource_id = $myResources->id;
                            $myKeywords->tid = $myTaxonomy->id;
                            $myKeywords->save();
                        }
                    }
                }
            }

            // Deleting Authors
            $theAuthors = ResourceAuthor::where('resource_id', $resourceId);
            $theAuthors?->delete();

            // Inserting Authors
            $authors = trim($finalArray['author'], ',');
            $authors = explode(',', $authors);
            foreach ($authors as $author) {
                $theTaxonomy = TaxonomyTerm::where('name', trim($author))
                    ->where('vid', 24)
                    ->first();

                $myAuthor = new ResourceAuthor;
                $myAuthor->resource_id = $resourceId;
                if ($theTaxonomy != null) {
                    $myAuthor->tid = $theTaxonomy->id;
                } else {
                    $myTaxonomy = new TaxonomyTerm;
                    $myTaxonomy->vid = 24;
                    $myTaxonomy->name = $author;
                    $myTaxonomy->language = $finalArray['language'];
                    $myTaxonomy->save();

                    $myAuthor->tid = $myTaxonomy->id;
                }
                $myAuthor->save();
            }

            // Deleting Publishers
            $thePublisher = ResourcePublisher::where('resource_id', $resourceId);
            if ($thePublisher != null) {
                $thePublisher->delete();
            }

            // Inserting Publisher
            if (isset($finalArray['publisher'])) {
                $publisherName = trim($finalArray['publisher'], ',');
                $theTaxonomy = TaxonomyTerm::where('name', $publisherName)
                    ->where('vid', 9)
                    ->first();

                $myPublisher = new ResourcePublisher;
                $myPublisher->resource_id = $resourceId;
                if ($theTaxonomy != null) {
                    $myPublisher->tid = $theTaxonomy->id;
                } else {
                    $myTaxonomy = new TaxonomyTerm;
                    $myTaxonomy->vid = 9;
                    $myTaxonomy->name = $publisherName;
                    $myTaxonomy->language = $finalArray['language'];
                    $myTaxonomy->save();

                    $myPublisher->tid = $myTaxonomy->id;
                }
                $myPublisher->save();
            }

            // Deleting Translators
            $theTranslator = ResourceTranslator::where('resource_id', $resourceId);
            $theTranslator?->delete();

            // Inserting Translators
            if (isset($finalArray['translator'])) {
                $translators = trim($finalArray['translator'], ',');
                $translators = explode(',', $translators);
                foreach ($translators as $translator) {
                    $theTaxonomy = TaxonomyTerm::where('name', trim($translator))
                        ->where('vid', 24)
                        ->first();

                    $myTranslator = new ResourceTranslator;
                    $myTranslator->resource_id = $resourceId;
                    if ($theTaxonomy != null) {
                        $myTranslator->tid = $theTaxonomy->id;
                        $myTranslator->save();
                    } else {
                        $myTaxonomy = new TaxonomyTerm;
                        $myTaxonomy->vid = 24;
                        $myTaxonomy->name = $translator;
                        $myTaxonomy->language = $finalArray['language'];
                        $myTaxonomy->save();

                        $myTranslator->tid = $myTaxonomy->id;
                    }
                    $myTranslator->save();
                }
            }

            // Deleting Learning Resource Types
            $theLearningResourceType = ResourceLearningResourceType::where('resource_id', $resourceId);
            $theLearningResourceType?->delete();

            // Inserting Learning Resource Types
            foreach ($finalArray['learning_resources_types'] as $ltype) {
                $myLearningType = new ResourceLearningResourceType;
                $myLearningType->resource_id = $resourceId;
                $myLearningType->tid = $ltype;
                $myLearningType->save();
            }

            // Deleting Educational Use
            $theEduUse = ResourceEducationalUse::where('resource_id', $resourceId);
            $theEduUse?->delete();

            // Inserting Educational Use
            foreach ($finalArray['educational_use'] as $edus) {
                $myEdu = new ResourceEducationalUse;
                $myEdu->resource_id = $resourceId;
                $myEdu->tid = $edus;
                $myEdu->save();
            }

            // Deleting Levels
            $theLevels = ResourceLevel::where('resource_id', $resourceId);
            $theLevels?->delete();

            // Inserting Resource Levels
            foreach ($finalArray['level'] as $level) {
                $myLevel = new ResourceLevel;
                $myLevel->resource_id = $resourceId;
                $myLevel->tid = $level;
                $myLevel->save();
            }

            // Deleting Translation Rights
            $theTransRight = ResourceTranslationRight::where('resource_id', $resourceId);
            $theTransRight?->delete();

            if (isset($finalArray['translation_rights'])) {
                // Inserting Translation Rights
                $myTranslationRight = new ResourceTranslationRight;
                $myTranslationRight->resource_id = $resourceId;
                $myTranslationRight->value = $finalArray['translation_rights'];
                $myTranslationRight->save();
            }

            // Deleting Educational Resource
            $theEduResource = ResourceEducationalResource::where('resource_id', $resourceId);
            $theEduResource?->delete();

            if (isset($finalArray['educational_resource'])) {
                // Inserting Educational Resource
                $myEduResource = new ResourceEducationalResource;
                $myEduResource->resource_id = $resourceId;
                $myEduResource->value = $finalArray['educational_resource'];
                $myEduResource->save();
            }

            // Deleting I am author
            $theIamAuthor = ResourceIamAuthor::where('resource_id', $resourceId);
            $theIamAuthor?->delete();

            if (isset($finalArray['iam_author'])) {
                // Inserting i am author
                $myIamAuthor = new ResourceIamAuthor;
                $myIamAuthor->resource_id = $resourceId;
                $myIamAuthor->value = $finalArray['iam_author'];
                $myIamAuthor->save();
            }

            // Deleting Copyright Holder
            $theCopyrightHolder = ResourceCopyrightHolder::where('resource_id', $resourceId);
            $theCopyrightHolder?->delete();

            if (isset($finalArray['copyright_holder'])) {
                // Inserting Copyright Holder
                $myCopyrightHolder = new ResourceCopyrightHolder;
                $myCopyrightHolder->resource_id = $resourceId;
                $myCopyrightHolder->value = $finalArray['copyright_holder'];
                $myCopyrightHolder->save();
            }

            // Deleting Creative Commons
            $theCC = ResourceCreativeCommon::where('resource_id', $resourceId);
            $theCC?->delete();

            if (isset($finalArray['creative_commons'])) {
                // Inserting Creative Commons
                $myCC = new ResourceCreativeCommon;
                $myCC->resource_id = $resourceId;
                $myCC->tid = $finalArray['creative_commons'];
                $myCC->save();
            }

            // Deleting Creative Commons
            $theCCOther = ResourceSharePermission::where('resource_id', $resourceId);
            $theCCOther?->delete();

            if (isset($finalArray['creative_commons_other'])) {
                // Inserting Sharing Permission
                $mySharePermit = new ResourceSharePermission;
                $mySharePermit->resource_id = $resourceId;
                $mySharePermit->tid = $finalArray['creative_commons_other'];
                $mySharePermit->save();
            }

            return true;
        });

        if ($result) {
            Session::flash('alert', [
                'message' => __('Resource updated successfully'),
                'level' => 'success',
            ]);

            return redirect("resource/$resourceId");
        }

        Session::flash('alert', [
            'message' => __('Resource could not be updated.'),
            'level' => 'danger',
        ]);

        return redirect("resource/$resourceId");
    }

    public function deleteFile(Request $request, $resourceId, $fileName): Redirector|Application|RedirectResponse
    {
        DB::beginTransaction();

        try {
            Storage::disk('s3')->delete('resources/'.$fileName);

            ResourceAttachment::where('resource_id', $resourceId)->where('file_name', $fileName)->delete();

            $resource = Resource::find($request->resourceId);

            $dataAttachments = $resource->resourceAttachments($resourceId)->toArray();
            foreach ($dataAttachments as $item) {
                $resourceAttachments[] = [
                    'file_name' => $item->file_name,
                    'file_size' => $item->file_size,
                    'file_mime' => $item->file_mime,
                ];
            }
            $resource['attc'] = $resourceAttachments;
            $request->session()->put('edit_resource_step_2', $resource);
            $request->session()->save();

            DB::commit();
            Session::flash('alert', [
                'message' => __('Your file successfully has been deleted.'),
                'level' => 'success',
            ]);

            return redirect("resources/edit/step2/$resourceId");
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('alert', [
                'message' => __('Operation has failed.'),
                'level' => 'danger',
            ]);

            return redirect("resources/edit/step2/$resourceId");
        }
    }

    public function published($resourceId): RedirectResponse
    {
        $rs = Resource::find($resourceId);
        if ($rs->status == 1) {
            $rs->status = 0;
        } else {
            $rs->status = 1;
            $rs->published_at = date('Y-m-d H:i:s');
        }
        $rs->save();

        return redirect()->back();
    }

    /**
     * Delete a resource
     */
    public function deleteResource($resourceId): RedirectResponse
    {
        $resource = Resource::findOrFail($resourceId);

        DB::beginTransaction();

        try {
            // 1. Delete physical attachment files from storage and database records
            $attachments = ResourceAttachment::where('resource_id', $resourceId)->get();
            $diskType = config('app.env') != 'production' ? 'public' : 's3';

            foreach ($attachments as $attachment) {
                try {
                    // Delete physical file from storage
                    Storage::disk($diskType)->delete('resources/'.$attachment->file_name);
                } catch (\Exception $e) {
                    Log::warning("Failed to delete attachment file: {$attachment->file_name}", [
                        'error' => $e->getMessage(),
                        'resource_id' => $resourceId,
                    ]);
                }
            }

            // Delete attachment records from database
            ResourceAttachment::where('resource_id', $resourceId)->delete();

            // 2. Handle ResourceFile relationship
            if ($resource->resource_file_id) {
                $resourceFile = ResourceFile::find($resource->resource_file_id);

                if ($resourceFile) {
                    if ($resourceFile->resource_id == $resourceId) {
                        $otherResourcesUsingFile = Resource::where('resource_file_id', $resourceFile->id)
                            ->where('id', '!=', $resourceId)
                            ->first();

                        if (! $otherResourcesUsingFile) {
                            $diskType = config('app.env') != 'production' ? 'public' : 's3';

                            try {
                                Storage::disk($diskType)->delete('files/'.$resourceFile->name);
                            } catch (Exception $e) {
                                Log::warning("Failed to delete ResourceFile main file: {$resourceFile->name}", [
                                    'error' => $e->getMessage(),
                                    'resource_file_id' => $resourceFile->id,
                                ]);
                            }

                            try {
                                Storage::disk($diskType)->delete('files/thumbnails/'.$resourceFile->name);
                            } catch (Exception $e) {
                                Log::warning("Failed to delete ResourceFile thumbnail: {$resourceFile->name}", [
                                    'error' => $e->getMessage(),
                                    'resource_file_id' => $resourceFile->id,
                                ]);
                            }

                            $resource->resource_file_id = null;
                            $resource->save();
                            $resourceFile->delete();
                        } else {
                            $resourceFile->resource_id = $otherResourcesUsingFile->id;
                            $resourceFile->save();
                        }
                    }
                }
            }

            // 3. Delete the resource
            $resource->delete();

            DB::commit();

            Session::flash('success', 'Resource deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('error', 'Failed to delete resource. Please try again.');

        }

        return redirect('admin/resources');
    }

    public function getValidatedData(mixed $resource, array $validatedData): array
    {
        if (isset($resource['attc'])) {
            for ($i = 0; $i < count($resource['attc']); $i++) {
                $validatedData['attc'][] = [
                    'file_name' => $resource['attc'][$i]['file_name'],
                    'file_size' => $resource['attc'][$i]['file_size'],
                    'file_mime' => $resource['attc'][$i]['file_mime'],
                ];
            }
        }

        return $validatedData;
    }

    public function getEpub($resourceAttachment, $key)
    {
        $secret = config('s3.config.secret');
        $decrypted_key = decrypt($key);
        $received_time = $decrypted_key / ($secret ?: 1);
        $current_time = time();

        if ($current_time - $received_time < 300) { // 300 - tolerance of 5 minutes
            return Storage::disk('s3')->temporaryUrl('resources/'.$resourceAttachment->file_name, now()->addHours(1));
        } else {
            abort(403);
        }
    }

    public function resourceDownloadCounter(Request $request)
    {
        $validated = $request->validate([
            'resource_id' => 'required|integer|exists:resources,id',
            'file_id' => [
                'required',
                'integer',
                'exists:resource_attachments,id,resource_id,'.$request->resource_id,
            ],
        ]);

        $counted = DownloadCount::create([
            'resource_id' => $validated['resource_id'],
            'file_id' => $validated['file_id'],
            'user_id' => auth()->id() ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'id' => $counted->id,
        ], 201);
    }
}
