<style>

</style>
<h4 class="color-titles font">ESTUDIOS INTERNACIONALES</h3>
  <table class="width-components font">
    <tbody>
      @foreach ($estudios as $estuio)
          @php($fecha = strtotime(@$estuio->fecha_fin))
          <tr>
            <td class="pl-3   small-font ">
                <label class="color-titles bold-font">{{ @$estuio->institucion}}</label>&nbsp;-&nbsp;{{ucfirst(@$estuio->ubicacion).'. '. @$meses[date('F',@$fecha)] .' '.date('Y',@$fecha) }} 
            </td>
          </tr>
      @endforeach
    </tbody>
</table>