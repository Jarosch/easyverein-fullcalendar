<?php

class Group
{
    public $id;
    public $name;
    public $index;

    public function __construct($id, $name, $index)
    {
        $this->id = $id;
        $this->name = $name;
        $this->index = $index;
    }
}

function easyVerein_setupMembersSyncSettings()
{
    // Save easyVerein member sync
    $hasSyncRights = easyVerein_saveMemberSync();

    // Load easyVerein member sync
    $state = easyVerein_loadMemberSync();

    // Return html
    return easyVerein_printMemberSync($state, $hasSyncRights);
}

function easyVerein_saveMemberSync()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $hasSyncRights = true;

    // Save easyVerein member sync in database
    if (isset($_POST['easyVerein_member_sync'])) {
        $state = $_POST['easyVerein_member_sync'] == 'on' ? '1' : '0';
        if ($state == '1') {
            if (!wp_next_scheduled('easyVerein_member_sync_hook')) {
                wp_schedule_event(time(), 'hourly', 'easyVerein_member_sync_hook');
            }
        }
        $data = array(
            'option' => 'easyVerein_member_sync',
            'value' => $state
        );
        $wpdb->replace($table_name, $data);
    }

    if (isset($_POST['easyVerein_sync_data'])) {
        $syncUsernames = isset($_POST['easyVerein_syncUsernames']) ? true : false;

        $data = array(
            'option' => 'easyVerein_syncUsernames',
            'value' => $syncUsernames ? '1' : '0'
        );
        $wpdb->replace($table_name, $data);

        wp_schedule_single_event(time(), 'easyVerein_member_sync_hook');
    }
    return $hasSyncRights;
}


function easyVerein_member_sync_check()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'easyVerein';

    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_member_sync'", $table_name);
    $db_results = $wpdb->get_results($sql);
    $easyVerein_member_sync = isset($db_results[0]->value) ? $db_results[0]->value : '';

    if ($easyVerein_member_sync == '1') {
        return true;
    } else {
        return false;
    }
}

function easyVerein_loadMemberSync()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get member sync from database
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_member_sync';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $easyVerein_member_sync = isset($db_results[0]->value) ? $db_results[0]->value : '';
    if ($easyVerein_member_sync == '1') {
        return true;
    }
    return false;
}

function easyVerein_printMemberSync($state, $hasSyncRights)
{
    $syncUsernames = easyVerein_loadSyncUsernames();
    $syncUsernamesChecked = $syncUsernames == '1' ? 'checked' : '';
    // Build html from for member sync
    $html = '';
    $html .= '<form method="post">';
    $html .= '<p class="easyVerein_headline">Synchronisieren von Mitgliedern</p>';
    $html .= '<p class="easyVerein_code">Aktivieren Sie diese Funktion, wenn die Mitglieder aus easyVerein importiert werden sollen. Diese Funktion ermöglicht ein Single Sign-on mittels der easyVerein Zugangsdaten. Alle Mitglieder werden stündlich automatisch zu WordPress synchronisiert.</p><br><br>';
    $html .= '<p class="easyVerein_code"><b>Wichtig: </b>In easyVerein ist es möglich, E-Mailadressen mehrfach zu verwenden sowie seinen Nutzernamen zu ändern. WordPress erlaubt dies nicht. Wird eine E-Mailadresse mehrfach verwendet, wird nur ein Mitglied in Wordpress mit dieser E-Mailadresse aus easyVerein übernommen. Wird der Benutzername in easyVerein geändert, können Sie auswählen, wie in WordPress verfahren werden soll. Administrator:innen werden nie verändert.</p><br>';
    if ($state) {
        if (!wp_next_scheduled('easyVerein_member_sync_hook')) {
            $html .= '<p>Der Cron-Daemon zur automatischen Synchronisierung ist <b>nicht</b> aktiv, bitte deaktivieren Sie die Synchronisierung und aktivieren Sie diese erneut.</p>';
        } else {
            $html .= '<p>Die automatische Synchronisierung ist aktiviert.</p>';
        }
        if (!$hasSyncRights) {
            $html .= '<p><span class="easyVerein_label easyVerein_error">Keine Berechtigung!</span> - Sie haben keine Berechtigung zur Synchronisierung von Mitgliedern.</p>';
        }
        $html .= '<br>';
        $html .= '<label class="easyVerein_switch"><input type="checkbox" name="easyVerein_syncUsernames" value="easyVerein_syncUsernames" ' . $syncUsernamesChecked . '><div class="easyVerein_slider round"></div></label>';
        $html .= '<label for="easyVerein_syncUsernames">Bei Änderung des Benutzernamens Nutzer löschen und neu anlegen</label>';
        $html .= '<p>Wenn diese Option ausgewählt ist, werden die Benutzernamen aktualisiert. Dabei werden Mitglieder, deren Benutzername in easyVerein geändert wurde, in WordPress gelöscht und neu angelegt, da WordPress das Ändern von Benutzernamen nicht unterstützt. Achtung: Durch das Löschen der Benutzer werden alle bestehenden Verknüpfungen, zum Beispiel zu WooCommerce, entfernt.</p>';
        $html .= '<button type="submit" id="easyVerein_sync_data" name="easyVerein_sync_data" class="easyVerein_button" value="off">Manuelle Synchronisierung ausführen</button>';
        if (isset($_POST['easyVerein_sync_data'])) {
            $html .= '<br><br>';
            $html .= '<p>Die Synchronisierung wird ausgeführt. Dies kann einen Moment dauern...</p>';
        }
        $html .= '<br><br>';
        $html .= '<button type="submit" id="easyVerein_member_sync" name="easyVerein_member_sync" class="easyVerein_button" value="off">Mitglieder synchronisieren deaktivieren</button>';
    } else {
        $html .= '<button type="submit" id="easyVerein_member_sync" name="easyVerein_member_sync" class="easyVerein_button" value="on">Mitglieder synchronisieren aktivieren</button>';
    }
    $html .= '</form>';
    return $html;
}

