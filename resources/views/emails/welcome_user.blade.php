<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olá, Seja Bem Vindo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #4CAF50;
            font-size: 22px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .password {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            color: #333;
        }
        .footer {
            background-color: #f4f7fc;
            text-align: center;
            padding: 15px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .button-container {
            text-align: center;
            margin-top: 20px; /* Adiciona um pequeno espaço acima do botão */
        }
        .button {
            padding: 10px 20px;
            background-color: #4CAF50; /* Cor do fundo */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #45a049;
        }
        .footer p {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Bem-vindo à - {{ $organizationName }}</h1>
        </div>

        <div class="content">
            <h2>Olá {{ $name }},</h2>
            <p>Estamos felizes em tê-lo conosco! Sua conta foi criada com sucesso no nosso sistema.</p>
            <p>Para acessar o sistema, basta usar a seguinte senha:</p>

            <div class="password">
                {{ $password }}
            </div>

            <p>Recomendamos que você altere sua senha após o primeiro login para garantir mais segurança.</p>

            <p>Se precisar de ajuda, nossa equipe está à disposição para assisti-lo. Aproveite a experiência!</p>

            <div class="button-container">
                <a href="{{ env('APP_URL') }}" class="button">Acessar o Sistema</a>
            </div>
            <br>
        </div>
        <div class="footer">
            <p>Atenciosamente, Equipe - {{ $organizationName }}</p>
        </div>
    </div>

</body>
</html>
