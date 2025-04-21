<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manipulação de Webhooks do PicPay e criação de endpoints REST API para integração.
 */

// Endpoint REST API para o webhook do PicPay (já existente)
function garagepro_register_picpay_webhook_endpoint() {
    register_rest_route('garagepro-picpay/v1', '/webhook', [
        'methods' => 'POST',
        'callback' => 'garagepro_handle_picpay_webhook',
        'permission_callback' => '__return_true', // Permite acesso público (validação será feita manualmente)
    ]);

    // Endpoint para listar cobranças de um usuário
    register_rest_route('garagepro-picpay/v1', '/cobrancas/(?P<user_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'garagepro_get_cobrancas_endpoint',
        'permission_callback' => 'garagepro_authenticate_request', // Validação de autenticação
    ]);

    // Endpoint para criar uma cobrança
    register_rest_route('garagepro-picpay/v1', '/cobrancas', [
        'methods' => 'POST',
        'callback' => 'garagepro_create_cobranca_endpoint',
        'permission_callback' => 'garagepro_authenticate_request', // Validação de autenticação
    ]);
}
add_action('rest_api_init', 'garagepro_register_picpay_webhook_endpoint');

// Função para listar cobranças de um usuário
function garagepro_get_cobrancas_endpoint(WP_REST_Request $request) {
    $user_id = intval($request['user_id']);

    // Verifica se o usuário existe
    if (!get_userdata($user_id)) {
        return new WP_REST_Response(['error' => 'Usuário não encontrado.'], 404);
    }

    // Obtém cobranças do usuário
    $cobrancas = garagepro_get_user_cobrancas($user_id);

    return new WP_REST_Response($cobrancas, 200);
}

// Função para criar uma cobrança
function garagepro_create_cobranca_endpoint(WP_REST_Request $request) {
    $params = $request->get_json_params();

    $referenceId = sanitize_text_field($params['referenceId']);
    $userId = intval($params['userId']);
    $value = floatval($params['value']);
    $status = sanitize_text_field($params['status'] ?? 'pendente');

    // Verifica se o usuário existe
    if (!get_userdata($userId)) {
        return new WP_REST_Response(['error' => 'Usuário não encontrado.'], 404);
    }

    // Cria a cobrança
    $cobranca_id = garagepro_create_cobranca($referenceId, $userId, $value, $status);

    if (!$cobranca_id) {
        return new WP_REST_Response(['error' => 'Erro ao criar a cobrança.'], 500);
    }

    return new WP_REST_Response(['success' => true, 'cobranca_id' => $cobranca_id], 201);
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