<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate_number',
        'driver_id',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}