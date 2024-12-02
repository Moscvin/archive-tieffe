<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\CoreUsers as User;

class CoordinateHistory extends Model
{
    protected $table = 'coordinates_history';
    protected $fillable = [
        'user_id',
        'lat',
        'lng'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
