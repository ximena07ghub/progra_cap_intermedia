
<?php
require_once dirname(__DIR__) . '/config/app.php';
require_role('administrador');

// Mensaje enviado por la acción de bloqueo/desbloqueo.
$mensajeUsuarios = $_SESSION['admin_usuarios_mensaje'] ?? null;
unset($_SESSION['admin_usuarios_mensaje']);

$pageTitle = 'Administración | AulaGo';
$pageDescription = 'Workspace de administración de AulaGo.';
$pageScripts = ['workspace.js'];
$bodyClass = 'min-h-screen bg-aula-bg font-sans text-aula-cream antialiased lg:overflow-hidden';

// Los módulos pendientes conservan sus datos actuales.
$portal = portal_data();
$adminCategories = $portal['adminCategories'] ?? [];
$adminComments = $portal['adminComments'] ?? [];

// Los usuarios se consultan realmente desde MySQL.
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/AdminUsuario.php';

try {
    $configuracion = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($configuracion);

    $modeloAdminUsuario = new AdminUsuario(
        $conexion->obtenerConexion()
    );

    $adminUsers = $modeloAdminUsuario->listar();

} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No fue posible cargar los usuarios.');
}

// Información de otras secciones del panel.
$payments = sales_transactions();

$paypalPayments = array_values(array_filter(
    $payments,
    fn(array $payment): bool =>
        ($payment['provider'] ?? '') === 'PayPal'
));

// El modelo real de usuarios puede no incluir reportes.
$tieneDatosReportes = false;
$totalReports = 0;

foreach ($adminUsers as $usuarioReporte) {
    if (array_key_exists('reports', $usuarioReporte)) {
        $tieneDatosReportes = true;
        $totalReports += (int) $usuarioReporte['reports'];
    }
}

$reportedComments = array_values(array_filter(
    $adminComments,
    fn(array $comment): bool =>
        ($comment['status'] ?? '') === 'Reportado'
));

$workspaceRoleLabel = 'Workspace administrativo';

$workspaceItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => '⌂'],
    ['id' => 'categorias', 'label' => 'Categorías', 'icon' => '▦'],
    ['id' => 'usuarios', 'label' => 'Usuarios', 'icon' => '◎'],
    ['id' => 'pagos', 'label' => 'Pagos', 'icon' => '$'],
    ['id' => 'comentarios', 'label' => 'Comentarios', 'icon' => '✉'],
    ['id' => 'reportes', 'label' => 'Reportes', 'icon' => '!'],
    ['id' => 'cuenta', 'label' => 'Mi cuenta', 'icon' => '○'],
];

require PROJECT_ROOT . '/includes/layout/head.php';
?>

