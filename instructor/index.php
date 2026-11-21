<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

require_role('instructor');

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/InstructorCurso.php';

$idInstructor =
    (int) ($_SESSION['usuario_id'] ?? 0);

$mensaje =
    $_SESSION['instructor_cursos_mensaje']
    ?? null;

unset($_SESSION['instructor_cursos_mensaje']);

try {
    $configuracion = require PROJECT_ROOT
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);

    $modelo = new InstructorCurso(
        $conexion->obtenerConexion()
    );

    $cursos =
        $modelo->listarPorInstructor($idInstructor);

    $categorias =
        $modelo->listarCategoriasActivas();

    $metricas =
        $modelo->obtenerMetricas($idInstructor);

    $ventas =
        $modelo->listarVentas($idInstructor);

    $estudiantes =
        $modelo->listarEstudiantes($idInstructor);

    $resenas =
        $modelo->listarResenas($idInstructor);

    $contenidos = [];

    foreach ($cursos as $curso) {
        $contenidos[
            (int) $curso['id_curso']
        ] = $modelo->listarNiveles(
            (int) $curso['id_curso'],
            $idInstructor
        );
    }

} catch (Throwable $error) {
    error_log($error->getMessage());

    http_response_code(500);

    exit(
        'No fue posible cargar el portal del instructor.'
    );
}

$pageTitle =
    'Workspace del instructor | AulaGo';

$pageDescription =
    'Administración de cursos del instructor.';

$pageScripts = ['workspace.js'];

$bodyClass =
    'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased lg:overflow-hidden';

$workspaceRoleLabel =
    'Workspace del instructor';

$workspaceItems = [
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'icon' => '⌂'
    ],
    [
        'id' => 'cursos',
        'label' => 'Mis cursos',
        'icon' => '▦'
    ],
    [
        'id' => 'crear',
        'label' => 'Crear curso',
        'icon' => '+'
    ],
    [
        'id' => 'estudiantes',
        'label' => 'Estudiantes',
        'icon' => '◎'
    ],
    [
        'id' => 'ventas',
        'label' => 'Ventas',
        'icon' => '$'
    ],
    [
        'id' => 'resenas',
        'label' => 'Reseñas',
        'icon' => '★'
    ],
    [
        'id' => 'cuenta',
        'label' => 'Mi cuenta',
        'icon' => '○'
    ]
];

require PROJECT_ROOT . '/includes/layout/head.php';
?>

<div
    class="min-h-screen bg-aula-bg text-aula-cream lg:grid lg:h-screen lg:grid-cols-[230px_minmax(0,1fr)_320px] lg:overflow-hidden"
    data-workspace
