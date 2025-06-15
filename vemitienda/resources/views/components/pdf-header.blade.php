
  <style>
    .subrayar{
      color: black;
    }
  </style>
  <table class="width-components font">
      <tbody>
        <tr>
          <td class="color-titles big-font bold-font"  rowspan="2" colspan="2">{{ ucfirst(@$datosB->nombres).' '.ucfirst(@$datosB->primer_apellido).' '.ucfirst(@$datosB->segundo_apellido)}}</td>
          <td class="small-font right-text cursive-font"><label class="subrayar">{{ @$datosC->telefono }}</label></td>
        </tr>
        <tr>
          <td class="small-font right-text cursive-font "><label class="subrayar">{{ @$email }}</label></td>
        </tr>
        <tr class="">
          <td class="middle-font" colspan="2" rowspan="2">{{ @$datosB->profesion }}</td>
          <td class="small-font right-text cursive-font "><label class="subrayar">{{ @$datosC->linkedin }}</label></td>
        </tr>
        <tr>
          <td class="small-font right-text cursive-font "><label class="subrayar">{{ @$datosC->twitter }}</label></td>
        </tr>
      </tbody>
    </table>
    <hr class="width-components color-titles  bg-color">

 