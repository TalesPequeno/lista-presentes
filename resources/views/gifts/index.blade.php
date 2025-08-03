@extends('layouts.app')

@section('title', 'Lista de Presentes')

@section('content')
    <h2>Itens Disponíveis</h2>

    <div class="product-grid">
        @forelse ($gifts as $gift)
            <div class="product-card">
                <img src="{{ asset($gift->image) }}" alt="{{ $gift->name }}">
                <p>{{ $gift->name }}</p>
            </div>
        @empty
            <p>Nenhum presente disponível no momento.</p>
        @endforelse
    </div>
@endsection
