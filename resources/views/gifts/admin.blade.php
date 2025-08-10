@extends('layouts.app')

@section('title', 'Lista de Presentes (Admin)')

@section('content')
<div class="container py-4">

    {{-- Header + ação rápida --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="mb-1 fw-semibold">Lista de Presentes</h1>
            <small class="text-muted">Gerencie os presentes, filtre por status e cadastre novos itens</small>
        </div>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createGiftModal">
            <i class="bi bi-plus-lg me-1"></i> Novo Presente
        </button>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-3 rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('gifts.admin') }}" class="row g-2 align-items-end" id="filtersForm">
                <div class="col-md-6">
                    <label for="q" class="form-label mb-1">Buscar por nome</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" id="q" class="form-control" placeholder="Ex.: Taças, Jogo de lençol..."
                               value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="reserved" class="form-label mb-1">Reservado?</label>
                    @php $reserved = request('reserved', 'all'); @endphp
                    <select name="reserved" id="reserved" class="form-select">
                        <option value="all" {{ $reserved==='all' ? 'selected' : '' }}>Todos</option>
                        <option value="1"   {{ $reserved==='1'   ? 'selected' : '' }}>Sim</option>
                        <option value="0"   {{ $reserved==='0'   ? 'selected' : '' }}>Não</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['q','reserved']) && (request('q')!==null || request('reserved')!=='all'))
                        <a href="{{ route('gifts.admin') }}" class="btn btn-outline-secondary">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela (lista clássica e compacta) --}}
    <div class="card shadow border-0 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-compact">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th style="width:100px">Reservado?</th>
                        <th style="width:100px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($gifts as $gift)
                    <tr>
                        <td class="fw-semibold name-col">
                        <span class="name-text">{{ $gift->name }}</span>
                        </td>

                        <td>
                            @if($gift->is_reserved)
                                <span class="badge badge-compact bg-success-subtle text-success-emphasis border border-success-subtle">
                                    <i class="bi bi-check2-circle me-1"></i> Sim
                                </span>
                            @else
                                <span class="badge badge-compact bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                    <i class="bi bi-circle me-1"></i> Não
                                </span>
                            @endif
                        </td>

   
                        <td>
                            <div class="d-flex gap-2">
                                {{-- Editar --}}
                                <a href="{{ route('gifts.edit', $gift) }}"
                                   class="btn btn-outline-primary btn-compact"
                                   title="Editar">
                                   <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- Detalhes --}}
                                <button type="button"
                                        class="btn btn-outline-secondary btn-compact"
                                        data-bs-toggle="modal"
                                        data-bs-target="#giftDetailsModal"
                                        title="Detalhes"
                                        data-id="{{ $gift->id }}"
                                        data-image="{{ $gift->image ? Storage::url($gift->image) : asset('images/no-image.png') }}"
                                        data-name="{{ $gift->name }}"
                                        data-category="{{ $gift->category ?? '-' }}"
                                        data-reserved="{{ $gift->is_reserved ? 'Sim' : 'Não' }}"
                                        data-reservedby="{{ $gift->is_reserved ? ($gift->reserved_by ?? '—') : '—' }}"
                                        data-observation="{{ $gift->is_reserved && $gift->observation ? e($gift->observation) : '—' }}"
                                        data-reservedat="{{ $gift->is_reserved && $gift->reserved_at ? $gift->reserved_at->format('d/m/Y H:i') : '—' }}"
                                >
                                    <i class="bi bi-info-circle"></i>
                                </button>

                                {{-- Excluir --}}
                                <form action="{{ route('gifts.destroy', $gift->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-compact"
                                            onclick="return confirm('Tem certeza que deseja excluir este presente?')"
                                            title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Nenhum presente encontrado com os filtros atuais.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação (se houver) --}}
        @if(method_exists($gifts, 'links'))
            <div class="card-footer bg-white border-0">
                {{ $gifts->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal de criação de presente --}}
