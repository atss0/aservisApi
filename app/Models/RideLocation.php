<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideLocation extends Model
{
    protected $fillable = ['ride_id', 'lat', 'lng', 'sent_at'];

    public function ride() {
        return $this->belongsTo(Ride::class);
    }
}
