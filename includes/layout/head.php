<?php
$pageTitle = $pageTitle ?? APP_NAME;
$pageDescription = $pageDescription ?? 'AulaGo, portal de cursos autodidactas.';
$useSwiper = $useSwiper ?? false;
$bodyClass = $bodyClass ?? 'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($pageDescription) ?>">
  <meta name="color-scheme" content="dark">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Sora:wght@100..800&display=swap" rel="stylesheet">

  <?php if ($useSwiper): ?>
    <link rel="stylesheet" href="<?= e(asset('vendor/swiper/swiper-bundle.min.css')) ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="<?= e(asset('css/tailwind.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/custom.css')) ?>">

  <title><?= e($pageTitle) ?></title>
</head>
<body class="<?= e($bodyClass) ?>">
