<?php

namespace App\Http\Middleware;

use App\Models\BusinessLocation;
use App\Models\Company;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Department;
use App\Models\DesignRequest;
use App\Models\DesignRequestOrder;
use App\Models\Industry;
use App\Models\Language;
use App\Models\Role;
use App\Models\System;
use App\Models\Team;
use App\Models\TranslatedLanguage;
use App\Models\User;

class DailyLogContentParser
{
    public function getContentOfDelete($page, $request)
    {
        # code...
        $id = '';
        $content = "";
        switch ($page) {
            case 'user':
                //  $request->route('user');
                $user = User::withTrashed()->findOrFail($request->route('user'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Firstname :</span>" . $user->firstname . "<br> <span class='text-subtitle-2 pe-1 content-width'>Last Name:</span>" . $user->lastname . "<br><span class='text-subtitle-2 pe-1 content-width'>Username:</span>" . $user->username;

                break;

            case 'team':
                $team = Team::withTrashed()->findOrFail($request->route('team'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Team:</span>" . $team->name;

                break;
            case 'role':

                $role = Role::withTrashed()->findOrFail($request->route('role'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Role:</span>" . $role->name;
                break;
            case 'department':
                $content = Department::withTrashed()->findOrFail($request->route('department'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Department:</span>" . $content->name;
                break;
            case 'country':
                $content = Country::find($id);
                $content = $content->name;
                break;
            case 'company':
                $content = Company::withTrashed()->findOrFail($request->route('company'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Company:</span>" . $content->name;

                break;
            case 'business_location':
                $content = BusinessLocation::withTrashed()->findOrFail($request->route('business_location'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Business Location:</span>" . $content->name;
                break;
            case 'language':
                $content = TranslatedLanguage::withTrashed()->with('countryLanguage.language:id,name')->findOrFail($request->route('languages_translated'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Language:</span>" . $content->countryLanguage->language->name;
                break;
            case 'design_request':
                $strIds = [];

                foreach (explode(",", $request->route('design_request')) as $value) {
                    $strIds[] = (string) $value;
                }
                $design = DesignRequest::withTrashed()->findOrFail($strIds);
                $products = '';
                $companies = 'm';
                foreach ($design as $key => $value) {
                    # code...
                    $company = Company::find($value->company_id);
                    if ($key == 0) {
                        $products =  $value->prodeuct_name . " " . $value->product_code;
                        $companies = $company->name;
                    } else {
                        $products = $products . ", " . $value->procuct_name . " " . $value->product_code;
                        $companies = $companies . ", " . $company->name;
                    }
                }

                $content = "<span class='text-subtitle-2 pe1 content-width'>Companies:</span>" . $companies . " <br><span class='text-subtitle-2 pe1 content-width'>Products:</span>" . $products;

                break;
            default:
                $content = "wrong subsysem name";
                break;
        }
        return $content;
    }

    public function getContentOfCompany($request)
    {
        $countries = Country::find($request->country_ids);
        $systems  = System::find($request->system_ids);
        $countries_name = "";
        $systems_name = "";
        foreach ($countries as $key => $value) {
            # code...
            if ($key == 0) {
                $countries_name = $value->name;
            } else {
                $countries_name = $countries_name . ", " . $value->name;
            }
        }
        foreach ($systems as $key => $value) {
            # code...
            if ($key == 0) {
                $systems_name = $value->name;
            } else {
                $systems_name = $systems_name . ", " . $value->name;
            }
        }
        $content = "<span class='text-subtitle-2 pe1 content-width'>Company:</span>" . $request->name . " <br><span class='text-subtitle-2 pe1 content-width'>Systems:</span>" . $systems_name . " <br><span class='text-subtitle-2 pe1 content-width'>Countries:</span>" . $countries_name;
        return $content;
    }

    public function getContentOfUser($request)
    {
        # code...
        $content = "<span class='text-subtitle-2 pe1 content-width'>First Name:</span>" . $request->firstname  . "<br><span class='text-subtitle-2 pe-1 content-width'>Last Name:</span>" .  $request->lastname . "<br><span class='text-subtitle-2 pe-1 content-width'>User Name:</span>" .  $request->username;
        return $content;
    }
    public function getContentOfDepartment($request)
    {

        $industry = Industry::find($request->industries);
        $industries = "";
        $companies = Company::find($request->company_ids);
        $company_name = "";
        foreach ($industry as $key => $value) {
            if ($key == 0) {
                $industries = $value->name;
            } else {
                $industries = $industries . ", " . $value->name;
            }
        }
        foreach ($companies as $key => $value) {
            if ($key == 0) {
                $company_name = $value->name;
            } else {
                $company_name = $company_name . ", " . $value->name;
            }
        }
        $content = "<span class='text-subtitle-2 pe1 content-width'>Department Name:</span>" . $request->name  . "<br><span class='text-subtitle-2 px-1 content-width'>Industries:</span>" .  $industries . "<br><span class='text-subtitle-2 px-1 content-width'>Companies:</span>" .  $company_name;
        return $content;
    }
    public function getContentOfBusinessLocation($request)
    {
        $content = "<span class='text-subtitle-2 pe1 content-width'>Name:</span>" . $request->name . " <br><span class='text-subtitle-2 pe1 content-width'>Email:</span>" . $request->email . " <br><span class='text-subtitle-2 pe1 content-width'>Address:</span>" . $request->address;
        return $content;
    }
    public function getContentOfLanguage($request)
    {
        //  selectedLangaugeId
        $content = CountryLanguage::with('language:id,name')->findOrFail($request->selectedLangaugeId);
        $content = "<span class='text-subtitle-2 pe-1 content-width'>Language:</span>" . $content->language->name;
        return $content;
    }
    public function getContentOfTeamOrRole($request)
    {
        $departments = Department::find($request->department_ids);
        $department_name = '';
        foreach ($departments as $key => $value) {
            # code...
            if ($key == 0) {
                $department_name = $value->name;
            } else {
                $department_name = $department_name . ", " . $value->name;
            }
        }

        $content = "<span class='text-subtitle-2 pe1 content-width'>Name:</span>" . $request->name . " <br><span class='text-subtitle-2 pe1 content-width'>Departments:</span>" . $department_name;

        return $content;
    }
    public function getContentOfDesignRequest($request)
    {
        $company = Company::find($request->company_id);
        $content = "<span class='text-subtitle-2 pe1 content-width'>Company:</span>" . $company->name . " <br><span class='text-subtitle-2 pe1 content-width'>Product Name:</span>" . $request->product_name . "<br> <span class='text-subtitle-2 pe1 content-width'>Product Code:</span> " . $request->product_code;
        return $content;
    }
    public function getcontentOfAssignUser($request)
    {
        // design_request_ids:

        $content_name = DesignRequest::whereIn('id', $request->design_request_ids)->get();
        $data = User::whereIn('id',  $request->user_ids)->get();
        $users = '';
        $products = '';
        $companies = '';
        foreach ($content_name as $key => $value) {
            # code...
            $company = Company::find($value->company_id);
            if ($key == 0) {
                $products =  $value->prodeuct_name . " " . $value->product_code;
                $companies = $company->name;
            } else {
                $products = $products . ", " . $value->procuct_name . " " . $value->product_code;
                $companies = $companies . ", " . $company->name;
            }
        }
        foreach ($data as $key => $value) {
            # code...

            if ($key == 0) {
                $users =  $value->firstname . " " . $value->lastname;
            } else {
                $users = $users . ", " . $value->firstname . " " . $value->lastname;
            }
        }
        $content = "<span class='text-subtitle-2 px-1 content-width'>Products :</span>" . $products . "<br><span class='text-subtitle-2 px-1 content-width'>Companies :</span>" . $companies . "<br><span class='text-subtitle-2 px-1 content-width'>Assigned Users:</span>" . $users;


        return $content;
    }
    public function getcontentOfUnAssignUser($request)
    {
        // $products = '';
        // $order =  DesignRequestOrder::whereIn('id', $request->orderIds)
        //     ->with(['designRequest', 'designRequestOrderUser.user'])->get();
        // $users = '';
        // $companies = '';
        // $company_array = [];

        // foreach ($order as $orderItem) {
        //     # code...
        //     $company = Company::find($orderItem->designRequest->company_id);
        //     $company_array[] = $company->name;

        //     $products = $products . ", " . $orderItem->designRequest->prodeuct_name . " " . $orderItem->designRequest->product_code;
        //     // $companies = $companies . ", " . $company->name;

        //     foreach ($orderItem->designRequestOrderUser as  $value) {
        //         # code...
        //         $users = $users . ", " . $value->user->firstname . " " . $value->user->lastname;
        //     }
        // }
        // foreach (array_unique($company_array) as   $key =>  $value) {
        //     if ($key == 0) {
        //         $companies = $value;
        //     } else {
        //         $companies = $companies . ", " . $value;
        //     }
        // }
        // $content = "<span class='text-subtitle-2 px-1 content-width'>Products :</span>" . $products . "<br><span class='text-subtitle-2 px-1 content-width'>Companies :</span>" . $companies . "<br><span class='text-subtitle-2 px-1 content-width'>Unassigned Users:</span>" . $users;

        return "no content";
    }
    public function getContentOfChangeStatus($page, $request)
    {
        # code...
        $id = '';
        $content = "";
        switch ($page) {
            case 'user':
                $user    = User::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Firstname :</span>" . $user->firstname . "<br> <span class='text-subtitle-2 pe-1 content-width'>Last Name:</span>" . $user->lastname . "<br><span class='text-subtitle-2 pe-1 content-width'>Username:</span>" . $user->username;
                break;

            case 'team':
                $team    = Team::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Team:</span>" . $team->name;


                break;
            case 'role':
                $role = Role::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Team:</span>" . $role->name;
                break;
            case 'department':
                $content = Department::withTrashed()->findOrFail($request->item_id);
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Role:</span>" . $content->name;

                break;
            case 'country':
                $content = Country::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Country:</span>" . $content->name;
                break;
            case 'company':
                $content = Company::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Company:</span>" . $content->name;

                break;
            case 'business_location':
                $content = BusinessLocation::withTrashed()->findOrFail($request->input('item_id'));
                $content = "<span class='text-subtitle-2 pe-1 content-width'>Business Location:</span>" . $content->name;
                break;
            case 'language':
                // $content = TranslatedLanguage::withTrashed()->findOrFail($request->input('item_id'));
                $content = TranslatedLanguage::withTrashed()->with('countryLanguage.language:id,name')->findOrFail($request->input('item_id'));

                $content = "<span class='text-subtitle-2 pe-1 content-width'>Language:</span>" . $content->countryLanguage->language->name;
                break;
            case 'design_request':
                $design_requests = DesignRequest::whereIn('id', $request->item_ids)->get();
                $company_array = [];
                $companies = '';
                $designs = "";
                // $content = $design_requests;
                foreach ($design_requests as  $value) {
                    # code...
                    $company = Company::find($value->company_id);
                    $company_array[] = $company->name;
                    $designs = $designs . ", " . $value->prodeuct_name . " " . $value->product_code;
                }

                foreach (array_unique($company_array) as $key =>  $value) {
                    if ($key == 0) {
                        $companies = $value;
                    } else {

                        $companies = $companies . ", " . $value;
                    }
                }
                $content = "<span class='text-subtitle-2 pe1 content-width'>Companies:</span>" . $companies . " <br><span class='text-subtitle-2 pe1 content-width'>Products:</span>" . $designs . "<br> <span class='text-subtitle-2 pe1 content-width'>Status :</span> " . $request->status;
                break;

            default:
                $content = "wrong subsysem name";
                break;
        }
        return $content;
    }

    public function getContentOfAutoReview($request)
    {
        # code...
        $design_requests = DesignRequest::whereIn('id', $request['item_ids'])->get();

        $company_array = [];
        $companies = '';
        $designs = "";
        // $content = $design_requests;
        foreach ($design_requests as  $value) {
            # code...
            $company = Company::find($value->company_id);
            $company_array[] = $company->name;
            $designs = $designs . ", " . $value->prodeuct_name . " " . $value->product_code;
        }

        foreach (array_unique($company_array) as $key =>  $value) {
            if ($key == 0) {
                $companies = $value;
            } else {

                $companies = $companies . ", " . $value;
            }
        }
        $content = "<span class='text-subtitle-2 pe1 content-width'>Companies:</span>" . $companies . " <br><span class='text-subtitle-2 pe1 content-width'>Products:</span>" . $designs . "<br> <span class='text-subtitle-2 pe1 content-width'>Status :</span> " . $request['status'];
        return $content;
    }
}
