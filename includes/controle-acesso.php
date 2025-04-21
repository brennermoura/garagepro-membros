<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Controle de acesso para usuários inadimplentes.
 */

// Hook para adicionar verificação de acesso em páginas protegidas
add_action('template_redirect', 'garagepro_restrict_access_for_inadimplentes');

/**
 * Função para verificar e restringir o acesso de usuários inadimplentes
 */
function garagepro_restrict_access_for_inadimplentes() {
    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        return;
    }

    // Obtém o ID do usuário logado
    $user_id = get_current_user_id();

    // Verifica se o usuário está inadimplente
    if (garagepro_is_user_inadimplente($user_id)) {
        // Redireciona para uma página personalizada de aviso ou bloqueio
        wp_redirect(home_url('/aviso-inadimplencia'));
        exit;
    }
}

/**
 * Função para verificar se um usuário está inadimplente
 * 
 * @param int $user_id ID do usuário
 * @return bool True se o usuário estiver inadimplente, False caso contrário
 */
function garagepro_is_user_inadimplente($user_id) {
    // Obtém todas as cobranças do usuário
    $cobrancas = garagepro_get_user_cobrancas($user_id);

    // Verifica se há alguma cobrança pendente ou vencida
    foreach ($cobrancas as $cobranca) {
        if (in_array($cobranca['status'], ['pendente', 'vencida'])) {
            return true;
        }
    }

    return false;
}