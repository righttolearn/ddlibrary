<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Resource;
use App\Models\TaxonomyTerm;
use Illuminate\View\View;

class ImpactController extends Controller
{
    public function index(): View
    {
        $totalResources = Resource::count();
        $totalSubjects = TaxonomyTerm::where('vid', 8)->where('language', App::getLocale())->count();
        return view('impact.impact_page', compact('totalResources', 'totalSubjects'));
    }
}
