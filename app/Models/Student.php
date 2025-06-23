<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'grade',
        'school',
        'pickup_lat',
        'pickup_lng',
        'pickup_time',
        'dropoff_lat',
        'dropoff_lng',
        'dropoff_time',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class);
    }
}