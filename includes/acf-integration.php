<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Integração com o Advanced Custom Fields (ACF).
 */

// Hook para popular os campos do ACF com base nos dados do usuário
add_filter('acf/load_value', 'garagepro_populate_acf_fields_from_user', 10, 3);

/**
 * Função para popular os campos do ACF com os dados do usuário logado.
 *
 * @param mixed $value Valor atual do campo.
 * @param int $post_id ID do post ao qual o campo está associado.
 * @param array $field Dados do campo ACF.
 * @return mixed
 */
function garagepro_populate_acf_fields_from_user($value, $post_id, $field) {
    // Verifica se o post está relacionado ao usuário logado
    if ($post_id !== 'user_' . get_current_user_id()) {
        return $value;
    }

    // Obter informações do usuário logado
    $user = wp_get_current_user();

    // Popular campos personalizados com base no nome do campo
    switch ($field['name']) {
        case 'first_name':
            return $user->first_name;
        case 'last_name':
            return $user->last_name;
        case 'email':
            return $user->user_email;
        default:
            return $value;
    }
}

// Hook para salvar os valores do ACF no perfil do usuário
add_action('acf/save_post', 'garagepro_save_acf_fields_to_user', 10);

/**
 * Função para salvar os valores dos campos do ACF no perfil do usuário.
 *
 * @param int $post_id ID do post que está sendo salvo.
 */
function garagepro_save_acf_fields_to_user($post_id) {
    // Verifica se o post está associado a um usuário
    if (strpos($post_id, 'user_') !== 0) {
        return;
    }

    // Obtém o ID do usuário
    $user_id = str_replace('user_', '', $post_id);

    // Verifica se o usuário existe
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }

    // Atualiza os dados do usuário com base nos campos do ACF
    $first_name = get_field('first_name', $post_id);
    $last_name = get_field('last_name', $post_id);
    $email = get_field('email', $post_id);

    wp_update_user([
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_email' => $email,
    ]);
}