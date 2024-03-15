<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class UserModuleCounts extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'modules_users_counts';
    protected $fillable = ['id','user_id','module','module_counts','created_at','updated_at'];
     
	/*protected $casts = [
        'attributes' => 'json',
    ];	 */
    public static function boot()
    {
        parent::boot();
    }

}
