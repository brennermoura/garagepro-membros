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

    // Integração Mercado Pago (modo teste)
    $access_token = 'TEST-2442597998385682-042108-ccc1ce561bc5beb217092a7e7b1721a7-2397809037';

    $body = [
        "transaction_amount" => (float) $value,
        "description" => "Cobrança #$referenceId",
        "payment_method_id" => "pix",
        "payer" => [
            "email" => "cliente_simulado@email.com",
            "first_name" => "Cliente",
            "last_name" => "Simulado"
        ],
        "external_reference" => $referenceId,
    ];

    $response = wp_remote_post('https://api.mercadopago.com/v1/payments', [
        'method' => 'POST',
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode($body),
    ]);

    if (!is_wp_error($response)) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['point_of_interaction']['transaction_data']['ticket_url'])) {
            update_post_meta($post_id, 'payment_url', $body['point_of_interaction']['transaction_data']['ticket_url']);
        }
    }

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
                'payment_url' => get_post_meta($post->ID, 'payment_url', true),
            ];
        }
    }

    return $cobrancas;
}
