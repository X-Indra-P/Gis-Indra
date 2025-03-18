<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    // Menyimpan marker ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $marker = Marker::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Marker berhasil disimpan!', 'marker' => $marker], 201);
    }

    // Mengambil semua marker dari database
    public function index()
    {
        return response()->json(Marker::orderBy('id', 'desc')->get());
    }
}