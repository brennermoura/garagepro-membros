<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configurações do plugin GaragePro PicPay.
 */

return [
    // Token da API do PicPay
    'picpay_token' => 'SEU_TOKEN_AQUI', // Substitua pelo token real da sua API do PicPay

    // URLs de callback e retorno
    'callback_url' => home_url('/wp-json/garagepro-picpay/v1/webhook'), // Endpoint do webhook
    'return_url' => home_url('/pagamento-concluido'), // Página para redirecionar após o pagamento

    // Outros ajustes do plugin
    'log_enabled' => true, // Habilitar ou desabilitar logs personalizados
];