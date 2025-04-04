<?php

namespace App\Http\Controllers;

use App\Models\Cinema;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::all();
        return view('cinemas.index', compact('cinemas'));
    }
}

