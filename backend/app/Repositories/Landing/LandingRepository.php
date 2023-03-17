<?php

namespace App\Repositories\Landing;

use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class LandingRepository extends Repository
{
    public Model $model;
    protected $filePath = "";

    public function __construct(Model $model, String $filePath = "")
    {
        $this->model = $model;
        $this->filePath = $filePath;
    }
}