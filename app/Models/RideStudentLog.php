<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideStudentLog extends Model
{
    protected $fillable = ['ride_id', 'student_id', 'status', 'timestamp'];

    public function ride() {
        return $this->belongsTo(Ride::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
