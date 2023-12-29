<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Testimonials extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'testimonials';
    protected $fillable = ['id','user_id','testimonial_name','testimonial_designation','testimonial_message',
	'testimonial_photo','status','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
