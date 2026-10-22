<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    public function index(Request $request): View
    {
        DDLClearSession();

        if (config('app.locale') == 'en') {
            return view('policies.privacy_en_web_current');
        } elseif (config('app.locale') == 'ps') {
            return view('policies.privacy_ps_web_current');
        }

        return view('policies.privacy_fa_web_current');
    }

    public function mobilePrivacyPolicy(Request $request): View
    {
        DDLClearSession();

        return view('policies.privacy_en_mob_current');
    }

    public function optOut(Request $request)
    {
        DDLClearSession();

        return view('layouts.opt_out');
    }
}
