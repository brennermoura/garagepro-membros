<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registro, login e edição de dados dos membros.
 */

// Shortcode para exibir formulário de registro
add_shortcode('garagepro_register_form', 'garagepro_register_form_shortcode');
function garagepro_register_form_shortcode() {
    if (is_user_logged_in()) {
        return '<p>Você já está registrado e logado.</p>';
    }

    ob_start();
    ?>
    <form method="post" action="">
        <label for="username">Usuário:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="password">Senha:</label>
        <input type="password" name="password" id="password" required>
        
        <button type="submit" name="garagepro_register">Registrar</button>
    </form>
    <?php
    return ob_get_clean();
}

// Processar registro do formulário
add_action('init', 'garagepro_process_register_form');
function garagepro_process_register_form() {
    if (isset($_POST['garagepro_register'])) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

        // Verificar se o usuário já existe
        if (username_exists($username) || email_exists($email)) {
            wp_die('Usuário ou e-mail já registrados.');
        }

        // Criar o usuário
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_die('Erro ao registrar o usuário.');
        }

        // Logar o usuário automaticamente
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Redirecionar após registro
        wp_redirect(home_url());
        exit;
    }
}

// Shortcode para exibir formulário de login
add_shortcode('garagepro_login_form', 'garagepro_login_form_shortcode');
function garagepro_login_form_shortcode() {
    if (is_user_logged_in()) {
        return '<p>Você já está logado.</p>';
    }

    ob_start();
    ?>
    <form method="post" action="">
        <label for="login_username">Usuário ou E-mail:</label>
        <input type="text" name="login_username" id="login_username" required>
        
        <label for="login_password">Senha:</label>
        <input type="password" name="login_password" id="login_password" required>
        
        <button type="submit" name="garagepro_login">Entrar</button>
    </form>
    <?php
    return ob_get_clean();
}

// Processar login do formulário
add_action('init', 'garagepro_process_login_form');
function garagepro_process_login_form() {
    if (isset($_POST['garagepro_login'])) {
        $username = sanitize_text_field($_POST['login_username']);
        $password = sanitize_text_field($_POST['login_password']);

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            wp_die('Usuário ou senha incorretos.');
        }

        // Logar o usuário
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        // Redirecionar após login
        wp_redirect(home_url());
        exit;
    }
}

// Shortcode para exibir formulário de edição de dados
add_shortcode('garagepro_edit_profile_form', 'garagepro_edit_profile_form_shortcode');
function garagepro_edit_profile_form_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Você precisa estar logado para editar seus dados.</p>';
    }

    $user = wp_get_current_user();

    ob_start();
    ?>
    <form method="post" action="">
        <label for="edit_email">E-mail:</label>
        <input type="email" name="edit_email" id="edit_email" value="<?php echo esc_attr($user->user_email); ?>" required>
        
        <label for="edit_password">Nova senha (opcional):</label>
        <input type="password" name="edit_password" id="edit_password">
        
        <button type="submit" name="garagepro_edit_profile">Atualizar</button>
    </form>
    <?php
    return ob_get_clean();
}

// Processar edição de perfil
add_action('init', 'garagepro_process_edit_profile_form');
function garagepro_process_edit_profile_form() {
    if (isset($_POST['garagepro_edit_profile'])) {
        // Verificar se o usuário está logado
        if (!is_user_logged_in()) {
            wp_die('Ação não permitida.');
        }

        $user_id = get_current_user_id();
        $email = sanitize_email($_POST['edit_email']);
        $password = sanitize_text_field($_POST['edit_password']);

        wp_update_user([
            'ID' => $user_id,
            'user_email' => $email,
        ]);

        if (!empty($password)) {
            wp_set_password($password, $user_id);
        }

        // Redirecionar após atualização
        wp_redirect(home_url('/perfil-atualizado'));
        exit;
    }
}