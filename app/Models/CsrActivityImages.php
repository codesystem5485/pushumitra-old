<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class CsrActivityImages extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'csr_activity_images';
    protected $fillable = ['id','csr_activity_id','image_name','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
