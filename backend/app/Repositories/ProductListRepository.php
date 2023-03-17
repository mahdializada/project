<?php

namespace App\Repositories;

use App\Models\CloudAttachment;
use App\Models\CloudinaryTempFile;
use App\Models\ProductList;
use App\Models\ProductListAttachment;
use App\Repositories\Files\CloudinaryFileUtils;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Response;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class ProductListRepository.
 */
class ProductListRepository extends Repository
{
    public function pagenate($request)
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new ProductList(), $request);
        if ($key == "with supplier") {
            $queryBuilder->query->whereHas('suppliers');
        }
        if ($key == "without supplier") {
            $queryBuilder->query->doesntHave('suppliers');
        }
        $queryBuilder->setRelations($this->getRelations());
        $query = $queryBuilder->build()->paginate()->getData();
        $extraData = $this->getTotal();
        return response()->json(['data' => $query, 'extraData' => $extraData]);
    }

    public function getTotal()
    {
        $count = ProductList::select(DB::raw('count(*) as withSupplierTotal'))
            ->whereHas("suppliers")
            ->get();
        $count1 = ProductList::select(DB::raw('count(*) as withoutSupplierTotal'))
            ->doesntHave("suppliers")
            ->get();
        $extraData['withSupplierTotal'] = $count[0]['withSupplierTotal'];
        $extraData['withoutSupplierTotal'] = $count1[0]['withoutSupplierTotal'];
        $extraData['allTotal'] = $count[0]['withSupplierTotal'] + $count1[0]['withoutSupplierTotal'];
        return $extraData;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->products as $key => $product) {
                $data = ProductList::create([
                    'code'         => rand(100000, 9999999999),
                    'product_name' => $product['product_name'],
                    'product_code' => $product['product_code'],

                ]);
                $storeFile =    CloudinaryFileUtils::storeFiles($data, $product['product_image']);
                if (!$storeFile['result']) {
                    DB::rollBack();
                    return $storeFile;
                }

                $result[$key] = $data->load($this->getRelations());
            }
            DB::commit();
            return $this->successResponse($result, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function getRelations()
    {
        return [
            'suppliers:id,supplier_name',
            'attachments'
        ];
    }

    public function storeRules(): array
    {
        return [
            'product_name'         => 'required|array',
            'product_code'         => 'required|array',
            'product_cost'         => 'required|array',
            'product_image'         => 'required|array',
        ];
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $data = ProductList::find($id);
            $data->update([
                'product_name' => $request->product_name,
                'product_code' => $request->product_code,
            ]);


            $updateFile =    CloudinaryFileUtils::updateFiles($data, $id, $request->product_image);
            if (!$updateFile['result']) {
                DB::rollBack();
                return $updateFile;
            }

            // foreach ($request->product_image as $images) {
            //     $pr = ProductListAttachment::where('product_list_id', $data->id)->get();
            //     foreach ($pr as $p) {
            //         $p->update([
            //             'path' => $images,
            //         ]);
            //     }
            // }

            DB::commit();
            return $this->successResponse($data->load($this->getRelations()), Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function changeProductSupplier($request)
    {
        try {
            DB::beginTransaction();
            $product = ProductList::whereIn('id', $request->product_ids)->get();
            foreach ($product as $data) {
                foreach ($request->suppliers as $value) {
                    if ($data->suppliers()->where('supplier_id', $value['id'])->exists())
                        $data->suppliers()->updateExistingPivot([$value['id']], ['product_cost' => $value['cost']]);
                    else
                        $data->suppliers()->attach([$value['id']], ['product_cost' => $value['cost']]);
                }
            }
            DB::commit();
            return $this->successResponse($product->load($this->getRelations()), Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }
}
