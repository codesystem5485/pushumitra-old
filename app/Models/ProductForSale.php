<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class ProductForSale extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','product_name','description','price','contact_number_of_owner','contact_name_of_owner','address','otp','otp_expiration','status','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
