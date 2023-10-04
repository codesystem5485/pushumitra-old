<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Books extends Model
{
    use HasFactory,LogsActivity; 
    protected $fillable = ['id','book_name','book_file','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
