<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Veterinaryhospitals extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'veterinary_hospitals';
    protected $fillable = ['id','user_id','user_code','latitude','longitude','subscriptionStartDate','subscriptionEndDate','taluka','district','hospital_name','veterinary_owner_name','type','education','svc_registration_number','parent_category','sub_category','description',
	'mobile_number','address','city_id','city_town','state_id','state','pincode','created_at','updated_at','deleted_at','status'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
