@extends('layouts.app')

@section('title', 'Editar Presente')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="mb-1 fw-semibold">Editar Presente</h1>
            <small class="text-muted">Atualize as informações do presente</small>
        </div>
        <a href="{{ route('gifts.admin') }}" class="btn btn-outline-secondary">
            Voltar para a lista
        </a>
    </div>

    {{-- Alertas de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os campos abaixo:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="editGiftForm" action="{{ route('gifts.update', $gift) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Coluna esquerda: dados principais --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Presente *</label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $gift->name) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Categoria</label>
                            <input type="text"
                                   class="form-control"
                                   id="category"
                                   name="category"
                                   value="{{ old('category', $gift->category) }}"
                                   placeholder="Ex.: Cozinha">
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Reservado?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_reserved" name="is_reserved" value="1"
                                       {{ old('is_reserved', $gift->is_reserved) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_reserved">
                                    Marcar como reservado
                                </label>
                            </div>
                            @if($gift->reserved_at)
                                <small class="text-muted ms-1">Reservado em:
                                    {{ $gift->reserved_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>

                        <div id="reservedFields" class="{{ old('is_reserved', $gift->is_reserved) ? '' : 'd-none' }}">
                            <div class="mb-3">
                                <label for="reserved_by" class="form-label">Reservado por</label>
                                <input type="text"
                                       class="form-control"
                                       id="reserved_by"
                                       name="reserved_by"
                                       value="{{ old('reserved_by', $gift->reserved_by) }}"
                                       placeholder="Nome de quem reservou">
                            </div>
                            <div class="mb-0">
                                <label for="observation" class="form-label">Observação</label>
                                <textarea class="form-control"
                                          id="observation"
                                          name="observation"
                                          rows="3"
                                          placeholder="Mensagem/observação opcional">{{ old('observation', $gift->observation) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna direita: imagem --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <label class="form-label">Imagem atual</label>
                        <div class="ratio ratio-4x3 rounded border overflow-hidden mb-3">
                            @if ($gift->image)
                                <img id="currentImg" src="{{ Storage::url($gift->image) }}" class="object-fit-cover" alt="Imagem atual">
                            @else
                                <img id="currentImg" src="{{ asset('images/no-image.png') }}" class="object-fit-cover" alt="Sem imagem">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Trocar imagem</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Se enviar uma nova, substituirá a atual.</small>
                        </div>

                        <div id="imagePreview" class="d-none">
                            <small class="text-muted d-block mb-1">Pré-visualização:</small>
                            <div class="ratio ratio-4x3 rounded border overflow-hidden mb-3">
                                <img id="previewImg" src="#" class="object-fit-cover" alt="Pré-visualização">
                            </div>
                        </div>

                        @if ($gift->image)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                                <label class="form-check-label" for="remove_image">
                                    Remover imagem atual
                                </label>
                            </div>
                            <small class="text-muted d-block">Se marcar “remover” e não enviar outra, o presente ficará sem imagem.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('gifts.admin') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2 me-1"></i> Salvar alterações
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Toggle campos de reserva
    const isReserved = document.getElementById('is_reserved');
    const reservedFields = document.getElementById('reservedFields');
    const reservedBy = document.getElementById('reserved_by');

    const toggleReserved = () => {
        if (isReserved.checked) {
            reservedFields.classList.remove('d-none');
        } else {
            reservedFields.classList.add('d-none');
        }
    };
    isReserved.addEventListener('change', toggleReserved);

    // Preview de nova imagem
    const inputFile = document.getElementById('image');
    const previewWrap = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    inputFile?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) { previewWrap.classList.add('d-none'); return; }
        const reader = new FileReader();
        reader.onload = (ev) => {
            previewImg.src = ev.target.result;
            previewWrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .object-fit-cover{ object-fit: cover; }
    .ratio-4x3{ aspect-ratio: 4 / 3; }
    .rounded-4{ border-radius: 1rem; }
</style>
@endpush
