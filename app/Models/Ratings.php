<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Ratings extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'review_ratings';
    protected $fillable = ['id','user_id','rateable_id','review_comments','star_ratings','module_id','status','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
