<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Grfiles extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'gr_files';
    protected $fillable = ['id','title','gr_file','is_active','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
