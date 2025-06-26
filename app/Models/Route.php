<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['name', 'description'];

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
}
