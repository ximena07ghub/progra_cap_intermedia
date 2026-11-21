<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

require_role('instructor');

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/InstructorCurso.php';
require_once PROJECT_ROOT . '/models/InstructorMensaje.php';
require_once PROJECT_ROOT . '/models/usuario.php';

$idInstructor =
    (int) ($_SESSION['usuario_id'] ?? 0);

$mensaje =
    $_SESSION['instructor_cursos_mensaje']
    ?? null;

unset($_SESSION['instructor_cursos_mensaje']);

$mensajeConversacion =
    $_SESSION['instructor_mensaje']
    ?? null;

unset($_SESSION['instructor_mensaje']);

$perfilMensaje =
    $_SESSION['perfil_mensaje']
    ?? null;

$perfilMensajeTipo =
    $_SESSION['perfil_mensaje_tipo']
    ?? null;

unset(
    $_SESSION['perfil_mensaje'],
    $_SESSION['perfil_mensaje_tipo']
);

try {
    $configuracion = require PROJECT_ROOT
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);

    $modelo = new InstructorCurso(
        $conexion->obtenerConexion()
    );

    $usuarioModel = new Usuario(
        $conexion->obtenerConexion()
    );

    $perfilUsuario = $usuarioModel->buscarPorId(
        $idInstructor
    );

    if ($perfilUsuario !== null) {
        $_SESSION['usuario_nombre'] = $perfilUsuario['nombre'];
        $_SESSION['usuario_correo'] = $perfilUsuario['email'];
        $_SESSION['usuario_avatar'] = $perfilUsuario['avatar'] ?? '';
    }

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

    $modeloMensajes = new InstructorMensaje(
        $conexion->obtenerConexion()
    );

    $rawConversaciones =
        $modeloMensajes->listarConversaciones(
            $idInstructor
        );

    $conversacionesInstructor = [];

    foreach ($rawConversaciones as $conversacion) {
        $partes = preg_split(
            '/\s+/u',
            trim((string) $conversacion['estudiante'])
        ) ?: [];

        $avatar = '';

        foreach (array_slice($partes, 0, 2) as $parte) {
            if ($parte !== '') {
                $avatar .= text_first_upper($parte);
            }
        }

        $conversacionesInstructor[] = [
            'id' => (int) $conversacion['id_conversacion'],
            'courseId' => (int) $conversacion['id_conversacion'],
            'participant' => (string) $conversacion['estudiante'],
            'avatar' => $avatar !== '' ? $avatar : 'AG',
            'avatarUrl' => !empty($conversacion['estudiante_avatar'])
                ? url((string) $conversacion['estudiante_avatar'])
                : '',
            'course' => (string) $conversacion['curso'],
            'messages' => $conversacion['mensajes'],
        ];
    }

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
        'id' => 'mensajes',
        'label' => 'Mensajes',
        'icon' => '✉'
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
                                            onsubmit="return confirm('El curso dejará de estar disponible públicamente, pero conservará su información relacionada. ¿Deseas continuar?');"
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
                                                value="dar_baja"
                                            >

                                            <button
                                                class="border border-red-400/30 px-3 py-2 text-xs font-bold text-red-300"
                                            >
                                                Dar de baja
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

    <div class="grid gap-6 xl:grid-cols-[1.35fr_.65fr]">

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

            <input
                type="hidden"
                name="accion"
                value="crear"
            >

            <section class="border border-white/10 p-6">

                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">
                    Información principal
                </p>

                <h2 class="mt-2 font-editorial text-2xl font-semibold">
                    Datos del curso
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
                            placeholder="Ej. Fotografía para principiantes"
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

                            <option value="">
                                Selecciona una categoría
                            </option>

                            <?php foreach ($categorias as $categoria): ?>

                                <option
                                    value="<?= e($categoria['id_categoria']) ?>"
                                >
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
                            placeholder="Explica qué aprenderá el estudiante."
                        ></textarea>

                    </label>

                </div>

            </section>

            <div class="flex flex-wrap justify-end gap-3">

                <button
                    type="submit"
                    name="estado"
                    value="borrador"
                    class="border border-white/10 px-4 py-3 text-xs font-bold"
                >
                    Crear como borrador
                </button>

                <button
                    type="submit"
                    name="estado"
                    value="publicado"
                    class="bg-aula-orange px-5 py-3 text-xs font-extrabold text-aula-bg"
                >
                    Crear y publicar
                </button>

            </div>

        </form>

        <aside class="border border-white/10 p-6">

            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-orange-soft">
                Categorías
            </p>

            <h2 class="mt-2 font-editorial text-2xl font-semibold">
                ¿No existe la categoría?
            </h2>

            <p class="mt-3 text-sm leading-6 text-white/40">
                Puedes crearla aquí. La categoría será global y podrá utilizarse en otros cursos de AulaGo.
            </p>

            <form
                action="<?= e(url('actions/instructor_cursos.php')) ?>"
                method="post"
                class="mt-6 space-y-4"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <input
                    type="hidden"
                    name="accion"
                    value="crear_categoria"
                >

                <label class="grid gap-2">

                    <span class="text-xs font-bold text-white/55">
                        Nombre
                    </span>

                    <input
                        name="nombre"
                        maxlength="100"
                        required
                        placeholder="Ej. Fotografía"
                        class="min-h-11 border border-white/10 bg-aula-bg px-3"
                    >

                </label>

                <label class="grid gap-2">

                    <span class="text-xs font-bold text-white/55">
                        Descripción
                    </span>

                    <textarea
                        name="descripcion"
                        class="min-h-24 border border-white/10 bg-aula-bg p-3"
                        placeholder="Descripción opcional"
                    ></textarea>

                </label>

                <button
                    type="submit"
                    class="w-full border border-aula-green/30 px-4 py-3 text-xs font-bold text-aula-green"
                >
                    + Crear categoría
                </button>

            </form>

            <?php if ($categorias !== []): ?>

                <div class="mt-7 border-t border-white/10 pt-5">

                    <span class="text-[10px] font-bold uppercase text-white/30">
                        Disponibles
                    </span>

                    <div class="mt-3 flex flex-wrap gap-2">

                        <?php foreach ($categorias as $categoria): ?>

                            <span class="border border-white/10 px-3 py-2 text-xs text-white/50">
                                <?= e($categoria['nombre']) ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>

        </aside>

    </div>

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

            <?php endforeach; ?>

