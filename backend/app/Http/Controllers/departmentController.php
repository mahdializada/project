<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Department;
use App\Models\Company;



class departmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $department = Department::with('company');

         // start mahdi's code
         if($request->type=="depEdit"){
            $id=$request->user_id;
            $dep=Department::whereHas('users',function(Builder $query) use($id){
                $query->where('user_id',$id);
            })->get();
            return $dep;
         }
         if ($request->type == "search"){
            $id = $request->company_id;
            $comp= Department::with('company')->whereHas('company',function(Builder $query) use($id) {
                $query->where('company_id',$id);
            })->get();

            return $comp;
        }
        // end of mahdi's coding
        if($request->type == 'search'){
            $id = $request->country_id;
            $company = Company::with('Location')->whereHas('Location', function(Builder $query) use($id){
                $query->where('country_id', $id);
            })->get();
            return $company;
        }

        if($request->tabkey && $request->tabkey !='all'){
            $department = $department->where('status',$request->tabkey);

        }
        $extraData = \DB::select("select count(status) as total,status from departments group by status");
        $total = [];
        $total["allTotal"] = 0;
        foreach ($extraData as $value) {
            $total["allTotal"] += $value->total;
            $total[$value->status."Total"] = $value->total;
        }

        if ($request->type == 'SearchByContent') {
            if($request->tabkey == "all" && $request->searchData == null){
               $department = $department;
            }
            elseif ($request->tabkey != "all") {
                if ($request->searchData ==  null) {

                    $department = $department->where('status', $request->tabkey);
                } else {
                    $department = $department->where('department_name', 'like', '%' . $request->searchData . '%')->where('status', $request->tabkey);
                }
            }
            else{
                $department = $department->where('department_name', 'like', '%' . $request->searchData . '%');
            }
        }
        if($request->type == 'SearchById' ){
            if($request->tabkey == "all" && $request->searchData == null){
                $department = $department;
             }
             elseif ($request->tabkey != "all") {
                if ( $request->searchData == null) {
                    $department = $department->where('status', $request->tabkey);
                } else {
                    $department = $department->where('id', $request->searchData)->where('status', $request->tabkey);
                }
            } else {
                $department = $department->where('id', $request->searchData);
            }
        }
        return response()->json(["data"=>$department->get(),"extraData"=>$total]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if($request->hasFile('logo')){
        //     $file = $request->logo;
        //     $name = time().'_'.$file->getClientOriginalName();
        //     $folder = $file->move(public_path('profile'), $name);
            $store = Department::create([
                'company_id'=>$request->company_id,
                // 'logo'=>'profile/'.$name,
                'department_name'=>$request->department_name,
            ]);
            return response()->json($store, 201);
        // }


        // $data = Department::create([
        //     'company_id' => $request->company_id,
        //     'department_name' => $request->department_name,
        //     'status' => $request->status,
        // ]);
        // return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // view code
        return Department::find($id);
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
        if($request->type == 'change_status'){
            Department::find($request->id)->update([
                'status'=>$request->status
            ]);
            return true;
        }else{
        $depa = Department::find($id)->update([
            'company_id' => $request->company_id,
            'department_name' => $request->department_name,
            'status' => $request->status
        ]);
        return response()->json($depa, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->type == 'deleted'){
            if($request->tabkey == 'removed'){
                $department = Department::whereIn('id', $request->ids)->get();
                foreach ($department as $value) {
                     \File::delete($value->logo);
                }
                Department::whereIn('id', $request->ids)->delete();
            }else{
                Department::whereIn('id', $request->ids)->update([
                    'status'=>'removed',
                    'pre_status'=> $request->tabkey,
                ]);
            }
        }else{
            $dep = Department::whereIn('id', $request->ids)->get();
            for($i=0; $i < count($request->ids); $i++){
                Department::where('id', $request->ids[$i])->update([
                    'status'=>$dep[$i]->pre_status,
                    'pre_status'=>null,
                ]);
            }
        }
        return true;
    }
}
