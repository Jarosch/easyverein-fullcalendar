<?php
if (! defined('ABSPATH') ) {
    exit;
}
    // Register shortcode
    add_shortcode('easyverein_memberlist', 'easyVerein_create_memberlist');

    // Setup member list
function easyVerein_create_memberlist()
{
    wp_enqueue_style('easyVerein_public_css_member_list', EASYVEREIN_REGISTER_PUBLIC_CSS."easyVereinShortcodeMemberList.css"); 
    // Load data
    include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $vf_sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_memberlist_isvisible_%'  AND `value` = 1;", array($table_name));
    $visible_fields = $wpdb->get_results($vf_sql);
        
    $uo_sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_memberlist_order';", array($table_name));
    $db_userOrder = $wpdb->get_results($uo_sql);
    $userOrder = isset($db_userOrder[0]->value) ? $db_userOrder[0]->value : 'easyVerein_memberlist_firstname,easyVerein_memberlist_lastname,easyVerein_memberlist_address,easyVerein_memberlist_zipandcity,easyVerein_memberlist_country,easyVerein_memberlist_mail,easyVerein_memberlist_group'; 
    
    $uc_sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_memberlist_header_%' OR `option` LIKE 'easyVerein_memberlist_table_%';", array($table_name));
    $db_userColor = $wpdb->get_results($uc_sql);
    $userColor = [];

    foreach ( $db_userColor as $res ) {
        $userColor[$res->option] = $res->value;
    }

    // Build table structur
    $contactdetails = '';
    $groupdetails = '';
    $shown_fields = [];
    foreach (explode(',', $userOrder) as $uo) {
        if (in_array(str_replace('easyVerein_memberlist', 'easyVerein_memberlist_isvisible', $uo), array_column($visible_fields, 'option'))) {
            switch($uo) {
            case 'easyVerein_memberlist_address':
                array_push($shown_fields, 'street');
                break;
            case 'easyVerein_memberlist_country':
                array_push($shown_fields, 'country');
                break;
            case 'easyVerein_memberlist_firstname':
                array_push($shown_fields, 'firstName');
                break;
            case 'easyVerein_memberlist_group':
                array_push($shown_fields, 'memberGroups');
                break;
            case 'easyVerein_memberlist_lastname':
                array_push($shown_fields, 'familyName');
                break;
            case 'easyVerein_memberlist_mail':
                array_push($shown_fields, 'privateEmail', 'companyEmail');
                break;
            case 'easyVerein_memberlist_zipandcity':
                array_push($shown_fields, 'zip', 'city');
                break;
            }
        }
    }

    foreach ($visible_fields as $vf) {
        switch($vf->option) {
        case 'easyVerein_memberlist_isvisible_address':
            $contactdetails = $contactdetails.'street,';
            break;
        case 'easyVerein_memberlist_isvisible_country':
            $contactdetails = $contactdetails.'country,';
            break;
        case 'easyVerein_memberlist_isvisible_firstname':
            $contactdetails = $contactdetails.'firstName,';
            break;
        case 'easyVerein_memberlist_isvisible_group':
            $groupdetails = 'memberGroup{name}';
            break;
        case 'easyVerein_memberlist_isvisible_lastname':
            $contactdetails = $contactdetails.'familyName,';
            break;
        case 'easyVerein_memberlist_isvisible_mail':
            $contactdetails = $contactdetails.'privateEmail,companyEmail,';
            break;
        case 'easyVerein_memberlist_isvisible_zipandcity':
            $contactdetails = $contactdetails.'zip,city,';
            break;
        }
    }
    $contactdetails = substr($contactdetails, 0, -1);

    // Build query
    $query = '';
    if (strlen($contactdetails) > 0) {
        $query = 'contactDetails{'.$contactdetails.'}';
        if (strlen($groupdetails) > 0) {
            $query = $query.',memberGroups{'.$groupdetails.'}';
        }
    } else if (strlen($groupdetails) > 0) {
        $query = 'memberGroups{'.$groupdetails.'}';
    } else {
        return '<h2>Keine Felder freigegeben!</h2>';
    }

    if (isset($_POST['easyVerein_memberlist_previous'])) {
        $tableData = easyVereinAPIRequestPlain(sanitize_url($_POST['easyVerein_memberlist_previous_link']));
    } else if (isset($_POST['easyVerein_memberlist_next'])) {
        $tableData = easyVereinAPIRequestPlain(sanitize_url($_POST['easyVerein_memberlist_next_link']));
    } else {
        $tableData = easyVereinAPIRequest('member', '{'.$query.'}&limit=20');
    }

    return easyVerein_memberlist_rendertable($tableData, $shown_fields, $userColor);       
}

    // Create table
