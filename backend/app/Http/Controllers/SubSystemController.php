<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Label;
use App\Models\System;
use App\Models\Company;
use App\Models\Country;
use App\Models\Language;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use App\Models\BusinessLocation;
use App\Models\DesignRequest;
use App\Models\SingleSales\Ispp;
use App\Models\Sourcer;

class SubSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $subsystem = $request->subsystem;
        $types  = [];
        switch ($subsystem) {
            case 'Business Locations':
                $types = BusinessLocation::getTypes();
                break;
            case 'Departments':
                $types = Department::getTypes();
                break;
            case 'Languages':
                $types = Language::getTypes();
                break;
            case "Labels":
            case "Labels (user)":
            case "Labels (master)":
            case "Labels (advertisement)":
            case "Labels (online_sales)":
            case "Labels (content_library)":
                $types = Label::getTypes();
                break;
            case "Countries":
                $types = Country::getTypes();
                break;
            case "Companies":
                $types = Company::getTypes();
                break;
            case "Teams":
                $types = Team::getTypes();
                break;
            case "Users":
                $types = User::getTypes();
                break;
            case "Roles":
                $types = Role::getTypes();
                break;
            case "Systems":
                $types = System::getTypes();
                break;
            case "Social Media":
                $types = SocialMedia::getTypes();
                break;
            case "Design Request":
                $types = ['cancelled', 'storyboard rejected', 'design rejected', 'removed', 'not assigned'];
                break;
            case "My Order":
                $types = [
                    'in storyboard', 'in storyboard review', 'in design',
                    'design review', 'in programming', "programming reivew"
                ];
                break;
            case "Settings":
                $types = ['rejected'];
                break;
            case "Categories":
                $types = ['inactive', 'removed'];
                break;
            case "Attributes":
                $types = ['inactive', 'removed'];
                break;
            case "Brands":
                $types = ['inactive', 'removed'];
                break;
            case "ISPP":
                $types = ['pending', 'in review', 'in preparation', 'in hold', 'completed', 'failed', 'cancelled', 'removed'];
                break;
            case "ipa":
                $types = ['pending', 'in review', 'rejected', 'in creation', 'in testing', 'sales moving', 'sales unstable', 'stopped', 'failed', 'cancelled', 'removed'];
                break;
            case "Products":
                $types = ['inactive', 'removed'];
                break;
            case "Sourcing Request":
                $types = ['pending', 'in sourcing', 'in hold', 'found', 'not found', 'cancelled', 'removed'];
            case "Suppliers":
                $types = Supplier::getTypes();
                break;
            case "Sourcers":
                $types = Sourcer::getTypes();
                break;
            case "ProductStudy":
                $types = ['inactive', 'removed'];
                break;
            case "Actions":
                $types = ['cancelled', 'failed', 'removed'];
                break;
            case "Quantity Reservation Request":
                $types = ['pending', 'rejected', 'in process', 'not possible', 'order made', 'order received', 'cancelled', 'removed'];
                break;
            case "Advertisement":
                $types = ['active', 'inactive', "removed"];
                break;
            case "Online Sales":
                $types = ['active', 'inactive', "removed"];
                break;
            case "Projects":
                $types = ['active', "removed"];
                break;
            case "Content library":
                $types = ['publish','not publish','rejected'];
                break;
            default:
                [];
        }

        $types = array_filter($types, function ($item) {

            return $item != 'pending' && $item != 'warning';
        });
        return response()->json(array_values($types));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
