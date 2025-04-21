<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Funções auxiliares genéricas para o plugin GaragePro PicPay.
 */

/**
 * Função para registrar logs personalizados.
 *
 * @param string $message Mensagem a ser registrada.
 * @param string $level Nível do log (info, warning, error). Padrão: info.
 */
function garagepro_log($message, $level = 'info') {
    $config = include GARAGEPRO_PICPAY_PATH . 'includes/config.php';

    if (!$config['log_enabled']) {
        return;
    }

    $log_file = GARAGEPRO_PICPAY_PATH . 'logs/garagepro-picpay.log';

    // Garante que o diretório de logs exista
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Função para validar e-mails.
 *
 * @param string $email E-mail a ser validado.
 * @return bool True se o e-mail for válido, False caso contrário.
 */
function garagepro_validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Função para validar valores numéricos.
 *
 * @param mixed $value Valor a ser validado.
 * @return bool True se for numérico, False caso contrário.
 */
function garagepro_validate_numeric($value) {
    return is_numeric($value);
}

/**
 * Função para obter o IP do cliente.
 *
 * @return string IP do cliente.
 */
function garagepro_get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}