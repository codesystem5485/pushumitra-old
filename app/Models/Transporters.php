<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Transporters extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','user_id','user_code','latitude','longitude','subscriptionStartDate','subscriptionEndDate','taluka','district','transporter_name','vehicle_name','description','mobile_number','address','address_line_2','city_id','city_town','state_id','state','pincode','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
