<?php
/*
Plugin Name: AmoCRM Integration
Description: Отправка заявок с сайта в AmoCRM
Version: 1.0
Author: Your Name
*/

defined('ABSPATH') || exit;

if (!function_exists('add_action')) {
    die('Прямой доступ запрещён');
}

define('AMOCRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMOCRM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once AMOCRM_PLUGIN_DIR . 'includes/settings-page.php';
require_once AMOCRM_PLUGIN_DIR . 'includes/form-handler.php';

class AmoCRM_Integration
{
    public function __construct()
    {
        new AmoCRM_Settings_Page();
        new AmoCRM_Form_Handler();

        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    public function enqueue_assets()
    {
        wp_enqueue_style(
            'amocrm-form-style',
            AMOCRM_PLUGIN_URL . 'assets/css/form.css'
        );

        wp_enqueue_script(
            'amocrm-form-script',
            AMOCRM_PLUGIN_URL . 'assets/js/form.js',
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script(
            'amocrm-form-script',
            'amocrm_ajax',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amocrm_nonce')
            )
        );
    }

    public function activate()
    {
        if (!get_option('amocrm_settings')) {
            add_option('amocrm_settings', array(
                'client_id' => '',
                'client_secret' => '',
                'subdomain' => '',
                'access_token' => '',
                'pipeline_id' => 9715250,
                'custom_field_id' => 919305,
                'time_spent_field' => ''
            ));
        }
    }
}

function init_amocrm_integration()
{
    new AmoCRM_Integration();
}
add_action('plugins_loaded', 'init_amocrm_integration');