function easyVerein_memberlist_rendertable($tableData, $shown_fields, $userColor)
{
    $display_names = [
        "firstName" => "Vorname",
        "familyName" => "Nachname",
        "street" => "Adresse",
        "memberGroups" => "Gruppe(n)",
        "zip" => "PLZ",
        "city" => "Stadt",
        "privateEmail" => "Private E-Mail",
        "companyEmail" => "Geschäfts E-Mail",
        "country" => "Land",
    ];
        
    // Build table header
    $header = '';
    foreach ($shown_fields as $th) {
        $header = $header . '<th style="background-color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_color']) ? $userColor['easyVerein_memberlist_header_color']:'#23985D') . '; color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_font_color']) ? $userColor['easyVerein_memberlist_header_font_color']:'#FFFFFF') . '";>' . $display_names[$th] . '</th>';
    }

    // Build table data
    $data = '';
    $html = '';
    if (isset($tableData['data']['results'])) {
        foreach ($tableData['data']['results'] as $a) {
            $current = '<tr>';
            foreach ($shown_fields as $td) {
                $current = $current . '<td style="background-color:' . esc_attr(isset($userColor['easyVerein_memberlist_table_color']) ? $userColor['easyVerein_memberlist_table_color']:'#FFFFFF') . '; color:' . esc_attr(isset($userColor['easyVerein_memberlist_table_font_color']) ? $userColor['easyVerein_memberlist_table_font_color']:'#000000') . '";>';
                if ($td == 'memberGroups') {
                    $groups = '';
                    if (is_array($a[$td])) {
                        foreach ($a[$td] as $group) {
                            $groups = $groups . $group['memberGroup']['name'] . '<br>';
                        }
                    }
                    $current = $current . $groups . '</td>';
                } else {
                    $current = $current . $a['contactDetails'][$td] . '</td>';
                }
            }
            $current = $current . '</tr>';
            $data = $data . $current;
        }

        // Build html table 
        $html = '<div class="easyVerein_memberlist_wrapper">';
        $html .= '<table class="easyVerein_memberlist"><tr>' . $header . '</tr>' . $data . '</table>';
        $html .= '<div id="easyVerein_memberlist_navigation">';
        $html .= '<form method="post">';
        $html .= '<input id="easyVerein_memberlist_previous_link" type="hidden" name="easyVerein_memberlist_previous_link" value="' . esc_attr($tableData['data']['previous']) . '">';
        $html .= '<input id="easyVerein_memberlist_next_link" type="hidden" name="easyVerein_memberlist_next_link" value="' . esc_attr($tableData['data']['next']) . '">';
        if ($tableData['data']['previous'] != "") {
            $html .= '<button type="submit" id="easyVerein_memberlist_previous" name="easyVerein_memberlist_previous" style="background-color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_color']) ? $userColor['easyVerein_memberlist_header_color']:'#23985D') . '; color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_font_color']) ? $userColor['easyVerein_memberlist_header_font_color']:'#FFFFFF') . '" class="easyVerein_memberlist_navigation_button"><</button>';
        }
        if ($tableData['data']['next'] != "") {
            $html .= '<button type="submit" id="easyVerein_memberlist_next" name="easyVerein_memberlist_next"  style="background-color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_color']) ? $userColor['easyVerein_memberlist_header_color']:'#23985D') . '; color:' . esc_attr(isset($userColor['easyVerein_memberlist_header_font_color']) ? $userColor['easyVerein_memberlist_header_font_color']:'#FFFFFF') . '" class="easyVerein_memberlist_navigation_button">></button>';
        }
        $html .= '</div></form>';
    } else {
        $html .= '<h2>Keine Daten!</h2>';
    }
    return $html;
}
?>