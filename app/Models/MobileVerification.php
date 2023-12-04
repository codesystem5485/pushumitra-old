<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class MobileVerification extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'mobile_otp_verification';
    protected $fillable = ['id','module_type','mobile_number','otp','otp_expiration','is_verified','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
