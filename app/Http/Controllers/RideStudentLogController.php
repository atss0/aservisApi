<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RideStudentLog;
use App\Models\Ride;

class RideStudentLogController extends Controller
{
    public function pickupStudent($ride_id, $student_id)
    {
        return $this->logStudentStatus($ride_id, $student_id, 'picked_up');
    }

    public function dropoffStudent($ride_id, $student_id)
    {
        return $this->logStudentStatus($ride_id, $student_id, 'dropped_off');
    }

    private function logStudentStatus($ride_id, $student_id, $status)
    {
        $user = auth()->user();

        $ride = Ride::where('id', $ride_id)
            ->where('driver_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$ride) {
            return response()->json(['message' => 'Active ride not found.'], 404);
        }

        // ❗ Aynı işlem zaten yapılmış mı kontrolü
        $exists = RideStudentLog::where('ride_id', $ride_id)
            ->where('student_id', $student_id)
            ->where('status', $status)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "Student already marked as {$status}."
            ], 409);
        }

        $log = RideStudentLog::create([
            'ride_id' => $ride_id,
            'student_id' => $student_id,
            'status' => $status,
            'timestamp' => now()
        ]);

        return response()->json([
            'message' => "Student " . ($status === 'picked_up' ? 'picked up' : 'dropped off'),
            'log' => $log
        ]);
    }
}
