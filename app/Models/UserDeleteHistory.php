<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Http\Request;

class UserDeleteHistory extends Model
{
    use HasFactory,LogsActivity;
	protected $table = 'user_delete_history';
    protected $fillable = ['id','deleted_user_id','replace_user_id','created_at','updated_at'];
     
	/*protected $casts = [
        'attributes' => 'json',
    ];	 */
    public static function boot()
    {
        parent::boot();
    }

}