<div class="min-h-screen bg-aula-bg text-aula-cream lg:grid lg:h-screen lg:grid-cols-[230px_minmax(0,1fr)_320px] lg:overflow-hidden" data-workspace>

  <?php require PROJECT_ROOT . '/includes/workspace/sidebar.php'; ?>

  <section class="flex min-w-0 flex-col border-white/10 lg:border-r">

    <header class="border-b border-white/10 bg-aula-bg/95 px-5 py-5 backdrop-blur md:px-7 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-aula-green" data-workspace-eyebrow>
            Estado general
          </p>

          <h1 class="mt-2 font-editorial text-3xl font-semibold sm:text-4xl" data-workspace-title>
            Dashboard
          </h1>
        </div>

        <a href="<?= e(url()) ?>"
           class="inline-flex min-h-10 items-center border border-white/10 px-4 text-xs font-bold text-white/45 transition hover:border-aula-orange/30 hover:text-aula-orange-soft">
          Ver sitio público ↗
        </a>

      </div>
    </header>

    <main class="workspace-main min-h-0 flex-1 overflow-y-auto px-5 py-6 md:px-7 lg:px-8 lg:py-7">

      <!-- DASHBOARD -->
      <section data-workspace-panel="dashboard"
               data-title="Dashboard"
               data-eyebrow="Estado general">

        <div class="admin-dashboard-metrics">

          <article class="border border-white/10 bg-white/[0.02] p-4">
            <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Usuarios</span>
            <strong class="mt-2 block text-2xl"><?= e(count($adminUsers)) ?></strong>
            <small class="mt-1 block text-[10px] text-white/30">Cuentas registradas</small>
          </article>

          <article class="border border-white/10 bg-white/[0.02] p-4">
            <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Categorías</span>
            <strong class="mt-2 block text-2xl text-aula-green"><?= e(count($adminCategories)) ?></strong>
            <small class="mt-1 block text-[10px] text-white/30">Catálogo activo</small>
          </article>

          <article class="border border-white/10 bg-white/[0.02] p-4">
            <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Comentarios</span>
            <strong class="mt-2 block text-2xl text-[#9fcbd5]"><?= e(count($adminComments)) ?></strong>
            <small class="mt-1 block text-[10px] text-white/30">Opiniones registradas</small>
          </article>

          <article class="border border-white/10 bg-white/[0.02] p-4">
            <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Pagos</span>
            <strong class="mt-2 block text-2xl text-aula-green"><?= e(count($payments)) ?></strong>
            <small class="mt-1 block text-[10px] text-white/30">
              <?= e(count($paypalPayments)) ?> confirmados por PayPal
            </small>
          </article>

          <article class="border border-white/10 bg-white/[0.02] p-4">
            <span class="text-[9px] uppercase tracking-[0.12em] text-white/30">Reportes</span>
            <strong class="mt-2 block text-2xl text-aula-orange-soft">
              <?= $tieneDatosReportes ? e($totalReports) : '—' ?>
            </strong>
            <small class="mt-1 block text-[10px] text-white/30">
              <?= $tieneDatosReportes ? 'Pendientes de revisar' : 'Datos pendientes de integración' ?>
            </small>
          </article>

        </div>

        <div class="mt-8 grid gap-7 xl:grid-cols-[1.15fr_.85fr]">

          <section>
            <div class="flex items-end justify-between border-b border-white/10 pb-4">

              <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-green">
                  Control del catálogo
                </p>
                <h2 class="mt-2 font-editorial text-3xl font-semibold">
                  Categorías y cursos.
                </h2>
              </div>

              <a href="#categorias"
                 data-workspace-link="categorias"
                 class="text-xs font-bold text-white/40 hover:text-aula-orange">
                Administrar →
              </a>

            </div>

            <div class="divide-y divide-white/10">
              <?php foreach (array_slice($adminCategories, 0, 6) as $category): ?>
                <article class="grid grid-cols-[1fr_auto] items-center gap-4 py-4">

                  <div>
                    <strong class="block text-sm text-white/80">
                      <?= e($category['name']) ?>
                    </strong>
                    <span class="mt-1 block text-xs text-white/30">
                      <?= e($category['courses']) ?> cursos asociados
                    </span>
                  </div>

                  <span class="text-[10px] font-bold uppercase tracking-[0.1em] <?= $category['active'] ? 'text-aula-green' : 'text-white/25' ?>">
                    <?= $category['active'] ? 'Activa' : 'Inactiva' ?>
                  </span>

                </article>
              <?php endforeach; ?>
            </div>

          </section>

          <aside class="border border-white/10 bg-white/[0.02] p-5">

            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-aula-orange-soft">
              Moderación reciente
            </p>

            <div class="mt-4 divide-y divide-white/10">

              <?php foreach ($adminComments as $comment): ?>
                <div class="py-4">

                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <strong class="block text-sm">
                        <?= e($comment['user']) ?>
                      </strong>
                      <span class="mt-1 block text-xs text-white/35">
                        <?= e($comment['course']) ?> · <?= e(format_date_es($comment['date'])) ?>
                      </span>
                    </div>

                    <span class="text-[10px] font-bold uppercase tracking-[0.08em] <?= $comment['status'] === 'Reportado' ? 'text-aula-orange-soft' : 'text-aula-green' ?>">
                      <?= e($comment['status']) ?>
                    </span>
                  </div>

                  <p class="mt-2 line-clamp-2 text-xs leading-5 text-white/35">
                    <?= e($comment['text']) ?>
                  </p>

                </div>
              <?php endforeach; ?>

            </div>

            <a href="#comentarios"
               data-workspace-link="comentarios"
               class="mt-5 inline-block text-xs font-bold uppercase tracking-[0.08em] text-aula-orange">
              Abrir moderación →
            </a>

          </aside>
        </div>
      </section>

      <!-- CATEGORÍAS -->
      <section data-workspace-panel="categorias"
               data-title="Categorías"
               data-eyebrow="Administración del catálogo"
               hidden>

        <div class="flex flex-col gap-4 border-b border-white/10 pb-5 sm:flex-row sm:items-end sm:justify-between">
          <p class="max-w-2xl text-sm leading-7 text-white/45">
            Crea, activa o revisa categorías. La tabla ya está preparada para que
            las operaciones se conecten a MySQL mediante acciones PHP.
          </p>

          <button type="button" class="min-h-10 bg-aula-orange px-4 text-xs font-extrabold text-aula-bg">
            + Nueva categoría
          </button>
        </div>

        <div class="mt-6 overflow-x-auto border border-white/10">
          <table class="min-w-full text-left text-sm">

            <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">
              <tr>
                <th class="px-5 py-4">Categoría</th>
                <th class="px-5 py-4">Slug</th>
                <th class="px-5 py-4">Cursos</th>
                <th class="px-5 py-4">Estado</th>
                <th class="px-5 py-4">Acción</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-white/10">
              <?php foreach ($adminCategories as $category): ?>
                <tr>
                  <td class="px-5 py-4 font-bold text-white/80">
                    <?= e($category['name']) ?>
                  </td>
                  <td class="px-5 py-4 text-white/35">
                    <?= e($category['slug']) ?>
                  </td>
                  <td class="px-5 py-4 text-white/45">
                    <?= e($category['courses']) ?>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-xs <?= $category['active'] ? 'text-aula-green' : 'text-white/30' ?>">
                      <?= $category['active'] ? 'Activa' : 'Inactiva' ?>
                    </span>
                  </td>
                  <td class="px-5 py-4">
                    <button type="button" class="text-xs font-bold text-aula-orange-soft">
                      Editar →
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
        </div>
      </section>

      <!-- USUARIOS: INTEGRACIÓN CON MYSQL -->
      <section data-workspace-panel="usuarios"
               data-title="Usuarios"
               data-eyebrow="Cuentas y permisos"
               hidden>

        <div class="mb-6 max-w-2xl">
          <p class="text-sm leading-7 text-white/45">
            Consulta las cuentas registradas y administra sus permisos de acceso.
          </p>
        </div>

        <!-- Mensaje de resultado enviado por el backend -->
        <?php if (is_string($mensajeUsuarios) && $mensajeUsuarios !== ''): ?>
          <p role="status"
             aria-live="polite"
             class="mb-5 border border-white/10 bg-white/[0.025] px-5 py-4 text-sm text-white/75">
            <?= e($mensajeUsuarios) ?>
          </p>
        <?php endif; ?>

        <div class="overflow-x-auto border border-white/10">

          <table class="min-w-full text-left text-sm">

            <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">
              <tr>
                <th scope="col" class="px-5 py-4">Usuario</th>
                <th scope="col" class="px-5 py-4">Rol</th>
                <th scope="col" class="px-5 py-4">Estado</th>
                <th scope="col" class="px-5 py-4">Reportes</th>
                <th scope="col" class="px-5 py-4">Acción</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-white/10">

              <?php if (empty($adminUsers)): ?>

                <tr>
                  <td colspan="5" class="px-5 py-8 text-center text-sm text-white/40">
                    No hay usuarios registrados para mostrar.
                  </td>
                </tr>

              <?php else: ?>

                <?php foreach ($adminUsers as $user): ?>

                  <?php
                  $rol = (string) ($user['role'] ?? '');
                  $estado = trim((string) ($user['status'] ?? ''));

                  // Solamente estudiantes e instructores pueden administrarse.
                  $rolAdministrable = in_array(
                      $rol,
                      ['estudiante', 'instructor'],
                      true
                  );

                  // Estados esperados en el contrato actual.
                  $estadoReconocido = in_array(
                      $estado,
                      [
                          'Activo',
                          'Bloqueado por intentos',
                          'Bloqueado por administrador',
                      ],
                      true
                  );

                  $estaBloqueado = $estado !== 'Activo';

                  $accion = $estaBloqueado
                      ? 'desbloquear'
                      : 'bloquear';

                  $etiquetaAccion = $estaBloqueado
                      ? 'Desbloquear'
                      : 'Bloquear';

                  $idUsuario = (int) ($user['id_usuario'] ?? 0);
                  ?>

                  <tr>

                    <td class="px-5 py-4">
                      <strong class="block text-white/80">
                        <?= e($user['name'] ?? 'Sin nombre') ?>
                      </strong>

                      <span class="mt-1 block text-xs text-white/30">
                        <?= e($user['email'] ?? '—') ?>
                      </span>
                    </td>

                    <td class="px-5 py-4 text-white/45">
                      <?= e(ucfirst($rol)) ?>
                    </td>

                    <td class="px-5 py-4">
                      <span class="text-xs font-semibold <?= $estado === 'Activo' ? 'text-aula-green' : 'text-aula-orange-soft' ?>">
                        <?= e($estado !== '' ? $estado : 'Estado no disponible') ?>
                      </span>
                    </td>

                    <td class="px-5 py-4 text-white/35">
                      <?= array_key_exists('reports', $user) ? e((int) $user['reports']) : '—' ?>
                    </td>

                    <td class="px-5 py-4">

                      <?php if ($rolAdministrable && $estadoReconocido && $idUsuario > 0): ?>

                        <form
                          method="post"
                          action="<?= e(url('actions/admin_usuarios.php')) ?>"
                          onsubmit="return confirm('¿Confirmas que deseas <?= e(strtolower($etiquetaAccion)) ?> esta cuenta?');"
                        >

                          <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_token()) ?>"
                          >

                          <input
                            type="hidden"
                            name="id_usuario"
                            value="<?= e($idUsuario) ?>"
                          >

                          <input
                            type="hidden"
                            name="accion"
                            value="<?= e($accion) ?>"
                          >

                          <button
                            type="submit"
                            class="min-h-9 border border-aula-orange/30 bg-aula-orange/10 px-3 text-xs font-bold text-aula-orange-soft transition hover:bg-aula-orange hover:text-aula-bg"
                          >
                            <?= e($etiquetaAccion) ?>
                          </button>

                        </form>

                      <?php else: ?>

                        <span class="text-xs text-white/30">
                          Sin acciones
                        </span>

                      <?php endif; ?>

                    </td>

                  </tr>

                <?php endforeach; ?>

              <?php endif; ?>

            </tbody>
          </table>

        </div>
      </section>

      <!-- PAGOS -->
      <section data-workspace-panel="pagos"
               data-title="Pagos"
               data-eyebrow="Transacciones e inscripciones"
               hidden>

        <div class="mb-6 flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-end sm:justify-between">

          <div>
            <p class="max-w-2xl text-sm leading-7 text-white/45">
              Esta vista recibe las transacciones confirmadas por el proveedor de pago.
              Mientras no haya MySQL, las compras nuevas de Sandbox viven en la sesión actual.
            </p>
          </div>

          <span class="text-xs font-bold text-aula-green">
            <?= e(count($paypalPayments)) ?> PayPal confirmadas
          </span>

        </div>

        <div class="overflow-x-auto border border-white/10">
          <table class="min-w-full text-left text-sm">

            <thead class="bg-white/[0.025] text-[10px] uppercase tracking-[0.12em] text-white/35">
              <tr>
                <th class="px-5 py-4">Orden</th>
                <th class="px-5 py-4">Curso</th>
                <th class="px-5 py-4">Estudiante</th>
                <th class="px-5 py-4">Proveedor</th>
                <th class="px-5 py-4">Estado</th>
                <th class="px-5 py-4">Fecha</th>
                <th class="px-5 py-4">Monto</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-white/10">

              <?php foreach ($payments as $payment): ?>
                <tr>

                  <td class="px-5 py-4 text-xs text-white/35">
                    <?= e($payment['order_id'] ?? $payment['id'] ?? '—') ?>
                  </td>

                  <td class="px-5 py-4 font-bold text-white/75">
                    <?= e($payment['course'] ?? '—') ?>
                  </td>

                  <td class="px-5 py-4 text-white/45">
                    <?= e($payment['student'] ?? '—') ?>
                  </td>

                  <td class="px-5 py-4 text-xs text-white/40">
                    <?= e($payment['provider'] ?? 'AulaGo') ?>
                  </td>

                  <td class="px-5 py-4 text-xs font-bold <?= ($payment['status'] ?? '') === 'COMPLETED' ? 'text-aula-green' : 'text-aula-yellow' ?>">
                    <?= e($payment['status'] ?? '—') ?>
                  </td>

                  <td class="px-5 py-4 text-white/40">
                    <?= e(format_date_es((string) ($payment['date'] ?? ''))) ?>
                  </td>

                  <td class="px-5 py-4 font-bold text-aula-orange-soft">
                    <?= e(format_transaction_amount($payment)) ?>
                  </td>

                </tr>
              <?php endforeach; ?>

            </tbody>
          </table>
        </div>
      </section>

      <!-- COMENTARIOS -->
      <section data-workspace-panel="comentarios"
               data-title="Comentarios"
               data-eyebrow="Moderación de contenido"
               hidden>

        <div class="space-y-4">

          <?php foreach ($adminComments as $comment): ?>

            <article class="border border-white/10 bg-white/[0.015] p-5">

              <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <strong class="text-sm text-white/80">
                      <?= e($comment['user']) ?>
                    </strong>

                    <span class="text-aula-yellow">
                      <?= str_repeat('★', (int) $comment['rating']) ?>
                    </span>
                  </div>

                  <p class="mt-1 text-xs text-white/30">
                    <?= e($comment['course']) ?> · <?= e(format_date_es($comment['date'])) ?>
                  </p>
                </div>

                <span class="text-[10px] font-bold uppercase tracking-[0.1em] <?= $comment['status'] === 'Reportado' ? 'text-aula-orange-soft' : 'text-aula-green' ?>">
                  <?= e($comment['status']) ?>
                </span>

              </div>

              <p class="mt-4 text-sm leading-7 text-white/50">
                <?= e($comment['text']) ?>
              </p>

              <div class="mt-4 flex gap-2 border-t border-white/10 pt-4">

                <button type="button" class="min-h-9 border border-white/10 px-3 text-xs font-bold text-white/45">
                  Ocultar
                </button>

                <button type="button" class="min-h-9 border border-aula-orange/30 bg-aula-orange/10 px-3 text-xs font-bold text-aula-orange-soft">
                  Revisar
                </button>

              </div>
            </article>

          <?php endforeach; ?>

        </div>
      </section>

      <!-- REPORTES -->
      <section data-workspace-panel="reportes"
               data-title="Reportes"
               data-eyebrow="Incidencias pendientes"
               hidden>

        <div class="grid gap-4 md:grid-cols-2">

          <article class="border border-aula-orange/25 bg-aula-orange/[0.04] p-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">
              Reportes de usuarios
            </p>

            <strong class="mt-3 block text-4xl">
              <?= $tieneDatosReportes ? e($totalReports) : '—' ?>
            </strong>

            <p class="mt-3 text-sm leading-6 text-white/40">
              <?= $tieneDatosReportes
                  ? 'Reportes registrados para las cuentas listadas.'
                  : 'La lista de usuarios todavía no incluye información de reportes.' ?>
            </p>
          </article>

          <article class="border border-white/10 bg-white/[0.015] p-6">

            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#9fcbd5]">
              Comentarios señalados
            </p>

            <strong class="mt-3 block text-4xl">
              <?= e(count($reportedComments)) ?>
            </strong>

            <p class="mt-3 text-sm leading-6 text-white/40">
              Comentarios que requieren decisión de moderación.
            </p>

          </article>
        </div>

        <div class="mt-7 border-t border-white/10">

          <?php foreach ($adminUsers as $user): ?>

            <?php if ((int) ($user['reports'] ?? 0) > 0): ?>

              <article class="grid gap-4 border-b border-white/10 py-5 sm:grid-cols-[1fr_auto] sm:items-center">

                <div>
                  <strong class="block text-sm">
                    <?= e($user['name'] ?? 'Sin nombre') ?>
                  </strong>

                  <span class="mt-1 block text-xs text-white/35">
                    <?= e($user['email'] ?? '—') ?> · <?= e($user['role'] ?? '—') ?>
                  </span>
                </div>

                <div class="sm:text-right">
                  <strong class="block text-sm text-aula-orange-soft">
                    <?= e($user['reports']) ?> reportes
                  </strong>

                  <button type="button" class="mt-2 text-xs font-bold text-white/45">
                    Abrir expediente →
                  </button>
                </div>

              </article>

            <?php endif; ?>

          <?php endforeach; ?>

        </div>
      </section>

      <!-- MI CUENTA -->
      <section data-workspace-panel="cuenta"
               data-title="Mi cuenta"
               data-eyebrow="Perfil administrativo"
               hidden>

        <div class="grid gap-6 xl:grid-cols-[.75fr_1.25fr]">

          <aside class="border border-white/10 bg-white/[0.02] p-6">

            <span class="grid h-20 w-20 place-items-center rounded-full bg-aula-green-dark text-xl font-extrabold text-aula-green">
              <?= e(user_initials()) ?>
            </span>

            <h2 class="mt-5 font-editorial text-3xl font-semibold">
              <?= e(user_name()) ?>
            </h2>

            <p class="mt-2 text-sm text-white/35">
              <?= e(user_email()) ?>
            </p>


          </aside>

          <form class="border border-white/10 p-6" onsubmit="return false;">

            <h2 class="font-editorial text-2xl font-semibold">
              Datos de acceso
            </h2>

            <div class="mt-6 grid gap-5">

              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/55">Nombre</span>
                <input
                  value="<?= e(user_name()) ?>"
                  class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"
                >
              </label>

              <label class="grid gap-2">
                <span class="text-xs font-bold text-white/55">Correo</span>
                <input
                  value="<?= e(user_email()) ?>"
                  class="min-h-11 border border-white/10 bg-aula-bg px-3 text-sm text-white outline-none focus:border-aula-orange"
                >
              </label>

            </div>

            <button type="button" class="mt-6 min-h-10 bg-aula-orange px-5 text-xs font-extrabold text-aula-bg">
              Guardar cambios
            </button>

          </form>
        </div>
      </section>

    </main>
  </section>

  <!-- PANEL DERECHO -->
  <aside class="workspace-context hidden overflow-hidden bg-[#151116] p-5 lg:block">

    <div class="flex items-center justify-between">
      <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/30">
        Panel de operaciones
      </p>
      <span class="text-aula-green">●</span>
    </div>

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
          <?= e(count($adminUsers)) ?>
        </strong>
      </div>

      <div class="bg-[#151116] p-4">
        <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
          Categorías
        </span>
        <strong class="mt-2 block text-2xl text-aula-green">
          <?= e(count($adminCategories)) ?>
        </strong>
      </div>

      <div class="bg-[#151116] p-4">
        <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
          Pagos
        </span>
        <strong class="mt-2 block text-2xl text-[#9fcbd5]">
          <?= e(count($payments)) ?>
        </strong>
      </div>

      <div class="bg-[#151116] p-4">
        <span class="text-[9px] uppercase tracking-[0.1em] text-white/30">
          Reportes
        </span>
        <strong class="mt-2 block text-2xl text-aula-orange-soft">
          <?= $tieneDatosReportes ? e($totalReports) : '—' ?>
        </strong>
      </div>

    </div>

    <section class="mt-8 border border-white/10 bg-aula-bg/35 p-4">

      <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-aula-orange-soft">
        Prioridad actual
      </p>

      <h2 class="mt-3 font-editorial text-xl font-semibold">
        Revisar contenido reportado
      </h2>

      <p class="mt-2 text-xs leading-5 text-white/35">
        <?php if ($tieneDatosReportes): ?>
          Hay <?= e($totalReports) ?> reportes vinculados a usuarios
          y <?= e(count($reportedComments)) ?> comentario señalado.
        <?php else: ?>
          Los reportes de usuarios están pendientes de integración.
          Hay <?= e(count($reportedComments)) ?> comentarios señalados
          en los datos actuales.
        <?php endif; ?>
      </p>

      <a href="#reportes"
         data-workspace-link="reportes"
         class="mt-4 inline-block text-xs font-bold text-aula-orange-soft">
        Abrir reportes →
      </a>

    </section>

    <section class="mt-8 border-t border-white/10 pt-6">

      <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/30">
        Sistema
      </p>

      <div class="mt-4 space-y-3 text-xs">

        <div class="flex justify-between">
          <span class="text-white/35">Catálogo</span>
          <strong class="text-aula-green">Operativo</strong>
        </div>

        <div class="flex justify-between">
          <span class="text-white/35">Sesiones</span>
          <strong class="text-aula-green">Operativas</strong>
        </div>

        <div class="flex justify-between">
          <span class="text-white/35">Moderación</span>
          <strong class="text-aula-orange-soft">Requiere atención</strong>
        </div>

      </div>
    </section>

  </aside>

</div>

<?php require PROJECT_ROOT . '/includes/layout/scripts.php'; ?>

</body>
</html>
