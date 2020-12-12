<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Create Index Main Page Dashboard
    public function index()
    {
        return view('dashboard');
    }
}
