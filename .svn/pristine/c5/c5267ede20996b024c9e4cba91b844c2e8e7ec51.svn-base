<?php

function easyVerein_setupLoginFormSettings()
{
    // Save settings in database
    if (isset($_POST['easyVerein_save_login_form'])) {
        easyVerein_saveloginform();
    }
    
    // Load login form data
    $userData = easyVerein_loadLoginFormData();

    // Return html element
    return easyVerein_printLoginFormSettings($userData);
}
    

function easyVerein_loadLoginFormData()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Load data from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_login_form_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);   

    // Map data
    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }

    return $userSettings;
}

function easyVerein_saveloginform()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
  
    // Set login url
    $easyVerein_login_form_url = array(
      'option' => 'easyVerein_login_form_url',
      'value' => isset($_POST['easyVerein_login_form_url']) ? sanitize_text_field($_POST['easyVerein_login_form_url']):'/'
    );
  
    // Set login form button color
    $easyVerein_login_form_button_color = array(
      'option' => 'easyVerein_login_form_button_color',
      'value' => isset($_POST['easyVerein_login_form_button_color']) ? sanitize_hex_color($_POST['easyVerein_login_form_button_color']):'#23985D'
    );
  
    // Set login form button text color
    $easyVerein_login_form_button_text_color = array(
      'option' => 'easyVerein_login_form_button_text_color',
      'value' => isset($_POST['easyVerein_login_form_button_text_color']) ? sanitize_hex_color($_POST['easyVerein_login_form_button_text_color']):'#FFFFFF'
    );
  
    // Save settings in database
    $wpdb->replace($table_name, $easyVerein_login_form_url);
    $wpdb->replace($table_name, $easyVerein_login_form_button_color);
    $wpdb->replace($table_name, $easyVerein_login_form_button_text_color);
}

function easyVerein_printLoginFormSettings($userData)
{
    // Buíld html
    $html = '';
    $html .= '<div id="easyVerein_login_form">';
        $html .= '<form method="post">';
            $html .= '<p class="easyVerein_headline">Login Formular</p>';
            $html .= '<p class="easyVerein_code">Shortcode: <code>[easyverein_login]</code><br>Dieser Shortcode ermöglicht das Login in WordPress mit den easyVerein Zugangsdaten. Diese Funktion ist nun in Kombination mit der Synchronisierung von Mitgliedern möglich.</p>';
            $html .= '<p><b>Einstellungen:</b></p>';
            $html .= '<div class="easyVerein_login_form_colorpicker">
                    <div class="easyVerein_login_form_colorpicker_item">
                    <p>Hintergrundfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_login_form_button_color']) ? esc_attr($userData['easyVerein_login_form_button_color']):'#23985D') . '" id="easyVerein_login_form_button_color" name="easyVerein_login_form_button_color" />
                    </div>

                    <div class="easyVerein_login_form_colorpicker_item">
                    <p>Schriftfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_login_form_button_text_color']) ? esc_attr($userData['easyVerein_login_form_button_text_color']):'#FFFFFF') . '" id="easyVerein_login_form_button_text_color" name="easyVerein_login_form_button_text_color"/>
                    </div>
                </div>';
            $html .= '<p>Login URL - Wird nach erfolgreichem einloggen aufgerufen</p>';
            $html .= '<input type="text" class="easyVerein_login_form_url" value="' . esc_attr(isset($userData['easyVerein_login_form_url']) ? esc_attr($userData['easyVerein_login_form_url']):"/") . '" id="easyVerein_login_form_url" name="easyVerein_login_form_url" />';
            $html .= '<button type="submit" id="easyVerein_save_login_form" name="easyVerein_save_login_form" class="easyVerein_button" disabled>Speichern</button>';
        $html .= '</form>';
    $html .= '</div>';
    return $html;
}

?>