<div class="modal fade" id="createGiftModal" tabindex="-1" aria-labelledby="createGiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="{{ route('gifts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="createGiftModalLabel">
                        <i class="bi bi-gift me-2"></i> Cadastrar Novo Presente
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Nome do Presente *</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg" required placeholder="Ex.: Conjunto de Panelas">
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label">Categoria</label>
                            <input type="text" name="category" id="category" class="form-control" placeholder="Ex.: Cozinha">
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label">Imagem</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <div id="imagePreview" class="rounded border d-none p-2">
                                <small class="text-muted d-block mb-1">Pré-visualização:</small>
                                <img id="previewImg" src="#" alt="Pré-visualização" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2 me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detalhes --}}
<div class="modal fade" id="giftDetailsModal" tabindex="-1" aria-labelledby="giftDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="giftDetailsModalLabel">
                    <i class="bi bi-info-circle me-2"></i> Detalhes do Presente
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 align-items-start">
                    <div class="col-md-4">
                        <div class="ratio ratio-4x3 rounded border overflow-hidden">
                            <img id="d_image" src="" alt="Imagem do presente" class="object-fit-cover">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">ID</small>
                                <div id="d_id" class="fw-semibold">—</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Categoria</small>
                                <div id="d_category" class="fw-semibold">—</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Nome</small>
                                <div id="d_name" class="fw-semibold">—</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Reservado?</small>
                                <div id="d_reserved" class="fw-semibold">—</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Reservado por</small>
                                <div id="d_reservedby" class="fw-semibold">—</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Observação</small>
                                <div id="d_observation" class="fw-semibold">—</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Data da reserva</small>
                                <div id="d_reservedat" class="fw-semibold">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Filtro "Reservado?" auto-submit
    const reservedSelect = document.getElementById('reserved');
    reservedSelect?.addEventListener('change', () => document.getElementById('filtersForm').submit());

    // Enter no campo de busca
    const qInput = document.getElementById('q');
    qInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filtersForm').submit();
        }
    });

    // Pré-visualização no modal de criação
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

    // Preenche o modal de detalhes
    const detailsModal = document.getElementById('giftDetailsModal');
    detailsModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;

        const get = (attr) => btn.getAttribute(attr) || '—';

        document.getElementById('d_image').src         = get('data-image');
        document.getElementById('d_id').textContent    = '#' + get('data-id');
        document.getElementById('d_name').textContent  = get('data-name');
        document.getElementById('d_category').textContent   = get('data-category');
        document.getElementById('d_reserved').textContent   = get('data-reserved');
        document.getElementById('d_reservedby').textContent = get('data-reservedby');
        document.getElementById('d_observation').textContent = get('data-observation');
        document.getElementById('d_reservedat').textContent  = get('data-reservedat');
    });
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .object-fit-cover{ object-fit: cover; }

  /* ========= TABELA COMPACTA ========= */
  .table-compact{
    font-size: .75rem;              /* ~12px geral */
  }
  @media (min-width: 992px){
    .table-compact{ font-size: .8125rem; } /* ~13px em telas grandes, melhora legibilidade */
  }
  .table-compact thead th{
    font-size: .72rem;              /* cabeçalho ainda menor */
    letter-spacing: .2px;
  }
  .table-compact td, .table-compact th{
    padding: .35rem .5rem;          /* menos espaço vertical/horizontal */
    vertical-align: middle;
    white-space: nowrap;
  }

  /* miniatura menor */
  .thumb-xs{
    width: 40px; height: 40px;
    border-radius: .5rem; overflow: hidden;
  }

  /* badge e botões menores */
  .badge-compact{
    font-size: .68rem;
    padding: .3em .5em;
    border-radius: .65rem;
    line-height: 1.1;
  }
  .btn-compact{
    --bs-btn-padding-y: .125rem;
    --bs-btn-padding-x: .45rem;
    --bs-btn-font-size: .75rem;     /* ~12px */
    --bs-btn-border-radius: .4rem;
    line-height: 1.2;
  }

  /* desktop mantém compacto normal */
    .table-compact .name-col { max-width: 420px; }

    /* mobile: quebra após ~15 caracteres por linha */
    @media (max-width: 575.98px){
    .table-compact .name-col .name-text{
        display: inline-block;
        max-width: 20ch;            /* ~15 letras por linha */
        white-space: normal;        /* permite quebrar */
    }
    }
</style>
@endpush
