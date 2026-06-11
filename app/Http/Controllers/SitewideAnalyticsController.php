<?php

namespace App\Http\Controllers;

use App\Enums\LanguageEnum;
use App\Models\Browser;
use App\Models\Device;
use App\Models\Platform;
use App\Models\SitewidePageView;
use App\Traits\GenderTrait;
use App\Traits\LanguageTrait;
use App\Traits\SitewidePageViewConditionTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SitewideAnalyticsController extends Controller
{
    use GenderTrait, LanguageTrait, SitewidePageViewConditionTrait;

    public function index(Request $request): View
    {
        $languages = $this->getLanguages();
        $genders = $this->genders();
        $devices = Device::all(['id', 'name']);
        $browsers = Browser::all(['id', 'name']);
        $platforms = Platform::all(['id', 'name']);

        $top10ViewedPages = $this->getTop10ViewedPages($request);
        $totalViews = $this->getTotalViews($request);
        $totalRegisteredUsersViews = $this->getTotalViews($request, 'no');

        $platformCounts = Platform::select(['id', 'name'])
            ->withCount([
                'sitewidePageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $browserCounts = Browser::select(['id', 'name'])
            ->withCount([
                'sitewidePageViews' => function ($query) use ($request) {
                    return $this->filterPageViews($query, $request);
                },
            ])
            ->get();

        $totalGuestViews = $this->getTotalViews($request, 'yes');
        $totalViewsBasedOnLanguage = $this->getTotalViewsBasedOnLanguage($request);

        return view('admin.analytics.sitewide.index', compact('languages', 'genders', 'devices', 'platforms', 'browsers', 'top10ViewedPages', 'totalViews', 'totalRegisteredUsersViews', 'totalGuestViews', 'platformCounts', 'browserCounts', 'totalViewsBasedOnLanguage'));
    }

    private function getTop10ViewedPages($request): Collection
    {
        $query = SitewidePageView::selectRaw('page_url, title, COUNT(*) AS visit_count')->groupBy('page_url', 'title')->orderByDesc('visit_count')->limit(10);

        $query = $this->filterPageViews($query, $request);

        return $query->get();
    }

    private function getTotalViews($request, $isGuest = null): float
    {
        $query = SitewidePageView::query();

        if ($isGuest) {
            if ($isGuest == 'yes') {
                $query->whereNull('user_id');
            } else {
                $query->whereNotNull('user_id');
            }
        }

        $query = $this->filterPageViews($query, $request);

        return $query->count();
    }

    private function getTotalViewsBasedOnLanguage($request): \Illuminate\Support\Collection
    {
        $totalResources = SitewidePageView::where(function ($query) use ($request) {
            return $this->filterPageViews($query, $request);
        })
            ->groupBy('language')
            ->select('language', DB::raw('count(*) as view_count'))
            ->orderBy('view_count', 'desc')
            ->get();

        return $totalResources->map(function ($item) {
            $item['language'] = LanguageEnum::tryFrom($item['language'])?->name ?? $item['language'];

            return $item;
        });
    }

    public function view(Request $request)
    {
        $languages = $this->getLanguages();
        $genders = $this->genders();
        $devices = Device::all(['id', 'name']);
        $browsers = Browser::all(['id', 'name']);
        $platforms = Platform::all(['id', 'name']);

        $query = SitewidePageView::query()->with(['platform:id,name', 'device:id,name', 'browser:id,name', 'user:id', 'user.profile:id,user_id,first_name,last_name']);
        $query = $this->filterPageViews($query, $request);
        $views = $query->paginate()
            ->appends($request->except(['page']));

        return view('admin.analytics.sitewide.get-views', compact(['views', 'languages', 'genders', 'devices', 'browsers', 'platforms']));
    }
}
