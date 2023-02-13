<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\Department;
use App\Models\UserDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Builder;
use function PHPSTORM_META\type;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::with('Departments','Departments.Company','Departments.Company.Location.Country');
        // count records
        $extraData = \DB::select("select count(status) as total,status from users group by status");
        $total = [];
        $total["allTotal"] = 0;
        foreach ($extraData as $value) {
            $total["allTotal"] += $value->total;
            $total[$value->status."Total"] = $value->total;
        }


        // end of count records


        // search
        if($request->searchData){
            if($request->type=="nameSearch"){
                if($request->searchTab != "All"){
                    if (empty($request->searchData)){
                        $use = $user->where('status', $request->searchTab);
                    } else {
                        $use = $user->where('first_name', 'like', '%' . $request->searchData . '%')->where('status', $request->searchTab);
                    }

                }
                else{
                    $use = $user->where('first_name', 'like', '%' . $request->searchData . '%');
                }

            } else{
                if ($request->searchTab != "All") {
                    if ( !$request->searchData ) {
                        $use = $user->where('status', $request->searchTab);
                    } else {
                        $use = $user->where('id', $request->searchData)->where('status', $request->searchTab);
                    }
                } else {
                    $use = $user->where('id', $request->searchData);
                }
            }
            return $use->get();
        }

        // end of search

        if ($request->has('item')){
            $id = $request->id;
            $dep= Department::with('users')->whereHas('users',function(Builder $query) use($id) {
                $query->where('user_id',$id);
            })->get();

            return $dep;
        }
        //

        if($request->type=="search"){
            $id=$request->company_id;
            $userComp= user::with('departments.company')->whereHas('departments',function(Builder $query) use ($id){
                $query->where('company_id',$id);
            })->get();
            return $userComp;
        }

        // filterig data
        if($request->type == 'filter'){
            if($request->country_id){
                $user = $user->whereHas('Departments.company.Location.Country', function(Builder $query) use ($request){
                     $query->where('id',$request->country_id);
                });
             }
             if($request->company_id){
                $user = $user->whereHas('Departments.company', function(Builder $query) use ($request){
                    $query->where('id',$request->company_id);
               });
             }
             if($request->created_date){
                $user = User::with('Departments.company.Location.Country','Departments.company')->whereDate('created_at',$request->created_date);
            }
            if($request->updated_date){
                $user = User::with('Departments.company.Location.Country','Departments.company')->whereDate('updated_at',$request->updated_date);
            }
            if($request->user_id){
                $user = User::with('Departments.company.Location.Country','Departments.company')->where('id',$request->user_id);
            }

            return $user->get();

        }
        // end of filtering data
        elseif($request->tabKey && $request->tabKey !='all'){
            $user= $user ->where('status', $request->tabKey)->get();

        }else{

            $user= $user->get();
        }
        return response()->json(["data"=>$user,"TolalCount"=>$total]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $departments=Department::all();
        if($request->id){
            $user1=user::find($request->id);
            if($request->hasFile('image'))
            {
                $path= 'assets/images/users'.$user1->image;
                if( $user1->image !='' && $user1->image !=null){
                    $file_old =$path.$user1->image;
                    // unlink($file_old);
                    if(File::exists($file_old)){
                        File::delete($file_old);
                    }
                }
                $image = $request->image;
                $imagename = time().'.'.$image->getClientOriginalName();

                $image->move(public_path('assets/images/users'), $imagename);
            }

                $user1->update([
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'user_name'=>$request->user_name,
                    'email'=>$request->email,
                    // 'password'=>Hash::make($request->password),
                    // 'change_password'=>Hash::make($request->change_password),
                    'phone'=>$request->phone,
                    'gender'=>$request->gender,
                    'birth_date'=>$request->birth_date,
                    'image'=>'assets/images/users/'.$imagename,
                    // 'image'=>'assets/images/users/b.jpg',
                    'permission_type'=>$request->permission_type,
                    'status'=>$request->status
                ]);

                return response()->json($user1->load('Departments.Company.Location.Country'),200);



        }else{

            if($request->hasFile('image')){
                $file=$request->image;
                 $name = time().'.'.$file->getClientOriginalName();
                $file->move(public_path('assets/images/users'),$name);

                $user=User::create([
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'user_name'=>$request->user_name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                    'change_password'=>Hash::make($request->change_password),
                    'phone'=>$request->phone,
                    'gender'=>$request->gender,
                    'birth_date'=>$request->birth_date,
                    'image'=>'assets/images/users/'.$name,
                    'permission_type'=>$request->permission_type,
                    'status'=>$request->status
                ]);
                 $user_id= User::orderBy('id', 'desc')->first()->id;
                 UserDepartment::create([
                     'user_id'=>$user_id,
                     'department_id' => $request->department_id,
                 ]);
                return response()->json($user,201);
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
        if($request->type == 'changeStatus'){
            $user=User::whereIn('id',$request->ids);
            $user->update([
                'status'=>$request->status,
            ]);
            return true;
        }else{
            $user1=user::find($id);
            // if($request->hasFile('image'))
            // {
            //     $path= public_path().'/';
            //     if( $user1->image !='' && $user1->image !=null){
            //         $file_old =$path.$user1->image;
            //         unlink($file_old);
            //     }

            //     $image = $request->image;
            //     $imagename = time().'.'.$image->getClientOriginalName();

            //     $image->move($path, $imagename);
            // }
                // $user1->update([
                //     'first_name'=>$request->first_name,
                //     'last_name'=>$request->last_name,
                //     'user_name'=>$request->user_name,
                //     'email'=>$request->email,
                    // 'password'=>Hash::make($request->password),
                    // 'change_password'=>Hash::make($request->change_password),
                    // 'phone'=>$request->phone,
                    // 'gender'=>$request->gender,
                    // 'birth_date'=>$request->birth_date,
                    // 'image'=>'assets/images/users/'.$imagename,
                //     'image'=>'assets/images/users/b.jpg',
                //     'permission_type'=>$request->permission_type,
                //     'status'=>$request->status
                // ]);
                // $user_id= User::orderBy('id', 'desc')->first()->id;
                // UserDepartment::create([
                //     'user_id'=>$user_id,
                //     'department_id' => $request->department_id,
                // ]);
                // return response()->json($user1->load('Departments.Company.Location.Country'),200);
                return response()->json($user1,200);

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
        if($request->type=='deleted'){
            if($request->tabKey=='removed'){
                User::whereIn('id',$request->ids)->delete();
            }else{
                User::whereIn('id',$request->ids)->update([
                    'status'=>'removed',
                    'prev_status'=>$request->tabKey,
                ]);
            }
        }else{
            $user=User::whereIn('id',$request->ids)->get();
            for ($i=0; $i <count($request->ids) ; $i++) {
                User::where('id',$request->ids[$i])->update([
                    'status'=>$user[$i]->prev_status?$user[$i]->prev_status:'pending',
                    'prev_status'=>null,
                ]);
            }
        }
        return null;
    }
}
