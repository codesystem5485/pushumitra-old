<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Advertisements extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'advertisements';
    protected $fillable = ['id','advertisement_balance_cost','advertisement_total_cost','advertisement_title','advertisement_startdate','advertisement_enddate','advertiser_name',
	'advertiser_address','advertiser_contactnumber','advertisement_cost','advertisement_cost_paid','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
