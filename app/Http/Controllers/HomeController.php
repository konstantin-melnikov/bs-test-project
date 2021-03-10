<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Home page with currency list
     */
    public function index()
    {
        return view('homepage');
    }
}
