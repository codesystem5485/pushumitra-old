<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;  
use Hash;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Auth; 
use Config;
use Illuminate\Http\Request;

class Cities extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','city_id','city'.'state_id','is_default','is_active','sort_order','lang','created_at','updated_at'];
    protected $appends = [];
    protected static $createBy = 0;
    public static  function boot()
    {
        parent::boot();
    }
    /**
     * Records activity event 
     */
    protected static $recordEvents = ['created','updated','deleted']; 

}
