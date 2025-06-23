<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('driver')->get();
        return response()->json($vehicles);
    }

    public function driverVehicles(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Only drivers can access their vehicles.'], 403);
        }

        $vehicles = Vehicle::where('driver_id', $user->id)->get();
        return response()->json($vehicles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|unique:vehicles',
            'driver_id' => 'required|exists:users,id',
        ]);

        $vehicle = Vehicle::create($request->only('plate_number', 'driver_id'));

        return response()->json(['message' => 'Vehicle created.', 'vehicle' => $vehicle], 201);
    }

    // Öğrenci ekle (ara tabloya)
    public function assignStudents(Request $request, $vehicle_id)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->students()->syncWithoutDetaching($request->student_ids);

        return response()->json(['message' => 'Students assigned to vehicle.']);
    }
}
