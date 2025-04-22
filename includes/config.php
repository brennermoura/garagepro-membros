<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configurações do plugin GaragePro com Mercado Pago.
 */

return [
    // Chaves da API Mercado Pago - Modo Sandbox
    'public_key' => 'TEST-b628631e-6b04-4c30-92ae-df9062e9cb83',
    'access_token' => 'TEST-2442597998385682-042108-ccc1ce561bc5beb217092a7e7b1721a7-2397809037',

    // URLs de callback e retorno
    'callback_url' => home_url('/wp-json/garagepro-mercadopago/v1/webhook'),
    'return_url' => home_url('/pagamento-concluido'),

    // Modo sandbox para testes
    'sandbox_mode' => true,

    // Outros ajustes
    'log_enabled' => true,
];
