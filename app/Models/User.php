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
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable,HasApiTokens,LogsActivity; 
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','middle_name','last_name','email','password','country_code','profile_photo','mobile_number','alternate_mobile_number','is_phone_verify','otp','otp_expiration','address_line_1','address_line_2','village','city_id','city_town','state_id','state','pincode','nationality','sex','marital_status','date_of_birth','age','education','education_certificate','is_active','last_login',
		'fcm_id','pm_code','is_verified'
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
        self::creating(function($model) {
            $model->create_by = self::$createBy;
            // $model->is_active = 1; 
            return true; 
        });  
        self::updating(function($model) { 
            $model->create_by = self::$createBy;
            return true; 
        }); 
    }
    public static function setCreateBy($id = 0){
        self::$createBy =  $id;
    } 

    public function setPasswordAttribute($password)
    {
        if(!empty($password)){
            $this->attributes['password'] = Hash::make($password);
        }
    }

    public function setDobAttribute($dob)
    {
        $this->attributes['date_of_birth'] = date('Y-m-d',strtotime($dob));
    }

    public function getUserDetail(){
        return $this->hasOne(UserDetail::class,'user_id');
    }

    public function getCreatedBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function getCreatedDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d-m-Y H:i:s');
    }
    
    public function getProfileImageAttribute()
    {
        $path = Config::get('constants.file.user_file_path');
        return url('/').'/'.$path.'/'.$this->profile_pic;
    }
}
