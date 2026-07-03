@extends('layouts.adminlte.index')
@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h5 class="text-default"><i class="fa fa-newspaper"></i> Nuevo artículo</h5>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('blog.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-md-8">
                    <div class="form-group">
                        <label>Título <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="Título del artículo..." value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label>Contenido <span class="text-danger">*</span></label>
                        <div id="quill-editor" style="height:350px;background:#fff"></div>
                        <textarea name="body" id="body-hidden" style="display:none">{{ old('body') }}</textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Sin categoría —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Etiquetas</label>
                        <select name="tags[]" class="form-control select2" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                    {{ $tag->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="status" class="form-control">
                            @foreach ($status as $s)
                                <option value="{{ $s['id'] }}" {{ old('status') == $s['id'] ? 'selected' : '' }}>
                                    {{ $s['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Imagen</label>
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-dark btn-sm"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Escribe el contenido aquí...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    })

    var existing = document.getElementById('body-hidden').value
    if (existing) quill.root.innerHTML = existing

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('body-hidden').value = quill.root.innerHTML
    })
</script>
@endsection
