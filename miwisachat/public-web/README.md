# public-web

Acá va el build web estático de MiWisaChat (cliente Expo/React Native).

Se genera en el repo del cliente (`MiWisaChat/cliente`) con:

```bash
npx expo export -p web
```

El comando de Expo está configurado con el prefijo de ruta `/miwisachat` para
que los assets no choquen con la raíz del dominio (que sirve Laravel).

Tras generar el build, copiar el contenido de la carpeta de salida (`dist/`
o `web-build/`, según la config de Expo) dentro de esta carpeta
(`miwisachat/public-web/`) antes de desplegar. No hay CI para esto, es un
paso manual.

Este backend Node sirve el contenido de `public-web/` bajo la ruta
`/miwisachat` (distinta de `/api/miwisachat`, que es el prefijo de la API).
