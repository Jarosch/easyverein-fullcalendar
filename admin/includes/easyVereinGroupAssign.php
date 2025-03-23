<?php

function easyVerein_saveGroupAssignments() {  
    $groupList = array();
    $index = 0;

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'easyVerein_group_') !== false) {
            $group = new Group($key, $value, $index);
            array_push($groupList, $group);
            $index += 1;
        }
    }
    easyVerein_saveGroupAssignData($groupList);
}

function easyVerein_loadGroupAssignData() {
    $groupList = array();

    // Global database
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Load settings from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_group_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    // Map results to array
    foreach ($db_userSettings as $res) {
        $decoded = json_decode($res->value);
        $group = new Group($decoded->id, $decoded->name, $decoded->index);
        array_push($groupList, $group);
    }
    
    usort($groupList, function($a, $b) {
        return $a->index <=> $b->index;
    });

    // Return group settings
    return $groupList;
}

function easyVerein_saveGroupAssignData($groupList){
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Save new settings
    foreach ($groupList as $group) {

        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name` WHERE `option` = %s", $group->id));

        if ($exists) {
            $sql = $wpdb->prepare("UPDATE `$table_name` SET `value` = %s WHERE `option` = %s", json_encode($group), $group->id);
        } else {
            $sql = $wpdb->prepare("INSERT INTO `$table_name` (`option`, `value`) VALUES (%s, %s)", $group->id, json_encode($group));
        }
        $wpdb->query($sql);
    }
}



function easyVerein_syncAllGroups() {
    if (isset($_POST['easyVerein_save_groups'])) {
        easyVerein_saveGroupAssignments();
    }
    try {

        // Include easyVerein api helper
        include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';

        // Get all groups from easyVerein
        $page_counter = 1;
        $easyVereinGroups = easyVereinAPIRequest('member-group', '{id, name, color, short}&limit=100'.$page_counter);
        
        if($easyVereinGroups['data'] == null) {
            return easyVerein_buildHTML([], null, [], []);
            return;
        }

        $results = $easyVereinGroups['data']['results'];
        $next_page = $easyVereinGroups['data']['next'] != null;
        while ($next_page) {
            $page_counter += 1;
            $easyVereinGroups = easyVereinAPIRequest('member-group', '{id, name, color, short}&limit=100&page='.$page_counter);
            $results = array_merge($results, $easyVereinGroups['data']['results']);
            if ($easyVereinGroups['data']['next'] == null) {
                $next_page = false;
            }
        }

        // Get all roles
        $wp_roles = wp_roles();
        $roles = $wp_roles->get_names();
        
        // Get default role
        $default_role = get_option('default_role');    

        // Load user settings
        $userSettings = easyVerein_loadGroupAssignData();

        // Sort groups by user settings
        $sortOrder = [];
        foreach ($userSettings as $group) {
            $numericId = substr($group->id, strrpos($group->id, '_') + 1);
            $sortOrder[$numericId] = $group->index;
        }

        usort($results, function($a, $b) use ($sortOrder) {
            $idA = $a['id'];
            $idB = $b['id'];
            $indexA = isset($sortOrder[$idA]) ? $sortOrder[$idA] : PHP_INT_MAX;
            $indexB = isset($sortOrder[$idB]) ? $sortOrder[$idB] : PHP_INT_MAX;
            return $indexA <=> $indexB;
        });

        return easyVerein_buildHTML($results, $default_role, $roles, $userSettings);
    } catch (Exception $e) {
        return easyVerein_buildHTML([], null, [], []);
    }
}

function easyVerein_buildHTML($results, $default_role, $roles, $userSettings) {
    // Build html element
    $html = '';
    $html .= '<div id="easyVerein_groups">';
        $html .= '<form method="post">';
        $html .= '<p class="easyVerein_headline">Gruppen zuweisen</p>';
        $html .= '<p class="easyVerein_code">Wählen Sie die Gruppen aus, die Sie zuweisen möchten. Die Gruppen werden in der Reihenfolge angezeigt, in der sie in easyVerein angelegt wurden. Die Reihenfolge kann durch Drag & Drop verändert werden. Die Reihenfolge bestimmt die Priorisierung der Zuweisung bei mehreren Gruppenzuweisungen, sodass immer die höchste (oben) Gruppe zugeweisen wird.</p><br>';
            $html .= '<button type="button" class="easyVerein_accordion">Hier finden Sie alle Ihre easyVerein Gruppen &#8595;</button>
                <div class="easyVerein_accordion_panel"><br>';
                $html .= '<p><b>Höchste Priorität</b></p>';
                $html .= '<ul id="easyVerein_sortable_groups">';
                foreach ($results as $easyVereinGroup) {

                    // Get selected role
                    $selected_role = $default_role;
                    $searchId = "easyVerein_group_" . $easyVereinGroup['id'];
                    foreach ($userSettings as $group) {
                        if ($group->id === $searchId) {
                            $selected_role = $group->name;
                        }
                    }

                    $html .= '<li class="easyVerein_group">';
                        $html .= '<label for="easyVerein_group_'.esc_attr($easyVereinGroup['id']).'">' . esc_attr($easyVereinGroup['name']) . '</label>';
                        $html .= '<span>⠿</span> <select class="easyVerein_group" name="easyVerein_group_'.esc_attr($easyVereinGroup['id']).'" id="easyVerein_group_'.esc_attr($easyVereinGroup['id']).'">';
                            // Build role options
                            foreach ($roles as $role_slug => $role_name) {
                                if ($role_slug == $selected_role) {
                                    $html .= '<option value="' . esc_attr($role_slug) . '" selected>' . esc_attr($role_name) . '</option>';
                                } else {
                                    $html .= '<option value="' . esc_attr($role_slug) . '">' . esc_attr($role_name) . '</option>';
                                }
                            }
                        $html .= '</select>';

                    $html .= '</li>';
                }
                $html .= '</ul>';  
                $html .= '<p><b>Niedrigste Priorität</b></p><br>';              
                $html .= '<br><br><button type="submit" id="easyVerein_save_groups" name="easyVerein_save_groups" class="easyVerein_button">Speichern</button><br><br>';
            $html .= '</div><br><br>';
        $html .= '</form>';
    $html .= '</div>';
    return $html;
}
?>