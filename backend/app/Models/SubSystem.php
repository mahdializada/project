<?php

namespace App\Models;

use App\Models\Advertisement\ColumnCategory;
use App\Models\Advertisement\SubSystemTab;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSystem extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["name", "system_id", "has_status", "scope", "phrase"];


    public function ActionsSubSystem()
    {
        return $this->belongsToMany(Action::class);
    }

    public function reasons(){
        return $this->belongsToMany(Reason::class ,'reason_sub_system');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class);
    }

    public function actionSubSystems()
    {
        return $this->hasMany(ActionSubSystem::class);
    }

    public function tabs()
    {
        return $this->hasMany(SubSystemTab::class);
    }
    public function categories()
    {
        # code...
        return $this->belongsToMany(
            ColumnCategory::class,
            'column_category_sub_systems',
        );
    }
}
