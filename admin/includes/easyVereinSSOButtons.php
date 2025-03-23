<?php

function easyVerein_setupSSOButtonFormSettings()
{
    // Save settings in database
    if (isset($_POST['easyVerein_save_ssobutton_form'])) {
        easyVerein_savessobuttonform();
    }
    
    // Load ssobutton form data
    $userData = easyVerein_loadSSOButtonFormData();

    // Return html element
    return easyVerein_printSSOButtonFormSettings($userData);
}
    

function easyVerein_loadSSOButtonFormData()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Load data from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_ssobutton_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);   

    // Map data
    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }

    return $userSettings;
}

function easyVerein_savessobuttonform()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
  
    // Set ssobutton url
    $easyVerein_ssobutton_url = array(
      'option' => 'easyVerein_ssobutton_url',
      'value' => isset($_POST['easyVerein_ssobutton_url']) ? sanitize_text_field($_POST['easyVerein_ssobutton_url']):'/'
    );
  
    // Set ssobutton form button color
    $easyVerein_ssobutton_button_color = array(
      'option' => 'easyVerein_ssobutton_button_color',
      'value' => isset($_POST['easyVerein_ssobutton_button_color']) ? sanitize_hex_color($_POST['easyVerein_ssobutton_button_color']):'#23985D'
    );
  
    // Set ssobutton form button text color
    $easyVerein_ssobutton_button_text_color = array(
      'option' => 'easyVerein_ssobutton_button_text_color',
      'value' => isset($_POST['easyVerein_ssobutton_button_text_color']) ? sanitize_hex_color($_POST['easyVerein_ssobutton_button_text_color']):'#FFFFFF'
    );
  
    // Save settings in database
    $wpdb->replace($table_name, $easyVerein_ssobutton_url);
    $wpdb->replace($table_name, $easyVerein_ssobutton_button_color);
    $wpdb->replace($table_name, $easyVerein_ssobutton_button_text_color);
}

function easyVerein_printSSOButtonFormSettings($userData)
{
    // Buíld html
    $html = '';
    $html .= '<div id="easyVerein_ssobutton">';
        $html .= '<form method="post">';
            $html .= '<p class="easyVerein_headline">Single Sign-On Buttons</p>';
            $html .= '<p class="easyVerein_code">Shortcodes:<br><code>[easyverein_profile]<br>Dieser Shortcode ermöglicht das Erstellen eines Single Sign-On Buttons zum easyVerein Profil. Um die Single Sign-On Funktion zu nutzen, muss die Mitglieder Synchronisierung aktiviert sein und das easyVerein Login.<br><br>
            [easyverein_club_calendar]<br>Dieser Shortcode ermöglicht das Erstellen eines Single Sign-On Buttons zum easyVerein Kalender. Um die Single Sign-On Funktion zu nutzen, muss die Mitglieder Synchronisierung aktiviert sein und das easyVerein Login.<br><br>
            [easyverein_invoices]<br>Dieser Shortcode ermöglicht das Erstellen eines Single Sign-On Buttons zur easyVerein Rechnungsübersicht. Um die Single Sign-On Funktion zu nutzen, muss die Mitglieder Synchronisierung aktiviert sein und das easyVerein Login.<br><br>
            [easyverein_reset_password]<br>Dieser Shortcode ermöglicht das Erstellen eines Buttons zur easyVerein Passwort zurücksetzen. Um die Single Sign-On Funktion zu nutzen, muss die Mitglieder Synchronisierung aktiviert sein und das easyVerein Login.<br><br>
            [easyverein_club_memberlist]</code><br>Dieser Shortcode ermöglicht das Erstellen eines Single Sign-On Buttons zur easyVerein Mitgliederliste. Um die Single Sign-On Funktion zu nutzen, muss die Mitglieder Synchronisierung aktiviert sein und das easyVerein Login.<br>';
            $html .= '<p><b>Einstellungen:</b></p>';
            $html .= '<div class="easyVerein_ssobutton_colorpicker">
                    <div class="easyVerein_ssobutton_colorpicker_item">
                    <p>Hintergrundfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_ssobutton_button_color']) ? esc_attr($userData['easyVerein_ssobutton_button_color']):'#23985D') . '" id="easyVerein_ssobutton_button_color" name="easyVerein_ssobutton_button_color" />
                    </div>

                    <div class="easyVerein_ssobutton_colorpicker_item">
                    <p>Schriftfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_ssobutton_button_text_color']) ? esc_attr($userData['easyVerein_ssobutton_button_text_color']):'#FFFFFF') . '" id="easyVerein_ssobutton_button_text_color" name="easyVerein_ssobutton_button_text_color"/>
                    </div>
                </div>';
            $html .= '<button type="submit" id="easyVerein_save_ssobutton_form" name="easyVerein_save_ssobutton_form" class="easyVerein_button" disabled>Speichern</button>';
        $html .= '</form>';
    $html .= '</div>';
    return $html;
}

?>