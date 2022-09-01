<style>

</style>
    

<h4 class="color-titles font">EXPERIENCIA</h3>
<table class="width-components font">
    <tbody>
      @foreach ($experiencias as $experiencia)
      @php($fecha = strtotime($experiencia->fecha_fin))
          <tr>
            <td class="pl-3   small-font ">
              <label class="color-titles bold-font">{{ $experiencia->empresa.', '.$experiencia->cargo}}</label>&nbsp;-&nbsp;{{ $meses[date('F',$fecha)] .' '.date('Y',$fecha) }} 
                @if ($experiencia->cargo_actual)
                    {{ ' - actualidad' }}
                @endif
            </td>
          </tr>
          <tr>
            <td class="pl-6 small-font">
              {{ $experiencia->descripcion }}
            </td>
          </tr>
          <BR>
      @endforeach
    </tbody>
</table>