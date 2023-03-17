<?php

namespace App\Http\Controllers;

use App\Models\Advertisement\SubSystemTab;
use App\Models\Advertisement\TabColumn;
use App\Models\Company;
use App\Models\ViewName;
use App\Repositories\ViewNameRepository;
use Exception;
use Illuminate\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ViewNameController extends Controller
{
    public function __construct()
    {
        // $this->middleware('dailylogs')->except('index');
    }

    public function index(Request $request)
    {
        //

        $personalData = ViewName::with('user:id,firstname,lastname')->where("user_id", "=", Auth::user()->id)
            ->where("page_name", "=", $request->view_name)->orderBy('created_at', 'desc')->get()->toArray();
        $sharedData =   ViewName::with('user:id,firstname,lastname')->where("page_name", $request->view_name)
            ->where("company_id", Company::first()->id)
            ->where("scope_type", 1)
            ->where("user_id", "!=", Auth::user()->id)->orderBy('created_at', 'desc')->get()->toArray();

        $data = array_merge($personalData, $sharedData);
        if ($request->view_name == 'graph_profile') {
            return $data;
        }
        return   response()->json($data);
    }

    public function store(Request $request)
    {
        $this->storeValidation($request);
        $request = $request->all();
        $prev_record = ViewName::where("user_id", "=", Auth::user()->id)
            ->where("page_name", "=", $request["page_name"])->exists();

        if (!$prev_record) {
            $request['default'] = 1;
        }
        try {
            $request["columns"]    = json_encode($request["columns"]);
            $request['user_id']    = Auth::user()->id;
            $request['company_id'] = Company::first()->id;
            $result = ViewName::where(['name' => $request['name'], 'user_id' => Auth::user()->id, 'page_name' =>  $request["page_name"]])->first();
            if ($result) {
                $result->update($request);
                return   response()->json($result->load('user:id,firstname,lastname'));
            }
            $result = ViewName::create($request);
            return   response()->json($result->load('user:id,firstname,lastname'));
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    public function show($id)
    {
        //
        return $id;
    }


    public function update(Request $request, $id)
    {

        $this->updateValidation($request);
        try {
            $result    = ViewName::where('name', $request->name)->where('user_id', Auth::user()->id)->where('page_name', $request->page_name)->get();
            $view = ViewName::find($result[0]->id);
            $view->update(["columns" => $request->columns]);
            return response()->json(true);
        } catch (Exception $exception) {
            return response()->json(["false", $exception->getMessage()]);
        }
    }


    public function destroy($id)
    {
        //
        try {
            //code...
            $view = ViewName::where('id', $id)->where('user_id', Auth::user()->id)->first();
            $result = 0;
            if ($view) {
                $result = $view->delete();
            }
            return response()->json($result);
        } catch (Exception $exception) {
            //throw $th;
            return response()->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    // validation roles
    public function storeValidation($request)
    {
        $request->validate([
            'name'      => 'required|min:2',
            'columns'   => 'required',
            'page_name' => 'required',
            // 'page_name' => 'required | unique:view_names,page_name,user_id',
        ]);
    }
    public function updateValidation($request)
    {
        $request->validate([
            'name'      => 'required|min:2',
            'columns'   => 'required',
        ]);
    }



    public function editDefault(Request $request)
    {
        # code...
        try {
            DB::beginTransaction();
            ViewName::where(['user_id' => auth()->id(), "default" => true])
                ->update(['default' => false]);
            ViewName::find($request->id)->update(['default' => $request->default]);
            DB::commit();
            return response()->json(['result' => true]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 400);
        }

        return $request->all();
    }

    public function fetchHeaders(Request $request)
    {
        $custom_column = ViewName::where("user_id", "=", Auth::user()->id)
            ->where(["page_name" => $request->tab_name, "default" => true])->pluck('columns')->first();
        $tab = SubSystemTab::where('tab_name', $request->tab_name)->first();
        $headers = TabColumn::with('category:id,category_name')->where('tab_id', "$tab->id")->get();
        if ($custom_column) {
            $ids = implode(",", $custom_column);
            $selected_header = TabColumn::with('category:id,category_name')->whereIn('id', $custom_column)->where('tab_id', "$tab->id")->orderByRaw("FIELD(id,$ids)")->get();
        } else {
            $selected_header = TabColumn::with('category:id,category_name')->where(['tab_id' => "$tab->id", "visible" => true])->get();
        }
        if ($request->serries_name) {
            $serries = ViewName::where("user_id", "=", Auth::user()->id)
                ->where(["page_name" => 'graph_profile', "default" => true])->pluck('columns')->first();
            $request->merge(['view_name' => "graph_profile"]);
            $all_setting =  $this->index($request);
            return response()->json(["selected_headers" => $selected_header, "selected_serries" => $serries, 'settings' => $all_setting]);
        }

        return response()->json(["selected_headers" => $selected_header, "headers" => $headers]);
    }
}
