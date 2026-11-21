<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

require_role('administrador');

require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/AdminUsuario.php';
require_once PROJECT_ROOT . '/models/AdminCategoria.php';
require_once PROJECT_ROOT . '/models/AdminDashboard.php';

$mensajeUsuarios =
    $_SESSION['admin_usuarios_mensaje'] ?? null;

unset($_SESSION['admin_usuarios_mensaje']);

$mensajeCategorias =
    $_SESSION['admin_categorias_mensaje'] ?? null;

unset($_SESSION['admin_categorias_mensaje']);

$pageTitle = 'Administración | AulaGo';
$pageDescription = 'Panel administrativo de AulaGo.';
$pageScripts = ['workspace.js'];

$bodyClass =
    'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased lg:overflow-hidden';

try {
    $configuracion = require PROJECT_ROOT
        . '/config/database.local.php';

    $conexion = new Conexion($configuracion);
    $pdo = $conexion->obtenerConexion();

    $modeloUsuarios = new AdminUsuario($pdo);
    $modeloCategorias = new AdminCategoria($pdo);
    $modeloDashboard = new AdminDashboard($pdo);

    $adminUsers = $modeloUsuarios->listar();
    $adminCategories = $modeloCategorias->listar();

    $metricas = $modeloDashboard->obtenerMetricas();
    $pagos = $modeloDashboard->listarPagos();
    $resenas = $modeloDashboard->listarResenas();

} catch (Throwable $error) {
    error_log($error->getMessage());

    http_response_code(500);

    exit(
        'No fue posible cargar el panel administrativo.'
    );
}

$workspaceRoleLabel = 'Workspace administrativo';

