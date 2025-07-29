<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index()
    {
        return view('gifts.index');
    }
}
