<?php
$steps = [
    ['01', 'Encuentra un curso', 'Explora categorías, cursos populares, novedades o utiliza el buscador.', 'bg-aula-orange', 'text-aula-orange-soft'],
    ['02', 'Inscríbete', 'Regístrate en un curso gratuito o selecciona una forma de pago cuando corresponda.', 'bg-[#79b8c7]', 'text-[#79b8c7]'],
    ['03', 'Avanza por niveles', 'Consulta videos, lecturas y actividades mientras el portal registra tu progreso.', 'bg-aula-green', 'text-aula-green'],
    ['04', 'Completa el curso', 'Finaliza los niveles y revisa el curso terminado desde tu espacio de aprendizaje.', 'bg-[#a66f91]', 'text-[#c291af]'],
    ['05', 'Obtén tu certificado', 'Cuando completes la ruta, tendrás disponible el certificado correspondiente.', 'bg-[#c8756f]', 'text-[#d99a94]'],
];
?>
<section id="como-funciona" class="relative overflow-hidden border-b border-white/10 bg-aula-bg px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="how-it-works-title">
  <div class="pointer-events-none absolute left-0 top-1/4 h-72 w-72 rounded-full bg-aula-green/[0.035] blur-3xl"></div>
  <div class="relative mx-auto grid max-w-aula gap-14 lg:grid-cols-[0.78fr_1.22fr] lg:gap-20">
    <div class="lg:sticky lg:top-28 lg:self-start">
      <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-aula-green">Tu ruta de aprendizaje</p>
      <h2 id="how-it-works-title" class="mt-4 max-w-xl font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Una ruta clara, de principio a fin.</h2>
      <p class="mt-6 max-w-md text-sm leading-7 text-white/42">Un recorrido continuo para que el progreso se sienta claro y natural, sin perderte entre pantallas.</p>
      <a href="<?= e(url('cursos.php')) ?>" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-aula-orange-soft transition hover:text-aula-cream">Ver todas las rutas <span>↗</span></a>
    </div>

    <div class="relative pl-10 sm:pl-14" aria-label="Proceso para completar un curso">
      <div class="absolute bottom-5 left-[15px] top-5 w-px bg-gradient-to-b from-aula-orange/50 via-white/15 to-aula-green/45 sm:left-[19px]"></div>
      <?php foreach ($steps as $index => $step): ?>
        <article class="relative border-b border-white/10 py-8 <?= $index === 0 ? 'pt-0' : '' ?> <?= $index === count($steps)-1 ? 'border-b-0 pb-0' : '' ?>">
          <span class="absolute -left-10 top-9 h-3.5 w-3.5 rounded-full ring-[7px] ring-aula-bg sm:-left-14 sm:h-4 sm:w-4 <?= e($step[3]) ?>"></span>
          <div class="grid gap-3 sm:grid-cols-[58px_1fr] sm:gap-6">
            <span class="text-[11px] font-bold tracking-[0.16em] <?= e($step[4]) ?>"><?= e($step[0]) ?></span>
            <div><h3 class="text-lg font-bold text-aula-cream sm:text-xl"><?= e($step[1]) ?></h3><p class="mt-2 max-w-xl text-sm leading-7 text-white/42"><?= e($step[2]) ?></p></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
