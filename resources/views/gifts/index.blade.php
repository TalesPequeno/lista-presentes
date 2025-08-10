@extends('layouts.app')

@section('title', 'Lista de Presentes')

@section('content')
    <h2>Itens Disponíveis</h2>

    <div class="product-grid">
        @forelse ($gifts as $gift)
            <div class="product-card" id="gift-card-{{ $gift->id }}">
                <img src="{{ $gift->image ? Storage::url($gift->image) : asset('images/no-image.png') }}"
                     alt="{{ $gift->name }}">
                <p class="mb-2">{{ $gift->name }}</p>

                {{-- Botão que abre o modal e passa dados via data-* --}}
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#giftModal"
                    data-gift-id="{{ $gift->id }}"
                    data-gift-name="{{ $gift->name }}"
                >
                    Presentear
                </button>
            </div>
        @empty
            <p>Nenhum presente disponível no momento.</p>
        @endforelse
    </div>

    {{-- Modal de reserva --}}
    <div class="modal fade" id="giftModal" tabindex="-1" aria-labelledby="giftModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="giftModalLabel" class="modal-title">
                        Presentear <span id="modalGiftName" class="text-primary fw-semibold"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
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
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name"
                                   value="{{ old('recipient_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Observação (opcional)</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button id="confirmBtn" type="submit" class="btn btn-success" form="giftForm">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de agradecimento --}}
    <div class="modal fade" id="thanksModal" tabindex="-1" aria-labelledby="thanksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-5">
                    <h4 id="thanksModalLabel" class="mb-3">Obrigado pelo presente! 🎁</h4>
                    <p class="mb-4">
                        Sua reserva de <strong id="thanksGiftName"></strong>
                        para <strong id="thanksRecipientName"></strong> foi registrada com sucesso.
                    </p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // IMPORTANTE: seu layouts.app PRECISA ter @stack('scripts') antes do </body>
  const giftModal   = document.getElementById('giftModal');
  const thanksModal = document.getElementById('thanksModal');
  const form        = document.getElementById('giftForm');
  const confirmBt   = document.getElementById('confirmBtn');
  const errBox      = document.getElementById('formErrors');

  if (!giftModal || !form) {
    console.error('Modal ou form não encontrados. Verifique IDs e se @stack("scripts") está no layout.');
    return;
  }

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
    console.log('SUBMIT INTERCEPTADO via JS');

    // UI: loading
    if (confirmBt) {
      confirmBt.disabled = true;
      var originalText = confirmBt.textContent;
      confirmBt.textContent = 'Enviando...';
    }

    try {
      const formData  = new FormData(form);
      const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

      console.log('POST para:', form.action, 'gift_id=', formData.get('gift_id'));

      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken, // além do _token no body
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
        const text = await res.text();
        console.error('Resposta não-OK:', res.status, text);
        errBox.textContent = 'Não foi possível concluir a reserva. Tente novamente.';
        errBox.classList.remove('d-none');
        return;
      }

      // Sucesso
      console.log('Reserva OK');
      const reserveInstance = bootstrap.Modal.getInstance(giftModal) || new bootstrap.Modal(giftModal);
      reserveInstance.hide();

      const giftName = document.getElementById('modalGiftName').textContent.replace(/[“”]/g, '').trim();
      const recipient = document.getElementById('recipient_name').value.trim();
      document.getElementById('thanksGiftName').textContent = giftName || 'seu presente';
      document.getElementById('thanksRecipientName').textContent = recipient || 'o(a) presenteado(a)';

      const giftId = document.getElementById('gift_id').value;
      const card = document.getElementById(`gift-card-${giftId}`);
      if (card) card.remove();

      const thanksInstance = new bootstrap.Modal(thanksModal);
      thanksInstance.show();

    } catch (err) {
      console.error('Erro fetch:', err);
      errBox.textContent = 'Erro de rede. Verifique sua conexão e tente novamente.';
      errBox.classList.remove('d-none');
    } finally {
      if (confirmBt) { confirmBt.disabled = false; confirmBt.textContent = originalText; }
    }
  });
});
</script>
@endpush


@push('styles')
<style>
    .product-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }
    .product-card {
        border: 1px solid #e9ecef;
        border-radius: .5rem;
        padding: .75rem;
        text-align: center;
        background: #fff;
    }
    .product-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: .375rem;
        margin-bottom: .5rem;
    }
</style>
@endpush
