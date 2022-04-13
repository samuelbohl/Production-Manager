<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.samuelbohl.com
 * @since      1.0.0
 *
 * @package    Production_Manager
 * @subpackage Production_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Production_Manager
 * @subpackage Production_Manager/admin
 * @author     Samuel Bohl <samuel@samuelbohl.com>
 */
class Production_Manager_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    /**
     * Add Production Manager settings page
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function add_pm_settings_page() {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' => 'Production Manager Settings',
                'menu_title' => 'PM Settings',
                'menu_slug'  => 'production-manager-settings',
                'capability' => 'edit_posts',
                'redirect'   => false
            ));
        }
    }

    /**
     * Save settings as options on acf/save.
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function save_settings($post_id) {
        $screen = get_current_screen();

        if (strpos($screen->id, "production-manager-settings") == true) {
            $values = get_fields($post_id);

            if (! update_option('pm_settings_allowed_users', $values['production_manager_users'])) {
                add_option('pm_settings_allowed_users', $values['production_manager_users']);
            }
        }
    }

    /**
     * Converts the given field to Read Only. (accessed via the acf/load_field filter)
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function read_only_field($field) {
        $field['disabled'] = '1';

        return $field;
    }

    /**
     * Adds column heads for the Production Order post type.
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function add_column_head($columns) {
        $columns['pm_status']      = 'Status';
        $columns['pm_coupon_code'] = 'Coupon Code';

        return $columns;
    }

    /**
     * Adds the column content for the Production Order post type.
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function add_column_content($column_name, $post_id) {
        if ($column_name == 'pm_status') {
            $post_status = get_field('pm_status', $post_id);
            if ($post_status) {
                echo $post_status;
            } else {
                echo 'not FOUND';
            }
        } else if ($column_name == 'pm_coupon_code') {
            $post_coupon_code = get_field('pm_coupon_code', $post_id);
            if ($post_coupon_code) {
                echo get_post($post_coupon_code)->post_name;
            } else {
                echo 'not FOUND';
            }
        }
    }

    /**
     * Handles the Coupon Code field on Save.
     * - Generates new Code if none exists
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    public function handle_coupon_code($post_id) {
        $post_type = get_post_type($post_id);
        if ($post_type != 'pm_production_order') {
            return;
        }

        // otherwise, generate coupon code
        $new_code      = $this->generate_random_coupon_code();
        $allowed_users = get_option('pm_settings_allowed_users');
        $args          = array(
            'discount_type'        => 'percent',
            'coupon_amount'        => '100',
            'individual_use'       => 'no',
            'usage_limit'          => '1',
            'usage_limit_per_user' => '1',
            'pm_restrict_users'    => 'true'
        );

        //add it to this field and woocommerce
        $new_coupon_id = $this->add_coupon_code($new_code, $args);
        update_field('pm_coupon_code', $new_coupon_id, $post_id);
    }

    /**
     * Generates a random coupon code.
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    private function generate_random_coupon_code() {
        $characters = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";

        return substr(str_shuffle($characters), 0, 8);
    }

    /**
     * Adds a new Woocommerce coupon code.
     *
     * @author Samuel Bohl
     * @since 1.0.0
     */
    private function add_coupon_code($coupon_code, $args = array()) {
        $coupon_args = array(
            'post_title'   => $coupon_code,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'shop_coupon'
        );

        $coupon_id = wp_insert_post($coupon_args);

        if (! empty($coupon_id) && ! is_wp_error($coupon_id)) {
            foreach ($args as $key => $val) {
                if (! update_post_meta($coupon_id, $key, $val)) {
                    add_post_meta($coupon_id, $key, $val);
                }
            }
        }

        return $coupon_id;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Production_Manager_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Production_Manager_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/production-manager-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Production_Manager_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Production_Manager_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/production-manager-admin.js', array('jquery'), $this->version, false);

    }

}
