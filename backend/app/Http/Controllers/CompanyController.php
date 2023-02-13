<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CompanySystem;
use App\Models\CompanySocialMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Supports\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Return_;

class CompanyController extends Controller
{

    public function index(Request $req)
    {
        $company = Company::with('Location.Country', 'systems');
        $countData = \DB::select("select count(status) as total,status from companies group by status");
        $total = [];
        $total["allTotal"] = 0;
        foreach ($countData as $data){
            $total[$data->status."Total"] = $data->total;
            $total["allTotal"] += $data->total;
        }

        // Returning data for Search

            if ($req->type == 'nameSearch') {
                if($req->searchTab == "All" && $req->searchData == null){
                   $comp = $company;
                }
                elseif ($req->searchTab != "All") {
                    if ($req->searchData ==  null) {

                        $comp = $company->where('status', $req->searchTab);
                    } else {
                        $comp = $company->where('company_name', 'like', '%' . $req->searchData . '%')->where('status', $req->searchTab);
                    }
                }
                else{
                    $comp = $company->where('company_name', 'like', '%' . $req->searchData . '%');
                }
                return  response()->json(["data"=>$comp->get(),"countData"=>$total]);
            }
            if($req->type == 'idSearch' ){
                if($req->searchTab == "All" && $req->searchData == null){
                    $comp = $company;
                 }
                 elseif ($req->searchTab != "All") {
                    if ( $req->searchData == null) {
                        $comp = $company->where('status', $req->searchTab);
                    } else {
                        $comp = $company->where('id', $req->searchData)->where('status', $req->searchTab);
                    }
                } else {
                    $comp = $company->where('id', $req->searchData);
                }
                // return $comp->get();
                return  response()->json(["data"=>$comp->get(),"countData"=>$total]);
            }


        // Returning data for filterations

        if ($req->type == 'filter') {
            if ($req->country_id) {
                $company = $company->whereHas('Location.Country', function (Builder $query) use ($req) {
                    $query->where('id', $req->country_id);
                });
            }
            if ($req->system_id) {
                $company = $company->whereHas('systems', function (Builder $query) use ($req) {
                    $query->where('systems.id', $req->system_id);
                });
            }
            if ($req->investment_type) {
                $company = Company::where('investment_type', $req->investment_type);
            }
            if ($req->company_name) {
                $company = Company::with('Location.Country', 'systems')->where('company_name', $req->company_name);
            }
            if ($req->created_date) {
                $company = Company::with('Location.Country', 'systems')->whereDate('created_at', $req->created_date);
            }
            if ($req->updated_date) {
                $company = Company::with('Location.Country', 'systems')->whereDate('updated_at', $req->updated_date);
            }
            if ($req->company_id) {
                $company = Company::with('Location.Country', 'systems')->where('id', $req->company_id);
            }


            // return $company->get();
            return  response()->json(["data"=>$company->get(),"countData"=>$total]);
        }


        // end of filteration


        // start mahdi's
        elseif ($req->type == "search") {
            $id = $req->country_id;
            $comp = Company::with('Location')->whereHas('Location', function (Builder $query) use ($id) {
                $query->where('country_id', $id);
            })->get();
            return $comp;
        }
        // end of mahdi's coding
        elseif ($req->tabKey && $req->tabKey != 'all') {
            $company = $company->where('status', $req->tabKey);
        }
        $company = $company->get();
        return  response()->json(["data"=>$company,"countData"=>$total]);
    }

    public function store(Request $req)
    {
        Location::create([
            'country_id' => $req->country_id,
            'state_district' => $req->state,
            'city' => $req->city,
            'address_line' => $req->address,
        ]);
        $location_id = Location::orderBy('id', 'desc')->first()->id;

        if ($req->hasFile('logo')) {
            $file = $req->logo;

            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('company'), $name);

            $response = Company::create([
                'company_name' => $req->company_name,
                'email' => $req->email,
                'logo' => $name,
                'investment_type' => $req->investment_type,
                'location_id' => $location_id,


            ]);
        }

        $company_id =  Company::orderBy('id', 'desc')->first()->id;
        CompanySystem::create([
            'system_id' => $req->system_id,
            'company_id' =>  $company_id,
        ]);

        CompanySocialMedia::create([
            'socialMedia_id' => $req->media_id,
            'company_id' =>  $company_id,
            'link' => $req->link,
        ]);



        return response()->json($response, 201);
    }

    public function update(Request $req, $id = null)
    {
        if ($req->type == 'change_status') {
            Company::whereIn('id', $req->ids)->update([
                'status' => $req->status

            ]);

            return true;
        } else {
            $company1 = Company::find($id);
            $location1 = Location::find($req->location_id);

            $location1->update([
                'country_id' => $req->country_id,
                'state_district' => $req->state,
                'city' => $req->city,
                'address_line' => $req->address,
            ]);

            $update = $company1->update([
                'comapny_name' => $req->company_name,
                'email' => $req->email,
                'logo' => '1667190819_IMG_4230.JPG',
                'invesment_type' => $req->investment_type,
                'location_id' => $req->location_id,
                'status' => $req->status,
            ]);

            $comSystem = CompanySystem::find($req->company_id);

            $comSystem->update([
                'system_id' => $req->system_id,
                'company_id' =>  $req->company_id,
            ]);

            $comMedia = CompanySocialMedia::find($req->country_id);
            $comMedia->update([
                'socialMedia_id' => $req->media_id,
                'company_id' =>  $req->company_id,
                'link' => $req->link,
            ]);


            return response()->json($update, 200);
        }
    }
    public function destroy(Request $req)
    {
        if ($req->type == 'delete') {
            if ($req->tabKey == 'removed') {
                Company::whereIn('id', $req->ids)->delete();
            } else {
                Company::whereIn('id', $req->ids)->update([
                    'status' => 'removed',
                    'pre_status' => $req->tabKey,
                ]);
            }
        } else {
            $companies = Company::whereIn('id', $req->ids)->get();
            for ($i = 0; $i < count($req->ids); $i++) {
                Company::where('id', $req->ids[$i])->update([
                    'status' => $companies[$i]->pre_status,
                    'pre_status' => null
                ]);
            }
        }
        return null;
    }
}
