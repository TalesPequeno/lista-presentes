@extends('layouts.app')

@section('title', 'Lista de Presentes')

@section('content')
    {{-- Hero / título da página --}}
    <section class="page-hero text-center">
        <div class="container">
            <h1 class="hero-title">Lista de Presentes</h1>
            <p class="hero-subtitle">Obrigado por fazer parte deste momento especial 💖</p>
        </div>
    </section>

    @php
        $categories = collect($gifts)->pluck('category')->filter()->unique()->sort()->values();
    @endphp

    {{-- Filtros (categoria + busca) --}}
    <div class="container pb-3">
      <div class="filter-bar">
        <div class="filter-top">
          <div class="cat-scroller" id="catScroller" role="tablist" aria-label="Categorias">
            <button type="button" class="cat-pill active" data-category="">Todos</button>
            @foreach($categories as $cat)
              <button type="button" class="cat-pill" data-category="{{ $cat }}">{{ $cat }}</button>
            @endforeach
            @if($categories->isEmpty())
              <button type="button" class="cat-pill" data-category="Sem categoria">Sem categoria</button>
            @endif
          </div>

          <div class="search-wrap">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                🔎
              </span>
              <input type="text"
                     id="searchInput"
                     class="form-control border-start-0"
                     placeholder="Buscar presente..."
                     autocomplete="off">
              <button id="clearSearch" class="btn btn-outline-secondary d-none" type="button" title="Limpar">×</button>
            </div>
          </div>
        </div>

        <div class="filter-meta">
          <small id="resultCount" class="text-muted">Exibindo 0 de 0</small>
        </div>
      </div>
    </div>

    {{-- Grid de presentes --}}
    <div class="container pb-5">
        <div id="noResults" class="text-center text-muted py-5 d-none">
            <p class="mb-1 fs-6">Nenhum presente corresponde ao filtro.</p>
            <small>Dica: limpe a busca ou escolha outra categoria ✨</small>
        </div>

        <div class="product-grid" id="productGrid">
            @forelse ($gifts as $gift)
                @php
                    $cat = $gift->category ?: 'Sem categoria';
                @endphp
                <div class="product-card"
                     id="gift-card-{{ $gift->id }}"
                     data-name="{{ $gift->name }}"
                     data-category="{{ $cat }}">
                    <div class="product-media">
                        <img src="{{ $gift->image ? Storage::url($gift->image) : asset('images/no-image.png') }}"
                             alt="{{ $gift->name }}">
                    </div>

                    <div class="product-body">
                        <div class="cat-row">
                          <span class="gift-cat-badge" title="Categoria">{{ $cat }}</span>
                        </div>

                        <h3 class="gift-name" title="{{ $gift->name }}">{{ $gift->name }}</h3>

                        <div class="d-grid card-cta">
                            <button
                                type="button"
                                class="btn btn-gradient btn-lg py-2"
                                data-bs-toggle="modal"
                                data-bs-target="#giftModal"
                                data-gift-id="{{ $gift->id }}"
                                data-gift-name="{{ $gift->name }}"
                            >
                                Presentear
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <p class="mb-1 fs-5">Nenhum presente disponível no momento.</p>
                    <small>Volte mais tarde — estamos atualizando a lista ✨</small>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal de reserva --}}
    <div class="modal fade" id="giftModal" tabindex="-1" aria-labelledby="giftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-elevated">
                <div class="modal-header modal-header-soft">
                    <h5 id="giftModalLabel" class="modal-title">
                        Presentear <span id="modalGiftName" class="text-primary fw-semibold"></span>
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    {{-- ALERTA DE ERROS DINÂMICO --}}
                    <div id="formErrors" class="alert alert-danger d-none"></div>

                    {{-- ACTION FIXO -> posta em /gifts/reserve --}}
                    <form id="giftForm" action="{{ route('gifts.reserve') }}" method="post">
                        @csrf
                        <input type="hidden" name="gift_id" id="gift_id" value="{{ old('gift_id') }}">

                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">Nome do(a) presenteado(a) *</label>
                            <input type="text" class="form-control form-control-lg" id="recipient_name" name="recipient_name"
                                   value="{{ old('recipient_name') }}" required placeholder="Ex.: Ana Clara">
                        </div>

                        <div class="mb-0">
                            <label for="note" class="form-label">Observação (opcional)</label>
                            <textarea class="form-control" id="note" name="note" rows="3"
                                      placeholder="Escreva uma mensagem carinhosa...">{{ old('note') }}</textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button id="confirmBtn" type="submit" class="btn btn-success btn-lg px-4" form="giftForm">
                        <span id="confirmSpinner" class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de agradecimento --}}
    <div class="modal fade" id="thanksModal" tabindex="-1" aria-labelledby="thanksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center modal-elevated">
                <div class="modal-body p-5">
                    <div class="thanks-icon mb-3">🎁</div>
                    <h4 id="thanksModalLabel" class="mb-2">Agradecemos pelo presente! 💕</h4>

                    <p class="mb-2 text-muted">
                        Nós ficamos muito felizes com o seu carinho.
                    </p>

                    <p class="mb-4 text-muted">
                        Registramos a reserva de <strong id="thanksGiftName"></strong>
                        dedicada a <strong id="thanksRecipientName"></strong>.
                    </p>

                    <p class="mb-4 small text-muted">
                        Com carinho,<br>
                        <strong>Lucas &amp; Nathália</strong>
                    </p>

                    <button type="button" class="btn btn-gradient px-4" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const giftModal   = document.getElementById('giftModal');
  const thanksModal = document.getElementById('thanksModal');
  const form        = document.getElementById('giftForm');
  const confirmBt   = document.getElementById('confirmBtn');
  const confirmSp   = document.getElementById('confirmSpinner');
  const errBox      = document.getElementById('formErrors');

  // ---- FILTROS (categoria + busca) ----
  const grid        = document.getElementById('productGrid');
  const cards       = Array.from(grid?.querySelectorAll('.product-card') || []);
  const catPills    = Array.from(document.querySelectorAll('.cat-pill'));
  const searchInput = document.getElementById('searchInput');
  const clearSearch = document.getElementById('clearSearch');
  const resultCount = document.getElementById('resultCount');
  const noResults   = document.getElementById('noResults');

  let selectedCategory = ''; // '' = todos

  const norm = (s) => (s || '')
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g,'')
    .toLowerCase()
    .trim();

  function applyFilters(){
    const q = norm(searchInput?.value || '');
    let shown = 0;

    cards.forEach(card => {
      const name = norm(card.dataset.name);
      const cat  = norm(card.dataset.category);

      const matchesText = !q || name.includes(q);
      const matchesCat  = !selectedCategory || cat === norm(selectedCategory);

      if (matchesText && matchesCat) {
        card.classList.remove('d-none');
        shown++;
      } else {
        card.classList.add('d-none');
      }
    });

    // meta
    if (resultCount) {
      resultCount.textContent = `Exibindo ${shown} de ${cards.length}`;
    }

    // mensagem "sem resultados"
    if (noResults) {
      noResults.classList.toggle('d-none', shown !== 0 || cards.length === 0);
    }

    // botão limpar
    if (clearSearch) {
      clearSearch.classList.toggle('d-none', !(searchInput && searchInput.value));
    }
  }

  // init
  applyFilters();

  // categoria
  catPills.forEach(btn => {
    btn.addEventListener('click', () => {
      catPills.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedCategory = btn.getAttribute('data-category') || '';
      applyFilters();
    });
  });

  // busca live
  searchInput?.addEventListener('input', applyFilters);
  clearSearch?.addEventListener('click', () => {
    searchInput.value = '';
    applyFilters();
    searchInput.focus();
  });

  // ESC para limpar
  searchInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchInput.value = '';
      applyFilters();
    }
  });

  // ---- MODAL DE RESERVA ----
  if (!giftModal || !form) return;

  // Preenche gift_id e nome sempre que abrir o modal
  giftModal.addEventListener('show.bs.modal', function (event) {
    const button   = event.relatedTarget;
    const giftId   = button?.getAttribute('data-gift-id');
    const giftName = button?.getAttribute('data-gift-name');

    document.getElementById('gift_id').value = giftId || '';
    document.getElementById('modalGiftName').textContent = giftName ? `“${giftName}”` : '';

    // limpa campos/erros a cada abertura
    document.getElementById('recipient_name').value = '';
    document.getElementById('note').value = '';
    errBox.classList.add('d-none'); errBox.innerHTML = '';
  });

  // Submit via AJAX
  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    // UI: loading
    confirmBt.disabled = true;
    confirmSp.classList.remove('d-none');

    try {
      const formData  = new FormData(form);
      const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: formData,
        credentials: 'same-origin',
      });

      if (res.status === 419) {
        errBox.textContent = 'Sessão expirada (419). Recarregue a página e tente novamente.';
        errBox.classList.remove('d-none');
        return;
      }

      if (res.status === 422) {
        const data = await res.json();
        const errors = data.errors || {};
        const list = Object.values(errors).flat().map(msg => `<li>${msg}</li>`).join('');
        errBox.innerHTML = `<ul class="mb-0">${list}</ul>`;
        errBox.classList.remove('d-none');
        return;
      }

      if (res.status === 409) {
        const data = await res.json().catch(() => ({}));
        errBox.textContent = (data && data.message) ? data.message : 'Este presente já foi reservado.';
        errBox.classList.remove('d-none');
        return;
      }

      if (!res.ok) {
        errBox.textContent = 'Não foi possível concluir a reserva. Tente novamente.';
        errBox.classList.remove('d-none');
        return;
      }

      // Sucesso
      const reserveInstance = bootstrap.Modal.getInstance(giftModal) || new bootstrap.Modal(giftModal);
      reserveInstance.hide();

      const giftName = document.getElementById('modalGiftName').textContent.replace(/[“”]/g, '').trim();
      const recipient = document.getElementById('recipient_name').value.trim();
      document.getElementById('thanksGiftName').textContent = giftName || 'seu presente';
      document.getElementById('thanksRecipientName').textContent = recipient || 'o(a) presenteado(a)';

      const giftId = document.getElementById('gift_id').value;
      const card = document.getElementById(`gift-card-${giftId}`);
      if (card) {
        card.classList.add('card-hide'); // anima
        setTimeout(() => { card.remove(); applyFilters(); }, 260); // remove e atualiza contagem
      }

      const thanksInstance = new bootstrap.Modal(thanksModal);
      thanksInstance.show();

    } catch (err) {
      errBox.textContent = 'Erro de rede. Verifique sua conexão e tente novamente.';
      errBox.classList.remove('d-none');
    } finally {
      confirmBt.disabled = false;
      confirmSp.classList.add('d-none');
    }
  });
});
</script>
@endpush

