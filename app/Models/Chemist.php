<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Chemist extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = [	'id','shop_name','owner_name','mobile_number','address_line_1','address_line_2','city_id','city_town','state_id','state','village','pincode','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
