@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('content')
@php
    $availableGifts   = max(0, ($totalGifts ?? 0) - ($reservedGifts ?? 0));
    $percentReserved  = ($totalGifts ?? 0) > 0 ? round(($reservedGifts / $totalGifts) * 100) : 0;
    $percentAvailable = ($totalGifts ?? 0) > 0 ? 100 - $percentReserved : 0;
@endphp

<div class="container py-4 admin-dashboard">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="mb-1 fw-semibold">Painel Administrativo</h1>
            <p class="text-muted mb-0">Visão geral dos presentes cadastrados e reservas</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('gifts.admin') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-task me-1"></i> Ver lista
            </a>
            <!-- <a href="{{ route('gifts.create') }}" class="btn btn-gradient">
                <i class="bi bi-plus-lg me-1"></i> Novo presente
            </a> -->
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card gradient-primary">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Produtos cadastrados</span>
                    <span class="stat-value">{{ $totalGifts }}</span>
                </div>
                <a href="{{ route('gifts.admin') }}" class="stretched-link" aria-label="Ver produtos cadastrados"></a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card gradient-success">
                <div class="stat-icon">
                    <i class="bi bi-gift"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Produtos reservados</span>
                    <span class="stat-value">{{ $reservedGifts }}</span>
                </div>
                <a href="{{ route('gifts.admin') }}" class="stretched-link" aria-label="Ver produtos reservados"></a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card gradient-neutral">
                <div class="stat-icon">
                    <i class="bi bi-bag"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Disponíveis</span>
                    <span class="stat-value">{{ $availableGifts }}</span>
                </div>
                <a href="{{ route('gifts.admin') }}" class="stretched-link" aria-label="Ver produtos disponíveis"></a>
            </div>
        </div>
    </div>

    {{-- Progresso de reservas --}}
    <div class="card mt-4 shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Taxa de reservas</h5>
                    <small class="text-muted">Percentual de itens reservados em relação ao total</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle me-1">
                        Reservados: {{ $percentReserved }}%
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                        Disponíveis: {{ $percentAvailable }}%
                    </span>
                </div>
            </div>

            <div class="progress progress-thick mb-2" role="progressbar" aria-label="Progresso de reservas"
                 aria-valuenow="{{ $percentReserved }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: {{ $percentReserved }}%"></div>
            </div>

            <div class="d-flex justify-content-between small text-muted">
                <span>{{ $reservedGifts }} reservados</span>
                <span>{{ $totalGifts }} no total</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Bootstrap Icons (para os ícones do dashboard) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root{
        --ink-700:#1f2937; --ink-600:#374151; --ink-500:#4b5563;
        --primary-500:#6366f1; --primary-600:#4f46e5; --primary-100:#eef2ff;
        --success-500:#22c55e; --success-600:#16a34a; --success-100:#ecfdf5;
        --neutral-100:#f8fafc;
    }

    .admin-dashboard h1{ letter-spacing:.2px }

    /* Botão degradê reaproveitado */
    .btn-gradient{
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
        color: #fff; border: 0; border-radius: .75rem;
        box-shadow: 0 6px 16px rgba(99,102,241,.35);
        transition: filter .2s ease, transform .06s ease;
    }
    .btn-gradient:hover{ filter: brightness(1.06); }
    .btn-gradient:active{ transform: translateY(1px); }

    /* Cards de estatística */
    .stat-card{
        position: relative;
        border: 0; border-radius: 1rem;
        padding: 1.25rem 1.25rem;
        color: #0f172a;
        box-shadow: 0 8px 24px rgba(2,6,23,.06);
        overflow: hidden;
        display: flex; align-items: center; gap: 1rem;
        background: #fff;
    }
    .stat-card .stat-icon{
        width: 56px; height: 56px; border-radius: 14px;
        display: grid; place-items: center;
        font-size: 28px; color: #fff;
        flex: 0 0 56px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.25);
    }
    .stat-card .stat-content{ display:flex; flex-direction:column; }
    .stat-label{ font-size:.9rem; color:#475569; }
    .stat-value{ font-size: clamp(1.4rem, 1.1rem + 1.5vw, 2rem); font-weight: 700; color:#0f172a; }

    .gradient-primary{ background: linear-gradient(180deg, var(--primary-100), #fff); }
    .gradient-primary .stat-icon{ background: linear-gradient(135deg, var(--primary-500), var(--primary-600)); }

    .gradient-success{ background: linear-gradient(180deg, var(--success-100), #fff); }
    .gradient-success .stat-icon{ background: linear-gradient(135deg, var(--success-500), var(--success-600)); }

    .gradient-neutral{ background: linear-gradient(180deg, var(--neutral-100), #fff); }
    .gradient-neutral .stat-icon{ background: linear-gradient(135deg, #94a3b8, #64748b); }

    .stretched-link::after{ border-radius: 1rem; }

    /* Barra de progresso mais "forte" */
    .progress-thick{
        height: 12px;
        background-color: #eef2f7;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-thick .progress-bar{
        background: linear-gradient(90deg, var(--success-500), var(--success-600));
    }

    /* Acessibilidade foco */
    .btn:focus, .btn:focus-visible{
        box-shadow: 0 0 0 .25rem rgba(99,102,241,.2) !important;
    }
</style>
@endpush
