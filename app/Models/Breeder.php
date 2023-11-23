<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Breeder extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'breeders';
    protected $fillable = ['id','breeder_name','firm_registration_number','mobile_number','animal_breed','age','animal_description','vaccination_done','expected_price','address','state','state_id','city_town','taluka','district','pincode','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