>

    <?php require PROJECT_ROOT . '/includes/workspace/sidebar.php'; ?>

    <section class="flex min-w-0 flex-col border-white/10 lg:border-r">

        <header class="border-b border-white/10 bg-aula-bg/95 px-5 py-5 md:px-7 lg:px-8">

            <div class="flex items-end justify-between gap-4">

                <div>

                    <p
                        class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green"
                        data-workspace-eyebrow
                    >
                        Actividad del instructor
                    </p>

                    <h1
                        class="mt-2 font-editorial text-3xl font-semibold"
                        data-workspace-title
                    >
                        Dashboard
                    </h1>

                </div>

                <a
                    href="#crear"
                    data-workspace-link="crear"
                    class="bg-aula-orange px-4 py-3 text-xs font-extrabold text-aula-bg"
                >
                    + Crear curso
                </a>

            </div>

        </header>

        <main class="workspace-main min-h-0 flex-1 overflow-y-auto px-5 py-6 md:px-7 lg:px-8">

            <?php if ($mensaje): ?>

                <div class="mb-6 border border-aula-green/25 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">
                    <?= e($mensaje) ?>
                </div>

            <?php endif; ?>

            <!-- DASHBOARD -->

            <section
                data-workspace-panel="dashboard"
                data-title="Dashboard"
                data-eyebrow="Actividad del instructor"
            >

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                    <article class="border border-white/10 p-4">
                        <span class="text-[9px] uppercase text-white/30">
                            Cursos
                        </span>
                        <strong class="mt-2 block text-2xl">
                            <?= e($metricas['cursos']) ?>
                        </strong>
                    </article>

                    <article class="border border-white/10 p-4">
                        <span class="text-[9px] uppercase text-white/30">
                            Estudiantes
                        </span>
                        <strong class="mt-2 block text-2xl text-aula-green">
                            <?= e($metricas['estudiantes']) ?>
                        </strong>
                    </article>

                    <article class="border border-white/10 p-4">
                        <span class="text-[9px] uppercase text-white/30">
                            Ingresos
                        </span>
                        <strong class="mt-2 block text-2xl text-aula-orange-soft">
                            <?= e(format_money($metricas['ingresos'])) ?>
                        </strong>
                    </article>

                    <article class="border border-white/10 p-4">
                        <span class="text-[9px] uppercase text-white/30">
                            Conversaciones
                        </span>
                        <strong class="mt-2 block text-2xl text-[#9fcbd5]">
                            <?= e($metricas['conversaciones']) ?>
                        </strong>
                    </article>

                </div>

                <section class="mt-7 border border-white/10 p-5">

                    <div class="flex items-end justify-between">

                        <div>
                            <p class="text-[10px] font-bold uppercase text-aula-green">
                                Tus cursos
                            </p>

                            <h2 class="mt-2 font-editorial text-2xl font-semibold">
                                Actividad reciente
                            </h2>
                        </div>

                        <a
                            href="#cursos"
                            data-workspace-link="cursos"
                            class="text-xs font-bold text-aula-orange-soft"
                        >
                            Ver todos →
                        </a>

                    </div>

                    <div class="mt-4 divide-y divide-white/10">

                        <?php if ($cursos === []): ?>

                            <p class="py-6 text-sm text-white/35">
                                Todavía no has creado cursos.
                            </p>

                        <?php endif; ?>

                        <?php foreach (array_slice($cursos, 0, 5) as $curso): ?>

                            <article class="py-4">

                                <strong class="block">
                                    <?= e($curso['titulo']) ?>
                                </strong>

                                <span class="mt-1 block text-xs text-white/35">
                                    <?= e($curso['categoria']) ?>
                                    ·
                                    <?= e($curso['estado']) ?>
                                    ·
                                    <?= e($curso['estudiantes']) ?>
                                    estudiantes
                                </span>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </section>

            </section>

            <!-- MIS CURSOS -->

            <section
                data-workspace-panel="cursos"
                data-title="Mis cursos"
                data-eyebrow="Contenido y publicación"
                hidden
            >

                <?php if ($cursos === []): ?>

                    <div class="border border-white/10 p-6">

                        <h2 class="font-editorial text-2xl">
                            Aún no tienes cursos
                        </h2>

                        <a
                            href="#crear"
                            data-workspace-link="crear"
                            class="mt-4 inline-block text-sm font-bold text-aula-orange"
                        >
                            Crear primer curso →
                        </a>

                    </div>

                <?php endif; ?>

                <div class="space-y-5">

                    <?php foreach ($cursos as $curso): ?>

                        <article class="border border-white/10 p-5">

                            <div class="grid gap-5 md:grid-cols-[150px_1fr]">

                                <div>

                                    <?php if (!empty($curso['imagen'])): ?>

                                        <img
                                            src="<?= e(asset($curso['imagen'])) ?>"
                                            alt="<?= e($curso['titulo']) ?>"
                                            class="aspect-video h-full max-h-28 w-full object-cover"
                                        >

                                    <?php else: ?>

                                        <div class="grid aspect-video place-items-center bg-white/[0.04] text-xs text-white/25">
                                            Sin portada
                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div>

                                    <div class="flex flex-wrap gap-2 text-[10px] font-bold uppercase">

                                        <span class="text-aula-green">
                                            <?= e($curso['categoria']) ?>
                                        </span>

                                        <span class="text-white/25">·</span>

                                        <span class="text-aula-orange-soft">
                                            <?= e($curso['estado']) ?>
                                        </span>

                                    </div>

                                    <h2 class="mt-2 font-editorial text-2xl font-semibold">
                                        <?= e($curso['titulo']) ?>
                                    </h2>

                                    <p class="mt-2 text-xs text-white/35">
                                        <?= e($curso['estudiantes']) ?>
                                        estudiantes ·
                                        ★
                                        <?= e($curso['calificacion'] ?? '—') ?>
                                        ·
                                        <?= e(format_money($curso['precio'])) ?>
                                    </p>

                                    <div class="mt-4 flex flex-wrap gap-2">

                                        <a
                                            href="#contenido-<?= e($curso['id_curso']) ?>"
                                            data-workspace-link="contenido-<?= e($curso['id_curso']) ?>"
                                            class="border border-white/10 px-3 py-2 text-xs font-bold"
                                        >
                                            Contenido
                                        </a>

                                        <a
                                            href="#editar-<?= e($curso['id_curso']) ?>"
                                            data-workspace-link="editar-<?= e($curso['id_curso']) ?>"
                                            class="border border-aula-orange/30 px-3 py-2 text-xs font-bold text-aula-orange-soft"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="<?= e(url('actions/instructor_cursos.php')) ?>"
                                            method="post"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e(csrf_token()) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id_curso"
                                                value="<?= e($curso['id_curso']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="<?= $curso['estado'] === 'publicado' ? 'desactivar' : 'publicar' ?>"
                                            >

                                            <button
                                                class="border border-white/10 px-3 py-2 text-xs font-bold"
                                            >
                                                <?= $curso['estado'] === 'publicado' ? 'Desactivar' : 'Publicar' ?>
                                            </button>

                                        </form>

                                        <form
                                            action="<?= e(url('actions/instructor_cursos.php')) ?>"
                                            method="post"
                                            onsubmit="return confirm('Se eliminará el curso y toda su información relacionada. ¿Continuar?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e(csrf_token()) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id_curso"
                                                value="<?= e($curso['id_curso']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="eliminar"
                                            >

                                            <button
                                                class="border border-red-400/30 px-3 py-2 text-xs font-bold text-red-300"
                                            >
                                                Eliminar
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

            <!-- CREAR CURSO -->

            <section
                data-workspace-panel="crear"
                data-title="Crear curso"
                data-eyebrow="Nuevo contenido"
                hidden
            >

                <?php if ($categorias === []): ?>

                    <div class="border border-aula-yellow/20 p-5 text-sm text-aula-yellow">
                        No existen categorías activas. Un administrador debe crear una antes de publicar cursos.
                    </div>

                <?php else: ?>

                    <form
                        action="<?= e(url('actions/instructor_cursos.php')) ?>"
                        method="post"
                        enctype="multipart/form-data"
                        class="space-y-6"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <section class="border border-white/10 p-6">

                            <h2 class="font-editorial text-2xl font-semibold">
                                Información del curso
                            </h2>

                            <div class="mt-5 grid gap-5 md:grid-cols-2">

                                <label class="grid gap-2 md:col-span-2">
                                    <span class="text-xs font-bold text-white/55">
                                        Título
                                    </span>

                                    <input
                                        name="titulo"
                                        maxlength="200"
                                        required
                                        class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                    >
                                </label>

                                <label class="grid gap-2">
                                    <span class="text-xs font-bold text-white/55">
                                        Categoría
                                    </span>

                                    <select
                                        name="id_categoria"
                                        required
                                        class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                    >

                                        <?php foreach ($categorias as $categoria): ?>

                                            <option value="<?= e($categoria['id_categoria']) ?>">
                                                <?= e($categoria['nombre']) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>
                                </label>

                                <label class="grid gap-2">
                                    <span class="text-xs font-bold text-white/55">
                                        Precio MXN
                                    </span>

                                    <input
                                        type="number"
                                        name="precio"
                                        min="0"
                                        step="0.01"
                                        value="0"
                                        required
                                        class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                    >
                                </label>

                                <label class="grid gap-2 md:col-span-2">
                                    <span class="text-xs font-bold text-white/55">
                                        Portada
                                    </span>

                                    <input
                                        type="file"
                                        name="imagen"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="border border-white/10 p-3 text-sm"
                                    >

                                    <small class="text-white/30">
                                        JPG, PNG o WebP. Máximo 5 MB.
                                    </small>
                                </label>

                                <label class="grid gap-2 md:col-span-2">
                                    <span class="text-xs font-bold text-white/55">
                                        Descripción
                                    </span>

                                    <textarea
                                        name="descripcion"
                                        required
                                        class="min-h-36 border border-white/10 bg-aula-bg p-3"
                                    ></textarea>
                                </label>

                            </div>

                        </section>

                        <div class="flex justify-end gap-3">

                            <button
                                name="estado"
                                value="borrador"
                                class="border border-white/10 px-4 py-3 text-xs font-bold"
                            >
                                Guardar borrador
                            </button>

                            <button
                                name="estado"
                                value="publicado"
                                class="bg-aula-orange px-5 py-3 text-xs font-extrabold text-aula-bg"
                            >
                                Crear y publicar
                            </button>

                            <input
                                type="hidden"
                                name="accion"
                                value="crear"
                            >

                        </div>

                    </form>

                <?php endif; ?>

            </section>

            <!-- EDITORES DE CURSO -->

            <?php foreach ($cursos as $curso): ?>

                <section
                    data-workspace-panel="editar-<?= e($curso['id_curso']) ?>"
                    data-title="Editar curso"
                    data-eyebrow="<?= e($curso['titulo']) ?>"
                    hidden
                >

                    <form
                        action="<?= e(url('actions/instructor_cursos.php')) ?>"
                        method="post"
                        enctype="multipart/form-data"
                        class="border border-white/10 p-6"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <input
                            type="hidden"
                            name="accion"
                            value="actualizar"
                        >

                        <input
                            type="hidden"
                            name="id_curso"
                            value="<?= e($curso['id_curso']) ?>"
                        >

                        <div class="grid gap-5 md:grid-cols-2">

                            <label class="grid gap-2 md:col-span-2">
                                <span class="text-xs font-bold">Título</span>
                                <input
                                    name="titulo"
                                    value="<?= e($curso['titulo']) ?>"
                                    required
                                    class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                >
                            </label>

                            <label class="grid gap-2">
                                <span class="text-xs font-bold">Categoría</span>

                                <select
                                    name="id_categoria"
                                    class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                >
                                    <?php foreach ($categorias as $categoria): ?>

                                        <option
                                            value="<?= e($categoria['id_categoria']) ?>"
                                            <?= (int) $categoria['id_categoria'] === (int) $curso['id_categoria'] ? 'selected' : '' ?>
                                        >
                                            <?= e($categoria['nombre']) ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label class="grid gap-2">
                                <span class="text-xs font-bold">Precio</span>
                                <input
                                    type="number"
                                    name="precio"
                                    min="0"
                                    step="0.01"
                                    value="<?= e($curso['precio']) ?>"
                                    required
                                    class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                >
                            </label>

                            <label class="grid gap-2 md:col-span-2">
                                <span class="text-xs font-bold">
                                    Nueva portada
                                </span>

                                <input
                                    type="file"
                                    name="imagen"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="border border-white/10 p-3"
                                >
                            </label>

                            <label class="grid gap-2 md:col-span-2">
                                <span class="text-xs font-bold">
                                    Descripción
                                </span>

                                <textarea
                                    name="descripcion"
                                    required
                                    class="min-h-36 border border-white/10 bg-aula-bg p-3"
                                ><?= e($curso['descripcion']) ?></textarea>
                            </label>

                        </div>

                        <button class="mt-5 bg-aula-orange px-5 py-3 text-xs font-bold text-aula-bg">
                            Guardar cambios
                        </button>

                    </form>

                </section>

                <!-- CONTENIDO -->

                <section
                    data-workspace-panel="contenido-<?= e($curso['id_curso']) ?>"
                    data-title="Contenido"
                    data-eyebrow="<?= e($curso['titulo']) ?>"
                    hidden
                >

                    <form
                        action="<?= e(url('actions/instructor_cursos.php')) ?>"
                        method="post"
                        class="border border-white/10 p-5"
                    >

                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="accion" value="crear_nivel">
                        <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">

                        <h2 class="font-editorial text-2xl">
                            Nuevo nivel
                        </h2>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">

                            <input
                                name="titulo"
                                maxlength="150"
                                required
                                placeholder="Título del nivel"
                                class="min-h-11 border border-white/10 bg-aula-bg px-3"
                            >

                            <input
                                name="descripcion"
                                placeholder="Descripción"
                                class="min-h-11 border border-white/10 bg-aula-bg px-3"
                            >

                        </div>

                        <button class="mt-4 text-xs font-bold text-aula-green">
                            + Agregar nivel
                        </button>

                    </form>

                    <div class="mt-6 space-y-6">

                        <?php foreach ($contenidos[(int) $curso['id_curso']] as $nivel): ?>

                            <article class="border border-white/10">

                                <header class="flex items-center justify-between border-b border-white/10 p-5">

                                    <div>
                                        <span class="text-xs text-aula-orange">
                                            Nivel <?= e($nivel['orden']) ?>
                                        </span>

                                        <h3 class="mt-1 font-editorial text-xl">
                                            <?= e($nivel['titulo']) ?>
                                        </h3>
                                    </div>

                                    <form
                                        action="<?= e(url('actions/instructor_cursos.php')) ?>"
                                        method="post"
                                        onsubmit="return confirm('Se eliminarán también las lecciones y su progreso relacionado. ¿Continuar?');"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="accion" value="eliminar_nivel">
                                        <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
                                        <input type="hidden" name="id_nivel" value="<?= e($nivel['id_nivel']) ?>">

                                        <button class="text-xs text-red-300">
                                            Eliminar nivel
                                        </button>
                                    </form>

                                </header>

                                <div class="divide-y divide-white/10">

                                    <?php foreach ($nivel['lecciones'] as $leccion): ?>

                                        <div class="flex items-start justify-between gap-4 p-4">

                                            <div>
                                                <strong class="text-sm">
                                                    <?= e($leccion['orden']) ?>.
                                                    <?= e($leccion['titulo']) ?>
                                                </strong>

                                                <?php if (!empty($leccion['url_video'])): ?>
                                                    <span class="mt-1 block text-xs text-[#9fcbd5]">
                                                        Video asociado
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <form
                                                action="<?= e(url('actions/instructor_cursos.php')) ?>"
                                                method="post"
                                                onsubmit="return confirm('¿Eliminar esta lección y su progreso relacionado?');"
                                            >
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="accion" value="eliminar_leccion">
                                                <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
                                                <input type="hidden" name="id_leccion" value="<?= e($leccion['id_leccion']) ?>">

                                                <button class="text-xs text-red-300">
                                                    Eliminar
                                                </button>
                                            </form>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                                <form
                                    action="<?= e(url('actions/instructor_cursos.php')) ?>"
                                    method="post"
                                    class="border-t border-white/10 p-5"
                                >

                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="accion" value="crear_leccion">
                                    <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
                                    <input type="hidden" name="id_nivel" value="<?= e($nivel['id_nivel']) ?>">

                                    <div class="grid gap-3">

                                        <input
                                            name="titulo"
                                            maxlength="200"
                                            required
                                            placeholder="Título de la lección"
                                            class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                        >

                                        <textarea
                                            name="contenido"
                                            placeholder="Contenido de la lección"
                                            class="min-h-28 border border-white/10 bg-aula-bg p-3"
                                        ></textarea>

                                        <input
                                            type="url"
                                            name="url_video"
                                            placeholder="URL de video (opcional)"
                                            class="min-h-11 border border-white/10 bg-aula-bg px-3"
                                        >

                                    </div>

                                    <button class="mt-4 text-xs font-bold text-aula-green">
                                        + Agregar lección
                                    </button>

                                </form>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </section>

            <?php endforeach; ?>

            <!-- ESTUDIANTES -->

            <section
                data-workspace-panel="estudiantes"
                data-title="Estudiantes"
                data-eyebrow="Inscripciones"
                hidden
            >

                <div class="overflow-x-auto border border-white/10">

                    <table class="min-w-full text-sm">

                        <thead class="text-left text-xs text-white/35">
                            <tr>
                                <th class="p-4">Estudiante</th>
                                <th class="p-4">Curso</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4">Inscripción</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">

                            <?php foreach ($estudiantes as $estudiante): ?>

                                <tr>
                                    <td class="p-4">
                                        <strong>
                                            <?= e($estudiante['estudiante']) ?>
                                        </strong>
                                        <span class="block text-xs text-white/30">
                                            <?= e($estudiante['email']) ?>
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <?= e($estudiante['curso']) ?>
                                    </td>

                                    <td class="p-4">
                                        <?= e($estudiante['estado']) ?>
                                    </td>

                                    <td class="p-4">
                                        <?= e(format_date_es($estudiante['fecha_inscripcion'])) ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <!-- VENTAS -->

            <section
                data-workspace-panel="ventas"
                data-title="Ventas"
                data-eyebrow="Ingresos de tus cursos"
                hidden
            >

                <div class="space-y-3">

                    <?php foreach ($ventas as $venta): ?>

                        <article class="flex justify-between border border-white/10 p-4">

                            <div>
                                <strong>
                                    <?= e($venta['curso']) ?>
                                </strong>

                                <span class="mt-1 block text-xs text-white/35">
                                    <?= e($venta['estudiante']) ?>
                                    ·
                                    <?= e($venta['metodo']) ?>
                                    ·
                                    <?= e($venta['estado']) ?>
                                </span>
                            </div>

                            <strong class="text-aula-green">
                                $<?= e(number_format((float) $venta['monto'], 2)) ?>
                                <?= e($venta['moneda']) ?>
                            </strong>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

            <!-- RESEÑAS -->

            <section
                data-workspace-panel="resenas"
                data-title="Reseñas"
                data-eyebrow="Opiniones sobre tus cursos"
                hidden
            >

                <div class="space-y-4">

                    <?php foreach ($resenas as $resena): ?>

                        <article class="border border-white/10 p-5">

                            <div class="flex justify-between">

                                <div>
                                    <strong>
                                        <?= e($resena['estudiante']) ?>
                                    </strong>

                                    <span class="block text-xs text-white/35">
                                        <?= e($resena['curso']) ?>
                                    </span>
                                </div>

                                <span class="text-aula-yellow">
                                    <?= e(str_repeat(
                                        '★',
                                        (int) $resena['calificacion']
                                    )) ?>
                                </span>

                            </div>

                            <?php if (!empty($resena['comentario'])): ?>
                                <p class="mt-4 text-sm text-white/50">
                                    <?= e($resena['comentario']) ?>
                                </p>
                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

            <!-- CUENTA -->

            <section
                data-workspace-panel="cuenta"
                data-title="Mi cuenta"
                data-eyebrow="Perfil del instructor"
                hidden
            >

                <div class="max-w-xl border border-white/10 p-6">

                    <span class="grid h-20 w-20 place-items-center rounded-full bg-aula-green-dark text-xl font-bold text-aula-green">
                        <?= e(user_initials()) ?>
                    </span>

                    <h2 class="mt-5 font-editorial text-3xl">
                        <?= e(user_name()) ?>
                    </h2>

                    <p class="mt-2 text-sm text-white/35">
                        <?= e(user_email()) ?>
                    </p>

                    <p class="mt-6 text-xs uppercase text-aula-green">
                        Instructor
                    </p>

                </div>

            </section>

        </main>

    </section>

    <!-- PANEL DERECHO -->

    <aside class="workspace-context hidden bg-[#151116] p-5 lg:block">

        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/30">
            Instructor
        </p>

        <div class="mt-6 flex items-center gap-4">

            <span class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark font-bold text-aula-green">
                <?= e(user_initials()) ?>
            </span>

            <div>
                <strong>
                    <?= e(user_name()) ?>
                </strong>

                <span class="block text-xs text-white/30">
                    Instructor · AulaGo
                </span>
            </div>

        </div>

        <div class="mt-8 grid grid-cols-2 gap-px bg-white/10">

            <div class="bg-[#151116] p-4">
                <span class="text-[9px] text-white/30">
                    CURSOS
                </span>
                <strong class="mt-2 block text-2xl">
                    <?= e($metricas['cursos']) ?>
                </strong>
            </div>

            <div class="bg-[#151116] p-4">
                <span class="text-[9px] text-white/30">
                    ESTUDIANTES
                </span>
                <strong class="mt-2 block text-2xl text-aula-green">
                    <?= e($metricas['estudiantes']) ?>
                </strong>
            </div>

        </div>

        <div class="mt-7 border-t border-white/10 pt-5">

            <span class="text-xs text-white/30">
                Ingresos registrados
            </span>

            <strong class="mt-2 block text-xl text-aula-orange-soft">
                <?= e(format_money($metricas['ingresos'])) ?>
            </strong>

        </div>

    </aside>

</div>

<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>

</body>
</html>