<?php
    
function easyVerein_setupAPIKeySettings()
{
    // Save api key
    easyVerein_saveAPIKey();

    // Load api key
    $api_key = easyVerein_loadAPIKey();
    $checkCollection = easyVereinAPIRequest('organization', '{name}');

    // Return html
    return easyVerein_printAPIKey($checkCollection, $api_key);
}

function easyVerein_saveAPIKey()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
        
    // Save api key in database
    if (isset($_POST['easyVerein_save_api_key'])) {
        $data = array(
            'option' => 'easyVerein_api_key',
            'value' => sanitize_text_field($_POST['easyVerein_api_key'])
        );
        $wpdb->replace($table_name, $data);
    }
}

function easyVerein_loadAPIKey()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
        
    // Get api key frop database
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_api_key';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $api_key = isset($db_results[0]->value) ? $db_results[0]->value : '';
    return $api_key;
}

function easyVerein_printAPIKey($checkCollection, $api_key)
{
    // Build html from for api key
    $html = '';
    $html .= '<p class="easyVerein_headline">easyVerein API-Schlüssel</p>';
    $html .= '<div class="easyVerein_container">';
    $html .= '<div class="easyVerein_left">';

    $html .= '<form method="post">';
    if ($checkCollection['error']) {
        $html .= '<div class="easyVerein_label easyVerein_error">Keine Verbindung möglich! - ' . esc_attr($checkCollection['error-message']) . '</div>';
    } else {
        $html .= '<div class="easyVerein_label easyVerein_success">Verbindung erfolgreich! - ' . esc_attr($checkCollection['data']["results"][0]["name"]) . '</div>';
    }
    $html .= '<br>'; 
    $html .= '<input placeholder="easyVerein API-Schlüssel" id="easyVerein_api_key" name="easyVerein_api_key" value="'. esc_attr($api_key) .'"/>';
    $html .= '<button type="submit" id="easyVerein_save_api_key" name="easyVerein_save_api_key" class="easyVerein_button" disabled>Speichern</button>';
    $html .= '</form>';

    $html .= '</div>'; // Close left div

    $html .= '<div class="easyVerein_right">';
    $html .= '<p>Um das WordPress Plugin mit easyVerein verbinden zu können, benötigen Sie einen API Key.<br><ul><li><a href="https://hilfe.easyverein.com/en/articles/361090">Zur easyVerein Dokumentation &raquo;</a></li><li><a href="https://easyverein.com/app/settings/easyVereinApi/">Zu den API-Einstellungen &raquo;</a></li></ul></p>';
    // Add more content as needed
    $html .= '</div>'; // Close right div

    $html .= '</div>'; // Close container div

    return $html;
}

function easyVerein_checkApiStatus($api_key) {
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    $request = wp_remote_get('https://easyverein.com/api/v2.0/organization/', $args);
    $status_code = wp_remote_retrieve_response_code($request);

    if ($status_code != 200) {
        $request = wp_remote_get('https://easyverein.com/api/v1.7/organization/', $args);
        $status_code = wp_remote_retrieve_response_code($request);
        return $status_code == 200 ? 'v1.7' : false;
    }

    return 'v2.0';
}
?>