function easyVerein_getUserByCustomField($meta_key, $meta_value)
{
    $user_query = new WP_User_Query(array(
        'meta_query' => array(
            array(
                'key' => $meta_key,
                'value' => $meta_value,
                'compare' => '='
            )
        )
    ));
    $users = $user_query->get_results();

    if (!empty($users)) {
        // Return the first user in the results array
        return $users[0];
    }

    return null;
}

function easyVerein_loadSyncUsernames()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_syncUsernames';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $syncUsernames = isset($db_results[0]->value) ? $db_results[0]->value : '';

    return $syncUsernames;
}

function easyVerein_syncAllMembers()
{
    $hasSyncRights = true;
    if (easyVerein_member_sync_check() == false) {
        return $hasSyncRights;
    }

    $page_counter = 1;
    require_once EASYVEREIN_REGISTER_HELPER . 'easyVereinApiHelper.php';
    $easyVereinMembers = easyVereinAPIRequest('member', '{emailOrUserName,id,contactDetails{firstName,familyName,street,city,zip,country,privateEmail,companyEmail},memberGroups{memberGroup{id}}}&&deleted=false&limit=100');

    if ($easyVereinMembers['data'] == null) {
        return $hasSyncRights;
    }

    if ($easyVereinMembers['data']['count'] == 0) {
        $rights = easyVereinAPIRequest('member/me/permissions');
        if ($rights['data']['module_members'] == 'n') {
            $hasSyncRights = false;
        }
        return $hasSyncRights;
    }

    $results = $easyVereinMembers['data']['results'];
    $next_page = $easyVereinMembers['data']['next'] != null;
    $error_array = array();

    while ($next_page) {
        $page_counter += 1;
        $easyVereinMembers = easyVereinAPIRequest('member', '{emailOrUserName,id,contactDetails{firstName,familyName,street,city,zip,country,privateEmail,companyEmail},memberGroups{memberGroup{id}}}&&deleted=false&limit=100&page=' . $page_counter);

        if (isset($easyVereinMembers['data']['results']) && is_array($easyVereinMembers['data']['results'])) {
            $results = array_merge($results, $easyVereinMembers['data']['results']);

            if (!isset($easyVereinMembers['data']['next']) || $easyVereinMembers['data']['next'] == null) {
                $next_page = false;
            }

        } else {
            array_push($error_array, $page_counter);
        }
    }

    easyVerein_syncMembers($results);

    $results = array();
    foreach ($error_array as $invalidPage) {
        $easyVereinMembers = easyVereinAPIRequest('member', '{emailOrUserName,id,contactDetails{firstName,familyName,street,city,zip,country,privateEmail,companyEmail},memberGroups{memberGroup{id}}}&&deleted=false&limit=100&page=' . $invalidPage);
        if (isset($easyVereinMembers['data']['results']) && is_array($easyVereinMembers['data']['results'])) {
            $results = array_merge($results, $easyVereinMembers['data']['results']);
        }
    }

    easyVerein_syncMembers($results);

    return $hasSyncRights;
}

