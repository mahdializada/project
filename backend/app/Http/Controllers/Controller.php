<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Label;
use App\Models\SubSystem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function getRelatedlabels($name)
    {
        $sub_system = SubSystem::whereName($name)->first();
        if (!$sub_system) {
            return [];
        }
        $labels = Label::join('label_sub_systems', 'labels.id', '=', 'label_sub_systems.label_id')
            ->where(['sub_system_id' => $sub_system->id, 'status' => 'live'])->select(['labels.id', 'labels.title'])->get();
        return $labels;
    }

    public function getAllCountries(Request $request)
    {
        $countries = Country::whereStatus('active')
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'iso2', 'name'])
            ->toArray();
        return $countries;
    }
    public function tokenCan($scope)
    {
        return in_array($scope, Auth::user()->get_scopes());
    }
}
