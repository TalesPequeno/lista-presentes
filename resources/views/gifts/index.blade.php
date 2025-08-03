<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presentes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background: #0088cc;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        main {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        h2 {
            color: #0088cc;
            margin-bottom: 20px;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #777;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <header>
        <h1>Lista de Presentes</h1>
    </header>

    <main>
        <h2>Itens disponíveis</h2>
        <ul>
            <li>Panela Elétrica</li>
            <li>Jogo de Cama</li>
            <li>Liquidificador</li>
            <li>Aparelho de Jantar</li>
        </ul>
    </main>

    <footer>
        &copy; {{ date('Y') }} Lista de Presentes. Todos os direitos reservados.
    </footer>

</body>
</html>
