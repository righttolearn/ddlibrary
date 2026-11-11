<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $roles = $this->getTotalUsersBasedOnRole($request);
        $totalRegisteredUsers = $this->getTotalRegisteredUsers($request);
        $totalUsersBaseOnGenders = $this->getTotalUsersBasedOnGender($request);
        $totalUsers = $this->getTotalUsersBasedOnRegistrationSource($request);
        $totalGoogleUsers = $this->getTotalUsersBasedOnRegistrationSource($request, 'google');
        $totalFacebookUsers = $this->getTotalUsersBasedOnRegistrationSource($request, 'facebook');

        return view('admin.analytics.users.index', compact(['roles', 'totalUsersBaseOnGenders', 'totalRegisteredUsers', 'totalUsers', 'totalGoogleUsers', 'totalFacebookUsers']));
    }

    private function getTotalUsersBasedOnRole($request): Collection
    {
        return Role::whereNotIn('id', [UserRoleEnum::AnonymousUser, UserRoleEnum::AuthenticatedUser])
            ->withCount([
                'users' => function ($query) use ($request) {
                    if ($request->date_from && $request->date_to) {
                        $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
                    }
                },
            ])
            ->get();
    }

    private function getTotalUsersBasedOnGender($request): Collection
    {
        return User::select(['user_profiles.gender', DB::raw('COUNT(users.id) as users_count')])
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->when($request->date_from && $request->date_to, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            })
            ->groupBy('user_profiles.gender')
            ->get();
    }

    private function getTotalRegisteredUsers($request): float
    {
        return User::when($request->date_from && $request->date_to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        })->count();
    }

    private function getTotalUsersBasedOnRegistrationSource($request, $providerName = null): float
    {
        $query = User::query()
            ->when($providerName, function ($query, $providerName) {
                return $query->whereProviderName($providerName);
            })
            ->unless($providerName, function ($query) {
                return $query->whereNull('provider_name');
            })
            ->when($request->date_from && $request->date_to, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            });

        return $query->count();
    }

}
