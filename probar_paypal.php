<?php

require_once __DIR__ . '/includes/PayPalClient.php';

try {
    $paypal = new PayPalClient();
    $token = $paypal->getAccessToken();

    echo '<h1>Conexión con PayPal exitosa</h1>';
    echo '<p>Access token obtenido correctamente.</p>';
} catch (Exception $e) {
    echo '<h1>Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}