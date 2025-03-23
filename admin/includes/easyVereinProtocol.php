<?php

function easyVerein_setupProtocolSettings()
{
    // Save settings in database
    if (isset($_POST['easyVerein_save_protocol'])) {
        easyVerein_saveprotocol();
    }
    
    // Load protocolData
    $userData = easyVerein_loadProtocolData();

    // Return html element
    return easyVerein_printProtocolSettings($userData);
}
    

function easyVerein_loadProtocolData()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Load data from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_protocol_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);   

    // Map data
    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }

    return $userSettings;
}

function easyVerein_saveprotocol()
{
    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
  
    // Set protocol limit
    $easyVerein_protocol_limit = array(
      'option' => 'easyVerein_protocol_limit',
      'value' => isset($_POST['easyVerein_protocol_limit']) ? sanitize_text_field($_POST['easyVerein_protocol_limit']):'5'
    );
  
    // Set protocol bnackground color
    $easyVerein_protocol_background_color = array(
      'option' => 'easyVerein_protocol_background_color',
      'value' => isset($_POST['easyVerein_protocol_background_color']) ? sanitize_hex_color($_POST['easyVerein_protocol_background_color']):'#23985D'
    );
  
    // Set protocol limit
    $easyVerein_protocol_text_color = array(
      'option' => 'easyVerein_protocol_text_color',
      'value' => isset($_POST['easyVerein_protocol_text_color']) ? sanitize_hex_color($_POST['easyVerein_protocol_text_color']):'#FFFFFF'
    );
  
    // Save settings in database
    $wpdb->replace($table_name, $easyVerein_protocol_limit);
    $wpdb->replace($table_name, $easyVerein_protocol_background_color);
    $wpdb->replace($table_name, $easyVerein_protocol_text_color);
}

function easyVerein_printProtocolSettings($userData)
{
    // Buíld html
    $html = '';
    $html .= '<div id="easyVerein_protocol">';
        $html .= '<form method="post">';
            $html .= '<p class="easyVerein_headline">Sitzungsprotokolle</p>';
            $html .= '<p class="easyVerein_code">Shortcode: <code>[easyverein_protocol showPrologue="true/false" showDescription="true/false"]</code><br><code>showPrologue</code> Bestimmt ob das Vorwort angezeigt wird.<br><code>showDescription="true/false"</code> Bestimmt ob die Beschreibung angezeigt wird.<br><br>Beispiel: <code>[easyverein_protocol showPrologue="true" showDescription="true"]</code> Zeigt Sitzungsprotokolle mit Vorwort und Beschreibung an.</p>';
            $html .= '<p><b>Einstellungen:</b></p>';
            $html .= '<div class="easyVerein_protocol_colorpicker">
                    <div class="easyVerein_protocol_colorpicker_item">
                    <p>Hintergrundfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_protocol_background_color']) ? esc_attr($userData['easyVerein_protocol_background_color']):'#23985D') . '" id="easyVerein_protocol_background_color" name="easyVerein_protocol_background_color" />
                    </div>

                    <div class="easyVerein_protocol_colorpicker_item">
                    <p>Schriftfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_protocol_text_color']) ? esc_attr($userData['easyVerein_protocol_text_color']):'#FFFFFF') . '" id="easyVerein_protocol_text_color" name="easyVerein_protocol_text_color"/>
                    </div>
                </div>';
            $html .= '<p>Anzahl der Sitzungsprotokolle pro Seite (1-20)</p>';
            $html .= '<input type="number" class="easyVerein_protocol_limit" min="1" max="20" value="' . esc_attr(isset($userData['easyVerein_protocol_limit']) ? esc_attr($userData['easyVerein_protocol_limit']):5) . '" id="easyVerein_protocol_limit" name="easyVerein_protocol_limit" list="protocolScale" />';
            $html .= '<button type="submit" id="easyVerein_save_protocol" name="easyVerein_save_protocol" class="easyVerein_button" disabled>Speichern</button>';
        $html .= '</form>';
    $html .= '</div>';
    return $html;
}

?>