<!-- CONTENIDO -->

<?php foreach ($cursos as $curso): ?>
<section
    data-workspace-panel="contenido-<?= e($curso['id_curso']) ?>"
    data-title="Contenido"
    data-eyebrow="<?= e($curso['titulo']) ?>"
    hidden
>
    <div class="mb-6 border border-white/10 p-5">
        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">Contenido del curso</p>
        <h2 class="mt-2 font-editorial text-3xl font-semibold"><?= e($curso['titulo']) ?></h2>
        <p class="mt-2 text-sm text-white/35">Organiza el curso por niveles. Dentro de cada nivel agrega las lecciones que completará el estudiante.</p>
    </div>

    <?php if ($contenidos[(int) $curso['id_curso']] === []): ?>
        <form action="<?= e(url('actions/instructor_cursos.php')) ?>" method="post" class="border border-white/10 p-6">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="accion" value="crear_nivel">
            <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
            <h3 class="font-editorial text-2xl font-semibold">Crear primer nivel</h3>
            <p class="mt-1 text-xs text-white/35">Este será el primer módulo del curso. Después podrás agregar sus lecciones aquí mismo.</p>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="grid gap-2"><span class="text-xs text-white/45">Título</span><input name="titulo" maxlength="150" required placeholder="Ej. Fundamentos" class="min-h-11 border border-white/10 bg-aula-bg px-3"></label>
                <label class="grid gap-2"><span class="text-xs text-white/45">Descripción</span><input name="descripcion" placeholder="Qué aprenderá el estudiante" class="min-h-11 border border-white/10 bg-aula-bg px-3"></label>
            </div>
            <button type="submit" class="mt-5 bg-aula-green px-5 py-3 text-xs font-extrabold text-aula-bg">Crear nivel →</button>
        </form>
    <?php else: ?>
        <?php foreach ($contenidos[(int) $curso['id_curso']] as $nivel): ?>
            <article class="mb-6 border border-white/10">
                <form action="<?= e(url('actions/instructor_cursos.php')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="accion" value="guardar_contenido">
                    <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
                    <header class="border-b border-white/10 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div><span class="text-[10px] font-bold uppercase text-aula-orange-soft">Nivel <?= e($nivel['orden']) ?></span><h3 class="mt-1 font-editorial text-2xl font-semibold"><?= e($nivel['titulo']) ?></h3></div>
                            <button type="submit" name="accion_secundaria" value="eliminar_nivel:<?= e($nivel['id_nivel']) ?>" formnovalidate onclick="return confirm('Se eliminarán este nivel, sus lecciones y el progreso relacionado. ¿Continuar?');" class="text-xs font-bold text-red-300">Eliminar nivel</button>
                        </div>
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <label class="grid gap-2"><span class="text-xs text-white/40">Título del nivel</span><input name="niveles[<?= e($nivel['id_nivel']) ?>][titulo]" value="<?= e($nivel['titulo']) ?>" maxlength="150" required class="min-h-11 border border-white/10 bg-aula-bg px-3"></label>
                            <label class="grid gap-2"><span class="text-xs text-white/40">Descripción</span><input name="niveles[<?= e($nivel['id_nivel']) ?>][descripcion]" value="<?= e($nivel['descripcion'] ?? '') ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3"></label>
                        </div>
                    </header>
                    <div class="p-5">
                        <p class="text-[10px] font-bold uppercase text-aula-green">Lecciones · <?= e(count($nivel['lecciones'])) ?></p>
                        <?php if ($nivel['lecciones'] === []): ?><p class="mt-3 border border-dashed border-white/10 p-4 text-sm text-white/30">Todavía no hay lecciones en este nivel.</p><?php endif; ?>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($nivel['lecciones'] as $leccion): ?>
                                <section class="border border-white/10 p-4">
                                    <div class="flex justify-between gap-4"><strong class="text-xs text-aula-green">Lección <?= e($leccion['orden']) ?></strong><button type="submit" name="accion_secundaria" value="eliminar_leccion:<?= e($leccion['id_leccion']) ?>" formnovalidate onclick="return confirm('¿Eliminar esta lección y su progreso relacionado?');" class="text-xs text-red-300">Eliminar</button></div>
                                    <div class="mt-3 grid gap-3">
                                        <input name="lecciones[<?= e($leccion['id_leccion']) ?>][titulo]" value="<?= e($leccion['titulo']) ?>" maxlength="200" required class="min-h-11 border border-white/10 bg-aula-bg px-3">
                                        <textarea name="lecciones[<?= e($leccion['id_leccion']) ?>][contenido]" placeholder="Contenido de la lección" class="min-h-24 border border-white/10 bg-aula-bg p-3"><?= e($leccion['contenido'] ?? '') ?></textarea>
                                        <input type="url" name="lecciones[<?= e($leccion['id_leccion']) ?>][url_video]" value="<?= e($leccion['url_video'] ?? '') ?>" placeholder="URL del video (opcional)" class="min-h-11 border border-white/10 bg-aula-bg px-3">
                                    </div>
                                </section>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="mt-4 border border-aula-green/30 px-4 py-2 text-xs font-bold text-aula-green">Guardar cambios del nivel</button>
                    </div>
                </form>

                <form action="<?= e(url('actions/instructor_cursos.php')) ?>" method="post" class="border-t border-white/10 bg-white/[0.015] p-5">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="accion" value="crear_leccion">
                    <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
                    <input type="hidden" name="id_nivel" value="<?= e($nivel['id_nivel']) ?>">
                    <h4 class="font-editorial text-xl font-semibold">Agregar lección al nivel <?= e($nivel['orden']) ?></h4>
                    <div class="mt-4 grid gap-3">
                        <input name="titulo" maxlength="200" required placeholder="Título de la lección" class="min-h-11 border border-white/10 bg-aula-bg px-3">
                        <textarea name="contenido" placeholder="Contenido que verá el estudiante" class="min-h-24 border border-white/10 bg-aula-bg p-3"></textarea>
                        <input type="url" name="url_video" placeholder="URL del video (opcional)" class="min-h-11 border border-white/10 bg-aula-bg px-3">
                    </div>
                    <button type="submit" class="mt-4 text-xs font-bold text-aula-green">+ Agregar lección</button>
                </form>
            </article>
        <?php endforeach; ?>

        <form action="<?= e(url('actions/instructor_cursos.php')) ?>" method="post" class="mt-8 border border-dashed border-white/10 p-5">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="accion" value="crear_nivel">
            <input type="hidden" name="id_curso" value="<?= e($curso['id_curso']) ?>">
            <h3 class="font-editorial text-xl font-semibold">Agregar otro nivel</h3>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <input name="titulo" maxlength="150" required placeholder="Título del nuevo nivel" class="min-h-11 border border-white/10 bg-aula-bg px-3">
                <input name="descripcion" placeholder="Descripción" class="min-h-11 border border-white/10 bg-aula-bg px-3">
            </div>
            <button type="submit" class="mt-4 text-xs font-bold text-aula-orange-soft">+ Crear otro nivel</button>
        </form>
    <?php endif; ?>
