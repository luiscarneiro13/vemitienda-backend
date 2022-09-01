<style>

</style>
<h4 class="color-titles font">CERTIFICACIONES Y CURSOS</h3>
<table class="width-components font">
    <tbody>
        @foreach ($cursos as $curso)
        @php($fecha = strtotime(@$curso->fecha_expedicion))
        <tr>
            <td class="pl-3 small-font">
                <label class="color-titles bold-font">{{ $curso->nombre }}</label> – 
                {{ $curso->empresa_emisora }}&nbsp;-&nbsp; {{  @$meses[date('F',@$fecha)] .' '.date('Y',@$fecha) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>