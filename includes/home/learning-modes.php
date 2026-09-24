<?php
$modes = [
    ['▶', 'Videos por nivel', 'Lecciones breves y organizadas para avanzar de manera progresiva.', 'border-aula-orange/25 bg-aula-orange/10 text-aula-orange-soft'],
    ['Aa', 'Lecturas y recursos', 'Material complementario para reforzar ideas sin saturar el recorrido.', 'border-[#79b8c7]/25 bg-[#79b8c7]/10 text-[#9fcbd5]'],
    ['+', 'Actividades', 'Ejercicios para convertir conceptos en experiencias prácticas y personales.', 'border-aula-green/25 bg-aula-green/10 text-aula-green'],
    ['★', 'Progreso personal', 'Consulta lo que terminaste y continúa exactamente donde lo dejaste.', 'border-[#a66f91]/25 bg-[#a66f91]/10 text-[#c291af]'],
];
?>
<section class="border-b border-white/10 bg-aula-bg-soft px-6 py-20 lg:px-8 lg:py-24" aria-labelledby="learning-modes-title">
  <div class="mx-auto max-w-aula">
    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
      <div class="max-w-3xl"><p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#79b8c7]">Dentro de cada curso</p><h2 id="learning-modes-title" class="mt-4 font-editorial text-4xl font-semibold leading-[0.98] tracking-[-0.025em] text-aula-cream sm:text-5xl lg:text-6xl">Diferentes formas de aprender.</h2></div>
      <p class="max-w-md text-sm leading-7 text-white/42">Recursos distintos para que aprender no dependa de una sola dinámica ni de sesiones demasiado largas.</p>
    </div>
    <div class="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <?php foreach ($modes as $mode): ?>
        <article class="rounded-[1.5rem] border border-white/10 bg-white/[0.02] p-6 transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.035]">
          <div class="flex h-11 w-11 items-center justify-center rounded-xl border text-sm font-black <?= e($mode[3]) ?>"><?= e($mode[0]) ?></div>
          <h3 class="mt-7 text-base font-bold text-aula-cream"><?= e($mode[1]) ?></h3>
          <p class="mt-3 text-sm leading-6 text-white/42"><?= e($mode[2]) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
