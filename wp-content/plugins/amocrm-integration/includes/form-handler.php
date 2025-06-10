<?php
class AmoCRM_Form_Handler
{
    public function __construct()
    {
        add_shortcode('amocrm_form', array($this, 'render_form'));
        add_action('wp_ajax_amocrm_submit_form', array($this, 'handle_submit'));
        add_action('wp_ajax_nopriv_amocrm_submit_form', array($this, 'handle_submit'));
    }

    public function render_form()
    {
        ob_start();
        include AMOCRM_PLUGIN_DIR . 'templates/form-template.php';
        return ob_get_clean();
    }

    public function handle_submit()
    {
        check_ajax_referer('amocrm_nonce', 'security');

        $options = get_option('amocrm_settings');

        // Получаем флаг, провёл ли пользователь больше 30 секунд
        $spentMoreThan30 = isset($_POST['spent_more_than_30']) && $_POST['spent_more_than_30'] == 1;

        $lead_data = array(
            array(
                'name' => sanitize_text_field($_POST['name']),
                'price' => (int)$_POST['price'],
                'created_by' => 0,
                'custom_fields_values' => array(
                    array(
                        'field_id' => (int)$options['time_spent_field'],
                        'values' => array(
                            array('value' => $spentMoreThan30)
                        )
                    )
                ),
                'pipeline_id' => (int)$options['pipeline_id'],
                '_embedded' => array(
                    'contacts' => array(
                        array(
                            'name' => sanitize_text_field($_POST['name']),
                            'custom_fields_values' => array(
                                array(
                                    'field_code' => 'EMAIL',
                                    'values' => array(
                                        array(
                                            'enum_code' => 'WORK',
                                            'value' => sanitize_email($_POST['email'])
                                        )
                                    )
                                ),
                                array(
                                    'field_code' => 'PHONE',
                                    'values' => array(
                                        array(
                                            'enum_code' => 'WORK',
                                            'value' => sanitize_text_field($_POST['phone'])
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $response = wp_remote_post(
            "https://{$options['subdomain']}.amocrm.ru/api/v4/leads/complex",
            array(
                'headers' => array(
                    'Authorization' => "Bearer {$options['access_token']}",
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode($lead_data),
                'timeout' => 30
            )
        );

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        } else {
            wp_send_json_success(json_decode($response['body'], true));
        }
    }
}
