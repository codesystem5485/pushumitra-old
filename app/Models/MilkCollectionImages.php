<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class MilkCollectionImages extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'milkcollection_center_images';
    protected $fillable = ['id','milkcollection_center_id','image_name','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
