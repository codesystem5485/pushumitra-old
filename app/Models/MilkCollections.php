<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class MilkCollections extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'milkcollection_centers';
    protected $fillable = ['id','user_id','user_code','latitude','longitude','subscriptionStartDate','subscriptionEndDate','taluka','district',
	'milkcollection_center_name','type','incharge_name','description','mobile_number','contact_number','address','city_id','city_town','state_id','state','pincode','created_at','updated_at','deleted_at','status'];
        
    public static function boot()
    {
        parent::boot();
    }

}
