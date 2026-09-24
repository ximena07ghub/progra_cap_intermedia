<?php
require_once __DIR__ . '/config/app.php';

$pageTitle = 'AulaGo | Aprende a tu ritmo';
$pageDescription = 'Cursos autodidactas para aprender a tu ritmo, desarrollar habilidades y cuidar tu bienestar.';
$useSwiper = true;
$pageScripts = ['home-carousel.js'];

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<div class="min-h-screen overflow-x-hidden bg-aula-bg text-aula-cream">
  <?php require PROJECT_ROOT . '/includes/layout/public-header.php'; ?>

  <main>
    <?php require PROJECT_ROOT . '/includes/home/hero.php'; ?>
    <?php require PROJECT_ROOT . '/includes/home/course-carousel.php'; ?>
    <?php require PROJECT_ROOT . '/includes/home/how-it-works.php'; ?>
    <?php require PROJECT_ROOT . '/includes/home/learning-modes.php'; ?>
    <?php require PROJECT_ROOT . '/includes/home/video-showcase.php'; ?>
    <?php require PROJECT_ROOT . '/includes/home/testimonials.php'; ?>
  </main>

  <?php require PROJECT_ROOT . '/includes/layout/public-footer.php'; ?>
</div>
<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>
</body>
</html>
