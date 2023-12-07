<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Rxreminder extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'rx_reminders';
    protected $fillable = ['id','animal_owner_id','role_id','animal_id','UID_number','prescription','description',
	'scheduled_date','scheduled_message','user_id','created_at','updated_at'];
        
    public static function boot()
    {
        parent::boot();
    }

}
