<?php
require_once(ABSPATH . 'wp-load.php');

function amocrm_refresh_token()
{
    $options = get_option('amocrm_settings');

    if (empty($options['client_id']) || empty($options['client_secret']) || empty($options['refresh_token'])) {
        error_log('AmoCRM: Missing required credentials for token refresh');
        return false;
    }

    $url = "https://{$options['subdomain']}.amocrm.ru/oauth2/access_token";

    $data = [
        'client_id' => $options['client_id'],
        'client_secret' => $options['client_secret'],
        'grant_type' => 'refresh_token',
        'refresh_token' => $options['refresh_token'],
        'redirect_uri' => home_url() // или ваш redirect_uri
    ];

    $response = wp_remote_post($url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data),
        'timeout' => 30
    ]);

    if (is_wp_error($response)) {
        error_log('AmoCRM Token Refresh Error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode($response['body'], true);

    if (isset($body['access_token'])) {
        $options['access_token'] = $body['access_token'];
        $options['refresh_token'] = $body['refresh_token'];
        $options['expires_in'] = time() + $body['expires_in'];

        update_option('amocrm_settings', $options);
        error_log('AmoCRM: Token successfully refreshed');
        return true;
    } else {
        error_log('AmoCRM Token Refresh Failed: ' . print_r($body, true));
        return false;
    }
}

// Для вызова из WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    amocrm_refresh_token();
}
