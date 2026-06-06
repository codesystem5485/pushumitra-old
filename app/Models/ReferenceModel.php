<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ReferenceModel extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'references';

    protected $fillable = [
        'name',
        'collaboration_code',
        'institution_code',
        'active',
        'create_by',
        'updated_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];
}
