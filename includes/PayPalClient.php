<?php

class PayPalClient
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/paypal.local.php';

        $this->clientId = $config['ASA58-TAkBPJj72N8nkDmRNU_8MxoBW0CE7zots36co8RSzZ0FShcskZk7OyQXOtPM2kJCUrXqWbrCFw'];
        $this->clientSecret = $config['TECBVPskYVuFztQn2Ev_G0B5lNBIQpWcB-wKGsoX_w8Ho3Gd9mvFLsebAkDxvf8yKWR_EShYXVTYp-A1e'];
        $this->baseUrl = $config['https://api-m.sandbox.paypal.com'];
    }

    public function getAccessToken(): string
    {
        $url = $this->baseUrl . '/v1/oauth2/token';

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception('Error de conexión con PayPal: ' . $error);
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200 || !isset($data['access_token'])) {
            throw new Exception(
                'No fue posible obtener el access token de PayPal. Respuesta: ' . $response
            );
        }

        return $data['access_token'];
    }
}