@push('styles')
<style>
  :root{
    --rose-50:#fff7f9; --rose-100:#ffeaf0; --rose-200:#ffd3e0; --rose-300:#ffb7cc;
    --rose-400:#ff97b6; --rose-500:#ff7aa3; --rose-600:#f45b8a; --rose-700:#e0467a;
    --ink-700:#1f2937; --ink-600:#374151; --ink-500:#4b5563; --ink-300:#cbd5e1;
    --radius-xl: 1rem; --radius-2xl: 1.25rem;
  }

  body{ --page-bg: radial-gradient(90% 140% at 10% 10%, var(--rose-50), #fff 60%); }
  
  /* Hero */
  .page-hero{
    background: radial-gradient(90% 140% at 10% 10%, var(--rose-50), #fff 60%) ;
    padding: 3rem 0 1rem;
  }
  .hero-title{
    font-family: 'Great Vibes', cursive;
    font-size: clamp(2rem, 4vw + 1rem, 3.25rem);
    line-height: 1.1;
    margin: 0;
  }
  .hero-subtitle{
    color: #6b7280;
    margin-top: .25rem;
  }

  /* Filter bar */
  .filter-bar{
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 1rem;
    padding: .75rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
  }
  .filter-top{
    display: grid; gap: .75rem;
    grid-template-columns: 1fr minmax(220px, 360px);
  }
  @media (max-width: 768px){
    .filter-top{ grid-template-columns: 1fr; }
  }
  .cat-scroller{
    display: flex; gap: .5rem; overflow-x: auto; padding-bottom: .25rem;
    scrollbar-width: thin;
  }
  .cat-pill{
    border: 1px solid #e9eef6;
    background: #fff;
    color: #374151;
    padding: .4rem .7rem;
    border-radius: 999px;
    font-size: .875rem;
    white-space: nowrap;
    transition: background .15s ease, border-color .15s ease, color .15s ease;
  }
  .cat-pill:hover{ background: #f9fafb; }
  .cat-pill.active{
    background: linear-gradient(135deg, var(--rose-500), var(--rose-600));
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 14px rgba(255,122,163,.35);
  }
  .search-wrap .input-group .form-control{ border-left: 0; }
  .filter-meta{ padding: 0 .25rem; margin-top: .25rem; }

  /* Grid */
  .product-grid{
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  }

  /* Cards */
  .product-card{
    border: 1px solid #eef0f3;
    border-radius: var(--radius-2xl);
    background: #fff;
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, opacity .2s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }
  .product-card:hover{
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
    border-color: #e9eef6;
  }
  .product-media{
    position: relative;
    aspect-ratio: 4 / 3;
    background: #fafafa;
  }
  .product-media img{
    width: 100%; height: 100%;
    object-fit: cover; display: block;
  }
  .product-body{ padding: .9rem .9rem 1rem; }
  .gift-name{
    font-size: 1rem;
    font-weight: 600;
    color: var(--ink-700);
    margin: .25rem 0 .8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .gift-cat-badge{
    font-size: .75rem;
    padding: .2rem .5rem;
    border-radius: 999px;
    background: var(--rose-50);
    color: var(--rose-700);
    border: 1px solid #ffd3e0;
    max-width: 100%;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
  }

  /* Botão degradê */
  .btn-gradient{
    background: linear-gradient(135deg, var(--rose-500), var(--rose-600));
    color: #fff;
    border: 0;
    border-radius: .75rem;
    box-shadow: 0 6px 16px rgba(255, 122, 163, .35);
    transition: filter .2s ease, transform .06s ease;
  }
  .btn-gradient:hover{ filter: brightness(1.05); }
  .btn-gradient:active{ transform: translateY(1px); }

  /* Modal */
  .modal-elevated{
    border: 0; border-radius: var(--radius-xl);
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
  }
  .modal-header-soft{
    border: 0;
    padding-bottom: 0;
    background: linear-gradient(180deg, #fff, var(--rose-50));
  }

  /* Remoção suave do card após reservar */
  .card-hide{
    opacity: 0;
    transform: translateY(6px);
    transition: all .25s ease;
  }

  /* Acessibilidade */
  .btn:focus, .form-control:focus, .btn-close:focus{
    box-shadow: 0 0 0 0.25rem rgba(244, 91, 138, .25) !important;
  }

  /* Modal de agradecimento com fundo igual ao da página */
  #thanksModal .modal-content {
    background: var(--bs-body-bg) !important;
    color: var(--bs-body-color);
    border: 1px solid rgba(0,0,0,.05);
  }
  #thanksModal .modal-content.modal-elevated {
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
  }

  /* ====== MOBILE: 2 colunas e cards menores ====== */
@media (max-width: 575.98px){
  /* 2 colunas no mobile */
  .product-grid{
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: .75rem !important;
  }

  /* card mais compacto */
  .product-card{
    border-radius: .75rem;
  }
  .product-media{
    aspect-ratio: 1 / 1;     /* thumb quadrada ocupa melhor o espaço */
  }
  .product-body{
    padding: .6rem .6rem .7rem;
  }

  /* título menor e com quebra em 2 linhas */
  .gift-name{
    font-size: .6rem;
    margin: .35rem 0 .5rem;
    white-space: normal;              /* permite quebrar */
    display: -webkit-box;
    -webkit-line-clamp: 2;            /* até 2 linhas */
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.25;
  }

  /* badge de categoria mais discreta */
  .gift-cat-badge{
    font-size: .4rem;
    padding: .15rem .4rem;
  }

  /* botão mais enxuto */
  .btn-gradient.btn-lg{
    padding: .45rem .6rem;
    font-size: .875rem;
    border-radius: .6rem;
  }
}

</style>
@endpush
