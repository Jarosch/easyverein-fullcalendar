<?php
/*
 * Plugin Name:       easyVerein
 * Plugin URI:        https://wordpress.org/plugins/easyverein
 * Description:       Das offizelle easyVerein Plugin für WordPress.
 * Version:           2.1.4
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            SD Software-Design GmbH
 * Author URI:        https://software-design.de
 * License:           GPLv2
 * License URI:       https://opensource.org/license/mit/
 * Text Domain:       easyverein
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load module loader
require_once 'easyVereinModuleLoader.php';

function easyVerein_register_schedule()
{
    if (!wp_next_scheduled('easyVerein_member_sync_hook')) {
        wp_schedule_event(time(), 'hourly', 'easyVerein_member_sync_hook');
    }
}

register_activation_hook(__FILE__, 'easyVerein_register_schedule');

require_once EASYVEREIN_REGISTER_ADMIN_INCLUDES . 'easyVereinMemberSync.php';
add_action('easyVerein_member_sync_hook', 'easyVerein_syncAllMembers');


add_filter('plugin_row_meta', 'easyVerein_row_meta', 10, 2);
function easyVerein_row_meta($links, $file)
{
    try {
        if (plugin_basename(__FILE__) == $file) {
            require_once EASYVEREIN_REGISTER_ADMIN_INCLUDES . 'easyVereinAPIKey.php';
            $api_key = easyVerein_loadAPIKey();
            $api_version = easyVerein_checkApiStatus($api_key);

            if (!$api_version) {
                $row_meta = array(
                    'docs' => '<a href="' . esc_url('/wp-admin/admin.php?page=easyVerein_dashboard') . '" target="_blank" style="color:#b32d2e;">' . esc_html__('Gültiger API Token erforderlich!', 'domain') . '</a>'
                );
            } elseif ($api_version == 'v1.7') {
                $row_meta = array(
                    'docs' => '<span class="dashicons dashicons-star-filled" aria-hidden="true" style="color:#b32d2e;font-size: 1.4em"></span> <a href="' . esc_url('/wp-admin/admin.php?page=easyVerein_dashboard') . '" target="_blank" style="color:#b32d2e;">' . esc_html__('ACHTUNG - neuer Token erforderlich!', 'domain') . '</a>'
                );
            } else {
                $row_meta = array(
                    'docs' => '<span class="dashicons dashicons-star-filled" aria-hidden="true" style="color:#23985D;font-size: 1.4em"></span> <a href="' . esc_url('/wp-admin/admin.php?page=easyVerein_dashboard') . '" target="_blank" style="color:#23985D;">' . esc_html__('API Token gültig', 'domain') . '</a>'
                );
            }

            return array_merge($links, $row_meta);
        }
    } catch (Exception $e) {
        return (array) $links;
    }

    return (array) $links;
}

function easyVerein_addCustomIdField($user)
{
    ?>
    <h3>easyVerein-ID</h3>
    <table class="form-table">
        <tr>
            <th><label for="easyVereinId">easyVerein-ID</label></th>
            <td>
                <input type="text" name="easyVereinId" id="easyVereinId"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'easyVereinId', true)); ?>"
                    class="regular-text" /><br />
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'easyVerein_addCustomIdField');
add_action('edit_user_profile', 'easyVerein_addCustomIdField');
?>