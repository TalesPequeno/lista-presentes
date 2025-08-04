<?php

namespace App\Http\Controllers;

use App\Models\Gift;

class PainelController extends Controller
{
    public function index()
    {
        $totalGifts = Gift::count(); // total cadastrados
        $reservedGifts = Gift::where('is_reserved', true)->count();

        return view('painel.index', compact('totalGifts', 'reservedGifts'));
    }
}
