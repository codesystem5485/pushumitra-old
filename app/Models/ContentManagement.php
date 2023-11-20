<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class ContentManagement extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'front_pages';	
    protected $fillable = ['id','title','page_content','common_content','common_image_1','common_image_2',
	'about_image_1','is_active','created_at','updated_at','deleted_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
