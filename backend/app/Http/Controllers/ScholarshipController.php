<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\Scholarship;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Database\Query\Builder;

class ScholarshipController extends Controller
{
    public function index(Request $req)
    {

        $scholar = Scholarship::with('User', 'Country');

        // returning data for search
        if($req->type == 'search'){
            $searchValue = $req->searchData;
            if($req->searchTab == "All" && $req->searchData == null){
                $data = $scholar;
             }
             elseif ($req->searchTab != "All") {
                 if ($req->searchData ==  null) {

                     $data = $scholar->where('is_active', $req->searchTab);
                 } else {

                     $data = $scholar->where('is_active', $req->searchTab)->Where( function(Builder $query) use ($searchValue){
                        $query->where('scholarship_title', 'like', '%' . $searchValue . '%')->orWhere('id', 'like', $searchValue);
                     });
                 }
             }
             else{
                 $data = $scholar->where('scholarship_title', 'like', '%' . $req->searchData . '%')->orWhere('id', 'like', $req->searchData);
             }
            $result = $data->get();
             return  response()->json($result);
        }

        // returning data according to tap
        if ($req->tabKey && $req->tabKey != 'all') {
            if($req->tabKey == 1){

                $data = $scholar->where('is_active', 1);
                return  $data->get();
            }else{

                $data = $scholar->where('is_active', 0);
                return  $data->get();
            }
        }else{
            return $scholar->get();
        }

    }

    public function store(Request $req)
    {
        if($req->id){
            $current = Scholarship::find($req->id);
            if($req->hasFile('attachment')){
                $attachPath ='scholarships/attachment'.$current->attachment;
                if($current->attachment !='' && $current->attachment != null){
                    $oldAttach = $attachPath.$current->attachment;
                    if(File::exists($oldAttach)){
                        File::delete($oldAttach);
                    }
                }
                $attachment = $req->attachment;
                $attachmentName = time().'.'.$attachment->getClientOriginalName();
                $attachment->move(public_path('scholarships/attachment/'),$attachmentName);

            }else{
                $attachmentName = $current->attachment;
            }
            if($req->hasFile('photo')){
                $photoPath = 'scholarships/photo'.$current->photo;
                if($current->photo != '' && $current->photo != null){
                    $oldPhoto = $photoPath.$current->photo;
                    if(File::exists($oldPhoto)){
                        File::delete($oldPhoto);
                    }
                }
                $photo = $req->photo;
                $photoName = time().'.'.$photo->getClientOriginalName();
                $photo->move(public_path('scholarships/photo'),$photoName);

            }else{
                $photoName = $current->photo;
            }
            $slug = Str::slug($req->title);


            $current->update([

                'closing_date' => $req->closing_date,
                'scholarship_title'=> $req->title,
                'slug'=> $slug,
                'gender'=> $req->gender,
                'degree'=> $req->degree,
                'language_certificate'=> $req->language,
                'photo'=> $photoName,
                'scholarship_type'=> $req->type,
                'attachment'=> $attachmentName,
                'descriptions'=> $req->description,
                'eligibilities'=> $req->eligibility,
                'benifits'=> $req->benifit,
                'required_documents'=> $req->document,
                'how_to_apply'=> $req->apply_method,
                'country_id'=> $req->countryID,
                'user_id'=>1,
                'number_of_views'=>0,
                'is_active'=>0,
                'meta_data'=> $req->meta_data,
            ]);
            return response()->json($current, 200);




        }else{
        if($req->hasFile('photo','attachment')){
            $file = $req->photo;
            $attachment = $req->attachment;
            $fileName = time().'.'.$file->getClientOriginalName();
            $attachmentName = time().'.'.$attachment->getClientOriginalName();
            $file->move(public_path('scholarships/photo/'),$fileName);
            $attachment->move(public_path('scholarships/attachment/'),$attachmentName);
            $slug = Str::slug($req->title);
            $scholarship = Scholarship::create([

                'closing_date' => $req->closing_date,
                'scholarship_title'=> $req->title,
                'slug'=> $slug,
                'gender'=> $req->gender,
                'degree'=> $req->degree,
                'language_certificate'=> $req->language,
                'photo'=> $fileName,
                'scholarship_type'=> $req->type,
                'attachment'=> $attachmentName,
                'descriptions'=> $req->description,
                'eligibilities'=> $req->eligibility,
                'benifits'=> $req->benifit,
                'required_documents'=> $req->document,
                'how_to_apply'=> $req->apply_method,
                'country_id'=> $req->countryID,
                'user_id'=>1,
                'number_of_views'=>0,
                'is_active'=>0,
                'meta_data'=> $req->meta_description,
            ]);
            return response()->json($scholarship, 201);
        }

        }


    }

    public function update(Request $req, $id = null)
    {
        $scholar=Scholarship::find($id);
           $change= $scholar->update(['is_active' => !$scholar->is_active ]);
            return response()->json($change ,200);

    }

    public function destroy(Request $req){
           if ($req->type == 'delete') {
            Scholarship::whereIn('id', $req->ids)->delete();
            }
            return null;

    }
}
