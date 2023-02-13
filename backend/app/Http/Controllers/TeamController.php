<?php

namespace App\Http\Controllers;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\Department;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $team = Team::with('department');
        $result = $team->count();
        if($request->tabkey && $request->tabkey != 'all'){
            $team=$team->where('status',$request->tabkey)->get();
        }else{
            $team=$team->get();
        }

        if ($request->searchData) {
            if ($request->column == 'id') {
                $team = $team->where('id', 'like','%'.$request->searchData.'%')->get();
            }
            if ($request->column == 'content') {
                $team = $team->where('team_name','like','%'.$request->searchData.'%')->get();
            }
        }
        return $team;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $department= Department::orderBy('id','desc')->first()->id;
        
            $file=$request->logo;
            $name = time().'.'.$file->getClientOriginalName();
            $file->move(public_path('assets/team'),$name);

            $team=Team::create([
            'team_name'=>$request->team_name,
            'note'=>$request->note,
            'logo'=>'assets/team/'.$name,
            'department_id'=>$request->department_id,
        ]);

        $exp = explode("," , $request->user_id);
        $team->users()->sync($exp);
        return response()->json($team,201);
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
        if($request->type=='change_status'){
                Team::whereIn('id',$request->ids)->update([
                    'status'=>$request->status
                ]);

            return true;
        }else if($request->type=='blocked'){
                Team::whereIn('id',$request->ids)->update([
                    'status'=>'blocked',
                ]);
            return true;
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
    * @param  \Illuminate\Http\Request  $req
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->type == 'delete'){
            if($request->tabkey == 'removed'){
                Team::whereIn('id',$request->ids)->delete();
            }else{
                Team::whereIn('id',$request->ids)->update([
                    'status'=>'removed',
                    'prev_status'=>$request->tabkey,
                ]);
            }
        }else if ($request->type == 'restore') {
            $teams = Team::whereIn('id',$request->ids)->get();
            for ($i=0; $i < count($request->ids) ; $i++) {
                Team::where('id',$request->ids[$i])->update([
                    'status'=>$teams[$i]->prev_status?$teams[$i]->prev_status:'active',
                    'prev_status'=>null,
                ]);
            }
        }
        return null;
    }
}