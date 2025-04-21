<?php
/**
 * Plugin Name: GaragePro PicPay
 * Description: Plugin para gerenciamento de cobranças e membros com integração ao PicPay.
 * Version: 1.0
 * Author: Brenner Moura
 */

// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define as constantes do plugin
define('GARAGEPRO_PICPAY_PATH', plugin_dir_path(__FILE__));
define('GARAGEPRO_PICPAY_URL', plugin_dir_url(__FILE__));

// Carregar arquivos de includes
function garagepro_picpay_load_includes() {
    $includes = [
        'includes/api-picpay.php',
        'includes/cobrancas.php',
        'includes/webhook-handler.php',
        'includes/controle-acesso.php',
        'includes/membros.php',
        'includes/acf-integration.php',
        'includes/config.php',
        'includes/helper-functions.php',
    ];

    foreach ($includes as $file) {
        $filepath = GARAGEPRO_PICPAY_PATH . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        }
    }
}
add_action('plugins_loaded', 'garagepro_picpay_load_includes');