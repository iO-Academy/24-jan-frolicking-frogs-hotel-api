<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    public $hidden = ['pivot', 'created_at', 'updated_at'];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }
}