</section>
<?php endforeach; ?>

            <!-- MENSAJES -->

            <section
                data-workspace-panel="mensajes"
                data-title="Mensajes"
                data-eyebrow="Conversaciones con estudiantes"
                hidden
                data-messages-panel
            >
                <?php if ($mensajeConversacion): ?>
                    <div class="mb-5 border border-aula-green/25 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">
                        <?= e($mensajeConversacion) ?>
                    </div>
                <?php endif; ?>

                <?php if ($conversacionesInstructor === []): ?>
                    <div class="border border-dashed border-white/10 p-8 text-center">
                        <p class="text-sm text-white/40">
                            Todavía no tienes conversaciones.
                        </p>
                        <p class="mt-2 text-xs text-white/25">
                            Cuando un estudiante inscrito te escriba, la conversación aparecerá aquí.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="workspace-message-shell grid min-h-0 border border-white/10 lg:grid-cols-[270px_minmax(0,1fr)]">
                        <aside class="conversation-list min-h-0 border-b border-white/10 bg-white/[0.015] lg:border-b-0 lg:border-r">
                            <div class="border-b border-white/10 p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.12em] text-white/35">
                                    Conversaciones
                                </p>
                                <span class="mt-1 block text-[10px] text-white/25">
                                    Estudiantes de tus cursos
                                </span>
                            </div>

                            <div class="conversation-list-scroll lg:overflow-y-auto">
                                <?php foreach ($conversacionesInstructor as $indice => $conversacion): ?>
                                    <?php $ultimoMensaje = $conversacion['messages'][count($conversacion['messages']) - 1] ?? null; ?>
                                    <button
                                        type="button"
                                        class="<?= $indice === 0 ? 'is-selected ' : '' ?>conversation-button flex w-full items-center gap-3 border-b border-white/10 p-4 text-left transition hover:bg-white/[0.025]"
                                        data-conversation-button
                                        data-conversation-id="<?= e($conversacion['id']) ?>"
                                    >
                                        <?php if ($conversacion['avatarUrl'] !== ''): ?>
                                            <img src="<?= e($conversacion['avatarUrl']) ?>" alt="Foto de <?= e($conversacion['participant']) ?>" class="h-10 w-10 shrink-0 rounded-full border border-white/10 object-cover">
                                        <?php else: ?>
                                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-aula-green"><?= e($conversacion['avatar']) ?></span>
                                        <?php endif; ?>
                                        <span class="min-w-0">
                                            <span class="flex items-center justify-between gap-2">
                                                <strong class="truncate text-sm"><?= e($conversacion['participant']) ?></strong>
                                                <small class="shrink-0 text-[9px] text-white/25"><?= e($ultimoMensaje['time'] ?? '') ?></small>
                                            </span>
                                            <span class="mt-1 block truncate text-xs text-white/35"><?= e($ultimoMensaje['text'] ?? $conversacion['course']) ?></span>
                                            <span class="mt-1 block truncate text-[10px] text-aula-green/70"><?= e($conversacion['course']) ?></span>
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </aside>

                        <?php $conversacionActiva = $conversacionesInstructor[0]; ?>

                        <section class="flex min-h-0 min-w-0 flex-col" data-message-thread>
                            <header class="flex items-center gap-3 border-b border-white/10 p-4 sm:p-5">
                                <?php if ($conversacionActiva['avatarUrl'] !== ''): ?>
                                    <img src="<?= e($conversacionActiva['avatarUrl']) ?>" alt="Foto de perfil" class="h-10 w-10 rounded-full border border-white/10 object-cover" data-thread-avatar-image>
                                    <span class="hidden" data-thread-avatar><?= e($conversacionActiva['avatar']) ?></span>
                                <?php else: ?>
                                    <span class="grid h-10 w-10 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold text-aula-green" data-thread-avatar><?= e($conversacionActiva['avatar']) ?></span>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <strong class="block truncate text-sm" data-thread-name><?= e($conversacionActiva['participant']) ?></strong>
                                    <span class="mt-1 block truncate text-xs text-white/35" data-thread-course><?= e($conversacionActiva['course']) ?></span>
                                </div>
                            </header>

                            <div class="message-stream min-h-0 flex-1 space-y-4 overflow-y-auto p-4 sm:p-5" data-message-stream></div>

                            <form
                                action="<?= e(url('actions/instructor_mensaje.php')) ?>"
                                method="post"
                                class="grid gap-3 border-t border-white/10 p-4 sm:grid-cols-[1fr_auto] sm:p-5"
                                data-message-form
                            >
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id_conversacion" value="<?= e($conversacionActiva['id']) ?>" data-message-course-id>
                                <input
                                    name="contenido"
                                    maxlength="2000"
                                    required
                                    class="min-h-11 min-w-0 border border-white/10 bg-aula-bg px-4 text-sm text-white outline-none placeholder:text-white/25 focus:border-aula-orange"
                                    placeholder="Escribe una respuesta"
                                >
                                <button type="submit" class="min-h-11 bg-aula-orange px-6 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">
                                    Enviar
                                </button>
                            </form>
                        </section>
                    </div>

                    <script type="application/json" data-conversation-data><?= json_encode($conversacionesInstructor, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
                <?php endif; ?>

            </section>

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
                <div class="grid gap-6 xl:grid-cols-[.75fr_1.25fr]">
                    <aside class="border border-white/10 bg-white/[0.02] p-6">
                        <?php if (user_avatar_url() !== ''): ?>
                            <img src="<?= e(user_avatar_url()) ?>" alt="Foto de perfil de <?= e(user_name()) ?>" class="h-24 w-24 rounded-full border border-white/10 object-cover">
                        <?php else: ?>
                            <span class="grid h-24 w-24 place-items-center rounded-full bg-aula-green-dark text-xl font-bold text-aula-green"><?= e(user_initials()) ?></span>
                        <?php endif; ?>

                        <h2 class="mt-5 font-editorial text-3xl"><?= e(user_name()) ?></h2>
                        <p class="mt-2 text-sm text-white/35"><?= e(user_email()) ?></p>
                        <p class="mt-6 text-xs font-bold uppercase tracking-[0.12em] text-aula-green">Instructor</p>
                        <p class="mt-5 border-t border-white/10 pt-5 text-xs leading-6 text-white/35">Estos datos identifican tu perfil de instructor dentro de AulaGo.</p>
                    </aside>

                    <form action="<?= e(url('actions/perfil.php')) ?>" method="post" enctype="multipart/form-data" class="border border-white/10 p-6">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <h2 class="font-editorial text-2xl font-semibold">Datos de perfil</h2>

                        <?php if ($perfilMensaje): ?>
                            <p class="mt-5 border px-4 py-3 text-sm <?= $perfilMensajeTipo === 'exito' ? 'border-aula-green/25 bg-aula-green/10 text-aula-green' : 'border-red-400/25 bg-red-400/10 text-red-200' ?>"><?= e($perfilMensaje) ?></p>
                        <?php endif; ?>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <label class="grid gap-2">
                                <span class="text-xs font-bold text-white/55">Nombre</span>
                                <input type="text" name="nombre" required maxlength="150" value="<?= e($perfilUsuario['nombre'] ?? user_name()) ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
                            </label>
                            <label class="grid gap-2">
                                <span class="text-xs font-bold text-white/55">Correo</span>
                                <input type="email" name="email" required maxlength="255" value="<?= e($perfilUsuario['email'] ?? user_email()) ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
                            </label>
                            <label class="grid gap-2">
                                <span class="text-xs font-bold text-white/55">Fecha de nacimiento</span>
                                <input type="date" name="fecha_nacimiento" value="<?= e($perfilUsuario['fecha_nacimiento'] ?? '') ?>" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
                            </label>
                            <label class="grid gap-2">
                                <span class="text-xs font-bold text-white/55">Género</span>
                                <?php $generoActual = (string) ($perfilUsuario['genero'] ?? ''); ?>
                                <select name="genero" class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange">
                                    <option value="">Sin especificar</option>
                                    <option value="mujer" <?= $generoActual === 'mujer' ? 'selected' : '' ?>>Mujer</option>
                                    <option value="hombre" <?= $generoActual === 'hombre' ? 'selected' : '' ?>>Hombre</option>
                                    <option value="otro" <?= $generoActual === 'otro' ? 'selected' : '' ?>>Otro</option>
                                    <option value="prefiero_no_decir" <?= $generoActual === 'prefiero_no_decir' ? 'selected' : '' ?>>Prefiero no decirlo</option>
                                </select>
                            </label>
                            <label class="grid gap-2 sm:col-span-2">
                                <span class="text-xs font-bold text-white/55">Foto de perfil</span>
                                <input type="file" name="avatar" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="min-h-11 border border-white/10 bg-aula-bg px-3 py-2 text-sm text-white file:mr-4 file:border-0 file:bg-aula-green-dark file:px-3 file:py-1 file:text-xs file:font-bold file:text-aula-green">
                                <small class="text-xs text-white/30">JPG o PNG, máximo 3 MB. Déjalo vacío para conservar tu foto actual.</small>
                            </label>
                        </div>

                        <button type="submit" class="mt-6 min-h-11 bg-aula-orange px-6 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Guardar cambios</button>
                    </form>
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

            <?php if (user_avatar_url() !== ''): ?>
                <img src="<?= e(user_avatar_url()) ?>" alt="Foto de perfil" class="h-16 w-16 rounded-full border border-white/10 object-cover">
            <?php else: ?>
                <span class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark font-bold text-aula-green">
                    <?= e(user_initials()) ?>
                </span>
            <?php endif; ?>

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