<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Comunicação com a API do Mercado Pago.
 */

// Função para criar uma cobrança no Mercado Pago
function garagepro_mercadopago_create_charge($referenceId, $value, $buyer) {
    $url = 'https://api.mercadopago.com/v1/payments';

    // Dados da cobrança
    $body = [
        'transaction_amount' => $value,
        'description' => "Cobrança #{$referenceId}",
        'payer' => [
            'email' => $buyer['email'],
            'first_name' => $buyer['firstName'],
            'last_name' => $buyer['lastName'],
            'identification' => [
                'type' => $buyer['docType'],
                'number' => $buyer['docNumber'],
            ],
        ],
        'external_reference' => $referenceId,
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . garagepro_mercadopago_get_config('mercadopago_access_token'),
        ],
        'body' => json_encode($body),
    ]);

    if (is_wp_error($response)) {
        return ['error' => $response->get_error_message()];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['id'])) {
        return $response_body;
    }

    return ['error' => 'Erro ao criar cobrança no Mercado Pago.'];
}

// Função para consultar o status de um pagamento
function garagepro_mercadopago_get_payment_status($referenceId) {
    $url = "https://api.mercadopago.com/v1/payments/search?external_reference={$referenceId}";

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . garagepro_mercadopago_get_config('mercadopago_access_token'),
        ],
    ]);

    if (is_wp_error($response)) {
        return ['error' => $response->get_error_message()];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['results'][0])) {
        return $response_body['results'][0];
    }

    return ['error' => 'Pagamento não encontrado no Mercado Pago.'];
}

// Função para obter valores de configuração
function garagepro_mercadopago_get_config($key) {
    $config = include GARAGEPRO_PICPAY_PATH . 'includes/config.php';
    return $config[$key] ?? null;
}