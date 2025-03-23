<?php
if (! defined('ABSPATH') ) {
    exit;
}
    add_shortcode('easyverein_protocol', 'easyVerein_printProtocol');

function easyVerein_printProtocol($atts = [])
{
    wp_enqueue_style('easyVerein_public_css_protocol', EASYVEREIN_REGISTER_PUBLIC_CSS."easyVereinShortcodeProtocol.css"); 
    // Load api helper
    include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';

    // Load database data
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_protocol_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }

    // Load attributes from shortcode
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $easyVerein_protocol_atts = shortcode_atts(
        array(
            'showdescription' => 'true',
            'showprologue' => 'true',
        ), $atts
    );

    // Valid description attributes
    if ($easyVerein_protocol_atts['showdescription'] == 'false') {
        $easyVerein_protocol_atts['showdescription'] = false;
    } else {
        $easyVerein_protocol_atts['showdescription'] = true;
    }

    // Valid prologue attributes
    if ($easyVerein_protocol_atts['showprologue'] == 'false') {
        $easyVerein_protocol_atts['showprologue'] = false;
    } else {
        $easyVerein_protocol_atts['showprologue'] = true;
    }

    // Response
    $html = '';

      
    // Collecting data
    $count = isset($userSettings['easyVerein_protocol_limit']) ? $userSettings['easyVerein_protocol_limit']:5;
    $protocolData = easyVereinAPIRequest('protocol', '{name,start,protocolElements,description,prologue}&deleted=false&limit=' . $count);
        
    // Build response
    $html .= '<div class="easyVerein_protocols">';
    if ($protocolData['data']['count'] == 0) {
        $html .= '<div class="easyVerein_protocol" style="background-color:' . esc_attr(isset($userSettings['easyVerein_protocol_background_color']) ? $userSettings['easyVerein_protocol_background_color']:'#23985D') . '; color:' . esc_attr(isset($userSettings['easyVerein_protocol_text_color']) ? $userSettings['easyVerein_protocol_text_color']:'#FFFFFF') . '; border-color:' . esc_attr(isset($userSettings['easyVerein_protocol_text_color']) ? $userSettings['easyVerein_protocol_text_color']:'#FFFFFF') . ';">';
        $html .= '<p class="easyVerein_protocol_title"><b>Es gibt derzeit keine Sitzungsprotokolle</b></p><div>';
        $html .= '</div>';
    } else {
        $timezone = get_option('timezone_string');
        if (empty($timezone)) {
            $gmt_offset = get_option('gmt_offset');
            if ($gmt_offset == 0) {
                $timezone = 'UTC';
            } else {
                $timezone = timezone_name_from_abbr('', $gmt_offset * 3600, 0);
                if ($timezone === false) $timezone = 'UTC';
            }
        }
        date_default_timezone_set($timezone);
        foreach ($protocolData['data']['results'] as $p) {
            $html .= '<div class="easyVerein_protocol" style="background-color:' . esc_attr(isset($userSettings['easyVerein_protocol_background_color']) ? $userSettings['easyVerein_protocol_background_color']:'#23985D') . '; color:' . esc_attr(isset($userSettings['easyVerein_protocol_text_color']) ? $userSettings['easyVerein_protocol_text_color']:'#FFFFFF') . '; border-color:' . esc_attr(isset($userSettings['easyVerein_protocol_text_color']) ? $userSettings['easyVerein_protocol_text_color']:'#FFFFFF') . ';">';
            $html .= '<p class="easyVerein_protocol_title"><b>' . $p['name'] . '</b></p>';
            $html .= '<p><b>' . date('d.m.Y', strtotime($p['start'])) . '</b></p>';
            if ($easyVerein_protocol_atts['showprologue']) {
                $html .= (strlen($p['prologue']) > 0) ? '<br><b>Vorwort:</b><br><p>' . $p['prologue'] . '</p>':'';
            }
            if ($easyVerein_protocol_atts['showdescription']) {
                $html .= (strlen($p['description']) > 0) ? '<br><b>Beschreibung:</b><br><p>' . $p['description'] . '</p>':'';
            }
            $html .= easyVerein_printProtocolElements($p['protocolElements'], $userSettings);
            $html .= '</div>'; 
        }
    }
    $html .= '</div>';
        
    return $html;
}

function easyVerein_printProtocolElements($urlList, $userSettings)
{
    // Build protocol elements
    $html = '<br>';
    foreach ($urlList as $ul) {
        $element = easyVereinAPIRequestPlain($ul);
        $html .= '<div class="easyVerein_protocol_elements style="border-color:' . esc_attr(isset($userSettings['easyVerein_protocol_text_color']) ? $userSettings['easyVerein_protocol_text_color']:'#FFFFFF') . ';">';
        $html .= '<p><b>#' . $element['data']['order'] . ' - ' . $element['data']['title'] . '</b></p>';
        $html .= '<p>' . $element['data']['text'] . '</p>';
        $html .= '</div>';
    }
    return $html;
}
?>