<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .error-container {
            max-width: 600px;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #dc3545;
            margin: 0;
            line-height: 1;
        }
        .error-message {
            font-size: 1.5rem;
            color: #343a40;
            margin-bottom: 2rem;
        }
        .home-button {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .home-button:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Página no encontrada</h2>
        <p>Lo sentimos, la página que estás buscando no existe o podría haber sido movida.</p>
        <a href="<?= APP_URL ?>" class="home-button">Ir al inicio</a>
    </div>
</body>
</html>