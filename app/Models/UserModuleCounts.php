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
    protected $fillable = ['id','user_id','module','module_counts','breeders','ngo','veterinary_hospitals',
	'chemists','dog_shelters','easy_cares','farms','institutions','labs','milkcollection_centers','panjarpol',
	'poultryhatchery_centers','product_for_sales','shops','suppliers','training_centers','transporters',
	'animal_for_sales','pashumitra_registrations','registered_vet_registrations','created_at','updated_at'];
     
	/*protected $casts = [
        'attributes' => 'json',
    ];	 */
    public static function boot()
    {
        parent::boot();
    }

}
