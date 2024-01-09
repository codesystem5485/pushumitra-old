<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class ProductForSale extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','user_id','user_code','latitude','longitude','subscriptionStartDate','subscriptionEndDate','taluka','district','product_name','description','price','contact_number_of_owner','contact_name_of_owner','address','city_town','state_id','state','pincode','status','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
