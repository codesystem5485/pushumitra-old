<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReference extends Model
{
    use HasFactory;

    protected $table = 'user_references';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'referenceid',
        'refereddate',
    ];

    public function reference()
    {
        return $this->belongsTo(ReferenceModel::class, 'referenceid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
