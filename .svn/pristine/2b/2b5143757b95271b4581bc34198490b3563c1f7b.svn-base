<?php

function easyVerein_setupWoocommerceSyncSettings()
{
    // Save easyVerein woocommerce sync
    easyVerein_saveWoocommerceSync();

    // Load easyVerein woocommerce sync
    $state = easyVerein_loadWoocommerceSync();

    // Return html
    return easyVerein_printWoocommerceSync($state);
}

function easyVerein_saveWoocommerceSync()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Save easyVerein woocommerce sync in database
    if (isset($_POST['easyVerein_woocommerce_sync'])) {
        $state = $_POST['easyVerein_woocommerce_sync'] == 'on' ? '1' : '0';
        $data = array(
            'option' => 'easyVerein_woocommerce_sync',
            'value' => $state
        );
        $wpdb->replace($table_name, $data);
    }
}

function easyVerein_loadWoocommerceSync()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get woocommerce sync from database
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_woocommerce_sync';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $easyVerein_woocommerce_sync = isset($db_results[0]->value) ? $db_results[0]->value : '';
    if ($easyVerein_woocommerce_sync == '1') {
        return true;
    }
    return false;
}

function easyVerein_printWoocommerceSync($state)
{
    // Build html from for woocommerce sync
    $html = '';
    $html .= '<form method="post">';
    $html .= '<p class="easyVerein_headline">Synchronisieren mit WooCommerce</p>';
    $html .= '<p class="easyVerein_code">Aktivieren Sie diese Funktion, wenn die Adressdaten aus easyVerein importiert werden sollen. Diese Funktion benötigt das WordPress Plugin "WooCommerce" und ist nur in der Kombination mit der Mitglieder-Synchronisation verfügbar.</p><br>';
    if ($state) {
        $html .= '<button type="submit" id="easyVerein_woocommerce_sync" name="easyVerein_woocommerce_sync" class="easyVerein_button" value="off">WooCommerce synchronisieren deaktivieren</button>';
    } else {
        $html .= '<button type="submit" id="easyVerein_woocommerce_sync" name="easyVerein_woocommerce_sync" class="easyVerein_button" value="on">WooCommerce synchronisieren aktivieren</button>';
    }
    $html .= '</form>';
    return $html;
}
?>