<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Easycares extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'easy_cares';
    protected $fillable = ['id','is_verified','title','user_code','solutions','link','education','user_id','created_at','updated_at','status'];
        
    public static function boot()
    {
        parent::boot();
    }

}
