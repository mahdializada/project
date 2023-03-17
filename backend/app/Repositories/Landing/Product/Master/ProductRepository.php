<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2022-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker 
 */

namespace App\Repositories\Landing\Product\Master;

use Illuminate\Http\Request;
use App\Repositories\Landing\Product\RootRepository;

class ProductRepository extends RootRepository
{
    public function __construct($model, $filePath)
    {
        parent::__construct($model, $filePath);
    }

    public function show(Request $request, $id)
    {
        $category = $this->model->with($this->getRelations())->findOrFail($id);
        return $this->successResponse($category);
    }

}
