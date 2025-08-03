@extends('layouts.app')

@section('title', 'Lista de Presentes')

@section('content')
        <h2>Itens Disponíveis</h2>

        <div class="product-grid">
            <!-- Produto 1 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/panela-eletrica.webp') }}" alt="Panela Elétrica">
                <p>Panela Elétrica</p>
            </div>
            <!-- Produto 2 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/jogo-de-cama.webp') }}" alt="Jogo de Cama">
                <p>Jogo de Cama Casal</p>
            </div>
            <!-- Produto 3 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/liquidificador.jpg') }}" alt="Liquidificador">
                <p>Liquidificador Turbo</p>
            </div>
            <!-- Produto 4 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/aparelho-de-jantar.webp') }}" alt="Aparelho de Jantar">
                <p>Aparelho de Jantar 16 Peças</p>
            </div>
            <!-- Produto 5 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/panela-eletrica.webp') }}" alt="Panela Multiuso">
                <p>Panela Multiuso</p>
            </div>
            <!-- Produto 6 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/jogo-de-cama.webp') }}" alt="Jogo de Cama Queen">
                <p>Jogo de Cama Queen</p>
            </div>
            <!-- Produto 7 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/liquidificador.jpg') }}" alt="Liquidificador Inox">
                <p>Liquidificador Inox</p>
            </div>
            <!-- Produto 8 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/aparelho-de-jantar.webp') }}" alt="Aparelho de Jantar Floral">
                <p>Aparelho de Jantar Floral</p>
            </div>
            <!-- Produto 9 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/panela-eletrica.webp') }}" alt="Panela Digital">
                <p>Panela Digital</p>
            </div>
            <!-- Produto 10 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/jogo-de-cama.webp') }}" alt="Jogo de Cama Solteiro">
                <p>Jogo de Cama Solteiro</p>
            </div>
            <!-- Produto 11 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/liquidificador.jpg') }}" alt="Liquidificador Compacto">
                <p>Liquidificador Compacto</p>
            </div>
            <!-- Produto 12 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/aparelho-de-jantar.webp') }}" alt="Aparelho de Jantar Moderno">
                <p>Aparelho de Jantar Moderno</p>
            </div>
            <!-- Produto 13 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/panela-eletrica.webp') }}" alt="Panela Rápida">
                <p>Panela Rápida</p>
            </div>
            <!-- Produto 14 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/jogo-de-cama.webp') }}" alt="Jogo de Cama Luxo">
                <p>Jogo de Cama Luxo</p>
            </div>
            <!-- Produto 15 -->
            <div class="product-card">
                <img src="{{ asset('assets/images/liquidificador.jpg') }}" alt="Liquidificador 5 Velocidades">
                <p>Liquidificador 5 Velocidades</p>
            </div>
        </div>
@endsection