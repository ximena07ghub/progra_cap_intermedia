<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once PROJECT_ROOT . '/models/conexion.php';
require_once PROJECT_ROOT . '/models/EstudiantePortal.php';

require_role('estudiante');

$codigo = trim((string) ($_GET['codigo'] ?? ''));

if ($codigo === '') {
    http_response_code(404);
    exit('Certificado no encontrado.');
}

try {
    $config = require PROJECT_ROOT . '/config/database.local.php';
    $conexion = new Conexion($config);
    $modelo = new EstudiantePortal($conexion->obtenerConexion());

    $certificado = $modelo->buscarCertificado(
        (int) ($_SESSION['usuario_id'] ?? 0),
        $codigo
    );
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No fue posible cargar el certificado.');
}

if ($certificado === null) {
    http_response_code(404);
    exit('Certificado no encontrado.');
}

$pageTitle = 'Certificado | AulaGo';
$pageDescription = 'Certificado de finalización de curso en AulaGo.';

require PROJECT_ROOT . '/includes/layout/head.php';
?>
<style>
@media print {
    body {
        background: #fff !important;
    }

    .certificate-actions {
        display: none !important;
    }

    .certificate-shell {
        min-height: auto !important;
        padding: 0 !important;
    }

    .certificate-card {
        box-shadow: none !important;
        border: 2px solid #1b171c !important;
        background: #fff !important;
        color: #1b171c !important;
    }

    .certificate-card * {
        color: #1b171c !important;
    }
}
</style>

<div class="certificate-shell min-h-screen bg-aula-bg px-6 py-10 text-aula-cream lg:px-8 lg:py-16">
    <div class="certificate-actions mx-auto mb-6 flex max-w-5xl items-center justify-between gap-4">
        <a href="<?= e(url('estudiante/#certificados')) ?>" class="text-xs font-bold text-white/45 hover:text-aula-cream">← Volver a certificados</a>
        <button type="button" onclick="window.print()" class="border border-aula-orange/40 bg-aula-orange/10 px-4 py-2 text-xs font-bold text-aula-orange-soft">
            Imprimir / Guardar PDF
        </button>
    </div>

    <main class="certificate-card relative mx-auto max-w-5xl overflow-hidden border border-aula-orange/30 bg-aula-surface p-8 shadow-aula sm:p-12 lg:p-16">
        <div class="absolute inset-x-0 top-0 h-1 bg-aula-orange"></div>
        <div class="absolute inset-y-0 left-0 w-1 bg-aula-green"></div>

        <header class="border-b border-white/10 pb-8 text-center">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-aula-green">AulaGo</p>
            <h1 class="mt-4 font-editorial text-4xl font-semibold sm:text-5xl">Certificado de finalización</h1>
            <p class="mt-3 text-sm text-white/40">Este documento acredita la finalización satisfactoria del curso.</p>
        </header>

        <section class="py-12 text-center">
            <p class="text-sm text-white/40">Se otorga a</p>
            <h2 class="mt-3 font-editorial text-5xl font-semibold text-aula-cream sm:text-6xl"><?= e($certificado['estudiante']) ?></h2>

            <p class="mt-10 text-sm text-white/40">por completar el curso</p>
            <h3 class="mt-3 font-editorial text-4xl text-aula-orange-soft sm:text-5xl"><?= e($certificado['curso']) ?></h3>

            <p class="mt-4 text-sm text-white/35">
                Categoría <?= e($certificado['categoria']) ?> · Instructor <?= e($certificado['instructor']) ?>
            </p>
        </section>

        <footer class="grid gap-6 border-t border-white/10 pt-8 text-sm sm:grid-cols-3">
            <div>
                <span class="block text-[10px] uppercase tracking-[0.12em] text-white/30">Finalización</span>
                <strong class="mt-2 block"><?= e(format_date_es((string) $certificado['fecha_finalizacion'])) ?></strong>
            </div>
            <div>
                <span class="block text-[10px] uppercase tracking-[0.12em] text-white/30">Emisión</span>
                <strong class="mt-2 block"><?= e(format_date_es((string) $certificado['fecha_emision'])) ?></strong>
            </div>
            <div>
                <span class="block text-[10px] uppercase tracking-[0.12em] text-white/30">Código</span>
                <strong class="mt-2 block break-all text-aula-green"><?= e($certificado['codigo']) ?></strong>
            </div>
        </footer>
    </main>
</div>
</body>
</html>
