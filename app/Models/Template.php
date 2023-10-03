<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Auth;
class Template extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = [
        'module_name','slug','type','created_at'  
    ];
    protected $appends = ['file_path'];
    protected static $createBy = 0;
    public static  function boot()
    {
        parent::boot();
        // beforeCreate
        // self::creating(function($model) {
        //     $model->create_by = self::$createBy;
        //     return true; 
        // });  
        // self::updating(function($model) { 
        //     $model->create_by = self::$createBy;
        //     return true; 
        // }); 
    }
    /**
     * Records activity event 
     */
    protected static $recordEvents = ['created','updated','deleted']; 

    
}
