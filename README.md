## Cookies de YouTube para MeTube

MeTube usa cookies para descargar videos con restriccion de edad o anti-bot.

### Exportar cookies (una vez cada ~3-6 meses)

1. Instala una extension en tu navegador (debes estar logeado en YouTube):
   - [Get cookies.txt LOCALLY](https://chrome.google.com/webstore/detail/get-cookiestxt-locally/cclelndahbckbenkjhflpdbgdldlbecc) (Chrome)
   - [cookies.txt](https://addons.mozilla.org/en-US/firefox/addon/cookies-txt/) (Firefox)

2. Ve a https://youtube.com y asegurate de estar logeado.

3. Usa la extensión para exportar las cookies. La extensión genera un archivo llamado `cookies.txt`.

4. Súbelo a la carpeta de descargas del VPS:
   ```bash
   scp cookies.txt root@<IP_VPS>:/home/vemitiendabackend/metube/downloads/cookies.txt
   ```

5. MeTube lo usará automáticamente desde `/downloads/cookies.txt`.

### Cuando renovar

Cuando MeTube muestre errores como:

```
ERROR: [youtube] ... Sign in to confirm ...
```

Repite los pasos 2-4 para exportar cookies frescas.
