<?php

namespace App\Models\Advertisement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Advertisement\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectionTemplate extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["connection_id", "name"];

    protected $appends = ['favorite'];


    /**
     * Get the connection that owns the ConnectionTemplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    /**
     * The roles that belong to the ConnectionTemplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'connection_templates_users', 'connection_template_id', 'user_id');
    }


    public function getFavoriteAttribute()
    {
        $auth_id = Auth::id();
        $res = DB::table('connection_templates_users')->where('user_id', $auth_id)->where('connection_template_id', $this->id)->count();
        return $res > 0;
    }
}
