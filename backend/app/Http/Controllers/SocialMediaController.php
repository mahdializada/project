<?php

namespace App\Http\Controllers;
use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $social=new SocialMedia();
        $social=SocialMedia::with('createdBy','updatedBy');
        // select count the record of every status
        $select=\DB::select("select count(status) as total,status from social_medias group by status");
        $total=[];
        $total['alltotal']=0;
        foreach($select as $count){
            $total[$count->status."total"] = $count->total;
            $total['alltotal'] +=  $count->total;
        }
        // search
        if($request->type == 'contentsearch'){
            if($request->search_tab == "all" && $request->searchValue==null){
                $socialsearchresult=$social;
            }

            elseif($request->search_tab != 'all'){
                if($request->searchValue == null){
                    $socialsearchresult = $social->where('status',$request->search_tab);
                }else{
                    $socialsearchresult = $social->where('name','like','%'.$request->searchValue.'%')->where('status',$request->search_tab);
                }
            }else{
                $socialsearchresult = $social->where('name','like','%'.$request->searchValue.'%');
            }
            return $socialsearchresult->get();
        }
        if($request->type == 'idsearch'){

            if ($request->search_tab == 'all' && $request->searchValue == null ){
                $socialsearchresult = $social;
            }
            elseif($request->search_tab != 'all'){
                if($request->searchValue == null){
                    $socialsearchresult = $social->where('status',$request->search_tab);
                }else{
                    $socialsearchresult = $social->where('id',$request->searchValue)->where('status',$request->search_tab);
                }
            }else{
                $socialsearchresult = $social->where('id',$request->searchValue);
            }
            return $socialsearchresult->get();
        }
        // filter social media
        if($request->type == 'filter'){
            if($request->statusfilter){
                $social=$social->where('status',$request->statusfilter);
            }
            if($request->website_id){
                $social=$social->where('id',$request->website_id);
            }
            if($request->name_id){
                $social=$social->where('id',$request->name_id);
            }
            if($request->created_at){
                $social=$social->whereDate('created_at',$request->created_at);
            }
            if($request->updated_at){
                $social=$social->whereDate('updated_at',$request->updated_at);
            }
            if($request->filterId){
                $social=$social->where('id',$request->filterId);
            }
           return $social->get();
        }
        // tabkey
        if($request->tabkey && $request->tabkey != 'all'){
            $social=$social->where('status',$request->tabkey)->get();
        }else{
            $social=$social->get();
        }
        return response()->json(["data"=>$social, "select" => $total]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('image')){
            $file=$request->image;
            $filename=$file->getClientOriginalName();
            $filename=str_replace(' ','',$filename);
            $file->move(public_path('image'),$filename);
            $store=SocialMedia::create([
                'name'=>$request->name,
                'website'=>$request->website,
                'created_by'=>$request->created_by,
                'updated_by'=>$request->updated_by,
                'image'=>'image/'.$filename,
                'status'=>$request->status,
            ]);
            return response()->json($store,201);
        }
        // return $request;
        // if($request->file('file')    ){

            // ----------single file upload--------------//
            // $file=$request->file('file');
            // $filename=$file->getClientOriginalName();
            // $filename=str_replace(' ','',$filename);
            // $file->move(public_path('images'),$filename);
            // $store=SocialMedia::create([
            //     'data'=>$request->name,
            //     'status'=>$request->status,
            //     'image'=>'image/'.$filename,
            //     'data'=>$request->website,
            // ]);
            // if($store){
            //     return response()->json(['message'=>'successfully uploaded'],201);
            // }

            //------------- Multiple file upload-------------------//

        //     $files=$request->file('file');
        //     if(!is_array($files)){
        //         $files=[$files];
        //     }
        //     for ($i=0; $i < count($files); $i++) {
        //         $file=$files[$i];
        //         $filename=$file->getClientOriginalName();
        //         $filename=str_replace(' ','',$filename);
        //         $file->move(public_path('images'),$filename);
        //     }
        //     return response()->json(['message'=>'file uploaded'],200);
        // }
            else{
                return response()->json(['message'=>'file not uploaded'],503);
            // $files=$request->file('file');
            // if(!is_array($files)){
            //     $files=[$files];
            // }
            // for ($i=0; $i < count($files); $i++) {
            //     $file=$files[$i],
            // }
            // $store=SocialMedia::create([
            // 'name'=>'something',
            // 'website'=>'wesite',
            // 'status'=>'pending',
            // ]);
        // $file=$request->image;
        // $filename=time().'_'.$file->getClientOriginalName();
        // $path=$file->move(public_path('image'),$filename);
        // $store=SocialMedia::create([
            // 'name'=>$request->name,
            // 'image'=>'image/'.$filename,
            // 'website'=>$request->webiste,
            // 'status'=>$request->status,
        // ]);
        // return response()->json($store,201);
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
        if($request->type=='change_status'){
                SocialMedia::whereIn('id',$request->ids)->update([
                    'status'=>$request->status
                ]);
            return true;
        }else{
        $find=SocialMedia::find($id);
        $find->update([
            'name'=>$request->name,
            'website'=>$request->website,
            'created_by'=>$request->created_by,
            'updated_by'=>$request->updated_by,
            'website'=>$request->website,
            'image'=>'image.jpg',
            'status'=>$request->status,
        ]);
        return response()->json($find,200);
    }
    }

    /**
     * blocked the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->type=='deleted'){
            if($request->tabkey == 'blocked'){
                $filedelete = SocialMedia::whereIn('id',$request->ids)->get();
                foreach ($filedelete as $value) {
                    \File::delete($value->image);
                }
                SocialMedia::whereIn('id',$request->ids)->delete();

        }else{
                SocialMedia::whereIn('id',$request->ids)->update(['status'=>'blocked',
                'per_status'=>$request->tabkey,
            ]);
            }

        }else{
            $store=SocialMedia::whereIn('id',$request->ids)->get();

            for ($i=0; $i <count($request->ids) ; $i++) {
                SocialMedia::where('id',$request->ids[$i])->update([
                    'status'=>$store[$i]->per_status,
                    'per_status'=>null,
                ]);
            }
        }
        return true;
    }
}
