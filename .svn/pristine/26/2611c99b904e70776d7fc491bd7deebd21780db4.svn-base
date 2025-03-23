<?php
if (! defined('ABSPATH') ) {
    exit;
}

add_action( 'init', 'easyVerein_login' );

function easyVerein_login() {
    global $easyVerein_login_error_message;
    if (isset($_POST['easyVerein_login_button'])) {
        include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];
        $short = easyVereinAPIRequest('organization', '{short}');
        $loginData = easyVereinAPIRequestLogin('https://easyverein.com/api/v2.0/get-token', $short['data']["results"][0]["short"]."_".$username, $password);

        // Load database data
        global $wpdb;
        $table_name = $wpdb->prefix . 'easyVerein';
        $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_login_form_%';", array($table_name));
        $db_userSettings = $wpdb->get_results($sql);

        $userSettings = [];
        foreach ( $db_userSettings as $res ) {
            $userSettings[$res->option] = $res->value;
        }

        if (isset($loginData['data']['token'])) {
            $user = get_user_by('login', $username);
            wp_clear_auth_cookie();
            wp_set_current_user ( $user->ID );
            wp_set_auth_cookie  ( $user->ID );    
            setcookie("easyVereinToken", $loginData['data']['token'], time() + 3600 * 24, "/");
            wp_redirect(esc_attr(isset($userSettings['easyVerein_login_form_url']) ? esc_attr($userSettings['easyVerein_login_form_url']):'/'));
            exit;
        } else {
            $easyVerein_login_error_message = 'Benutzername oder Passwort falsch';
        }
    }
}



add_shortcode('easyverein_login', 'easyVerein_printLogin');


function easyVerein_printLogin() {

    global $easyVerein_login_error_message;
    wp_enqueue_style('easyVerein_public_css_login', EASYVEREIN_REGISTER_PUBLIC_CSS."easyVereinShortcodeLogin.css"); 
    include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';

    // Load database data
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_login_form_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }

    $login_form = '
        <div class="easyVerein_login">
            <h2>Anmelden</h2>';
            if (!empty($easyVerein_login_error_message)) {
                $login_form .= '<h5>'.$easyVerein_login_error_message.'</h5>';
            }
            $login_form .=
            '<form method="post">
                <label for="username">Benutzername:</label><br>
                <input type="text" id="username" name="username"><br>
                <label for="password">Passwort:</label><br>
                <input type="password" id="password" name="password"><br>
                <input style="color: '. esc_attr(isset($userSettings['easyVerein_login_form_button_text_color']) ? esc_attr($userSettings['easyVerein_login_form_button_text_color']):'#23985D') .'; background-color:' . esc_attr(isset($userSettings['easyVerein_login_form_button_color']) ? esc_attr($userSettings['easyVerein_login_form_button_color']):'#FFFFFF') . ';" name="easyVerein_login_button" type="submit" value="Anmelden">
            </form>
        </div>
    ';
    return $login_form;
}
?>
