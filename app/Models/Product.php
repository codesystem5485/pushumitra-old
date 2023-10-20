<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Product extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','product_name','description','price','product_owner','mobile_number','address','otp','otp_expiration','status','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

    public function getProductOwner()
    {
        return $this->belongsTo(User::class,'product_owner');
    }
}
