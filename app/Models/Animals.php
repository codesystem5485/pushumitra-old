<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Animals extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','delete_reason','delete_note','animal_owner','name','user_id','description','mobile_number','species','breed','UID_number','age','sex','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

    public function getAnimalOwner()
    {
        return $this->belongsTo(User::class,'animal_owner');
    }
}
