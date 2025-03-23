<?php
function easyVerein_setupMemberListSettings()
{
    // Save member list
    if (isset($_POST['easyVerein_save_memberlist'])) {
        easyVerein_saveMemberList();
    }

        // Load data for member list
        $userResults = easyVerein_loadMemberListSettings();
        $userOrder = easyVerein_loadMemberListOrder();
        $userColor = easyVerein_loadMemberListColors();

        // Return html element
        return easyVerein_printMemberList($userResults, $userOrder, $userColor);
}

function easyVerein_loadMemberListSettings()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get data from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_memberlist_isvisible_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);
    $userResults = [];

    // Map results
    foreach ( $db_userSettings as $res ) {
        if ($res->value == 1) {
            $userResults[$res->option] = 'checked="true"';
        } else {
            $userResults[$res->option] = '';
        }
    }

        // Return user settings
        return $userResults;
}

function easyVerein_loadMemberListOrder()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get user order from database
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_memberlist_order';", array($table_name));
    $db_userOrder = $wpdb->get_results($sql);
    $userOrder = isset($db_userOrder[0]->value) ? $db_userOrder[0]->value : 'easyVerein_memberlist_firstname,easyVerein_memberlist_lastname,easyVerein_memberlist_address,easyVerein_memberlist_zipandcity,easyVerein_memberlist_country,easyVerein_memberlist_mail,easyVerein_memberlist_group';
      
    // Return user order
    return $userOrder;
}

function easyVerein_loadMemberListColors()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Get user color from database
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_memberlist_header_%' OR `option` LIKE 'easyVerein_memberlist_table_%';", array($table_name));
    $db_userColor = $wpdb->get_results($sql);
    $userColor = [];

    // Map data
    foreach ( $db_userColor as $res ) {
          $userColor[$res->option] = $res->value;
    }

        // Return user color settings
        return $userColor;
}

function easyVerein_saveMemberList()
{
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
      
    // Set default order
    $userOrder = 'easyVerein_memberlist_firstname,easyVerein_memberlist_lastname,easyVerein_memberlist_address,easyVerein_memberlist_zipandcity,easyVerein_memberlist_country,easyVerein_memberlist_mail,easyVerein_memberlist_group';
    if (strlen($_POST['easyVerein_memberlist_order']) != 0) {
        $userOrder = sanitize_text_field($_POST['easyVerein_memberlist_order']); 
    }

        // Save order in database
        $order = array(
        'option' => 'easyVerein_memberlist_order',
        'value' => $userOrder
        );

        // Save firstname in database
        $easyVerein_firstname_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_firstname',
        'value' => isset($_POST['easyVerein_firstname_isvisible']) ? 1:0
        );

        // Save lastname in database
        $easyVerein_lastname_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_lastname',
        'value' => isset($_POST['easyVerein_lastname_isvisible']) ? 1:0
        );

        // Save address in database
        $easyVerein_address_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_address',
        'value' => isset($_POST['easyVerein_address_isvisible']) ? 1:0
        );

        // Save zip and city in database
        $easyVerein_zipandcity_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_zipandcity',
        'value' => isset($_POST['easyVerein_zipandcity_isvisible']) ? 1:0
        );

        // Save country in database
        $easyVerein_country_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_country',
        'value' => isset($_POST['easyVerein_country_isvisible']) ? 1:0
        );

        // Save mail in database
        $easyVerein_mail_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_mail',
        'value' => isset($_POST['easyVerein_mail_isvisible']) ? 1:0
        );

        // Save group in database
        $easyVerein_group_isvisible = array(
        'option' => 'easyVerein_memberlist_isvisible_group',
        'value' => isset($_POST['easyVerein_group_isvisible']) ? 1:0
        );

        // Save header color in database
        $easyVerein_header_color = array(
        'option' => 'easyVerein_memberlist_header_color',
        'value' => isset($_POST['easyVerein_memberlist_header_color']) ? sanitize_hex_color($_POST['easyVerein_memberlist_header_color']):'#23985D'
        );

        // Save header font color in database
        $easyVerein_header_font_color = array(
        'option' => 'easyVerein_memberlist_header_font_color',
        'value' => isset($_POST['easyVerein_memberlist_header_font_color']) ? sanitize_hex_color($_POST['easyVerein_memberlist_header_font_color']):'#FFFFFF'
        );

        // Save table color in database
        $easyVerein_table_color = array(
        'option' => 'easyVerein_memberlist_table_color',
        'value' => isset($_POST['easyVerein_memberlist_table_color']) ? sanitize_hex_color($_POST['easyVerein_memberlist_table_color']):'#FFFFFF'
        );

        // Save table font color in database
        $easyVerein_table_font_color = array(
        'option' => 'easyVerein_memberlist_table_font_color',
        'value' => isset($_POST['easyVerein_memberlist_table_font_color']) ? sanitize_hex_color($_POST['easyVerein_memberlist_table_font_color']):'#000000'
        );

        // Save settings in database
        $wpdb->replace($table_name, $order);
        $wpdb->replace($table_name, $easyVerein_firstname_isvisible);
        $wpdb->replace($table_name, $easyVerein_lastname_isvisible);
        $wpdb->replace($table_name, $easyVerein_address_isvisible);
        $wpdb->replace($table_name, $easyVerein_zipandcity_isvisible);
        $wpdb->replace($table_name, $easyVerein_country_isvisible);
        $wpdb->replace($table_name, $easyVerein_mail_isvisible);
        $wpdb->replace($table_name, $easyVerein_group_isvisible);
        $wpdb->replace($table_name, $easyVerein_header_color);
        $wpdb->replace($table_name, $easyVerein_header_font_color);
        $wpdb->replace($table_name, $easyVerein_table_color);
        $wpdb->replace($table_name, $easyVerein_table_font_color);
}

