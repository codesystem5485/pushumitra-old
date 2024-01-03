<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Notifications extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','link','type','title','message','scheduled_date','sender_user_id','rx_reminder_id','send_date',
	'read_flag','send_flag','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
