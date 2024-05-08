<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class AnimalForSale extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','animal_id','delete_reason','delete_note','email_id','user_code','pm_code','latitude','subscriptionStartDate','subscriptionEndDate','longitude','user_id','city_town','state','taluka','district','pincode','state_id','species','breed','type','age','sex','UID_number','description','price','contact_number_of_owner','contact_name_of_owner','address','otp','otp_expiration','status','views_count','views','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
