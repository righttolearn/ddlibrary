<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @method static find($resourceId)
 * @method static findOrFail($resourceId)
 * @method static published()
 */
class Resource extends Model
{
    use CausesActivity;
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    protected static $logAttributes = ['*'];

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_levels', 'resource_id', 'tid');
    }

    public function IamAuthors()
    {
        return $this->HasOne(ResourceIamAuthor::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(ResourceKeyword::class);
    }

    public function LearningResourceTypes(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_learning_resource_types', 'resource_id', 'tid');
    }

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_publishers', 'resource_id', 'tid');
    }

    public function SharePermissions(): HasOne
    {
        return $this->hasOne(ResourceSharePermission::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_subject_areas', 'resource_id', 'tid');
    }

    public function subjectsIcons(): BelongsToMany
    {
        return $this->belongsToMany(StaticSubjectIcon::class, 'resource_subject_areas', 'resource_id', 'tid');
    }

    public function TranslationRights(): HasOne
    {
        return $this->hasOne(ResourceTranslationRight::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ResourceView::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ResourceAttachment::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_authors', 'resource_id', 'tid');
    }

    public function translators(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_translators', 'resource_id', 'tid');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ResourceComment::class);
    }

    public function CopyrightHolder(): HasOne
    {
        return $this->hasOne(ResourceCopyrightHolder::class);
    }

    public function creativeCommons(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'resource_creative_commons', 'resource_id', 'tid');
    }

    public function EducationalResources(): HasMany
    {
        return $this->hasMany(ResourceEducationalResource::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_favorites', 'resource_id', 'user_id');
    }

    public function flags(): HasMany
    {
        return $this->hasMany(ResourceFlag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function published($query)
    {
        return $query->where('status', 1);
    }

    public function getResources($resourceId)
    {
        return DB::table('resources AS rs')
            ->select('rs.id', 'rs.language', 'rs.status', 'rs.title', 'rs.abstract', 'ttda.name AS author', 'ttdp.name AS publisher', 'ttdt.name AS translator')
            ->leftJoin('resource_authors AS ra', 'ra.resource_id', '=', 'rs.id')
            ->leftJoin('resource_publishers AS rp', 'rp.resource_id', '=', 'rs.id')
            ->leftJoin('resource_translators AS rt', 'rt.resource_id', '=', 'rs.id')
            ->leftJoin('taxonomy_term_data AS ttda', 'ttda.id', '=', 'ra.tid')
            ->leftJoin('taxonomy_term_data AS ttdp', 'ttdp.id', '=', 'rp.tid')
            ->leftJoin('taxonomy_term_data AS ttdt', 'ttdt.id', '=', 'rt.tid')
            ->where('rs.id', $resourceId)
            ->orderBy('rs.created_at', 'desc')
            ->first();
    }

    public function filterResources($requestArray)
    {
        return DB::table('resources AS rs')
            ->select('rs.id', 'rs.language', 'rs.title', 'rs.abstract', 'rs.user_id', 'rs.tnid', 'users.username AS addedby', 'rs.status', 'rs.created_at', 'rs.updated_at')
            ->LeftJoin('users', 'users.id', '=', 'rs.user_id')
            ->LeftJoin('resource_subject_areas AS rsa', 'rsa.resource_id', '=', 'rs.id')
            ->LeftJoin('resource_levels AS rl', 'rl.resource_id', '=', 'rs.id')
            ->LeftJoin('resource_learning_resource_types AS rlrt', 'rlrt.resource_id', '=', 'rs.id')
            ->LeftJoin('resource_attachments AS ra', 'ra.resource_id', '=', 'rs.id')
            ->when(! empty($requestArray['title']), function ($query) use ($requestArray) {
                return $query->where('rs.title', 'like', '%'.$requestArray['title'].'%');
            })
            ->when(isset($requestArray['status']), function ($query) use ($requestArray) {
                return $query->where('rs.status', $requestArray['status']);
            })
            ->when(isset($requestArray['language']), function ($query) use ($requestArray) {
                return $query->where('rs.language', $requestArray['language']);
            })
            ->when(isset($requestArray['date_from']) && isset($requestArray['date_to']), function ($query) use ($requestArray) {
                return $query->whereBetween('rs.created_at', [$requestArray['date_from'], $requestArray['date_to']]);
            })
            ->when(isset($requestArray['subject_area']), function ($query) use ($requestArray) {
                return $query->where('rsa.tid', $requestArray['subject_area']);
            })
            ->when(isset($requestArray['level']), function ($query) use ($requestArray) {
                return $query->where('rl.tid', $requestArray['level']);
            })
            ->when(isset($requestArray['type']), function ($query) use ($requestArray) {
                return $query->where('rlrt.tid', $requestArray['type']);
            })
            ->when(isset($requestArray['format']), function ($query) use ($requestArray) {
                return $query->where('ra.file_mime', $requestArray['format']);
            })
            ->orderBy('rs.created_at', 'desc')
            ->groupBy('rs.id', 'rs.language', 'rs.title', 'rs.abstract', 'rs.user_id', 'users.username', 'rs.status', 'rs.updated_at', 'rs.tnid', 'rs.created_at')
            ->paginate(10);
    }

    public function paginateResources(): LengthAwarePaginator
    {
        return DB::table('resources AS rs')
            ->select('rs.id', 'rs.language', 'rs.title', 'rs.abstract', 'rs.user_id', 'users.username AS author', 'rs.status', 'rs.updated_at')
            ->join('users', 'users.id', '=', 'rs.user_id')
            ->where('rs.language', config('app.locale'))
            ->where('rs.status', 1)
            ->orderBy('rs.created', 'desc')
            ->groupBy('rs.id', 'rs.language', 'rs.title', 'rs.abstract', 'rs.user_id', 'users.username', 'rs.status', 'rs.updated_at', 'rs.created_at')
            ->paginate(32);
    }

    public function resourceAttributes($resourceId, $tableName, $fieldName, $staticTable): Collection
    {
        return DB::table($tableName)
            ->select($staticTable.'.name', $staticTable.'.id')
            ->join($staticTable, $staticTable.'.id', '=', $tableName.'.'.$fieldName)
            ->where('resource_id', $resourceId)
            ->get();
    }

    public function searchResourceAttributes($keyword, $staticTable, $vid): Collection
    {
        return DB::table($staticTable)
            ->select($staticTable.'.name AS value')
            ->where($staticTable.'.name', 'like', '%'.$keyword.'%')
            ->where($staticTable.'.vid', $vid)
            ->get();
    }

    public function resourceAttributesList($tableName, $vid, $language = null): Collection
    {
        $language = $language ?? config('app.locale');
        return DB::table($tableName.' AS ttd')
            ->select('ttd.id', 'ttd.name', 'tth.parent', 'ttd.tnid')
            ->leftJoin('taxonomy_term_hierarchy AS tth', 'tth.tid', '=', 'ttd.id')
            ->where('vid', $vid)
            ->where('language', $language)
            ->orderBy('ttd.name')
            ->orderBy('ttd.weight', 'desc')
            ->get();
    }

    // Total resources based on language
    public function totalResourcesByLanguage(): Collection
    {
        return DB::table('resources AS rs')
            ->select('rs.language', DB::raw('count(rs.id) as total'))
            ->groupBy('rs.language')
            ->get();
    }

    // Total resources based on subject area List
    public function totalResourcesBySubject($lang = 'en', $date_from = '', $date_to = ''): Collection
    {
        return DB::table('resource_subject_areas AS rsa')
            ->select('ttd.id', 'ttd.name', 'ttd.language', 'ttd.tnid', DB::raw('count(rsa.id) as total'))
            ->LeftJoin('taxonomy_term_data AS ttd', function ($join) {
                $join->on('ttd.id', '=', 'rsa.tid')->where('ttd.vid', 8);
            })
            ->where('ttd.language', $lang)
            ->when($date_from, function ($query) use ($date_from, $date_to) {
                return $query->join('resources AS r', 'r.id', '=', 'rsa.resource_id')->whereBetween('r.created_at', [$date_from, $date_to]);
            })
            ->groupBy('ttd.name', 'ttd.id', 'ttd.language', 'ttd.tnid')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function paginateResourcesBy($request): LengthAwarePaginator
    {
        $subjectAreaIds = $request['subject_area'];
        $levelIds = $request['level'];
        $typeIds = $request['type'];

        if ($sessionQuery = session('search')) {
            $searchQuery = $sessionQuery;
        } else {
            $searchQuery = $request->input('search');
        }

        return DB::table('resources AS rs')
            ->select([
                'rs.id',
                'rs.language',
                'rs.abstract',
                'rs.title',
                'rs.status',
                'rf.name'
            ])
            ->leftjoin('resource_files AS rf', 'rf.id', '=', 'rs.resource_file_id')
            ->when($subjectAreaIds != null, function ($query) use ($subjectAreaIds) {
                return $query
                    ->join('resource_subject_areas AS rsa', 'rsa.resource_id', '=', 'rs.id')
                    ->join('taxonomy_term_hierarchy AS tth', 'tth.tid', '=', 'rsa.tid')
                    ->where(function ($q) use ($subjectAreaIds) {
                        $q->whereIn('tth.parent', (array) $subjectAreaIds)
                            ->orWhereIn('tth.tid', (array) $subjectAreaIds);
                    });
            })
            ->when($levelIds != null, function ($query) use ($levelIds) {
                return $query->join('resource_levels AS rl', function ($join) use ($levelIds) {
                    $join
                        ->on('rl.resource_id', '=', 'rs.id')
                        ->where('rl.tid', $levelIds);
                });
            })
            ->when($typeIds != null, function ($query) use ($typeIds) {
                return $query->join('resource_learning_resource_types AS rlrt', function ($join) use ($typeIds) {
                    $join
                        ->on('rlrt.resource_id', '=', 'rs.id')
                        ->where('rlrt.tid', $typeIds);
                });
            })
            ->when($searchQuery != null, function ($query) use ($searchQuery) {
                return $query
                    ->leftJoin('resource_authors AS ra', 'ra.resource_id', '=', 'rs.id')
                    ->leftJoin('resource_publishers AS rp', 'rp.resource_id', '=', 'rs.id')
                    ->leftJoin('resource_translators AS rt', 'rt.resource_id', '=', 'rs.id')
                    ->leftJoin('taxonomy_term_data AS ttd', 'ttd.id', '=', 'ra.tid')
                    ->leftJoin('taxonomy_term_data AS ttdp', 'ttdp.id', '=', 'rp.tid') // publisher
                    ->leftJoin('taxonomy_term_data AS ttdt', 'ttdt.id', '=', 'rt.tid') // translator
                    ->where(function ($query) use ($searchQuery) {
                        return $query
                            ->where('rs.title', 'like', '%'.$searchQuery.'%')
                            ->orwhere('rs.abstract', 'like', '%'.$searchQuery.'%')
                            ->orwhere('ttd.name', 'like', '%'.$searchQuery.'%')
                            ->orwhere('ttdp.name', 'like', '%'.$searchQuery.'%')
                            ->orwhere('ttdt.name', 'like', '%'.$searchQuery.'%');
                    });
            })
            ->when($request->filled('publisher'), function ($query) use ($request) {
                return $query
                    ->leftJoin('resource_publishers AS rpub', 'rpub.resource_id', '=', 'rs.id')
                    ->where('rpub.tid', $request['publisher']);
            })
            ->when(
                $request->filled('language'),
                function ($query) use ($request) {
                    return $query->where('rs.language', $request['language']);
                },
                function ($query) {
                    return $query->where('rs.language', config('app.locale'));
                }
            )
            ->where('rs.status', 1)
            ->where(function ($query) {
                $query->where('rs.id', '>=', 11479)->orWhere('rs.id', '<', 10378); // TODO: remove after restoration
            })
            ->orderBy('rs.created_at', 'desc')
            ->groupBy(
                'rs.id',
                'rs.language',
                'rs.title',
                'rs.status',
                'rf.name',
                'rs.abstract',
                'rs.created_at',
            )
            ->paginate(30);
    }

    // Total resources based on level
    public function totalResourcesByLevel($lang = 'en'): Collection
    {
        return DB::table('resource_levels AS rl')
            ->select('ttd.id', 'ttd.name', 'ttd.language', DB::Raw('count(rl.id) as total'))
            ->join('taxonomy_term_data AS ttd', function ($join) {
                $join->on('ttd.id', '=', 'rl.tid')->where('ttd.vid', 13);
            })
            ->where('ttd.language', $lang)
            ->groupBy('ttd.name', 'ttd.id', 'ttd.language')
            ->orderBy('total', 'DESC')
            ->get();
    }

    // Total resources based on Resource Type
    public function totalResourcesByType($lang = 'en'): Collection
    {
        return DB::table('resource_learning_resource_types AS rlrt')
            ->select('ttd.id', 'ttd.name', 'ttd.language', DB::Raw('count(rlrt.id) as total'))
            ->join('taxonomy_term_data AS ttd', function ($join) {
                $join->on('ttd.id', '=', 'rlrt.tid')->where('ttd.vid', 7);
            })
            ->where('ttd.language', $lang)
            ->groupBy('ttd.id', 'ttd.name', 'ttd.language')
            ->orderBy('total', 'DESC')
            ->get();
    }

    // Total resources by attachment type (format)
    public function totalResourcesByFormat($lang = 'en'): Collection
    {
        return DB::table('resources AS rs')
            ->select('ra.file_mime', 'rs.language', DB::raw('count(rs.id) as total'))
            ->join('resource_attachments AS ra', 'ra.resource_id', '=', 'rs.id')
            ->where('rs.language', $lang)
            ->groupBy('ra.file_mime', 'rs.language')
            ->orderby('rs.language')
            ->orderBy('total', 'DESC')
            ->get();
    }

    // Total resources by attachment type (format)
    public function downloadCounts($date_from, $date_to)
    {
        return DB::table('download_counts')
            ->select(DB::raw('count(id) as total'))
            ->when($date_from, function ($query) use ($date_from, $date_to) {
                return $query->whereBetween('created_at', [$date_from, $date_to]);
            })
            ->first()->total;
    }

    public function getRelatedResources($resourceId, $subjectAreas): Collection
    {
        $ids = [];
        foreach ($subjectAreas as $item) {
            $ids[] = $item->id;
        }

        return DB::table('resources AS rs')
            ->select('rs.id', 'rs.title', 'rs.abstract', 'rf.name')
            ->join('resource_subject_areas AS rsa', 'rsa.resource_id', '=', 'rs.id')
            ->leftjoin('resource_files AS rf', 'rf.id', '=', 'rs.resource_file_id')
            // not to include the record itself in the related items part
            ->where('rs.id', '!=', $resourceId)
            ->where('rs.status', 1)
            ->whereIn('rsa.tid', $ids)
            ->limit(5)
            ->get();
    }

    public function subjectIconsAndTotal($lang = ''): Collection
    {
        $lang = ! $lang ? config('app.locale') : $lang;

        return DB::table('resource_subject_areas AS sarea')
            ->select('sticons.file_name', 'ttd.name', 'ttd.id', 'sarea.tid AS subject_area')
            ->leftJoin('taxonomy_term_data AS ttd', function ($join) {
                $join->on('ttd.id', '=', 'sarea.tid')->where('ttd.vid', 8);
            })
            ->join('static_subject_area_icons AS sticons', 'sticons.tid', '=', 'ttd.id')
            ->where('ttd.language', $lang)
            ->groupBy('sarea.tid', 'sticons.file_name', 'ttd.name', 'ttd.id')
            ->get();
    }

    public static function countSubjectAreas($sId): ?object
    {
        return DB::table('resource_subject_areas AS rsa')
            ->select(DB::raw('count(rsa.tid) AS total'))
            ->join('resources AS rs', 'rs.id', '=', 'rsa.resource_id')
            ->join('taxonomy_term_hierarchy AS tth', 'tth.tid', '=', 'rsa.tid')
            ->where('tth.parent', $sId)
            ->where('rs.status', 1)
            ->where('rs.language', config('app.locale'))
            ->first();
    }

    public function featuredCollections($lang = ''): Collection
    {
        $lang = ! $lang ? config('app.locale') : $lang;

        return DB::table('featured_collections AS fcid')
            ->select('fcid.id', 'ttd.name', 'fcid.icon', 'ttd.language', 'fu.url', 'frt.type_id', 'frs.subject_id', 'frls.level_id')
            ->leftJoin('taxonomy_term_data AS ttd', function ($join) {
                $join->on('ttd.id', '=', 'fcid.name_tid')->where('ttd.vid', 21);
            })
            ->leftJoin('featured_resource_levels AS frl', 'frl.fcid', '=', 'fcid.id')
            ->leftJoin('featured_urls AS fu', 'fu.fcid', '=', 'fcid.id')
            ->leftJoin('featured_resource_types AS frt', 'frt.fcid', '=', 'fcid.id')
            ->leftJoin('featured_resource_subjects AS frs', 'frs.fcid', '=', 'fcid.id')
            ->leftJoin('featured_resource_levels AS frls', 'frls.fcid', '=', 'fcid.id')
            ->where('ttd.language', $lang)
            ->orderBy('fcid.id')
            ->get();
    }

    public function resourceAttachments($resourceId): Collection
    {
        return DB::table('resource_attachments AS ra')
            ->select('*')
            ->where('ra.resource_id', $resourceId)
            ->get();
    }

    public function getResourceTranslations($resourceId): Collection
    {
        return DB::table('resources AS rs')
            ->select('rs.id', 'rs.language', 'rs.title')
            ->where('rs.tnid', $resourceId)
            ->get();
    }

    public function updateResourceCounter($data): int
    {
        $agent = new Agent;

        return DB::table('resource_views')->insertGetId([
            'resource_id' => $data['resource_id'],
            'user_id' => $data['userid'],
            'ip' => $data['ip'],
            'is_bot' => $agent->isBot(),
            'browser_name' => $data['browser_name'],
            'browser_version' => $data['browser_version'],
            'platform' => $data['platform'],
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['title', 'created_at']);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DownloadCount::class);
    }

    public function resourceViews(): HasMany
    {
        return $this->hasMany(ResourceView::class);
    }

    public function resourceFavorites(): HasMany
    {
        return $this->hasMany(ResourceFavorite::class);
    }

    public function resourceFile(): BelongsTo
    {
        return $this->belongsTo(ResourceFile::class);
    }
}
