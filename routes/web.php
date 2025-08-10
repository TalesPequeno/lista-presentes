<?php

use App\Http\Controllers\GiftController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelController;

// Página inicial com a lista de presentes (pública, sem login)
Route::get('/', [GiftController::class, 'index'])->name('gifts.index');

// Enviar reserva de presente (pública) — recebe gift_id no corpo do POST
Route::post('/gifts/reserve', [GiftController::class, 'reserve'])->name('gifts.reserve');

// Dashboard (somente para admin logado)
Route::get('/painel', [PainelController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('painel');

// Área de administração protegida por login
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/presentes', [GiftController::class, 'admin'])->name('gifts.admin');
    Route::get('/admin/presentes/novo', [GiftController::class, 'create'])->name('gifts.create');
    Route::post('/admin/presentes', [GiftController::class, 'store'])->name('gifts.store');
    Route::delete('/admin/presentes/{gift}', [GiftController::class, 'destroy'])->name('gifts.destroy');

    // Rotas padrão do Breeze para o perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
