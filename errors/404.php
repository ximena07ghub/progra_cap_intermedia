<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

http_response_code(404);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 · Página no encontrada | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/custom.css')) ?>">
</head>
<body class="min-h-screen text-[#f5f0e8]">
    <main class="error-page-shell">
        <section class="error-page-card" aria-labelledby="error-title">
            <div class="error-page-brand">
                <span class="error-page-brand-mark">A</span>
                <span>AulaGo</span>
            </div>

            <img
                class="error-page-illustration"
                src="<?= e(asset('images/error-404-cable.svg')) ?>"
                alt="Cable desconectado"
            >

            <p class="error-page-kicker">Página desconectada</p>
            <h1 id="error-title" class="error-page-code">404</h1>
            <h2 class="error-page-title">No encontramos esta página</h2>
            <p class="error-page-copy">
                El enlace que abriste no existe, cambió de dirección o ya no está disponible.
                Puedes volver al inicio y continuar explorando AulaGo.
            </p>

            <div class="error-page-actions">
                <a class="error-page-primary" href="<?= e(url()) ?>">Volver al inicio</a>
                <button class="error-page-secondary" type="button" onclick="history.back()">Regresar</button>
            </div>
        </section>
    </main>
</body>
</html>
