<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gift;
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

        foreach ($gifts as $gift) {
            if ($gift->image) {
                dump([
                    'id' => $gift->id,
                    'image_db' => $gift->image, // Caminho salvo no banco
                    'storage_url' => Storage::url($gift->image), // URL que a view vai usar
                    'full_path' => public_path('storage/'.$gift->image), // Caminho físico do arquivo
                    'exists' => file_exists(public_path('storage/'.$gift->image)), // Se o arquivo existe
                ]);
            }
        }

        dd('Fim do debug');

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
}
