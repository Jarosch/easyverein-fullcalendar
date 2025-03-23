<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('easyverein_calendar', 'easyVerein_printCalendar');

function easyVerein_printCalendar($atts = [])
{
    wp_enqueue_style('easyVerein_public_css_calendar', EASYVEREIN_REGISTER_PUBLIC_CSS . "easyVereinShortcodeCalendar.css");
    // Load api helper
    include_once EASYVEREIN_REGISTER_HELPER . 'easyVereinApiHelper.php';

    // Load database data
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_calendar_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    $userSettings = [];
    foreach ($db_userSettings as $res) {
        $userSettings[$res->option] = $res->value;
    }

    // Load attributes from shortcode
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $easyVerein_calendar_atts = shortcode_atts(
        array(
            'id' => '0',
            'onlypublic' => 'true',
            'showdescription' => 'true',
        ),
        $atts
    );

    // Valid public attributes
    if ($easyVerein_calendar_atts['onlypublic'] == 'false') {
        $easyVerein_calendar_atts['onlypublic'] = 'false';
    } else {
        $easyVerein_calendar_atts['onlypublic'] = 'true';
    }

    // Valid description attributes
    if ($easyVerein_calendar_atts['showdescription'] == 'false') {
        $easyVerein_calendar_atts['showdescription'] = false;
    } else {
        $easyVerein_calendar_atts['showdescription'] = true;
    }

    // Response
    $html = '';

    // Check calendar id
    if ($easyVerein_calendar_atts['id'] == 0) {
        return "<h2>Kalender-ID fehlerhaft!</h2>";
    } else {
        // Collecting data
        $count = isset($userSettings['easyVerein_calendar_limit']) ? sanitize_text_field($userSettings['easyVerein_calendar_limit']) : 5;
        $isPublicParam = ($easyVerein_calendar_atts['onlypublic'] == 'true') ? '&isPublic=true' : '';
        $calendarData = easyVereinAPIRequest('event', '{id,name,start,end,allDay,description,locationName}&ordering=start&start__gte=' . date("Y-m-d H:i:s.u") . '&calendar=' . $easyVerein_calendar_atts['id'] . $isPublicParam . '&deleted=false&limit=' . $count);
        $organizationData = easyVereinAPIRequest('organization', '{name,short}');
        $organizationShort = isset($organizationData['data']['results'][0]['short']) ? $organizationData['data']['results'][0]['short'] : 'Verein';
        // Build response
        $html .= '<div class="easyVerein_calendar">';
        if ($calendarData['data']['count'] == 0) {
            $html .= '<div class="easyVerein_calendar_event" style="background-color:' . esc_attr(isset($userSettings['easyVerein_calendar_background_color']) ? $userSettings['easyVerein_calendar_background_color'] : '#23985D') . '; color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . '; border-color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . ';">';
            $html .= '<p class="easyVerein_calendar_event_title"><b>Derzeit gibt es keine Termine</b></p><div>';
            $html .= '</div>';
        } else {
            $timezone = get_option('timezone_string');
            if (empty($timezone)) {
                $gmt_offset = get_option('gmt_offset');
                if ($gmt_offset == 0) {
                    $timezone = 'UTC';
                } else {
                    $timezone = timezone_name_from_abbr('', $gmt_offset * 3600, 0);
                    if ($timezone === false)
                        $timezone = 'UTC';
                }
            }
            date_default_timezone_set($timezone);
            foreach ($calendarData['data']['results'] as $c) {
                $html .= '<div class="easyVerein_calendar_event" style="background-color:' . esc_attr(isset($userSettings['easyVerein_calendar_background_color']) ? $userSettings['easyVerein_calendar_background_color'] : '#23985D') . '; color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . '; border-color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . ';">';
                $html .= '<p class="easyVerein_calendar_event_title"><b>' . $c['name'] . '</b></p>';
                if (date('d.m.Y', strtotime($c['start'])) == date('d.m.Y', strtotime($c['end']))) {
                    if ($c['allDay']) {
                        $html .= '<p><b>' . date('d.m.Y', strtotime($c['start'])) . ' | Ganztägig</b> ' . $c['locationName'] . '</p>';
                    } else {
                        $html .= '<p><b>' . date('d.m.Y', strtotime($c['start'])) . ' | ' . date('H:i', strtotime($c['start'])) . ' Uhr - ' . date('H:i', strtotime($c['end'])) . ' Uhr</b> ' . $c['locationName'] . '</p>';
                    }
                } else {
                    if ($c['allDay']) {
                        $html .= '<p><b>' . date('d.m.Y', strtotime($c['start'])) . ' - ' . date('d.m.Y', strtotime($c['end'])) . ' | Ganztägig</b> ' . $c['locationName'] . '</p>';
                    } else {
                        $html .= '<p><b>' . date('d.m.Y', strtotime($c['start'])) . ' | ' . date('H:i', strtotime($c['start'])) . ' Uhr</b> ' . $c['locationName'] . '</p>';
                        $html .= '<p><b>' . date('d.m.Y', strtotime($c['end'])) . ' | ' . date('H:i', strtotime($c['end'])) . ' Uhr</b></p>';
                    }
                }
                $html .= '<p><a href="https://easyverein.com/public/' . $organizationShort . '/calendar/' . $c['id'] . '" target="_blank" style="color: ' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . ';">' . 'Link zum Termin' . '</a></p><br>';
                if ($easyVerein_calendar_atts['showdescription']) {
                    $html .= (strlen($c['description']) > 0) ? '<br><p>' . $c['description'] . '</p>' : '';
                }
                $html .= '</div>';
            }
        }
        $html .= '</div>';
    }
    return $html;
}
?>