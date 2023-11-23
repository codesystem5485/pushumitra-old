<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class Fee extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'fee_structure';
    protected $fillable = ['id','name','fee','status','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
