<?php
if (! defined('ABSPATH') ) {
    exit;
}

// Register admin menu
add_action('admin_menu', 'easyVerein_admin_menu');
function easyVerein_admin_menu()
{
    // Add easyVerein element to admin menu    
    add_menu_page(
        'easyVerein', 'easyVerein', 'manage_options', 'easyVerein_dashboard', 'easyVerein_createAdminDashboard', 
        'data:image/svg+xml;base64,' . base64_encode('<svg id="easyVerein" data-name="easyVerein" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><defs><style>.cls-1{fill:#fff;}</style></defs><path class="cls-1" d="M1.1,11.71a4.66,4.66,0,0,0,.19,1.36,3.1,3.1,0,0,0,.56,1.12,2.58,2.58,0,0,0,.93.76,3,3,0,0,0,1.31.28,2.87,2.87,0,0,0,1.73-.53,2.58,2.58,0,0,0,1-1.51H7.93a5.09,5.09,0,0,1-.5,1.17,3.53,3.53,0,0,1-.77,1,3.28,3.28,0,0,1-1.1.62,4.36,4.36,0,0,1-1.47.23,4.17,4.17,0,0,1-1.84-.37,3.27,3.27,0,0,1-1.27-1,4.2,4.2,0,0,1-.74-1.48A6.75,6.75,0,0,1,0,11.5,5.75,5.75,0,0,1,.28,9.67a4.66,4.66,0,0,1,.81-1.51,3.75,3.75,0,0,1,3-1.38A3.55,3.55,0,0,1,7,8a5.56,5.56,0,0,1,1,3.67Zm5.8-.86a4.16,4.16,0,0,0-.18-1.23,2.94,2.94,0,0,0-.53-1A2.55,2.55,0,0,0,5.31,8a2.83,2.83,0,0,0-1.22-.25A2.57,2.57,0,0,0,2.88,8a2.89,2.89,0,0,0-.9.68,3.67,3.67,0,0,0-.59,1,4.39,4.39,0,0,0-.29,1.2Z"/><path class="cls-1" d="M9.17,3.56h2.76l2.68,9.38,2.71-9.38H20L15.78,15.92H13.34Z"/></svg>')
    );
}

function easyVerein_createAdminDashboard()
{
    // Global imports 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    include EASYVEREIN_REGISTER_HELPER.'easyVereinApiHelper.php';

    // Load admin css
    wp_enqueue_style('easyVerein_admin_css', EASYVEREIN_REGISTER_ADMIN_CSS . 'easyVereinSettings.css');
    wp_enqueue_script('easyVerein_admin_settings_js', EASYVEREIN_REGISTER_ADMIN_JS . 'easyVereinSettings.js');
    wp_enqueue_script('easyVerein_admin_accordion_js', EASYVEREIN_REGISTER_ADMIN_JS . 'easyVereinAccordion.js');
    wp_enqueue_script('jquery-ui-sortable');


    $html = '';
    // easyVerein admin page
    $html .= '<div class="easyVerein">';
        // Header
        $html .= '<div class="easyVerein_header">';
            $html .= '<h1>easy<span class="bold_text">Verein</span><sup>®</sup></h1>';
            $html .= '<h2>Vereinsverwaltungssoftware</h2>';
        $html .= '</div>';
        
    
        // Content
        $html .= '<div class="easyVerein_content">';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES . 'easyVereinAPIKey.php';
                $html .= easyVerein_setupAPIKeySettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinMemberList.php';
                $html .= easyVerein_setupMemberListSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinCalendar.php';
                $html .= easyVerein_setupCalendarSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinProtocol.php';
                $html .= easyVerein_setupProtocolSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include_once EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinMemberSync.php';
                $html .= easyVerein_setupMembersSyncSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinGroupAssign.php';
                $html .= easyVerein_syncAllGroups();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinLoginForm.php';
                $html .= easyVerein_setupLoginFormSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinWoocommerceSync.php';
                $html .= easyVerein_setupWoocommerceSyncSettings();
            $html .= '</div>';
            $html .= '<hr>';
            $html .= '<div class="easyVerein_content_wrapper">';
                include EASYVEREIN_REGISTER_ADMIN_INCLUDES.'easyVereinSSOButtons.php';
                $html .= easyVerein_setupSSOButtonFormSettings();
            $html .= '</div>';
        $html .= '</div>';
        $html .= '<hr>';
    $html .= '</div>';
    echo wp_kses(
        $html,
        array(
            'a' => array(
                'href' => array(),
            ),
            'br' => array(),
            'hr' => array(),
            'em' => array(),
            'strong' => array(),
            'b' => array(),
            'div' => array(
                'class' => array(),
                'id' => array(),
                'style' => array(),
            ),
            'p' => array(
                'class' => array(),
                'id' => array(),
                'style' => array(),
            ),
            'form' => array(
                'action' => array(),
                'method' => array(),
                'id' => array(),
                'class' => array(),
            ),
            'input' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'class' => array(),
                'id' => array(),
                'placeholder' => array(),
                'checked' => array(),
            ),
            'select' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'class' => array(),
                'id' => array(),
                'placeholder' => array(),
            ),
            'label' => array(
                'for' => array(),
                'name' => array(),
                'class' => array(),
                'id' => array(),
            ),
            'option' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'class' => array(),
                'id' => array(),
                'selected' => array(),
            ),
            'textarea' => array(
                'name' => array(),
                'class' => array(),
                'id' => array(),
                'placeholder' => array(),
                'rows' => array(),
            ),
            'h1' => array(
                'class' => array(),
                'id' => array(),
                'style' => array(),
            ),
            'h2' => array(
                'class' => array(),
                'id' => array(),
                'style' => array(),
            ),
            'ul' => array(
                'class' => array(),
                'id' => array(),
            ),
            'li' => array(
                'class' => array(),
                'id' => array(),
            ),
            'button' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'class' => array(),
                'id' => array(),
                'disabled' => array(),
            ),
            'span' => array(
                'style' => array(),
            ),
        )
    );
}
?>