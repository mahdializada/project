<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UuidTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DesignRequest extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        "product_code", "product_name", "status", "type",
        "priority", "company_id",
        "updated_by", "created_by", "template_id", "label_id"
    ];

    protected $appends = ['percentage', "download_status", "has_files"];

    static public function  getPriority(): array
    {
        return ["high", "medium", "low"];
    }

    static public function  getStatus(): array
    {
        return [
            "waiting", "in storyboard", "storyboard review", "storyboard rejected",
            "in design", "design review", "design rejected",
            "in programming", "completed", "cancelled", "removed",
        ];
    }


    static public function getTypes(): array
    {
        return [
            'landing page design', 'advertisement content', 'social media design',
            'translation', 'texts and writings', 'general design', 'shooting'
        ];
    }


    public function getCodeAttribute($value)
    {
        return generateUniqueCode($value, "LDR", 4);
        // return "LBR_" . $value;
    }
    public function getDownloadStatusAttribute()
    {
        $files = $this->files()->withTrashed()->get();
        if ($files->isNotEmpty()) {
            $download_count = 0;
            $file_length =  $files->count();
            foreach ($files as $file) {
                if ($file->downloads()->exists()) $download_count++;
            }

            $percentage =  ($download_count / $file_length) * 100;
            $percentage = round($percentage);
            if ($download_count > 0) {
                return ["status" => "downloaded", "percentage" => $percentage];
            }
            return ["status" => "not_downloaded"];
        }
        return ["status" => "no_media",];
    }


    public function getPercentageAttribute()
    {
        $orders = $this->designRequestOrder;
        if ($orders) {
            $orderId =  $this->designRequestOrder->id;
            $progressValue = DesignRequestOrderUser::where("design_request_order_id", $orderId)
                ->average("task_prograss");
            return (int) $progressValue;
        }
        return 0;
    }


    /**
     * Get the DesignRequestOrder that owns the DesignRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function designRequestOrder(): HasOne
    {
        return $this->hasOne(DesignRequestOrder::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function statusTimes()
    {
        return $this->hasMany(StatusTimeConsumed::class);
    }
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
    public function label()
    {
        return $this->belongsTo(Label::class);
    }


    /**
     * Get all of the files for the DesignRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(LibraryFile::class);
    }

    public function getHasFilesAttribute(){
        return $this->files()->exists();
    }
}
