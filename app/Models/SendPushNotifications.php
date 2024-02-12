<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class SendPushNotifications extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'send_push_notifications';
    protected $fillable = ['id','user_type','user_id','message','created_at','updated_at'];
        
    public static function boot()
    {
        parent::boot();
    }

}