$workspaceItems = [
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'icon' => '⌂'
    ],
    [
        'id' => 'categorias',
        'label' => 'Categorías',
        'icon' => '▦'
    ],
    [
        'id' => 'usuarios',
        'label' => 'Usuarios',
        'icon' => '◎'
    ],
    [
        'id' => 'pagos',
        'label' => 'Pagos',
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

        <header class="border-b border-white/10 bg-aula-bg/95 px-5 py-5 backdrop-blur md:px-7 lg:px-8">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <p
                        class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green"
                        data-workspace-eyebrow
                    >
                        Estado general
                    </p>

                    <h1
                        class="mt-2 font-editorial text-3xl font-semibold sm:text-4xl"
                        data-workspace-title
                    >
                        Dashboard
                    </h1>

                </div>

                <a
                    href="<?= e(url()) ?>"
                    class="inline-flex min-h-10 items-center border border-white/10 px-4 text-xs font-bold text-white/45 transition hover:border-aula-orange/30 hover:text-aula-orange-soft"
                >
                    Ver sitio público ↗
                </a>

            </div>

        </header>

        <main class="workspace-main min-h-0 flex-1 overflow-y-auto px-5 py-6 md:px-7 lg:px-8 lg:py-7">

            <!-- DASHBOARD -->

            <section
                data-workspace-panel="dashboard"
                data-title="Dashboard"
                data-eyebrow="Estado general"
            >

                <div class="admin-dashboard-metrics">

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Usuarios
                        </span>

                        <strong class="mt-2 block text-2xl">
                            <?= e($metricas['usuarios']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            Cuentas registradas
                        </small>

                    </article>

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Categorías
                        </span>

                        <strong class="mt-2 block text-2xl text-aula-green">
                            <?= e($metricas['categorias']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            <?= e($metricas['categorias_activas']) ?> activas
                        </small>

                    </article>

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Cursos
                        </span>

                        <strong class="mt-2 block text-2xl text-[#9fcbd5]">
                            <?= e($metricas['cursos']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            Cursos registrados
                        </small>

                    </article>

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Inscripciones
                        </span>

                        <strong class="mt-2 block text-2xl">
                            <?= e($metricas['inscripciones']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            Registros de estudiantes
                        </small>

                    </article>

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Pagos
                        </span>

                        <strong class="mt-2 block text-2xl text-aula-orange-soft">
                            <?= e($metricas['pagos']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            Operaciones registradas
                        </small>

                    </article>

                    <article class="border border-white/10 bg-white/[0.02] p-4">

                        <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">
                            Reseñas
                        </span>

                        <strong class="mt-2 block text-2xl text-aula-yellow">
                            <?= e($metricas['resenas']) ?>
                        </strong>

                        <small class="mt-1 block text-[10px] text-white/30">
                            Opiniones registradas
                        </small>

                    </article>

                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-2">

                    <section class="border border-white/10 p-5">

                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">
                            Catálogo
                        </p>

                        <h2 class="mt-2 font-editorial text-2xl font-semibold">
                            Categorías
                        </h2>

                        <div class="mt-5 divide-y divide-white/10">

                            <?php if ($adminCategories === []): ?>

                                <p class="py-5 text-sm text-white/35">
                                    No hay categorías registradas.
                                </p>

                            <?php else: ?>

                                <?php foreach (array_slice($adminCategories, 0, 5) as $categoria): ?>

                                    <div class="flex items-center justify-between gap-4 py-3">

                                        <div>

                                            <strong class="block text-sm text-white/75">
                                                <?= e($categoria['nombre']) ?>
                                            </strong>

                                            <span class="text-xs text-white/30">
                                                <?= e($categoria['total_cursos']) ?>
                                                cursos
                                            </span>

                                        </div>

                                        <span class="text-xs <?= (int) $categoria['activo'] === 1 ? 'text-aula-green' : 'text-white/30' ?>">
                                            <?= (int) $categoria['activo'] === 1 ? 'Activa' : 'Inactiva' ?>
                                        </span>

                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                        <a
                            href="#categorias"
                            data-workspace-link="categorias"
                            class="mt-5 inline-block text-xs font-bold text-aula-orange-soft"
                        >
                            Administrar categorías →
                        </a>

                    </section>


                </div>

            </section>

            <!-- CATEGORÍAS -->

            <section
                data-workspace-panel="categorias"
                data-title="Categorías"
                data-eyebrow="Administración del catálogo"
                hidden
            >

                <?php if ($mensajeCategorias): ?>

                    <div class="mb-6 border border-aula-green/25 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">
                        <?= e($mensajeCategorias) ?>
                    </div>

                <?php endif; ?>

                <form
                    action="<?= e(url('actions/admin_categorias.php')) ?>"
                    method="post"
                    class="border border-white/10 bg-white/[0.02] p-5"
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

                    <h2 class="font-editorial text-2xl font-semibold">
                        Nueva categoría
                    </h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">

                        <label class="grid gap-2">

                            <span class="text-xs font-bold text-white/55">
                                Nombre
                            </span>

                            <input
                                type="text"
                                name="nombre"
                                maxlength="100"
                                required
                                class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"
                            >

                        </label>

                        <label class="grid gap-2">

                            <span class="text-xs font-bold text-white/55">
                                Descripción
                            </span>

                            <input
                                type="text"
                                name="descripcion"
                                class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"
                            >

                        </label>

                    </div>

                    <button
                        type="submit"
                        class="mt-5 min-h-10 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg"
                    >
                        Crear categoría
                    </button>

                </form>

                <div class="mt-7 overflow-x-auto border border-white/10">

                    <table class="min-w-full text-left text-sm">

                        <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">

                            <tr>
                                <th class="px-5 py-4">Categoría</th>
                                <th class="px-5 py-4">Slug</th>
                                <th class="px-5 py-4">Cursos</th>
                                <th class="px-5 py-4">Estado</th>
                                <th class="px-5 py-4">Acciones</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            <?php if ($adminCategories === []): ?>

                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-5 py-8 text-center text-white/35"
                                    >
                                        No hay categorías registradas.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            <?php foreach ($adminCategories as $categoria): ?>

                                <tr>

                                    <td class="px-5 py-4">

                                        <strong class="block text-white/75">
                                            <?= e($categoria['nombre']) ?>
                                        </strong>

                                        <?php if (!empty($categoria['descripcion'])): ?>

                                            <span class="mt-1 block max-w-xs text-xs text-white/30">
                                                <?= e($categoria['descripcion']) ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="px-5 py-4 text-xs text-white/35">
                                        <?= e($categoria['slug']) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= e($categoria['total_cursos']) ?>
                                    </td>

                                    <td class="px-5 py-4">

                                        <?php if ((int) $categoria['activo'] === 1): ?>

                                            <span class="text-xs font-bold text-aula-green">
                                                Activa
                                            </span>

                                        <?php else: ?>

                                            <span class="text-xs font-bold text-white/30">
                                                Inactiva
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="px-5 py-4">

                                        <div class="flex flex-wrap gap-2">

                                            <form
                                                action="<?= e(url('actions/admin_categorias.php')) ?>"
                                                method="post"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e(csrf_token()) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id_categoria"
                                                    value="<?= e($categoria['id_categoria']) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="<?= (int) $categoria['activo'] === 1 ? 'desactivar' : 'activar' ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="min-h-9 border border-white/10 px-3 text-xs font-bold text-white/50"
                                                >
                                                    <?= (int) $categoria['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                                                </button>

                                            </form>

                                            <form
                                                action="<?= e(url('actions/admin_categorias.php')) ?>"
                                                method="post"
                                                onsubmit="return confirm('Esta acción eliminará la categoría y toda la información relacionada con sus cursos. ¿Deseas continuar?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e(csrf_token()) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id_categoria"
                                                    value="<?= e($categoria['id_categoria']) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="eliminar"
                                                >

                                                <button
                                                    type="submit"
                                                    class="min-h-9 border border-red-400/25 px-3 text-xs font-bold text-red-300"
                                                >
                                                    Eliminar
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <!-- USUARIOS -->

            <section
                data-workspace-panel="usuarios"
                data-title="Usuarios"
                data-eyebrow="Control de cuentas"
                hidden
            >

                <?php if ($mensajeUsuarios): ?>

                    <div class="mb-6 border border-aula-green/25 bg-aula-green/10 px-4 py-3 text-sm text-aula-green">
                        <?= e($mensajeUsuarios) ?>
                    </div>

                <?php endif; ?>

                <div class="overflow-x-auto border border-white/10">

                    <table class="min-w-full text-left text-sm">

                        <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">

                            <tr>
                                <th class="px-5 py-4">Nombre</th>
                                <th class="px-5 py-4">Correo</th>
                                <th class="px-5 py-4">Rol</th>
                                <th class="px-5 py-4">Estado</th>
                                <th class="px-5 py-4">Acción</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            <?php foreach ($adminUsers as $usuario): ?>

                                <tr>

                                    <td class="px-5 py-4 font-bold text-white/75">
                                        <?= e($usuario['name']) ?>
                                    </td>

                                    <td class="px-5 py-4 text-white/40">
                                        <?= e($usuario['email']) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= e(ucfirst($usuario['role'])) ?>
                                    </td>

                                    <td class="px-5 py-4 text-xs">
                                        <?= e($usuario['status']) ?>
                                    </td>

                                    <td class="px-5 py-4">

                                        <?php if (in_array(
                                            $usuario['role'],
                                            ['estudiante', 'instructor'],
                                            true
                                        )): ?>

                                            <form
                                                action="<?= e(url('actions/admin_usuarios.php')) ?>"
                                                method="post"
                                                onsubmit="return confirm('¿Deseas cambiar el estado de esta cuenta?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e(csrf_token()) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id_usuario"
                                                    value="<?= e($usuario['id_usuario']) ?>"
                                                >

                                                <?php
                                                $estaBloqueado =
                                                    $usuario['status'] !== 'Activo';
                                                ?>

                                                <input
                                                    type="hidden"
                                                    name="accion"
                                                    value="<?= $estaBloqueado ? 'desbloquear' : 'bloquear' ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="min-h-9 border border-white/10 px-3 text-xs font-bold text-white/50"
                                                >
                                                    <?= $estaBloqueado ? 'Desbloquear' : 'Bloquear' ?>
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-xs text-white/25">
                                                Sin acciones
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <!-- PAGOS -->

            <section
                data-workspace-panel="pagos"
                data-title="Pagos"
                data-eyebrow="Operaciones registradas"
                hidden
            >

                <div class="overflow-x-auto border border-white/10">

                    <table class="min-w-full text-left text-sm">

                        <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">

                            <tr>
                                <th class="px-5 py-4">Referencia</th>
                                <th class="px-5 py-4">Curso</th>
                                <th class="px-5 py-4">Estudiante</th>
                                <th class="px-5 py-4">Método</th>
                                <th class="px-5 py-4">Estado</th>
                                <th class="px-5 py-4">Monto</th>
                                <th class="px-5 py-4">Fecha</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            <?php if ($pagos === []): ?>

                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-5 py-8 text-center text-white/35"
                                    >
                                        No hay pagos registrados.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            <?php foreach ($pagos as $pago): ?>

                                <tr>

                                    <td class="px-5 py-4 text-xs text-white/35">
                                        <?= e($pago['referencia_externa'] ?: '—') ?>
                                    </td>

                                    <td class="px-5 py-4 font-bold text-white/70">
                                        <?= e($pago['curso']) ?>
                                    </td>

                                    <td class="px-5 py-4 text-white/45">
                                        <?= e($pago['estudiante']) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= e($pago['metodo']) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= e(ucfirst($pago['estado'])) ?>
                                    </td>

                                    <td class="px-5 py-4 font-bold text-aula-orange-soft">
                                        $<?= e(number_format((float) $pago['monto'], 2)) ?>
                                        <?= e($pago['moneda']) ?>
                                    </td>

                                    <td class="px-5 py-4 text-white/40">
                                        <?= e(format_date_es($pago['fecha_pago'])) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <!-- RESEÑAS -->

            <section
                data-workspace-panel="resenas"
                data-title="Reseñas"
                data-eyebrow="Opiniones de estudiantes"
                hidden
            >

                <div class="space-y-4">

                    <?php if ($resenas === []): ?>

                        <div class="border border-white/10 p-6 text-sm text-white/35">
                            No hay reseñas registradas.
                        </div>

                    <?php endif; ?>

                    <?php foreach ($resenas as $resena): ?>

                        <article class="border border-white/10 bg-white/[0.015] p-5">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                <div>

                                    <strong class="text-sm text-white/80">
                                        <?= e($resena['estudiante']) ?>
                                    </strong>

                                    <p class="mt-1 text-xs text-white/30">
                                        <?= e($resena['curso']) ?>
                                        ·
                                        <?= e(format_date_es($resena['fecha_creacion'])) ?>
                                    </p>

                                </div>

                                <div class="text-right">

                                    <span class="text-aula-yellow">
                                        <?= e(str_repeat('★', (int) $resena['calificacion'])) ?>
                                    </span>

                                    <span class="mt-1 block text-[10px] uppercase text-white/35">
                                        <?= e($resena['estado']) ?>
                                    </span>

                                </div>

                            </div>

                            <?php if (!empty($resena['comentario'])): ?>

                                <p class="mt-4 text-sm leading-7 text-white/50">
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
                data-eyebrow="Perfil administrativo"
                hidden
            >

                <div class="max-w-xl border border-white/10 bg-white/[0.02] p-6">

                    <span class="grid h-20 w-20 place-items-center rounded-full bg-aula-green-dark text-xl font-extrabold text-aula-green">
                        <?= e(user_initials()) ?>
                    </span>

                    <h2 class="mt-5 font-editorial text-3xl font-semibold">
                        <?= e(user_name()) ?>
                    </h2>

                    <p class="mt-2 text-sm text-white/35">
                        <?= e(user_email()) ?>
                    </p>

                    <div class="mt-6 border-t border-white/10 pt-5">

                        <span class="text-xs uppercase tracking-[0.12em] text-white/30">
                            Rol
                        </span>

                        <strong class="mt-2 block text-sm text-aula-green">
                            Administrador
                        </strong>

                    </div>

                </div>

            </section>

        </main>

    </section>

    <!-- PANEL DERECHO -->

    <aside class="workspace-context hidden overflow-y-auto bg-[#151116] p-5 lg:block">

        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/30">
            Panel de operaciones
        </p>

        <div class="mt-6 flex items-center gap-4">

            <span class="grid h-16 w-16 place-items-center rounded-full bg-aula-green-dark text-lg font-extrabold text-aula-green">
                <?= e(user_initials()) ?>
            </span>

            <div>

                <strong class="block text-sm text-white/85">
                    <?= e(user_name()) ?>
                </strong>

                <span class="mt-1 block text-xs text-white/30">
                    Administrador · AulaGo
                </span>

            </div>

        </div>

        <div class="mt-8 grid grid-cols-2 gap-px bg-white/10">

            <div class="bg-[#151116] p-4">

                <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
                    Usuarios
                </span>

                <strong class="mt-2 block text-2xl">
                    <?= e($metricas['usuarios']) ?>
                </strong>

            </div>

            <div class="bg-[#151116] p-4">

                <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
                    Categorías
                </span>

                <strong class="mt-2 block text-2xl text-aula-green">
                    <?= e($metricas['categorias']) ?>
                </strong>

            </div>

            <div class="bg-[#151116] p-4">

                <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
                    Cursos
                </span>

                <strong class="mt-2 block text-2xl text-[#9fcbd5]">
                    <?= e($metricas['cursos']) ?>
                </strong>

            </div>

            <div class="bg-[#151116] p-4">

                <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
                    Pagos
                </span>

                <strong class="mt-2 block text-2xl text-aula-orange-soft">
                    <?= e($metricas['pagos']) ?>
                </strong>

            </div>

        </div>

        <section class="mt-8 border-t border-white/10 pt-6">

            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30">
                Datos
            </p>

            <div class="mt-4 space-y-3 text-xs">

                <div class="flex justify-between">

                    <span class="text-white/35">
                        Base de datos
                    </span>

                    <strong class="text-aula-green">
                        Conectada
                    </strong>

                </div>

                <div class="flex justify-between">

                    <span class="text-white/35">
                        Categorías activas
                    </span>

                    <strong>
                        <?= e($metricas['categorias_activas']) ?>
                    </strong>

                </div>

                <div class="flex justify-between">

                    <span class="text-white/35">
                        Inscripciones
                    </span>

                    <strong>
                        <?= e($metricas['inscripciones']) ?>
                    </strong>

                </div>

            </div>

        </section>

    </aside>

</div>

<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>

</body>
</html>