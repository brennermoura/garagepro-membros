<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manipulação de Webhooks do Mercado Pago e criação de endpoints REST API para integração.
 */

// Endpoint REST API para o webhook do Mercado Pago (atualizado)
function garagepro_register_mercadopago_webhook_endpoint() {
    register_rest_route('garagepro-mercadopago/v1', '/webhook', [
        'methods' => 'POST',
        'callback' => 'garagepro_handle_mercadopago_webhook',
        'permission_callback' => '__return_true', // Permite acesso público (validação será feita manualmente)
    ]);

    // Endpoint para listar cobranças de um usuário
    register_rest_route('garagepro-mercadopago/v1', '/cobrancas/(?P<user_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'garagepro_get_cobrancas_endpoint',
        'permission_callback' => 'garagepro_authenticate_request', // Validação de autenticação
    ]);

    // Endpoint para criar uma cobrança
    register_rest_route('garagepro-mercadopago/v1', '/cobrancas', [
        'methods' => 'POST',
        'callback' => 'garagepro_create_cobranca_endpoint',
        'permission_callback' => 'garagepro_authenticate_request', // Validação de autenticação
    ]);
}
add_action('rest_api_init', 'garagepro_register_mercadopago_webhook_endpoint');

// Função para manipular o webhook do Mercado Pago
function garagepro_handle_mercadopago_webhook(WP_REST_Request $request) {
    $payload = $request->get_json_params();

    // Verifica se o payload contém o external_reference
    if (empty($payload['data']['id'])) {
        return new WP_REST_Response(['error' => 'Dados inválidos no webhook.'], 400);
    }

    $payment_id = sanitize_text_field($payload['data']['id']);

    // Obtém os detalhes do pagamento usando a API do Mercado Pago
    $payment_details = garagepro_mercadopago_get_payment_status($payment_id);

    if (isset($payment_details['error'])) {
        return new WP_REST_Response(['error' => 'Erro ao obter detalhes do pagamento.'], 500);
    }

    $referenceId = $payment_details['external_reference'];
    $status = $payment_details['status'];

    // Atualiza o status da cobrança no banco de dados
    $updated = garagepro_update_cobranca_status($referenceId, $status);

    if (!$updated) {
        return new WP_REST_Response(['error' => 'Erro ao atualizar o status da cobrança.'], 500);
    }

    return new WP_REST_Response(['success' => true], 200);
}

// Função de autenticação para proteger os endpoints
function garagepro_authenticate_request(WP_REST_Request $request) {
    // Exemplo de autenticação simples com tokens
    $token = $request->get_header('Authorization');

    if ($token !== 'SEU_TOKEN_PERSONALIZADO') {
        return new WP_Error('unauthorized', 'Token inválido.', ['status' => 401]);
    }

    return true;
}