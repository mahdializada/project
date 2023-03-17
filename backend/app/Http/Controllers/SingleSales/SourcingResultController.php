<?php

namespace App\Http\Controllers\SingleSales;

use ActivityLog;
use App\Http\Controllers\Controller;
use App\Repositories\SingleSales\Sourcing\SourcingRequestRepository;

class SourcingResultController extends Controller
{
    private $repository;
    private $ActivityLog;

    public function __construct()
    {
        $this->repository = new SourcingRequestRepository();
        $this->ActivityLog = new ActivityLog();
    }


}
