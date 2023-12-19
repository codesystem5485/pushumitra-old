<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Breeds extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','breed','species','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
