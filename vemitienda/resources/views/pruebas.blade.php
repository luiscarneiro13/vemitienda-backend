<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Envío Directo a App-B</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 50px;
            background: #f4f4f4;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            display: block;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #e3342f;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #cc1f1a;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h3>Enviar a App-B</h3>

        <form method="POST"
            action="
            https://life-api.territorium.com/saberes/redirect/share/credential/eyJpdiI6InZ0NTM2K1BBbllkRzNpQ2toTXJBWlE9PSIsInZhbHVlIjoiTzdyTGd5aEo1RDlHU3E5Nm5kdmtYQT09IiwibWFjIjoiNjZlOWMwYzJjNTdiZjdhNmFlYzYxODNjMTg1OTEwNjIxMzRkNGIzYTY2MjM1OTMzYjFhMjYyN2U5N2Y3NDkwMCIsInRhZyI6IiJ9/NDc5OTc=
            ">
            <button type="submit">Enviar y Redirigir ahora mismo</button>
        </form>
    </div>

</body>

</html>
