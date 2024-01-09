<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class TransporterRcbookImages extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'transporter_rcbook_images';
    protected $fillable = ['id','transporter_id','image_name','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
