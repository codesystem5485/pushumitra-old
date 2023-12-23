<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;  
use Hash;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Auth; 
use Config;
use Illuminate\Http\Request;
class TempUsersSignup extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable,HasApiTokens,LogsActivity; 
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 protected $table = 'temp_users_signup';
	protected $fillable = ['role','email','password','mobile_number','is_phone_verify','otp','otp_expiration','address_line_1','city_town','state_id','state',
		'pincode','is_active','is_verified','taluka','district','full_name',
    ]; 

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Records activity event 
     */
    protected static $recordEvents = ['created','deleted'];

    /**
     * Modify activity response
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if($eventName == 'created'){
            if(isset($activity->subject->name)){
                $activity->description = trans('messages.user_register',['name' => $activity->subject->name]);
            }
            $activity->log_name = trans('messages.create');
        }
        if($eventName == 'deleted'){
            if(isset($activity->subject->name)){
                $activity->description = trans('messages.delete_user',['name' => $activity->subject->name]);
            }
            $activity->log_name = trans('messages.delete');
        }
        $activity->causer_id = !empty(Auth::user()->id) ? Auth::user()->id:$activity->subject_id;
    }

    protected $appends = ['profile_image','created_date'];
    protected static $createBy = 0;

    public static function boot()
    {
        
        parent::boot();
        // beforeCreate
        
    }
	
   
    public function setPasswordAttribute($password)
    {
        if(!empty($password)){
            $this->attributes['password'] = Hash::make($password);
        }
    }

}
