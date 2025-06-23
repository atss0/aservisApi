<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;
use App\Models\RideLocation;

class RideLocationController extends Controller
{
    public function sendLocation(Request $request, $ride_id)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Sadece kendi aktif ride'ına konum gönderebilir
        $ride = Ride::where('id', $ride_id)
            ->where('driver_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$ride) {
            return response()->json(['message' => 'Active ride not found.'], 404);
        }

        $location = RideLocation::create([
            'ride_id' => $ride->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'sent_at' => now()
        ]);

        return response()->json(['message' => 'Location stored', 'location' => $location]);
    }
}
