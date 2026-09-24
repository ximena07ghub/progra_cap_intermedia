<?php
$useSwiper = $useSwiper ?? false;
$pageScripts = $pageScripts ?? [];
?>
<script src="<?= e(asset('js/layout.js')) ?>" defer></script>
<?php if ($useSwiper): ?>
<script src="<?= e(asset('vendor/swiper/swiper-bundle.min.js')) ?>" defer></script>
<?php endif; ?>
<?php foreach ($pageScripts as $script): ?>
<script src="<?= e(asset('js/' . ltrim($script, '/'))) ?>" defer></script>
<?php endforeach; ?>
