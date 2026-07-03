@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-newspaper"></i> Blog</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('blog.create') }}" class="btn btn-dark btn-xs"><i class="fa fa-plus-circle"></i> Nuevo artículo</a>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th width="60">Imagen</th>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th width="120"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['infoData'] as $post)
                    <tr>
                        <td>
                            @if ($post->image)
                                <img src="{{ $post->image->url }}" width="50" height="50" style="object-fit:cover;border-radius:4px">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $post->name }}</td>
                        <td>{{ $post->category->name ?? '—' }}</td>
                        <td>
                            @if ($post->status == 2)
                                <span class="badge badge-success">Publicado</span>
                            @else
                                <span class="badge badge-secondary">Borrador</span>
                            @endif
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <a class="dropdown-toggle btn btn-sm btn-dark" data-toggle="dropdown">Opciones</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('blog.edit', $post->id) }}">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('blog.destroy', $post->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este artículo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay artículos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px">
            <div class="float-left" style="margin-top:10px">
                Mostrando {{ $data['infoData']->perPage() }} de {{ $data['infoData']->total() }} registros
            </div>
            <div class="float-right">
                {{ $data['infoData']->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
