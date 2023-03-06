<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\ConnectionTemplate;
use App\Models\Advertisement\Platform;
use App\Models\Company;
use App\Models\Country;
use App\Models\Template;
use Exception;
use Illuminate\Http\Request;

class ConnectionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchItems(Request $request,  String $type)
    {
        try {


            $data = null;
            switch ($type) {
                case 'country':
                    $data = $this->getCountries();
                    break;
                case 'company':
                    $data = $this->getCompanies();
                    break;
                case 'platform':
                    $data = $this->getPlatfroms();
                    break;
                case 'organization':
                    $data = $this->getOrganization();
                    break;
                case 'pcode':
                    $data = $this->getPCode();
                    break;
            }
            return response()->json(['result' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['result' => false, 'data' => $e->getMessage()], 500);
        }
    }


    private function getCountries()
    {
        $ids = Connection::select('country_id')->distinct()->pluck('country_id');
        return Country::select(['id', 'name', 'iso2'])->whereIn('id', $ids)->get();
    }

    private function getCompanies()
    {
        $ids = Connection::select('company_id')->distinct()->pluck('company_id');
        return Company::select(['id', 'name', 'logo'])->whereIn('id', $ids)->get();
    }

    private function getPlatfroms()
    {
        $ids = Connection::select('platform_id')->distinct()->pluck('platform_id');
        return Platform::select(['id', 'platform_name'])->whereIn('id', $ids)->get();
    }

    private function getOrganization()
    {
        $ids = Connection::select('application_id')->distinct()->pluck('application_id');
        return Application::select(['id', 'name'])->whereIn('id', $ids)->get();
    }

    private function getPCode()
    {
        $ids = Connection::select('pcode as id', 'pname')->distinct()->get();
        return $ids;
    }


    public function index()
    {
        $data = ConnectionTemplate::with('connection')->get();
        return response()->json(['result' => true, 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $id = $request->id;
            $user_id = $request->user()->id;
            $connection_template = ConnectionTemplate::where('id', $id)->first();
            if ($request->favorite) {
                $connection_template->users()->attach($user_id);
            } else {
                $connection_template->users()->detach($user_id);
            }
            return response()->json(['result' => true]);
        } catch (Exception $e) {
            return response()->json(['result' => false, 'data' => $e->getMessage()], 500);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
