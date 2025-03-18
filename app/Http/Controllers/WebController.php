<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Map',
        ];
        return view('layouts.view', $data);
    }
}
