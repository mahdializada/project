<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class GenerateCreatedByUpdatedByObserver
{

    private string $userId;

    public function __construct()
    {
        $this->userId = (string)Auth::id();
    }

    public function updated($model)
    {
        $model->updatedBy = $this->userId;
    }

    public function created($model)
    {
        $model->createdBy = $this->userId;
    }
}
