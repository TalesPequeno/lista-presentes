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

    {{-- Grid de presentes --}}
    <div class="container pb-5">
        <div class="product-grid">
            @forelse ($gifts as $gift)
                <div class="product-card" id="gift-card-{{ $gift->id }}">
                    <div class="product-media">
                        <img src="{{ $gift->image ? Storage::url($gift->image) : asset('images/no-image.png') }}"
                             alt="{{ $gift->name }}">
                    </div>

                    <div class="product-body">
                        <h3 class="gift-name" title="{{ $gift->name }}">{{ $gift->name }}</h3>

                        <div class="d-grid">
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
                    <strong>Lucas &amp; Nathália</strong> {{-- troque pelos nomes do casal, se quiser --}}
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
      if (card) card.classList.add('card-hide'); // anima e remove
      setTimeout(() => { if (card) card.remove(); }, 260);

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
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
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
  .product-body{
    padding: .9rem .9rem 1rem;
  }
  .gift-name{
    font-size: 1rem;
    font-weight: 600;
    color: var(--ink-700);
    margin: .25rem 0 .8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

  /* Ajustes acessibilidade */
  .btn:focus, .form-control:focus, .btn-close:focus{
    box-shadow: 0 0 0 0.25rem rgba(244, 91, 138, .25) !important;
  }

  /* Modal de agradecimento com fundo igual ao da página */
    #thanksModal .modal-content {
    background: var(--bs-body-bg) !important;  /* usa a mesma cor/fundo do body do Bootstrap */
    color: var(--bs-body-color);
    border: 1px solid rgba(0,0,0,.05);
    }

    /* mantém o mesmo estilo de borda/sombra que você já tinha */
    #thanksModal .modal-content.modal-elevated {
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
    }

</style>
@endpush
