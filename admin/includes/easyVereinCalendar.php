<?php
function easyVerein_setupCalendarSettings()
{
    // Save settings in database
    if (isset($_POST['easyVerein_save_calendar'])) {
        easyVerein_saveCalendar();
    }
        
    // Return html
    return easyVerein_loadCalendar();
}    

function easyVerein_loadCalendar()
{
    // Collect data 
    $userData = easyVerein_loadCalendarData();
    $calendar = easyVerein_loadCalendarTable();

    // Return html
    return easyVerein_calendarSettings($userData, $calendar);
}

function easyVerein_loadCalendarData()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Load settings from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_calendar_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    // Map result
    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }
        
    // Return calendar user settings
    return $userSettings;
}

function easyVerein_loadCalendarTable()
{
    // Include easyVerein api helper
    include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';
        
    // Get claender id for admin dashboard 
    $calendarIDs = easyVereinAPIRequest('calendar', '{id,name,color,short}&limit=100');

    // Build html table
    $html = '';
    if (isset($calendarIDs['data']['results'])) {
        foreach ($calendarIDs['data']['results'] as $c) {
            $html .= '<p><b>' . $c['id'] . '</b> - <span style="color:' . $c['color'] . '">&#11044;</span> ' . $c['name'] . ' (' . $c['short'] . ')</p>';
        }
    }

    return $html;
}

function easyVerein_saveCalendar()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
  
    // Set calendar limit
    $easyVerein_calendar_limit = array(
      'option' => 'easyVerein_calendar_limit',
      'value' => isset($_POST['easyVerein_calendar_limit']) ? sanitize_text_field($_POST['easyVerein_calendar_limit']):'5'
    );
  
    // Set calendar backgrodund color
    $easyVerein_calendar_background_color = array(
      'option' => 'easyVerein_calendar_background_color',
      'value' => isset($_POST['easyVerein_calendar_background_color']) ? sanitize_hex_color($_POST['easyVerein_calendar_background_color']):'#23985D'
    );
  
    // Set calendar text color
    $easyVerein_calendar_text_color = array(
      'option' => 'easyVerein_calendar_text_color',
      'value' => isset($_POST['easyVerein_calendar_text_color']) ? sanitize_hex_color($_POST['easyVerein_calendar_text_color']):'#FFFFFF'
    );
  
    // Save settings in database
    $wpdb->replace($table_name, $easyVerein_calendar_limit);
    $wpdb->replace($table_name, $easyVerein_calendar_background_color);
    $wpdb->replace($table_name, $easyVerein_calendar_text_color);
}

function easyVerein_calendarSettings($userData, $calendar)
{
    // Build html element
    $html = '';
    $html .= '<div id="easyVerein_calendar">';
        $html .= '<form method="post">';
        $html .= '<p class="easyVerein_headline">Kalender</p>';
            $html .= '<p class="easyVerein_code">Shortcode: <code>[easyverein_calendar id="KalenderId" onlyPublic="true/false" showDescription="true/false"]</code><br><code>id="KalenderId"</code> Die KalenderId ist eine einzigartige Nummer, die jeden Kalender genau identifiziert. Alle verfügbaren KalenderId\'s stehen unten in der Liste.<br><code>onlyPublic="true/false"</code> Legt fest ob nur öffentliche Termine angezeigt werden.<br><code>showDescription="true/false"</code> Bestimmt ob die Beschreibung des Termins angezeigt wird.<br><br>Beispiel: <code>[easyverein_calendar id="123456789" onlyPublic="true" showDescription="true"]</code> Zeigt vom Kalender mit der ID <code>123456789</code> alle öffentlichen Termine mit Beschreibung an.</p>';
            $html .= '<button type="button" class="easyVerein_accordion">Hier findet Ihr eure KalenderIds &#8595;</button>
                <div class="easyVerein_accordion_panel">';
                $html .= $calendar;
            $html .= '</div><br><br>';
            $html .= '<p><b>Einstellungen:</b></p>';
            $html .= '<div class="easyVerein_calendar_colorpicker">
                    <div class="easyVerein_calendar_colorpicker_item">
                    <p>Hintergrundfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_calendar_background_color']) ? esc_attr($userData['easyVerein_calendar_background_color']):'#23985D') . '" id="easyVerein_calendar_background_color" name="easyVerein_calendar_background_color" />
                    </div>
                    <div class="easyVerein_calendar_colorpicker_item">
                    <p>Schriftfarbe</p>
                    <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userData['easyVerein_calendar_text_color']) ? esc_attr($userData['easyVerein_calendar_text_color']):'#FFFFFF') . '" id="easyVerein_calendar_text_color" name="easyVerein_calendar_text_color"/>
                    </div>
                </div>';
            $html .= '<p>Anzahl der Kalendereinträge (1-20)</p>';
            $html .= '<input type="number" class="easyVerein_calendar_limit" min="1" max="20" value="' . esc_attr(isset($userData['easyVerein_calendar_limit']) ? $userData['easyVerein_calendar_limit']:5) . '" id="easyVerein_calendar_limit" name="easyVerein_calendar_limit" list="calendarScale" />';
            $html .= '<button type="submit" id="easyVerein_save_calendar" name="easyVerein_save_calendar" class="easyVerein_button" disabled>Speichern</button>';
        $html .= '</form>';
    $html .= '</div>';
    return $html;
}

?>