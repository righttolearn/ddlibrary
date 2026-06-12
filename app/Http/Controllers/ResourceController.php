<?php

namespace App\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Http\Requests\ResourceSaveRequest;
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
use App\Services\ResourceService;
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
use Illuminate\Support\Facades\Cache;
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
    public function __construct(
        private readonly Resource $resource,
        private readonly ResourceService $resourceService
    ) {}

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

    public function list(Request $request): View
    {
        DDLClearSession();
        $language = $request['language'] ?? null;
        $this->pageView($request, 'Resource List');

        $resource = new Resource;

        if ($request->filled('search')) {
            session(['search' => $request->input('search')]);
        }

        $request = $this->resolveSubjectAreaIds($request);

        $hasFilters = $request->hasAny(['search', 'subject_area', 'level', 'type', 'publisher']);

        if (!$hasFilters) {
            $lang = config('app.locale');
            $page = $request->input('page', 1);
            $resources = Cache::tags(['resource_list'])
                ->remember("resource_list_{$lang}_page_{$page}", 300, fn() =>
                $resource->paginateResourcesBy($request)
                );
        }
        else
            $resources = $resource->paginateResourcesBy($request);

        $resourceObject = new Resource;
        $parentSubjects = $resourceObject
            ->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceSubject)
            ->where('parent', 0);
        $resourceTypes = $resourceObject->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceType);
        $literacyLevels = $resourceObject
            ->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceLevels)
            ->where('parent', 0);
        $languages = $this->getLanguages();


        return view('resources.resources_list', compact(
            'resources',
            'parentSubjects',
            'resourceTypes',
            'literacyLevels',
            'languages'
        ));
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
    public function viewPublicResource(Request $request, $resourceId): View
    {
        $resource = Resource::with([
            'attachments',
            'resourceFile:id,name',
            'favorites' => fn($q) => $q->where('users.id', auth()->id())
        ])
            ->withCount('favorites')
            ->findOrFail($resourceId);

        if ($resource->status == 0 && ! (isAdmin() || isLibraryManager())) {  // We don't want anyone else to access unpublished resources
            abort(403);
        }

        $resource->load('subjects', 'levels', 'LearningResourceTypes', 'authors', 'translators', 'publishers', 'creativeCommons');
        $resource->attachments->each(function ($file) {
            $file->extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
        });

        $relatedItems = $this->resource->getRelatedResources($resourceId, $resource->subjects);
        $comments = ResourceComment::where('resource_id', $resourceId)->published()->get();
        $comments->load('user');

        $this->pageView($request, $resource->title); // TODO: To be deprecated
        $this->resourceViewCounter($request, $resourceId); // TODO: To be deprecated
        Resource::where('id', $resourceId)->increment('views_count');

        ['languages_available' => $languages_available, 'translations' => $translations] = $this->resolveAvailableLanguages($resource);

        $ePub = $this->resolveEpubUrl($resource);

        $viewData = compact(
            'resource',
            'relatedItems',
            'comments',
            'languages_available',
            'translations',
            'ePub'
        );

        if ($ePub) {
            $viewData['prevArrow'] = app()->getLocale() == 'en' ? '←' : '→';
            $viewData['nextArrow'] = app()->getLocale() == 'en' ? '→' : '←';
        }

        return view('resources.resources_view', $viewData);
    }

    public function form(?int $resourceId = null): View
    {
        $myResources = $this->resource;
        $edit = $resourceId !== null;

        $resource = $edit
            ? Resource::with([
                'authors',
                'translators',
                'publishers',
                'resourceFile:id,name',
                'attachments',
                'subjects',
                'levels',
                'LearningResourceTypes',
                'educationalUses',
                'creativeCommons',
                'translationRights',
                'educationalResources',
                'iamAuthors',
                'copyrightHolder',
                'sharePermissions',
                'keywords',
            ])->findOrFail($resourceId)
            : null;

        // Resource attribute lists
        $creativeCommons = $myResources->resourceAttributesList(
            'taxonomy_term_data',
            TaxonomyVocabularyEnum::CreativeCommons->value,
            config('app.locale'),
            [168, 535]
        );

        $subjects = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceSubject);
        $levels = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceLevels);
        $learningResourceTypes = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::ResourceType);
        $educationalUse = $myResources->resourceAttributesList('taxonomy_term_data', TaxonomyVocabularyEnum::EducationalUse);

        $subjects = $subjects->map(function ($item) use ($subjects) {
            $item->children = $subjects->where('parent', $item->id);
            return $item;
        });

        $levels = $levels->map(function ($item) use ($levels) {
            $item->children = $levels->where('parent', $item->id);
            return $item;
        });

        if ($edit) {
            $resourceSubjectAreas = $resource->subjects->pluck('id')->toArray();
            $resourceLearningResourceTypes = $resource->LearningResourceTypes->pluck('id')->toArray();
            $editEducationalUse = $resource->educationalUses->pluck('id')->toArray();
            $resourceLevels = $resource->levels->pluck('id')->toArray();
            $resourceKeywords = $resource->keywords->pluck('name')->implode(', ');
            $resourceRights = match(true) {
                $resource->TranslationRights !== null    => 'translation',
                $resource->educationalResources->isNotEmpty() => 'educational',
                $resource->iamAuthors !== null           => 'author',
                default                                  => null,
            };
            $resourceCopyrightHolder = $resource->CopyrightHolder?->value;
            $resourceCreativeCommons = $resource->creativeCommons->first()?->id;
            $resourceSharePermissions = $resource->SharePermissions?->tid;
        } else {
            $resourceSubjectAreas = null;
            $resourceLearningResourceTypes = null;
            $editEducationalUse = null;
            $resourceLevels = null;
            $resourceKeywords = '';
            $resourceRights = null;
            $resourceCopyrightHolder = null;
            $resourceCreativeCommons = null;
            $resourceSharePermissions = null;
        }

        return view('resources.resource_form', compact(
            'resource',
            'edit',
            'creativeCommons',
            'subjects',
            'levels',
            'learningResourceTypes',
            'educationalUse',
            'resourceSubjectAreas',
            'resourceLearningResourceTypes',
            'editEducationalUse',
            'resourceLevels',
            'resourceKeywords',
            'resourceRights',
            'resourceCopyrightHolder',
            'resourceCreativeCommons',
            'resourceSharePermissions',
        ));
    }

    public function save(ResourceSaveRequest $request, ?int $resourceId = null): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Only admins and library managers can publish directly
            if (!isAdmin() && !isLibraryManager()) {
                $data['published'] = 0;
            }

            $resource = $this->resourceService->save($data, $resourceId);

            $message = $resourceId
                ? __('Resource updated successfully.')
                : (isAdmin() || isLibraryManager()
                    ? __('Resource successfully added!')
                    : __('Resource successfully added! It will be published after review.'));

            Session::flash('alert', [
                'message' => $message,
                'level'   => 'success',
            ]);

            return redirect("resource/{$resource->id}");

        } catch (\Throwable $e) {
            Session::flash('alert', [
                'message' => $resourceId
                    ? __('Resource could not be updated.')
                    : __('Resource couldn\'t be added.'),
                'level' => 'danger',
            ]);

            return back()->withInput();
        }
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
            Resource::where('id', $resourceId)
                ->where('favorites_count', '>', 0)
                ->decrement('favorites_count');
            $action = 'deleted';
        } else {
            ResourceFavorite::create([
                'resource_id' => $resourceId,
                'user_id' => $userId,
            ]);
            Resource::where('id', $resourceId)->increment('favorites_count');
            $action = 'added';
        }

        return response()->json([
            'action' => $action,
            'favorite_count' => Resource::where('id', $resourceId)->value('favorites_count'),
        ]);
    }

    public function flag(Request $request): Redirector|Application|RedirectResponse
    {
        $userId = auth()->id();
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
        $userId = auth()->id();
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

    public function deleteFile(Request $request, $resourceId, $fileName): Redirector|Application|RedirectResponse
    {
        DB::beginTransaction();

        try {
            Storage::delete('resources/'.$fileName);

            ResourceAttachment::where('resource_id', $resourceId)
                ->where('file_name', $fileName)
                ->delete();

            DB::commit();

            Session::flash('alert', [
                'message' => __('Your file successfully has been deleted.'),
                'level' => 'success',
            ]);

            return redirect()->route('resource.form.edit', $resourceId);

        } catch (\Exception $e) {
            DB::rollback();

            Session::flash('alert', [
                'message' => __('Operation has failed.'),
                'level' => 'danger',
            ]);

            return redirect()->route('resource.form.edit', $resourceId);
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

            foreach ($attachments as $attachment) {
                try {
                    // Delete physical file from storage
                    Storage::delete('resources/'.$attachment->file_name);
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
                            try {
                                Storage::delete('files/'.$resourceFile->name);
                            } catch (Exception $e) {
                                Log::warning("Failed to delete ResourceFile main file: {$resourceFile->name}", [
                                    'error' => $e->getMessage(),
                                    'resource_file_id' => $resourceFile->id,
                                ]);
                            }

                            try {
                                Storage::delete('files/thumbnails/'.$resourceFile->name);
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

    private function resolveSubjectAreaIds(Request $request): Request
    {
        if (
            $request->filled('subjectAreaParent') ||
            $request->filled('subjectAreaChild')
        ) {
            $subjectAreaParentIds = [];
            $parentIdsfromChildren = [];
            $subjectAreaChildIds = [];

            if ($request->filled('subjectAreaParent')) {
                $subjectAreaParentIds = $request->input('subjectAreaParent', []);
            }

            if ($request->filled('subjectAreaChild')) {
                $subjectAreaChildIds = $request->input('subjectAreaChild', []);
                $parentIdsfromChildren = (new Resource)
                    ->resourceAttributesList('taxonomy_term_data', 8)
                    ->whereIn('id', $subjectAreaChildIds)
                    ->pluck('parent')
                    ->toArray();
            }

            $bothParentIds = array_merge($parentIdsfromChildren, $subjectAreaParentIds);
            $noDuplicateParentAreaIds = array_keys(
                array_intersect(
                    array_count_values($bothParentIds),
                    [1]
                )
            );

            $finalSubjectAreaIds = array_merge($noDuplicateParentAreaIds, $subjectAreaChildIds);
            $finalSubjectAreaIds = array_map('strval', $finalSubjectAreaIds);
            $request->query->remove('subjectAreaParent');
            $request->query->add(['subject_area' => $finalSubjectAreaIds]);
        }

        return $request;
    }

    private function resolveAvailableLanguages(Resource $resource): array
    {
        $translation_id = $resource->tnid;

        if (!$translation_id) {
            return ['languages_available' => [], 'translations' => null];
        }

        $translations = $this->resource->getResourceTranslations($translation_id);

        if (!$translations) {
            return ['languages_available' => [], 'translations' => null];
        }

        $supportedLocals = array_keys(config('laravellocalization.localesOrder'));

        $newId = $translations
            ->filter(fn($tr) => in_array($tr->language, $supportedLocals))
            ->pluck('id', 'language')
            ->toArray();

        $languages_available = [];
        foreach (\LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            if (isset($newId[$localeCode]) && $newId[$localeCode] != 0) {
                $currentUrl = explode('/', url()->current());
                $currentUrl[count($currentUrl) - 1] = $newId[$localeCode];
                $languages_available[$localeCode] = [
                    'url'    => implode('/', $currentUrl),
                    'native' => $properties['native'],
                ];
            }
        }

        return ['languages_available' => $languages_available, 'translations' => $translations];
    }

    private function resolveEpubUrl(Resource $resource): ?string
    {
        $ePubFile = $resource->attachments->where('file_mime', 'application/epub+zip')->first();
        if (!$ePubFile) return null;

        return config('app.env') != 'production'
            ? asset('files/resources/' . $ePubFile->file_name)
            : getFile("resources/{$ePubFile->file_name}");
    }
}
