<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class State extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','state_id','state','country_id','is_default','is_active','sort_order','lang','created_at','updated_at'];
    public static  function boot()
    {
        parent::boot();
    }

}