function easyVerein_printMemberList($userResults, $userOrder, $userColor)
{
    // Build html element
    $html = '';
    $html .= '<form method="post">';
    $html .= '<p class="easyVerein_headline">Mitglieder-Liste</p>';   
    $html .= '<p class="easyVerein_code">Shortcode: <code>[easyverein_memberlist]</code></p>';
    $html .= '<p><b>Einstellungen:</b></p>';
    $html .= '<input type="hidden" id="easyVerein_memberlist_order" name="easyVerein_memberlist_order" value="' . esc_attr($userOrder) . '"/>';   
    $html .= '<div class="easyVerein_sortable_memberlist_colorpicker">
            <div class="easyVerein_sortable_memberlist_colorpicker_item">
              <p>Header Hintergrundfarbe</p>
              <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userColor['easyVerein_memberlist_header_color']) ? esc_attr($userColor['easyVerein_memberlist_header_color']):'#23985D') . '" id="easyVerein_memberlist_header_color" name="easyVerein_memberlist_header_color" />
            </div>

            <div class="easyVerein_sortable_memberlist_colorpicker_item">
              <p>Header Schriftfarbe</p>
              <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userColor['easyVerein_memberlist_header_font_color']) ? esc_attr($userColor['easyVerein_memberlist_header_font_color']):'#FFFFFF') . '" id="easyVerein_memberlist_header_font_color" name="easyVerein_memberlist_header_font_color"/>
            </div>

            <div class="easyVerein_sortable_memberlist_colorpicker_item">
              <p>Tabelle Hintergrundfarbe</p>
              <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userColor['easyVerein_memberlist_table_color']) ? esc_attr($userColor['easyVerein_memberlist_table_color']):'#FFFFFF') . '" id="easyVerein_memberlist_table_color" name="easyVerein_memberlist_table_color"/>
            </div>

            <div class="easyVerein_sortable_memberlist_colorpicker_item">
              <p>Tabelle Schriftfarbe</p>
              <input type="color" class="easyVerein_colorpicker" value="' . esc_attr(isset($userColor['easyVerein_memberlist_table_font_color']) ? esc_attr($userColor['easyVerein_memberlist_table_font_color']):'#000000') . '" id="easyVerein_memberlist_table_font_color" name="easyVerein_memberlist_table_font_color"/>
            </div>
          </div>';
    $html .= '<p>Um Spalten zu der Tabelle hinzuzufügen oder zu entfernen setze einen Haken. Die Reihenfolge der Felder lässt sich durch ziehen bestimmen.</p>';   
    $html .= '<ul id="easyVerein_sortable_memberlist">
          <li id="easyVerein_memberlist_firstname" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_firstname_isvisible" value="easyVerein_firstname_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_firstname']) ? $userResults['easyVerein_memberlist_isvisible_firstname']:'') . '/> Vorname <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_lastname" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_lastname_isvisible" value="easyVerein_lastname_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_lastname']) ? $userResults['easyVerein_memberlist_isvisible_lastname']:'') . '/> Nachname <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_address" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_address_isvisible" value="easyVerein_address_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_address']) ? $userResults['easyVerein_memberlist_isvisible_address']:'') . '/> Adresse <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_zipandcity" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_zipandcity_isvisible" value="easyVerein_zipandcity_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_zipandcity']) ? $userResults['easyVerein_memberlist_isvisible_zipandcity']:'') . '/> PLZ & Stadt <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_country" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_country_isvisible" value="easyVerein_country_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_country']) ? $userResults['easyVerein_memberlist_isvisible_country']:'') . '/> Land <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_mail" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_mail_isvisible" value="easyVerein_mail_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_mail']) ? $userResults['easyVerein_memberlist_isvisible_mail']:'') . '/> E-Mail Adresse <span class="easyVerein_sortIcon">&#10303;</span></li>
          <li id="easyVerein_memberlist_group" class="easyVerein_sortableItem"><input type="checkbox" class="easyVerein_sortableItem_isvisible" name="easyVerein_group_isvisible" value="easyVerein_group_isvisible" ' . esc_attr(isset($userResults['easyVerein_memberlist_isvisible_group']) ? $userResults['easyVerein_memberlist_isvisible_group']:'') . '/> Gruppe <span class="easyVerein_sortIcon">&#10303;</span></li>
        </ul><br><br>
        <button type="submit" id="easyVerein_save_memberlist" name="easyVerein_save_memberlist" class="easyVerein_button" disabled>Speichern</button>';
    $html .= '</form>';
    return $html;
}
?>