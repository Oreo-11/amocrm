<?php
class AmoCRM_Settings_Page
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_init', array($this, 'settings_init'));
    }

    public function add_menu_item()
    {
        add_options_page(
            'Настройки AmoCRM',
            'AmoCRM Integration',
            'manage_options',
            'amocrm-settings',
            array($this, 'render_settings_page')
        );
    }

    public function settings_init()
    {
        register_setting('amocrm_settings_group', 'amocrm_settings');

        add_settings_section(
            'amocrm_api_section',
            'API Settings',
            array($this, 'render_section_info'),
            'amocrm-settings'
        );

        $fields = array(
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'subdomain' => 'Subdomain',
            'access_token' => 'Access Token',
            'pipeline_id' => 'Pipeline ID',
            'time_spent_field' => 'Time Spent Field ID'
        );

        foreach ($fields as $name => $title) {
            add_settings_field(
                'amocrm_' . $name,
                $title,
                array($this, 'render_field'),
                'amocrm-settings',
                'amocrm_api_section',
                array('name' => $name)
            );
        }
    }

    public function render_field($args)
    {
        $options = get_option('amocrm_settings');
        $value = $options[$args['name']] ?? '';
?>
        <input type="text" name="amocrm_settings[<?php echo esc_attr($args['name']); ?>]"
            value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
    }

    public function render_section_info()
    {
        echo '<p>Введите данные для интеграции с AmoCRM API</p>';
    }

    public function render_settings_page()
    {
    ?>
        <div class="wrap">
            <h1>Настройки интеграции с AmoCRM</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('amocrm_settings_group');
                do_settings_sections('amocrm-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
