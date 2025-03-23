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
		
		// Rearranging timezone setup earlier as already important for building Fullcalendar
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
		
		// Building FullCalendar
		
		$html .= "\n";
		$html .= "<link rel='stylesheet' href='https://unpkg.com/tippy.js@6/animations/scale.css'/>\n";
		$html .= "<script src='https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js'></script>";
		$html .= "<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.40/builds/moment-timezone-with-data.min.js'></script>";
  	  	$html .= "<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>\n";
		$html .= "<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js'></script>";
		$html .= "<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/moment-timezone@6.1.15/index.global.min.js'></script>";
		$html .= "<script src='https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js'></script>";
		$html .= "<script src='https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js'></script>";
   		$html .= "<script>\n";

		$html .= "document.addEventListener('DOMContentLoaded', function() {\n";
        $html .= "	var calendarEl = document.getElementById('calendar');\n";
        $html .= "	var calendar = new FullCalendar.Calendar(calendarEl, {\n";
  		$html .= "		initialView: 'multiMonthFourMonth',\n";
		$html .= "		timeZone: 'Europe/Berlin',\n";
		$html .= "		locale: 'de',\n";
		$html .= "		nextDayThreshold: '07:00:00',\n";
  		$html .= "		views: {\n";
		$html .= "			multiMonthFourMonth: {\n";
      	$html .= "			type: 'multiMonth',\n";
      	$html .= "			duration: { months: 3 }\n";
		$html .= "		}},\n";
		$html .= "		eventDidMount: function(info) {\n";
		$html .= "			var tooltip = new tippy(info.el, {\n";
		$html .= "				content: info.event.extendedProps.description,\n";
		$html .= "			});\n";
      	$html .= "		},\n";
		$html .= "		events : [\n";

		
		$i = 0;
		$last_key = count($calendarData['data']['results']);

		foreach ($calendarData['data']['results'] as $c) {
			
			
			if ($i == $last_key) {
				
				// if last element then string should end without comma
				
				if (date('H:i:s', strtotime($c['start'])) == "00:00:00") {
					
					/* When working with all-day events, it seems that Fullcalendar doesn't show the duration properly when the events lasts longer than one day.
					It becomes necessary to work the 'nextDayThreshold' param within the Fullcalendar object and to define the start time greater than 00:00:00. 
					*/
					$html .= "{ start: \"".date('Y-m-d', strtotime($c['start']))."T08:00:00\"";
				} 
				else {
					$html .= "{ start: \"".date('Y-m-d', strtotime($c['start']))."T".date('H:i:s', strtotime($c['start']))."\"";
				}

				$html .= ", end: \"". date('Y-m-d', strtotime($c['end']))."T".date('H:i:s', strtotime($c['end']))."\"";
				$html .= ", description: \"".$c['name']."\" }\n";
				
			} else {
				
				if (date('H:i:s', strtotime($c['start'])) == "00:00:00") {
					// 
					$html .= "{ start: \"".date('Y-m-d', strtotime($c['start']))."T08:00:00\"";
				} 
				else {
					$html .= "{ start: \"".date('Y-m-d', strtotime($c['start']))."T".date('H:i:s', strtotime($c['start']))."\"";
				}
				
				$html .= ", end: \"". date('Y-m-d', strtotime($c['end']))."T".date('H:i:s', strtotime($c['end']))."\"";
				$html .= ", description: \"".$c['name']."\" },\n";
			}
			
	
			$i++;
		}

		$html .= "]\n";
		$html .= "});\n";

    	$html .= "calendar.render();\n";
    	$html .= "});\n";
		$html .= "</script>\n";
		$html .= "<div id='calendar' style='min-width: 90%; max-height: 600px;'></div>";
		
        // Build response
        $html .= '<div class="easyVerein_calendar">';
        if ($calendarData['data']['count'] == 0) {
            $html .= '<div class="easyVerein_calendar_event" style="background-color:' . esc_attr(isset($userSettings['easyVerein_calendar_background_color']) ? $userSettings['easyVerein_calendar_background_color'] : '#23985D') . '; color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . '; border-color:' . esc_attr(isset($userSettings['easyVerein_calendar_text_color']) ? $userSettings['easyVerein_calendar_text_color'] : '#FFFFFF') . ';">';
            $html .= '<p class="easyVerein_calendar_event_title"><b>Derzeit gibt es keine Termine</b></p><div>';
            $html .= '</div>';
        } else {

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