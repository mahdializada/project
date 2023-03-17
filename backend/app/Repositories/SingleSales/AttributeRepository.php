<?php

namespace App\Repositories\SingleSales;

use App\Models\CloudinaryTempFile;
use Exception;
use App\Models\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\SingleSales\Attribute;
use App\Repositories\Files\CloudinaryFileUtils;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryRepository.
 */
class AttributeRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        if ($request->query->get("item")) {
            return $this->getAttributePerCategory($request);
        }
        if ($request->query->get("fetch_for_form")) {
            return $this->getForForm($request);
        }
        $key = $request->query->get("key");

        $queryBuilder = new UriQueryBuilder(new Attribute(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $columns = "*";
        if ($request->fields) {
            $allowed = ['id', 'name'];
            $fields = explode(",", $request->fields);
            $containsAllValues = !array_diff($fields, $allowed);
            if (!$containsAllValues) {
                throw new Exception("invalid fields", 1);
            }
            $columns = $fields;
        }
        $query = $queryBuilder->query->select($columns);
        $searchInColumns        = ['name',  'updated_at', 'created_at'];
        $query    = $this->filterRecords($query, $request,  $searchInColumns);
        $statusQuery            = clone  $query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($query, $key);
        $queryBuilder->query->orderBy("created_at", 'desc');
        $attributes             = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'removedTotal',
        ];

        $attributes = $this->getStatusCount($statusQuery, $attributes, $extraData, false);
        return response()->json($attributes);
    }


    private function getForForm(Request $request)
    {

        $data = Attribute::select(['id', 'name', 'values'])
            ->orderBy('name')
            ->get();

        return response()->json($data);
    }
    private function getAttributePerCategory(Request $request)
    {

        $id = $request->item;
        $test = Attribute::with('categories')->whereHas('categories', function (Builder $query) use ($id) {
            $query->where('category_id', $id);
        })->select(['id', 'name','arabic_name', 'values', 'type'])
            ->orderBy('name')->get();

        return response()->json($test);

        // $data = Attribute::with('categories')->where('category_id',$request->item)->select(['id', 'name', 'values'])
        //     ->orderBy('name')
        //     ->get();

        // return response()->json($data);
    }


    public function filterCategory()
    {
    }

    public function storeRules()
    {
        if (request()->input('attribute_section', 'value_input')) {
            $rules = [];
            // $rules = [
            //     'value_input' => 'required',
            //     'value_name' => ['required', 'min:2', 'max:32'],
            //     'arabic_name' => ['required', 'min:2', 'max:32'],
            // ];
        //     // if (request()->input('value_input', 'text'))
        //     //     $rules['text_value'] = ['required', 'min:2', 'max:32'];

        //    if (request()->input('value_input', 'file'))
        //         $rules['file_value'] = 'required';

        //     else if (request()->input('value_input', 'phone'))
        //         $rules['phone_value'] = ['required', 'numeric'];

        //     else if (request()->input('value_input', 'link'))
        //         $rules['link'] = 'required';
        } else
            $rules =
                [
                    'attributes.*.name' => ['required', 'min:2', 'max:32'],
                    'attributes.*.arabic_name' => ['required', 'min:2', 'max:32'],
                    'attributes.*.selectedAttr' => ['required', 'array'],
                    "attributes.*.selectedAtrr.*" => "required"
                ];
        return $rules;
    }
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            if($request->value_input){
                $attrModel = Attribute::create([
                    'name'=>$request->value_name,
                    'arabic_name'=>$request->arabic_name,
                    // 'type'=>$request->value_input,
                    // 'extension' => json_encode($request['num']),
                    'type'=>'value_input',
                    'created_by'=> auth()->user()->id,
                    'code'=> rand(100000, 9999999999),
                    'values'=>json_encode([$request->value_input])
                ]);
                $data[] = $attrModel->load($this->getRelations())->refresh();
            }else{

                foreach ($request['attributes'] as $key) {
                    $key['name'] =  $key['name'];
                    $key['arabic_name'] =  $key['arabic_name'];
                    $key['type'] =  'value_select';
                    $key['values'] =  json_encode($key['selectedAttr']);
                    // $key['section'] = 'value_select';
                    $key['created_by'] = auth()->user()->id;
                    $key['code'] = rand(100000, 9999999999);
                    $attrModel = Attribute::create($key);
                    $data[] = $attrModel->load($this->getRelations())->refresh();
                }
            }
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }
    }


    public function update(Request $request)
    {
        // return "this is for test";
        try {
            $data = [];
            $values = null;
            DB::beginTransaction();
            if($request->attribute_section=="value_input"){
                $model = Attribute::find($request->id);
                // if($request->value_input=='file'){
                //     CloudinaryFileUtils::updateFiles($model, $request->id, $request->file_value);
                // }else{
                    $values = [$request->value];
                // }
                $model->update([
                    'name'=>$request->value_name,
                    'arabic_name'=>$request->arabic_name,
                    // 'type'=>$request->value_input,
                    'type'=>'value_input',
                    'created_by'=> auth()->user()->id,
                    'values'=>json_encode($values)
                ]);
                $data = $model->load($this->getRelations())->refresh();
            }else{
                $model = Attribute::find($request->id);
                $model->update([
                    'name'=>$request->input('attributes')[0]['name'],
                    'arabic_name'=>$request->input('attributes')[0]['arabic_name'],
                    // 'type'=>$request->attribute_section,
                    'type'=>'value_select',
                    'created_by'=> auth()->user()->id,
                    'values'=>json_encode($request->input('attributes')[0]['selectedAttr'])
                ]);
                $data = $model->load($this->getRelations())->refresh();

            }
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $attributes = Attribute::whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($attributes as $attribute) {
                if (!$attribute->trashed()) {
                    foreach ($reasons as $reason) {
                        $attribute->reasons()->save($reason);
                    }
                    $attribute->reasons()->syncWithoutDetaching($reasons);
                    $attribute->delete();
                } else {
                    $attribute->reasons()->detach();
                    $attribute->forceDelete();
                }
            }
            DB::commit();

            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }
    }

    private function getRelations(): array
    {

        return [
            'createdBy',
            'categories'
        ];
    }
    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Attribute(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function changeAttributesStatus(Request $request)
    {

        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');
            $noReasonTabs = array_map('trim', explode(',', $request->input('no_reason_tabs')));
            $noReasonOperations = array_map('trim', explode(',', $request->input('no_reason_operations')));
            $newStatus = $request->input('status');
            $reasons = $request->input('reasons');
            foreach ($itemIds as $id) {
                $item = Attribute::withTrashed()->findOrFail($id);
                if ($newStatus == 'restore') {    // Restore if current status is removed
                    $item->restore();
                } else if ($newStatus != 'restore') {
                    $item->status = $newStatus;
                }
                if (!in_array($item->status, $noReasonTabs) && !in_array($newStatus, $noReasonOperations) && gettype($reasons) === 'array') {
                    $item->reasons()->syncWithoutDetaching($reasons);
                }
                $item->save();
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
    private function statusValidations($request)
    {
        $request->validate([
            'type' => ['required', 'string'],
            'status' => ['string'],
            'item_ids' => ['required'],
            'reasons' => ['required_if:type,hasReason', 'array'],
        ]);
    }
}
