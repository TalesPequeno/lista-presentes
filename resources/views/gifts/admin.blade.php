@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Presentes</h1>

    <!-- Botão abre modal -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createGiftModal">
        + Novo Presente
    </button>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Reservado?</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gifts as $gift)
                        <tr>
                            <td>{{ $gift->id }}</td>
                            <td>{{ $gift->name }}</td>
                            <td>{{ $gift->category ?? '-' }}</td>
                            <td>
                                @if($gift->is_reserved)
                                    <span class="badge bg-success">Sim</span>
                                @else
                                    <span class="badge bg-secondary">Não</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('gifts.destroy', $gift->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este presente?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum presente cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de criação de presente -->
<div class="modal fade" id="createGiftModal" tabindex="-1" aria-labelledby="createGiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('gifts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createGiftModalLabel">Cadastrar Novo Presente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Presente</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Categoria</label>
                        <input type="text" name="category" id="category" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
