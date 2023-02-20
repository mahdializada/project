<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employer;
use Illuminate\Support\Facades\File;

class EmployeerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $emp = Employer::with('user');
            $emp = $emp->where('name', 'like', '%' . $request->searchData . '%')->orWhere('id', $request->searchData);
                return $emp->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id){

            $find=Employer::find($request->id);
            if($request->hasFile('logo')){
                $photo_path='profile/'.$find->logo;
                if($find->logo != '' && $find->logo != null){
                    $old_photo = $photo_path.$find->logo;
                    if(File::exists($old_photo)){
                        File::delete($old_photo);
                    }
                }
                $photo=$request->logo;
                $photo_name=$photo->getClientOriginalName();
                $photo_name=str_replace(' ','',$photo_name);
                $photo->move(public_path('profile'),$photo_name);

                $update_employee=$find->update([
                    'logo'=>'profile/'.$photo_name,
                    'name'=>$request->emp_name,
                    'type'=>$request->type,
                    'email'=>$request->email,
                    'website'=>$request->website,
                    'phone'=>$request->phone,
                    'description'=>$request->description,
                    'user_id'=>$request->user_id,
                    'slug'=>str()->slug($request->slug),
                ]);
                return response()->json($update_employee,200);
            }
        }
        else{
        if($request->hasFile('logo')){
            $file = $request->logo;
            $name = time().'_'.$file->getClientOriginalName();
            $folder = $file->move(public_path('profile'), $name);
        $store = Employer::create([
            'logo'=>'profile/'.$name,
            'name'=>$request->emp_name,
            'type'=>$request->type,
            'email'=>$request->email,
            'website'=>$request->website,
            'phone'=>$request->phone,
            'description'=>$request->description,
            'user_id'=>$request->user_id,
            'slug'=> str()->slug($request->slug),
        ]);
        return response()->json($store, 201);
    }
    }
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
        // $emp_upd = Employer::find($id);
        // $update = $emp_upd ->update([
        //     'logo'=>$request->logo,
        //     'name'=>$request->name,
        //     'content'=>$request->content
        // ]);
        // return response()->json($post2, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
                $employees = Employer::whereIn('id', $request)->get();
                foreach ($employees as $value) {
                     \File::delete($value->logo);
                }
                Employer::whereIn('id', $request)->delete();


        return true;
    }
}
