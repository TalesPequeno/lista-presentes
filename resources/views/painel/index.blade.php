@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Painel Administrativo</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Produtos Cadastrados</h5>
                    <p class="card-text display-4">{{ $totalGifts }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Produtos Reservados</h5>
                    <p class="card-text display-4">{{ $reservedGifts }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
