<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;


class Guestusers extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'guestusers';
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_number','otp','otp_expiration',
    ];

    public static  function boot()
    {
        parent::boot();
    }	

    
}
