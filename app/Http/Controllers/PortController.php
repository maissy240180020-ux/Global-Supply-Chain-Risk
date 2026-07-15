<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    public function index()
    {
        $ports = DB::table('ports')->get();
        return response()->json($ports);
    }
}
