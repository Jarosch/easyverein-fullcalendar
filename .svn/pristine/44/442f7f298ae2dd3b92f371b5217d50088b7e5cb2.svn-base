<?php
if (! defined('ABSPATH') ) {
    exit;
}

add_action('init', function() {
    if (isset($_POST['easyverein_redirect_memberlist'])) {
        include_once EASYVEREIN_REGISTER_HELPER . 'easyVereinApiHelper.php';
        wp_redirect(esc_attr(easyVerein_sso_redirect("members")));
        exit;
    }
});

add_shortcode('easyverein_club_memberlist', 'easyVerein_printClubMemberlist');

function easyVerein_printClubMemberlist($atts = [])
{

    include_once EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';

    // Load database data
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT * FROM %i WHERE `option` LIKE 'easyVerein_ssobutton_button_%';", array($table_name));
    $db_userSettings = $wpdb->get_results($sql);

    $userSettings = [];
    foreach ( $db_userSettings as $res ) {
        $userSettings[$res->option] = $res->value;
    }
   
        
    // Build response
    $html = '<form method="post"><button type="submit" style="color: '. esc_attr(isset($userSettings['easyVerein_ssobutton_button_text_color']) ? esc_attr($userSettings['easyVerein_ssobutton_button_text_color']):'#FFFFFF') .'; background-color:' . esc_attr(isset($userSettings['easyVerein_ssobutton_button_color']) ? esc_attr($userSettings['easyVerein_ssobutton_button_color']):'#23985D') . ';" id="easyverein_redirect_memberlist" name="easyverein_redirect_memberlist">Mitgliederliste</button></form>';
        
    return $html;
}

?>