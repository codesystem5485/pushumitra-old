<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class CsrActivityCategories extends Model
{
    use HasFactory,LogsActivity; 
	protected $table = 'csr_activity_categories';
    protected $fillable = ['id','name','created_at','updated_at'];
        
    public static  function boot()
    {
        parent::boot();
    }

}
