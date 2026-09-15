<template>
  <div class="min-h-screen bg-aula-bg text-aula-cream">
    <StudentHeader />

    <main class="mx-auto max-w-aula px-6 py-14 lg:px-8 lg:py-20">
      <div class="grid gap-10 lg:grid-cols-[300px_1fr]">
        <aside>
          <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.16em] text-aula-green">Mi cuenta</p>
          <h1 class="mt-4 font-editorial text-5xl text-aula-cream">Tu perfil de aprendizaje.</h1>
          <p class="mt-5 text-sm leading-7 text-white/45">En esta vista después conectarás los datos reales del usuario desde tu backend o API.</p>

          <div class="mt-8 border border-white/10 bg-aula-surface/60 p-5">
            <div class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark text-lg font-extrabold">E</div>
            <strong class="mt-4 block text-base text-aula-cream">Estudiante AulaGo</strong>
            <span class="mt-1 block text-xs text-white/35">Perfil de demostración</span>
          </div>
        </aside>

        <div class="space-y-6">
          <section class="border border-white/10 bg-aula-surface/60 p-6 sm:p-8">
            <div class="border-b border-white/10 pb-5">
              <h2 class="font-editorial text-3xl text-aula-cream">Información personal</h2>
              <p class="mt-2 text-sm text-white/40">Datos visibles en tu cuenta.</p>
            </div>

            <form class="mt-6 grid gap-5 sm:grid-cols-2" @submit.prevent>
              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/55">Nombre</span>
                <input value="Estudiante AulaGo" class="border border-white/10 bg-aula-bg/60 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange" />
              </label>
              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/55">Correo</span>
                <input value="estudiante@aulago.demo" type="email" class="border border-white/10 bg-aula-bg/60 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange" />
              </label>
              <label class="grid gap-2 sm:col-span-2">
                <span class="text-xs font-bold text-white/55">Intereses</span>
                <input value="Bienestar, Diseño, IT & Software" class="border border-white/10 bg-aula-bg/60 px-4 py-3 text-sm text-white outline-none focus:border-aula-orange" />
              </label>
              <button type="submit" class="w-fit bg-aula-orange px-5 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Guardar cambios</button>
            </form>
          </section>

          <section class="grid gap-4 md:grid-cols-2">
            <div class="border border-white/10 bg-aula-surface/60 p-6">
              <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-aula-green">Preferencias</p>
              <h2 class="mt-3 font-editorial text-2xl text-aula-cream">Recomendaciones</h2>
              <p class="mt-3 text-sm leading-6 text-white/40">Usa tus cursos vistos para sugerir rutas relacionadas.</p>
              <label class="mt-6 flex items-center justify-between gap-4 border-t border-white/10 pt-5 text-sm text-white/65">
                Personalizar recomendaciones
                <input type="checkbox" checked class="h-4 w-4 accent-[#ef8a43]" />
              </label>
            </div>

            <div class="border border-white/10 bg-aula-surface/60 p-6">
              <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-aula-green">Seguridad</p>
              <h2 class="mt-3 font-editorial text-2xl text-aula-cream">Acceso</h2>
              <p class="mt-3 text-sm leading-6 text-white/40">Aquí podrás agregar cambio de contraseña y cierre de sesiones cuando exista autenticación real.</p>
              <RouterLink to="/login" class="mt-6 inline-flex border-t border-white/10 pt-5 text-sm font-bold text-aula-orange">Ir al acceso →</RouterLink>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import StudentHeader from '../../components/layout/StudentHeader.vue'
</script>
