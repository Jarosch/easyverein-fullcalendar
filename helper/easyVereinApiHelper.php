<?php
// Create easyVerein request
function easyVereinAPIRequest($url, $query = '{*}')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_api_key';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $api_key = isset($db_results[0]->value) ? $db_results[0]->value : '';

    $response = array();
    $response['error'] = false;
    $response['status-code'] = 0;
    $response['error-message'] = '';
    $response['data'] = array();

    // easyVerein auth header
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'timeout' => 130,
        ),
    );
    $request = wp_remote_get('https://easyverein.com/api/v2.0/' . $url . '/?query=' . $query, $args);
    $status_code = wp_remote_retrieve_response_code($request);

    $response['status-code'] = $status_code;
    if ($status_code != 200) {
        $error = wp_remote_retrieve_body($request);
        $error = json_decode($error, true);
        $response['error'] = true;
        $response['request'] = 'https://easyverein.com/api/v2.0/' . $url . '/?query=' . $query;
        $response['error-message'] = isset($error['detail']) ? $error['detail'] : "Keine Fehlermeldung erhalten";
    } else {
        $header = wp_remote_retrieve_headers($request);
        if (isset($header['tokenrefreshneeded']) && $header['tokenrefreshneeded'] == 'true') {
            easyVereinRefreshToken($api_key);
        }
        $response['data'] = json_decode(wp_remote_retrieve_body($request), true);
    }

    return $response;
}

function easyVereinRefreshToken($api_key)
{
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );
    $request = wp_remote_get('https://easyverein.com/api/v2.0/refresh-token', $args);
    $body = wp_remote_retrieve_body($request);
    $body = json_decode($body, true);
    // Global database 
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';

    // Save api key in database
    if (isset($_POST['easyVerein_save_api_key'])) {
        $data = array(
            'option' => 'easyVerein_api_key',
            'value' => sanitize_text_field($body['Bearer']),
        );
        $wpdb->replace($table_name, $data);
    }
}

// Create easyVerein plain request
function easyVereinAPIRequestPlain($url)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_api_key';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $api_key = isset($db_results[0]->value) ? $db_results[0]->value : '';

    $response = array();
    $response['error'] = false;
    $response['status-code'] = 0;
    $response['error-message'] = '';
    $response['data'] = array();

    // easyVerein auth header
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );
    $request = wp_remote_get($url, $args);
    $status_code = wp_remote_retrieve_response_code($request);
    $response['status-code'] = $status_code;
    if ($status_code != 200) {
        $error = wp_remote_retrieve_body($request);
        $error = json_decode($error, true);
        $response['error'] = true;
        $response['request'] = $url;
        $response['error-message'] = isset($error['detail']) ? $error['detail'] : "Keine Fehlermeldung erhalten";
    } else {
        $response['data'] = json_decode(wp_remote_retrieve_body($request), true);
    }

    return $response;
}

// Create easyVerein login request
function easyVereinAPIRequestLogin($url, $username, $password)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $sql = $wpdb->prepare("SELECT `value` FROM %i WHERE `option` = 'easyVerein_api_key';", array($table_name));
    $db_results = $wpdb->get_results($sql);
    $api_key = isset($db_results[0]->value) ? $db_results[0]->value : '';

    $response = array();
    $response['error'] = false;
    $response['status-code'] = 0;
    $response['error-message'] = '';
    $response['data'] = array();

    $body_data = array(
        'username' => $username,
        'password' => $password
    );

    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($body_data),
    );

    $request = wp_remote_post($url, $args);
    $status_code = wp_remote_retrieve_response_code($request);
    $response['status-code'] = $status_code;
    if ($status_code != 200) {
        $error = wp_remote_retrieve_body($request);
        $error = json_decode($error, true);
        $response['error'] = true;
        $response['request'] = $url;
        $response['error-message'] = isset($error['detail']) ? $error['detail'] : "Keine Fehlermeldung erhalten";
    } else {
        $response['data'] = json_decode(wp_remote_retrieve_body($request), true);
    }

    return $response;
}

function easyVerein_sso_redirect($url)
{

    $short = easyVereinAPIRequest('organization', '{short}');
    $base_url = "https://easyverein.com/public/" . $short['data']["results"][0]["short"] . "/";
    if (!isset($_COOKIE['easyVereinToken'])) {
        return ($base_url . "" . $url);
        exit;
    } else {
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $_COOKIE['easyVereinToken'],
            ),
        );
        $request = wp_remote_get('https://easyverein.com/api/v2.0/get-sso-token/?', $args);
        $request = json_decode(wp_remote_retrieve_body($request), true);
        if (!isset($request['sso_token'])) {
            return ($base_url . "" . $url);
        } else {
            return ($base_url . "" . $url . "?sso_token=" . $request['sso_token']);
        }
        exit;
    }
}
?>