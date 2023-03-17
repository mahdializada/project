<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CustomizeTheme extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','global_theme', 'toolbar_theme', 'toolbar_style','content_layout','menu_theme',"rtl",'primary_color'];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
