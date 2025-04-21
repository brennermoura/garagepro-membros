<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gestão de cobranças no WordPress.
 */

// Função para registrar o custom post type "Cobranças"
function garagepro_register_cobrancas_cpt() {
    $labels = [
        'name' => 'Cobranças',
        'singular_name' => 'Cobrança',
        'menu_name' => 'Cobranças',
        'name_admin_bar' => 'Cobrança',
        'add_new' => 'Adicionar Nova',
        'add_new_item' => 'Adicionar Nova Cobrança',
        'new_item' => 'Nova Cobrança',
        'edit_item' => 'Editar Cobrança',
        'view_item' => 'Ver Cobrança',
        'all_items' => 'Todas as Cobranças',
        'search_items' => 'Procurar Cobranças',
        'not_found' => 'Nenhuma cobrança encontrada.',
        'not_found_in_trash' => 'Nenhuma cobrança encontrada na lixeira.',
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-money-alt',
        'supports' => ['title', 'custom-fields'],
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => 'do_not_allow', // Bloqueia criação direta pelo painel
        ],
        'map_meta_cap' => true,
    ];

    register_post_type('garagepro_cobranca', $args);
}
add_action('init', 'garagepro_register_cobrancas_cpt');

// Função para criar uma nova cobrança e armazená-la no banco de dados
function garagepro_create_cobranca($referenceId, $userId, $value, $status = 'pendente') {
    $post_id = wp_insert_post([
        'post_type' => 'garagepro_cobranca',
        'post_title' => "Cobrança #{$referenceId}",
        'post_status' => 'publish',
        'meta_input' => [
            'reference_id' => $referenceId,
            'user_id' => $userId,
            'value' => $value,
            'status' => $status,
        ],
    ]);

    return $post_id;
}

// Função para atualizar o status de uma cobrança
function garagepro_update_cobranca_status($referenceId, $newStatus) {
    $args = [
        'post_type' => 'garagepro_cobranca',
        'meta_query' => [
            [
                'key' => 'reference_id',
                'value' => $referenceId,
                'compare' => '=',
            ],
        ],
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0];
        update_post_meta($post->ID, 'status', $newStatus);
        return true;
    }

    return false;
}

// Função para recuperar cobranças de um usuário específico
function garagepro_get_user_cobrancas($userId) {
    $args = [
        'post_type' => 'garagepro_cobranca',
        'meta_query' => [
            [
                'key' => 'user_id',
                'value' => $userId,
                'compare' => '=',
            ],
        ],
    ];

    $query = new WP_Query($args);

    $cobrancas = [];
    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $cobrancas[] = [
                'reference_id' => get_post_meta($post->ID, 'reference_id', true),
                'value' => get_post_meta($post->ID, 'value', true),
                'status' => get_post_meta($post->ID, 'status', true),
            ];
        }
    }

    return $cobrancas;
}