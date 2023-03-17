<?php

namespace App\Repositories;

use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Remark;
use Illuminate\Http\Request;
use App\Models\Advertisement\HistoryAd;
use App\Models\Advertisement\HistoryAdset;
use App\Models\Advertisement\HistoryCampaign;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\ContentLibrary\ContentLibraryMedia;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use Aws\History;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class RemarkRepository.
 */
class RemarkRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function index($request)
    {

        $id = $request->query('id');
        $filter = $request->query('filter');

        $query = Remark::where('remarkable_id', $id)->where('sub_system', $request->sub_system);
        $query->with($this->getRelations());

        if ($request->start_date && $request->end_date)
            $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($request->start_date, $request->end_date));
        else
            $query = $query->whereDate('created_at', date("Y-m-d"));
        $data = $query->get();
        return response()->json([
            'result' => true,
            'data' => $data
        ]);
    }
    public function store(Request $request)
    {
        $model = $request->model;
        $id = $request->id;
        $user = $request->user();
        $record = null;
        switch ($model) {
            case 'ad':
                $record = HistoryAd::where('ad_id', $id)->first();
                break;
            case 'ad_set':
                $record = HistoryAdset::where('adset_id', $id)->first();
                break;
            case 'campaign':
                $record = HistoryCampaign::where('campaign_id', $id)->first();
                break;
            case 'ad_account':
                $record = AdAccount::findOrFail($id);
                break;
            case 'organization':
                $record = Application::findOrFail($id);
                break;
            case 'platform':
                $record = Platform::find($id);
                break;

            case 'company':
                $record = Company::find($id);
                break;

            case 'country':
                $record = Country::find($id);
                break;
            case 'item_code':
                if ($request->sub_system == 'advertisement') {
                    $record = Connection::where('pcode', $id)->first();
                } else if ($request->sub_system == 'online_sales') {
                    OnlineSales::setcustomPrimaryKey('product_code');
                    $record = OnlineSales::where('product_code', $id)->first();
                }
                break;
            case 'sales_type':
                if ($request->sub_system == 'advertisement') {
                    HistoryAd::setcustomPrimaryKey('sales_type');
                    $record = HistoryAd::where('sales_type', $id)->first();
                } else if ($request->sub_system == 'online_sales') {
                    OnlineSales::setcustomPrimaryKey('sales_type');
                    $record = OnlineSales::where('sales_type', $id)->first();
                }

                break;
            case 'project':
                $record = Project::find($id);

                break;
            case 'content_library_media':
                $record = ContentLibraryMedia::find($id);
                break;
        }

        if ($record) {
            $data = [
                'remark' => $request->remark,
                'user_id' => $user->id,
                "sub_system" => $request->sub_system
            ];

            $remark =  $record->remarks()->create($data);
            return response()->json(
                [
                    'result' => true,
                    'data' => $remark->load($this->getRelations())
                ]
            );
        } else {
            return response()->json(
                [
                    'result' => false,
                    'not_found' => true,
                ],
                404
            );
        }
    }
    public function storeRules()
    {
        return [
            'model' => ['required'],
            'id' => ['required'],
            'remark' => ['required']
        ];
    }
    public function update($request)
    {
        try {
            $remark = Remark::find($request->id);
            $remark->update(['remark' => $request->remark]);
            return response()->json(
                [
                    'result' => true,
                    'data' => $remark->load($this->getRelations())
                ]
            );
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(
                [
                    'result' => false,
                    'error' => $th
                ],
                500
            );
        }
    }
    public function show()
    {
    }
    public function destroy($id)
    {
        $res = Remark::destroy($id);
        return response()->json(['result' => $res == 1]);
    }
    private function getRelations()
    {
        return
            [
                'user:id,code,firstname,lastname,image',
                'reactions:id,reaction_type,remark_id,user_id',
                'reactions.user:id,firstname,lastname,image',
            ];
    }
}
