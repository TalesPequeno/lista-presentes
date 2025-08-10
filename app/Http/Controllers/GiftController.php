<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::where('is_reserved', 0)->get();

        return view('gifts.index', compact('gifts'));
    }

    public function admin()
    {
        $gifts = Gift::all();
        return view('gifts.admin', compact('gifts'));
    }

    public function store(Request $request)
    {
        // 1️⃣ Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // 2️⃣ Upload da imagem (se houver)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('gifts', 'public');
        }

        // 3️⃣ Criação do presente
        Gift::create([
            'name' => $request->name,
            'category' => $request->category,
            'image' => $imagePath,
            'is_reserved' => false, // começa como não reservado
        ]);

        // 4️⃣ Redireciona com sucesso
        return redirect()->route('gifts.admin')->with('success', 'Presente cadastrado com sucesso!');
    }

    public function reserve(Request $request)
    {
        Log::info('reserve(): request recebido', [
            'expectsJson' => $request->expectsJson(),
            'ajax'        => $request->ajax(),
            'input'       => $request->all(),
            'ip'          => $request->ip(),
        ]);

        $data = $request->validate([
            'gift_id'        => ['required','integer','exists:gifts,id'],
            'recipient_name' => ['required','string','max:255'],
            'note'           => ['nullable','string','max:1000'],
        ]);

        $updated = Gift::where('id', $data['gift_id'])
            ->where('is_reserved', 0)
            ->update([
                'reserved_by' => $data['recipient_name'],
                'observation' => $data['note'] ?? null,
                'reserved_at' => now(),
                'is_reserved' => 1,
            ]);

        Log::info('reserve(): resultado update', ['gift_id' => $data['gift_id'], 'updated' => $updated]);

        if (! $updated) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Este presente já foi reservado por outra pessoa.'
                ], 409);
            }
            return back()->withErrors('Este presente já foi reservado por outra pessoa.')->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Presente reservado com sucesso!');
    }


}
