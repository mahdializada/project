<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Blog;
// use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $blog=Blog::with('user');
        // returning data for search
            if($request->type == 'search'){
                $searchValue = $request->searchData;
                if($request->searchTab == "All" && $request->searchData == null){
                    $data = $blog;
                }
                elseif ($request->searchTab != "All") {

                    if ($request->searchData ==  null) {
                        $data = $blog->where('is_active', $request->searchTab);

                    } else {

                        $data = $blog->where('is_active', $request->searchTab)->where( function (Builder $query) use ($searchValue){
                            $query->where('blog_title', 'like', '%' . $searchValue . '%')->orWhere('id', 'like', $searchValue);
                        });
                    }
                }
                else{
                    $data = $blog->where('blog_title', 'like', '%' . $request->searchData . '%')->orWhere('id', 'like', $request->searchData);
                }
                $result = $data->get();
                return  response()->json($result);
            }
            // returning data according to tap
            if ($request->tabkey && $request->tabkey != 'all') {
                if($request->tabkey == 1){
                    $data = $blog->where('is_active', 1);
                    return  $data->get();
                }else{
                    $data = $blog->where('is_active', 0);
                    return  $data->get();
                }
            }else{
                return $blog->get();
            }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slug = Str::slug($request->blog_title);
        if($request->id){
            $find=Blog::find($request->id);
            if($request->hasFile('photo','attachment')){
                $photo_path='photo/blog/'.$find->photo;
                $attachment_path='attachment/blog/'.$find->attachment;
                if($find->photo != '' && $find->photo != null){
                    $old_photo = $photo_path.$find->photo;
                    if(File::exists($old_photo)){
                        File::delete($old_photo);
                    }
                }
                if($find->attachment != '' && $find->attachment != null){
                    $old_attachment = $attachment_path.$find->attachment;
                    if(File::exists($old_attachment)){
                        File::delete($old_attachment);
                    }
                }
                $photo=$request->photo;
                $attachment=$request->attachment;
                $photo_name=$photo->getClientOriginalName();
                $attachment_name=$attachment->getClientOriginalName();
                $photo_name=str_replace(' ','',$photo_name);
                $attachment_name=str_replace(' ','',$attachment_name);
                $photo->move(public_path('photo/blog/'),$photo_name);
                $attachment->move(public_path('attachment/blog/'),$attachment_name);

                $update_blog=$find->update([
                    'blog_title'=>$request->blog_title,
                    'number_of_views'=>'12',
                    'photo'=>'photo/blog/'.$photo_name,
                    'attachment'=>'attachment/blog/'.$attachment_name,
                    'descriptions'=>$request->descriptions,
                    'user_id'=>$request->user_id,
                    'slug'=>$slug
                ]);
                return response()->json($update_blog,200);
            }
        }
        else{
        if($request->hasFile('photo','attachment')){
        $file=$request->photo;
        $attachment=$request->attachment;
        $filename=$file->getClientOriginalName();
        $attachment_name=$attachment->getClientOriginalName();
        $filename=str_replace(' ','',$filename);
        $attachment_name=str_replace(' ','',$attachment_name);
        $file->move(public_path('photo/blog/'),$filename);
        $attachment->move(public_path('attachment/blog/'),$attachment_name);
        $create_blog=Blog::create([
            'blog_title'=>$request->blog_title,
            'number_of_views'=>'12',
            'photo'=>'photo/blog/'.$filename,
            'attachment'=>'attachment/blog/'.$attachment,
            'descriptions'=>$request->descriptions,
            'user_id'=>$request->user_id,
            'slug'=>$slug
        ]);
        return response()->json($create_blog,201);
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
        $blog=Blog::find($id);
        $change= $blog->update(['is_active' => !$blog->is_active ]);
         return response()->json($change ,200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $filedelete = Blog::whereIn('id',$request->ids)->get();
                foreach ($filedelete as $del) {
                    \File::delete($del->photo);
                    \File::delete($del->attachment);
                }
                Blog::whereIn('id',$request->ids)->delete();

        //  Blog::whereIn('id',$ids)->delete();
        //  return response()->json(true,200);
    }
}
