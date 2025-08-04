@extends('layouts.app')

@section('title', 'Lista de Presentes')

@section('content')
    <h2>Itens Disponíveis</h2>

    <div class="product-grid">
        @forelse ($gifts as $gift)
            <div class="product-card">
                @if($gift->image)
                    <img src="{{ asset('storage/' . $gift->image) }}" alt="{{ $gift->name }}">
                @else
                    <img src="{{ asset('images/no-image.png') }}" alt="Sem imagem">
                @endif
                <p>{{ $gift->name }}</p>
            </div>
        @empty
            <p>Nenhum presente disponível no momento.</p>
        @endforelse
    </div>
@endsection
