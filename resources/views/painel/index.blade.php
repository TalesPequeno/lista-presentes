@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Painel Administrativo</h1>

    <div class="row">
        {{-- Card de Produtos Cadastrados --}}
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Produtos Cadastrados</h5>
                    <p class="card-text display-4 mb-4">{{ $totalGifts }}</p>
                    <a href="{{ route('gifts.admin') }}" class="btn btn-light">
                        Ver Lista
                    </a>
                </div>
            </div>
        </div>

        {{-- Card de Produtos Reservados --}}
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Produtos Reservados</h5>
                    <p class="card-text display-4 mb-4">{{ $reservedGifts }}</p>
                    <a href="{{ route('gifts.admin') }}" class="btn btn-light">
                        Ver Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
