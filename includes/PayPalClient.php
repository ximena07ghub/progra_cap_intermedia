<?php

declare(strict_types=1);

final class PayPalClient
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;
    private string $currency;

    public function __construct()
    {
        $configFile = __DIR__ . '/../config/paypal.local.php';
        if (!is_file($configFile)) {
            throw new RuntimeException('Falta config/paypal.local.php. Copia config/paypal.example.php y agrega tus credenciales de Sandbox.');
        }

        $config = require $configFile;
        if (!is_array($config)) {
            throw new RuntimeException('La configuración de PayPal no es válida.');
        }

        $this->clientId = trim((string) ($config['client_id'] ?? ''));
        $this->clientSecret = trim((string) ($config['client_secret'] ?? ''));
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://api-m.sandbox.paypal.com'), '/');
        $this->currency = strtoupper((string) ($config['currency'] ?? 'USD'));

        if ($this->clientId === '' || $this->clientSecret === '') {
            throw new RuntimeException('Configura client_id y client_secret en config/paypal.local.php.');
        }

        if (!function_exists('curl_init')) {
            throw new RuntimeException('La extensión cURL de PHP es necesaria para conectar con PayPal.');
        }
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAccessToken(): string
    {
        $ch = curl_init($this->baseUrl . '/v1/oauth2/token');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error de conexión con PayPal: ' . $error);
        }

        curl_close($ch);
        $data = json_decode($response, true);

        if ($httpCode !== 200 || !is_array($data) || !isset($data['access_token'])) {
            throw new RuntimeException('No fue posible obtener el access token de PayPal. Código HTTP: ' . $httpCode);
        }

        return (string) $data['access_token'];
    }

    public function createOrder(
        string $referenceId,
        string $description,
        string $amount,
        string $currency,
        string $returnUrl,
        string $cancelUrl
    ): array {
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $referenceId,
                'description' => $description,
                'amount' => [
                    'currency_code' => strtoupper($currency),
                    'value' => $amount,
                ],
            ]],
            'application_context' => [
                'brand_name' => 'AulaGo',
                'landing_page' => 'LOGIN',
                'user_action' => 'PAY_NOW',
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ];

        return $this->request('POST', '/v2/checkout/orders', $payload, [
            'PayPal-Request-Id: AULAGO-CREATE-' . bin2hex(random_bytes(8)),
            'Prefer: return=representation',
        ]);
    }

    public function captureOrder(string $orderId): array
    {
        $orderId = trim($orderId);
        if ($orderId === '' || !preg_match('/^[A-Z0-9-]+$/i', $orderId)) {
            throw new InvalidArgumentException('El identificador de la orden de PayPal no es válido.');
        }

        return $this->request('POST', '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', new stdClass(), [
            'PayPal-Request-Id: AULAGO-CAPTURE-' . $orderId,
            'Prefer: return=representation',
        ]);
    }

    public function approvalUrl(array $order): ?string
    {
        foreach (($order['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve' && isset($link['href'])) {
                return (string) $link['href'];
            }
        }
        return null;
    }

    private function request(string $method, string $path, array|stdClass|null $payload = null, array $extraHeaders = []): array
    {
        $accessToken = $this->getAccessToken();
        $ch = curl_init($this->baseUrl . $path);

        $headers = array_merge([
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
            'Content-Type: application/json',
        ], $extraHeaders);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($payload !== null) {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error de conexión con PayPal: ' . $error);
        }

        curl_close($ch);
        $data = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300 || !is_array($data)) {
            $message = is_array($data)
                ? (string) ($data['message'] ?? $data['name'] ?? 'Respuesta no válida de PayPal')
                : 'Respuesta no válida de PayPal';
            throw new RuntimeException($message . ' (HTTP ' . $httpCode . ')');
        }

        return $data;
    }
}
