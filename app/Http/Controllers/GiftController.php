<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gift;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::where('is_reserved', 0)->get();

        return view('gifts.index', compact('gifts'));
    }
}
