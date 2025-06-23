<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Ride;
use App\Models\RideStudentLog;
use App\Models\RideLocation;

class ParentController extends Controller
{
    public function getChildren(Request $request)
    {
        $parent = $request->user();

        if ($parent->role !== 'parent') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $children = Student::where('parent_id', $parent->id)->get();

        return response()->json([
            'children' => $children
        ]);
    }

    public function getChildStatus($id)
    {
        $parent = auth()->user();

        // Veli sadece kendi çocuğuna erişebilsin
        $student = Student::where('id', $id)->where('parent_id', $parent->id)->first();

        if (!$student) {
            return response()->json(['message' => 'Child not found or unauthorized'], 404);
        }

        // En son ride’ı bul (aktif varsa onu, yoksa en son completed)
        $ride = Ride::whereHas('vehicle.students', function ($q) use ($student) {
            $q->where('students.id', $student->id);
        })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ride) {
            return response()->json(['message' => 'No ride found for this student']);
        }

        // Pickup & Dropoff loglarını çek
        $pickup = RideStudentLog::where('ride_id', $ride->id)
            ->where('student_id', $student->id)
            ->where('status', 'picked_up')
            ->first();

        $dropoff = RideStudentLog::where('ride_id', $ride->id)
            ->where('student_id', $student->id)
            ->where('status', 'dropped_off')
            ->first();

        // En son konum
        $location = RideLocation::where('ride_id', $ride->id)
            ->latest('sent_at')
            ->first();

        return response()->json([
            'student' => $student->name,
            'ride_status' => $ride->status,
            'picked_up' => $pickup ? true : false,
            'dropped_off' => $dropoff ? true : false,
            'last_location' => $location ? [
                'lat' => $location->lat,
                'lng' => $location->lng,
                'timestamp' => $location->sent_at
            ] : null
        ]);
    }

    public function childRideHistory($id)
    {
        $parent = auth()->user();

        $student = Student::where('id', $id)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Child not found or unauthorized'], 404);
        }

        $rides = Ride::whereHas('vehicle.students', function ($q) use ($student) {
            $q->where('students.id', $student->id);
        })
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['rides' => $rides]);
    }
}
