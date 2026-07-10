<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NilaiTukarController extends Controller
{
    public function index()
    {
        return view('currency.index');
    }
}