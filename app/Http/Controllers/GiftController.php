<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::where('is_reserved', 0)->get();

        return view('gifts.index', compact('gifts'));
    }

    public function admin(Request $request)
    {
        $q        = $request->string('q')->toString();
        $reserved = $request->input('reserved', 'all');

        $gifts = Gift::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function($q2) use ($q){
                    $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->when($reserved !== 'all', fn($query) => $query->where('is_reserved', (int)$reserved))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('gifts.admin', compact('gifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('gifts', 'public');
        }

        Gift::create([
            'name' => $request->name,
            'category' => $request->category,
            'image' => $imagePath,
            'is_reserved' => false,
        ]);

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

    public function edit(Gift $gift)
    {
        return view('gifts.edit', compact('gift'));
    }

    public function update(Request $request, Gift $gift)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:255'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
            'is_reserved'  => ['sometimes', 'boolean'],
            'reserved_by'  => ['required_if:is_reserved,1', 'nullable', 'string', 'max:255'],
            'observation'  => ['nullable', 'string', 'max:1000'],
        ]);

        $wasReserved  = (bool) $gift->is_reserved;
        $makeReserved = $request->boolean('is_reserved');

        if ($request->boolean('remove_image') || $request->hasFile('image')) {
            if ($gift->image && Storage::disk('public')->exists($gift->image)) {
                Storage::disk('public')->delete($gift->image);
            }
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('gifts', 'public');
                $gift->image = $path;
            } else {
                $gift->image = null;
            }
        }

        $gift->name     = $data['name'];
        $gift->category = $data['category'] ?? null;

        if ($makeReserved) {
            $gift->is_reserved = true;
            $gift->reserved_by = $data['reserved_by'] ?? $gift->reserved_by;
            $gift->observation = $data['observation'] ?? null;

            if (!$wasReserved || !$gift->reserved_at) {
                $gift->reserved_at = now();
            }
        } else {
            $gift->is_reserved = false;
            $gift->reserved_by = null;
            $gift->observation = null;
            $gift->reserved_at = null;
        }

        $gift->save();

        return redirect()
            ->route('gifts.admin')
            ->with('success', 'Presente atualizado com sucesso!');
    }
    public function destroy($id)
    {
        $gift = Gift::findOrFail($id);
        $gift->delete();

        return redirect()
            ->route('gifts.admin')
            ->with('success', 'Gift excluído com sucesso.');
    }
}