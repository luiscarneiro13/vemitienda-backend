@extends('layouts.adminlte.index')
@section('content')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h5 class="text-default"><i class="fa fa-user-circle"></i> Crear Post</h5>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('blog.index') }}" class="btn btn-dark btn-xs">Cancelar</a>
                </div>
            </div>
        </div>
        {{ Session::get('errors') }}
        <div class="card-body">
            <form id="form" action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf()
                <div class="card-body">
                    <div class="row">

                        <x-text name="name" columns="6" label="Título" required="true" />

                        <div class="col-md-6">
                            <label for="">Imagen principal</label><br>
                            <input type="file" name="image" /><br>
                            <small>Tamaño: 1920 x 541px</small>
                        </div>

                        <x-select class="select2" columns="3" label="Categoría" required="true" id="category_id"
                            name="category_id" :datos="@$categories" />

                        <x-radio name="status" columns="3" label="Publicar" selected="2" columnsSeparate="4"
                            required="true" :datos="@$status" />

                        <x-multiselect required="true" name="tags[]" columns="6" label="Tags" multiple="true"
                            class="select2" :datos="@$tags" />

                        <input type="hidden" id="extract" name="extract">
                        <input type="hidden" id="body" name="body">

                    </div>

                    <label class="control-label mt-3">Cuerpo completo <span class="text-danger">*</span></label>
                    <div id="bodyEditor"></div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn-sm btn btn-dark float-right"><i class="fa fa-save"></i>
                        Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike', 'link', 'image', ], // toggled buttons
            ['blockquote', 'code-block'],

            [{
                'header': 1
            }, {
                'header': 2
            }], // custom button values
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }], // superscript/subscript
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }], // outdent/indent
            [{
                'direction': 'rtl'
            }], // text direction

            [{
                'size': ['small', false, 'large', 'huge']
            }], // custom dropdown
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],

            [{
                'color': []
            }, {
                'background': []
            }], // dropdown with defaults from theme
            [{
                'font': []
            }],
            [{
                'align': []
            }],

            ['clean'] // remove formatting button
        ];

        var quillBody = new Quill('#bodyEditor', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            }
        });

        // Obtén el formulario por su ID
        var formulario = document.getElementById("form");

        // Agrega un event listener para el evento "submit"
        formulario.addEventListener("submit", function(event) {
            // Previene el comportamiento predeterminado del formulario de enviarlo
            event.preventDefault();

            document.getElementById('body').value = quillBody.root.innerHTML;

            // Continúa con el envío del formulario
            formulario.submit();
        });
    </script>
@endsection
