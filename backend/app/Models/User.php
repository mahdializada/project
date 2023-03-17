<?php

namespace App\Models;

use App\Models\Currency;
use App\Traits\UuidTrait;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Models\Advertisement\Application;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  HasApiTokens, HasFactory, Notifiable, SoftDeletes, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "code",
        'firstname',
        "lastname",
        "username",
        'email',
        'password',
        "phone",
        "whatsapp",
        "gender",
        "birth_date",
        "tracing_status",
        "status",
        "image",
        "address",
        "change_password",
        "schedule_type",
        "time_range",
        "date_range",
        "note",
        "selected_companies",
        "language_id",
        "country_id",
        "state_id",
        "current_country_id",
        "currency_id",
        "translated_language_id",
        "created_by",
        "updated_by"
    ];

    protected $casts = [
        'coords' => 'array',
    ];


    protected $hidden = ['password', 'pivot'];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];




    public static function getTypes()
    {
        return User::$types;
    }


    public function currentCurrency(){
        if(!$this->currency_id){
            $dollar = Currency::where("code", "USD")->first();
            $this->currency_id = $dollar->id;
            $this->save();
            return $dollar;
        }
        $currency = $this->load("currency");
        return $this->currency;
    }
    public function getImageAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return env("APP_URL") . Storage::url($value);
    }

    // parse and return date range column
    public function getDateRangeAttribute($value)
    {
        if ($value) {
            return json_decode($value);
        }
        return $value;
    }

    // parse and return time range column
    public function getTimeRangeAttribute($value)
    {
        if ($value) {
            return json_decode($value);
        }
        return $value;
    }

    // public function getImageAttribute($value): string
    // {
    //     if (filter_var($value, FILTER_VALIDATE_URL)) {
    //         return $value;
    //     }
    //     return env("APP_URL") . Storage::url($value);
    // }


    public function lastseen()
    {
        return $this->hasOne(UserLastSeen::class);
    }

    public function note()
    {
        return $this->hasMany(Note::class);
    }


    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }


    public function currentCountry()
    {
        return $this->belongsTo(Country::class, 'current_country_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function locale()
    {
        return $this->belongsTo(TranslatedLanguage::class, "translated_language_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, "currency_id");
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, "reason_user", "user_id", "reason_id")->withPivot("status", "changed_by")->withTimestamps();
    }

    public function changedBy()
    {
        return $this->belongsToMany(User::class, "reason_user", "user_id", "changed_by");
    }

    public function permissions()
    {
        return $this->belongsToMany(
            ActionSubSystem::class,
            "permission_user",
            "user_id",
            "action_sub_system_id"
        )
            ->with(['sub_system:id,name,scope', 'action:id,name', 'action.subActions:id,name']);
    }

    public function userPermissions()
    {
        return $this->permissions()
            ->with(['sub_system.system', 'action:id,name', 'action.subActions:id,name,action']);
    }

    public function hasPersonalStorage()
    {
        return false;
        $my_drive = SubSystem::wherePhrase('my_drive')
            ->whereHas("system", function ($query) {
                $query->wherePhrase("personal_file_management");
            })->select(["id"])->first();


        $action_ids = ActionSubSystem::where("sub_system_id", $my_drive->id)->get("id")->pluck("id")->toArray();

        $has_my_drive = DB::table("permission_user")->whereIn("action_sub_system_id", $action_ids)
            ->where("user_id", $this->id)->exists();
        return $has_my_drive;
    }

    public function get_scopes()
    {
        $arr = [];
        $permissions = $this->permissions;
        foreach ($permissions as $perm) {
            foreach ($perm->action->subActions as $action) {
                $str = strtolower(str_replace(' ', '', $perm->sub_system->scope . $action['name']));
                if (!in_array($str, $arr)) {
                    array_push($arr, $str);
                }
            }
        }
        foreach ($this->teams as $team) {
            foreach ($team->permissions as $perm) {
                foreach ($perm->action->subActions as $action) {
                    $str = strtolower(str_replace(' ', '', $perm->sub_system->scope . $action['name']));
                    if (!in_array($str, $arr)) {
                        array_push($arr, $str);
                    }
                }
            }
        }
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $perm) {
                foreach ($perm->action->subActions as $action) {
                    $str = strtolower(str_replace(' ', '', $perm->sub_system->scope . $action['name']));
                    if (!in_array($str, $arr)) {
                        array_push($arr, $str);
                    }
                }
            }
        }
        return $arr;
    }

    public function customizeTheme()
    {
        return $this->hasOne(CustomizeTheme::class, 'user_id');
    }

    public function designRequestOrders()
    {
        return $this->belongsToMany(
            DesignRequestOrder::class,
            'design_request_order_user',
            'design_request_order_id',
            'user_id'
        );
    }

    public function fileLimition()
    {
        return $this->hasOne(FileLimitionForUsers::class, 'user_id');
    }

    public function requestStorage()
    {
        return $this->hasOne(RequestStorage::class, 'user_id');
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }
}
