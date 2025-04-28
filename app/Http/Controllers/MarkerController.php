<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    /**
     * Menampilkan semua marker dari database
     */
    public function getMarkers()
    {
        $markers = Marker::all(); // Mengambil semua marker
        
        // Pastikan semua nilai latitude dan longitude diubah ke float
        $markers = $markers->map(function($marker) {
            return [
                'id' => $marker->id,
                'name' => $marker->name,
                'latitude' => (float) $marker->latitude,
                'longitude' => (float) $marker->longitude,
                'created_at' => $marker->created_at,
                'updated_at' => $marker->updated_at
            ];
        });
        
        return response()->json($markers);
    }

    /**
     * Menyimpan marker baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $marker = Marker::create([
            'name' => $request->name,
            'latitude' => (float) $request->latitude,
            'longitude' => (float) $request->longitude,
        ]);

        return response()->json(['message' => 'Marker berhasil disimpan!', 'marker' => $marker], 201);
    }
}