<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Comunicação com a API do PicPay.
 */

// Função para criar uma cobrança no PicPay
function garagepro_picpay_create_charge($referenceId, $value, $buyer) {
    $url = 'https://appws.picpay.com/ecommerce/public/payments';

    // Dados da cobrança
    $body = [
        'referenceId' => $referenceId,
        'callbackUrl' => get_site_url() . '/wp-json/garagepro-picpay/v1/webhook',
        'value' => $value,
        'buyer' => $buyer,
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'x-picpay-token' => garagepro_picpay_get_config('picpay_token'),
        ],
        'body' => json_encode($body),
    ]);

    if (is_wp_error($response)) {
        return ['error' => $response->get_error_message()];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['paymentUrl'])) {
        return $response_body;
    }

    return ['error' => 'Erro ao criar cobrança no PicPay.'];
}

// Função para consultar o status de um pagamento
function garagepro_picpay_get_payment_status($referenceId) {
    $url = "https://appws.picpay.com/ecommerce/public/payments/{$referenceId}/status";

    $response = wp_remote_get($url, [
        'headers' => [
            'x-picpay-token' => garagepro_picpay_get_config('picpay_token'),
        ],
    ]);

    if (is_wp_error($response)) {
        return ['error' => $response->get_error_message()];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    return $response_body;
}

// Função para cancelar uma cobrança
function garagepro_picpay_cancel_charge($referenceId) {
    $url = "https://appws.picpay.com/ecommerce/public/payments/{$referenceId}/cancellations";

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'x-picpay-token' => garagepro_picpay_get_config('picpay_token'),
        ],
    ]);

    if (is_wp_error($response)) {
        return ['error' => $response->get_error_message()];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    return $response_body;
}

// Função para obter valores de configuração
function garagepro_picpay_get_config($key) {
    $config = include GARAGEPRO_PICPAY_PATH . 'includes/config.php';
    return $config[$key] ?? null;
}