<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class JobController extends Controller
{
    public function index(Request $request){
        $job = Job::with('employer','category','user');
         // returning data for search
         if($request->type == 'search'){
            $searchValue = $request->searchData;
            if($request->searchTab == "All" && $request->searchData == null){
                $data = $job;
             }
             elseif ($request->searchTab != "All") {
                 if ($request->searchData ==  null) {

                     $data = $job->where('is_active', $request->searchTab);
                 } else {

                     $data = $job->where('is_active', $request->searchTab)->Where( function(Builder $query) use ($searchValue){
                        $query->where('title', 'like', '%' . $searchValue . '%')->orWhere('id', 'like', $searchValue);
                     });
                 }
             }
             else{
                 $data = $job->where('title', 'like', '%' . $request->searchData . '%')->orWhere('id', 'like', $request->searchData);
             }
            $result = $data->get();
             return  response()->json($result);
        }

        if($request->tabKey && $request->tabKey !='all'){

            $job= $job->where('is_active', $request->tabKey)->get();

        }else{

            $job= $job->get();
        }
        return $job;
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
            $job1=job::find($request->id);
            if($request->hasFile('attachments'))
            {
                $path= 'assets/attachments/jobs'.$job1->attachments;
                if( $job1->attachments !='' && $job1->attachments !=null){
                    $file_old =$path.$job1->attachments;
                    // unlink($file_old);
                    if(File::exists($file_old)){
                        File::delete($file_old);
                    }
                }

                $attachments = $request->attachments;
                $attachmentsname = time().'.'.$attachments->getClientOriginalName();

                $attachments->move(public_path('assets/attachments/jobs'), $attachmentsname);
            }else{
                $attachmentsname =$job1->attachments;
            }

                $slug = Str::slug($request->title);
                $job1->update([
                    'employer_id'=>$request->employer_id,
                    'category_id'=>$request->category_id,
                    'user_id'=>$request->user_id,
                    'title'=>$request->title,
                    'slug'=>$slug,
                    'closing_date'=>$request->closing_date,
                    'number_of_jobs'=>$request->number_of_jobs,
                    'functional_area'=>$request->functional_area,
                    'submission_email'=>$request->submission_email,
                    'salary_range'=>$request->salary_range,
                    'reference_no'=>$request->reference_no,
                    'gender'=>$request->gender,
                    'contract_duration'=>$request->contract_duration,
                    'work_type'=>$request->work_type,
                    'nationality'=>$request->nationality,
                    'experience'=>$request->experience,
                    'education'=>$request->education,
                    'funcational_area'=>$request->funcational_area,
                    'contract_extension'=>$request->contract_extension,
                    'probation_period'=>$request->probation_period,
                    'job_descriptions'=>$request->job_descriptions,
                    'attachments'=>'assets/attachments/jobs/'.$attachmentsname,
                    'job_locations'=>$request->job_locations,
                    'job_requirements'=>$request->job_requirements,
                    'submission_guideline'=>$request->submission_guideline

                ]);

                return response()->json($job1,200);



        }else{

            if($request->hasFile('attachments')){
                $file=$request->attachments;
                 $attachments = 'assets/attachments/jobs/'.time().'.'.$file->getClientOriginalName();
                $file->move(public_path('assets/attachments/jobs'),$attachments);
            }else{
                $attachments='';
            }
            // $string = preg_replace('/[^A-Za-z0-9\-]/', '-', $request->slug); //Removed all Special Character and replace with hyphen
            // $final_slug = preg_replace('/-+/', '-', $string); //Removed double hyphen
            // $slug = strtolower($final_slug);
                $slug = Str::slug($request->title);
              
                $job=job::create([
                    'employer_id'=>$request->employer_id,
                    'category_id'=>$request->category_id,
                    'user_id'=>$request->user_id,
                    'title'=>$request->title,
                    'slug'=>$slug,
                    'closing_date'=>$request->closing_date,
                    'number_of_jobs'=>$request->number_of_jobs,
                    'functional_area'=>$request->functional_area,
                    'submission_email'=>$request->submission_email,
                    'salary_range'=>$request->salary_range,
                    'reference_no'=>$request->reference_no,
                    'gender'=>$request->gender,
                    'contract_duration'=>$request->contract_duration,
                    'work_type'=>$request->work_type,
                    'nationality'=>$request->nationality,
                    'experience'=>$request->experience,
                    'education'=>$request->education,
                    'funcational_area'=>$request->funcational_area,
                    'contract_extension'=>$request->contract_extension,
                    'probation_period'=>$request->probation_period,
                    'job_descriptions'=>$request->job_descriptions,
                    'attachments'=>$attachments,
                    'job_locations'=>$request->job_locations,
                    'job_requirements'=>$request->job_requirements,
                    'submission_guideline'=>$request->submission_guideline
                ]);
                return response()->json($job,201);
        }
    }
    public function update( $id)
    {
           $job=Job::find($id);
           $change= $job->update(['is_active' => !$job->is_active ]);
            return response()->json($change ,200);


    }
    public function changePromot($id){
        $promot=Job::find($id);
        $change= $promot->update(['is_promoted' => !$promot->is_promoted ]);
        return response()->json($change ,200);
    }
    public function changeUrgent($id){
        $urgent=Job::find($id);
        $change= $urgent->update(['is_urgent' => !$urgent->is_urgent ]);
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
                $jobs = Job::whereIn('id', $request)->get();
                foreach ($jobs as $value) {
                     \File::delete($value->attachments);
                }
                Job::whereIn('id', $request)->delete();


        return true;
    }
}

