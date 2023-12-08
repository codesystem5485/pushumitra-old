<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Payments extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'payments';
    protected $fillable = ['id','user_id','role_id','module_details','order_id','module_type_id','status','payment_date','payment_response','payment_request',
	'amount','type','payment_id','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
