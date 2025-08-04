@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Presentes</h1>

    <a href="{{ route('gifts.create') }}" class="btn btn-primary mb-3">
        + Novo Presente
    </a>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Reservado?</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gifts as $gift)
                        <tr>
                            <td>{{ $gift->id }}</td>
                            <td>{{ $gift->name }}</td>
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
                            <td colspan="4" class="text-center">Nenhum presente cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
