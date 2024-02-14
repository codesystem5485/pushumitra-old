<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class CsrActivities extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'csr_activities';
    protected $fillable = ['id','category','title','user_id','description','schedule_date','address','city_town','state_id','state','pincode','created_at','updated_at','deleted_at','status'];
        
    public static function boot()
    {
        parent::boot();
    }

}
