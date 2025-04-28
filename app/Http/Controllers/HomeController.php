<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     * Semua method dalam controller ini hanya bisa diakses oleh user yang sudah login.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman dashboard utama.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Tampilkan halaman marker.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function marker()
    {
        return view('layouts.marker');
    }
}