<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ride;
use App\Models\Vehicle;
use App\Models\Student;

class RideController extends Controller
{
    public function startRide(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Only drivers can start rides.'], 403);
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'route_id' => 'required|exists:routes,id'
        ]);

        $existing = Ride::where('driver_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Active ride already exists.'], 400);
        }

        $ride = Ride::create([
            'driver_id' => $user->id,
            'vehicle_id' => $request->vehicle_id,
            'route_id' => $request->route_id,
            'started_at' => now(),
            'status' => 'active'
        ]);

        return response()->json(['message' => 'Ride started.', 'ride' => $ride]);
    }

    public function endRide($id)
    {
        $user = Auth::user();

        $ride = Ride::where('id', $id)->where('driver_id', $user->id)->where('status', 'active')->first();

        if (!$ride) {
            return response()->json(['message' => 'Active ride not found.'], 404);
        }

        $ride->update([
            'ended_at' => now(),
            'status' => 'completed'
        ]);

        return response()->json(['message' => 'Ride completed.', 'ride' => $ride]);
    }

    public function getDriverStudents()
    {
        $user = auth()->user();

        $vehicle = Vehicle::where('driver_id', $user->id)->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found for driver'], 404);
        }

        $students = $vehicle->students()->with(['vehicles', 'parent'])->get();

        return response()->json(['students' => $students]);
    }

    public function driverRideHistory()
    {
        $user = auth()->user();

        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rides = Ride::with('vehicle')
            ->where('driver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['rides' => $rides]);
    }
}
