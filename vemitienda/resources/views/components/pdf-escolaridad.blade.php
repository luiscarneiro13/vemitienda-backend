<style>

</style>
<h4 class="color-titles font">EDUCACIÓN</h3>
  <table class="width-components font">
    <tbody>
      @foreach ($escolaridad as $educacion)
          @php($fecha = strtotime(@$educacion->fecha_fin))
          <tr>
            <td class="pl-3   small-font ">
                <label class="color-titles bold-font">{{ @$educacion->institucion}}</label>&nbsp;-&nbsp;{{ucfirst(@$educacion->ubicacion).'. '. @$meses[date('F',@$fecha)] .' '.date('Y',@$fecha) }} 
            </td>
          </tr>
      @endforeach
    </tbody>
</table>