function easyVerein_syncMembers($results)
{
    $syncUsernames = easyVerein_loadSyncUsernames() == '1' ? true : false;
    foreach ($results as $user_data) {
        $emailOrUserName = $user_data['emailOrUserName'];
        $easyVereinId = $user_data['id'];
        $contactDetails = $user_data['contactDetails'];
        $memberGroups = $user_data['memberGroups'];

        $private_email = $contactDetails['privateEmail'];
        $company_email = $contactDetails['companyEmail'];
        $first_name = isset($contactDetails['firstName']) ? sanitize_text_field($contactDetails['firstName']) : '';
        $last_name = isset($contactDetails['familyName']) ? sanitize_text_field($contactDetails['familyName']) : '';

        $email_to_use = $private_email ? $private_email : $company_email;

        $email_to_use = strtolower($email_to_use);

        $existing_user = easyVerein_getUserByCustomField('easyVereinId', $easyVereinId);
        if (!$existing_user) {
            $existing_user = get_user_by('email', $email_to_use);
        }

        $user_id = 0;
        if ($existing_user) {
            $existing_user_username = $existing_user->user_login;
            $user_roles = $existing_user->roles;
            if ($syncUsernames && $existing_user_username != $emailOrUserName && !in_array('administrator', $user_roles)) {
                $password = $existing_user->user_pass;
                wp_delete_user($existing_user->ID);
                $username = sanitize_user($emailOrUserName);
                $user_args = array(
                    'user_login' => $username,
                    'user_email' => $email_to_use,
                    'user_pass' => $password,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                );

                $user_id = wp_insert_user($user_args);
                if (is_wp_error($user_id)) {
                    $error_message = $user_id->get_error_message();
                    $error_array[] = "Fehler beim Anlegen des Benutzers mit der E-Mail-Adresse oder dem Benutzernamen '$emailOrUserName': $error_message";
                }
            } else {
                $user_id = $existing_user->ID;
                $username = sanitize_user($emailOrUserName);
                $user_args = array(
                    'ID' => $user_id,
                    'user_email' => $email_to_use,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                );
                wp_update_user($user_args);
            }
        } else {
            $password = wp_generate_password(16);
            $username = sanitize_user($emailOrUserName);
            $user_args = array(
                'user_login' => $username,
                'user_email' => $email_to_use,
                'user_pass' => $password,
                'first_name' => $first_name,
                'last_name' => $last_name,
            );

            $user_id = wp_insert_user($user_args);
            if (is_wp_error($user_id)) {
                $error_message = $user_id->get_error_message();
                $error_array[] = "Fehler beim Anlegen des Benutzers mit der E-Mail-Adresse oder dem Benutzernamen '$emailOrUserName': $error_message";
            }
        }

        $user = get_user_by('id', $user_id);
        if ($user) {
            $user->set_role(easyVerein_findUserGroup($memberGroups));
            update_user_meta($user_id, 'easyVereinId', $easyVereinId);
        }
        easyVerein_addWoocommerceData($easyVereinId, $email_to_use, $contactDetails['street'], $contactDetails['city'], $contactDetails['zip'], $contactDetails['country']);
    }
}

function easyVerein_loadAllUserGroups()
{
    $groupList = array();

    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_group_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    foreach ($db_userSettings as $res) {
        $decoded = json_decode($res->value);
        $group = new Group($decoded->id, $decoded->name, $decoded->index);
        array_push($groupList, $group);
    }

    usort($groupList, function ($a, $b) {
        return $a->index <=> $b->index;
    });

    return $groupList;
}


function easyVerein_findUserGroup($memberGroups)
{
    $userSettings = easyVerein_loadAllUserGroups();
    $selected_group = get_option('default_role');
    $bestIndex = PHP_INT_MAX;

    if (empty($memberGroups) || empty($userSettings)) {
        return $selected_group;
    }

    foreach ($memberGroups as $group) {
        $memberGroupID = $group['memberGroup']['id'];
        foreach ($userSettings as $userSetting) {
            $numericId = substr($userSetting->id, strrpos($userSetting->id, '_') + 1);

            if ($memberGroupID == $numericId) {
                if ($userSetting->index < $bestIndex) {
                    $bestIndex = $userSetting->index;
                    $selected_group = $userSetting->name;
                }
            }
        }
    }
    return $selected_group;
}


function easyVerein_addWoocommerceData($easyVereinId, $email, $address_1, $city, $postcode, $country)
{

    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get woocommerce sync from database
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_woocommerce_sync';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $easyVerein_woocommerce_sync = isset($db_results[0]->value) ? $db_results[0]->value : '';
    if ($easyVerein_woocommerce_sync == '1') {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $existing_user = easyVerein_getUserByCustomField('easyVereinId', $easyVereinId);
            if (!$existing_user) {
                $existing_user = get_user_by('email', $email);
            }
            if ($existing_user) {
                $user_id = $existing_user->ID;
                update_user_meta($user_id, 'billing_address_1', $address_1);
                update_user_meta($user_id, 'billing_city', $city);
                update_user_meta($user_id, 'billing_postcode', $postcode);
                update_user_meta($user_id, 'billing_state', $country);
            } else {
                return;
            }
        }
    }
}

?>