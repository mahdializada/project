<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonStatusTeam extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'reason_status_team';

    protected $fillable = [
        'status_id',
        'changed_by',
        'reason_id',
        'team_id'
    ];

    // public function status()
    // {
    //     return $this->belongsTo(Status::class);
    // }

    // public function reason()
    // {
    //     return $this->belongsTo(Reason::class);
    // }

    // public function changedBy()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function team()
    // {
    //     return $this->belongsTo(Team::class);
    // }
}
