<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.samuelbohl.com
 * @since      1.0.0
 *
 * @package    Production_Manager
 * @subpackage Production_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Production_Manager
 * @subpackage Production_Manager/includes
 * @author     Samuel Bohl <samuel@samuelbohl.com>
 */
class Production_Manager {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Production_Manager_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('PRODUCTION_MANAGER_VERSION')) {
            $this->version = PRODUCTION_MANAGER_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'production-manager';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_common_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Production_Manager_Loader. Orchestrates the hooks of the plugin.
     * - Production_Manager_i18n. Defines internationalization functionality.
     * - Production_Manager_Admin. Defines all hooks for the admin area.
     * - Production_Manager_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-production-manager-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-production-manager-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-production-manager-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-production-manager-public.php';

        $this->loader = new Production_Manager_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Production_Manager_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Production_Manager_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register common hooks here.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_common_hooks() {
        $this->loader->add_action('init', $this, 'register_custom_post_types', 11);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Production_Manager_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_filter('manage_posts_columns', $plugin_admin, 'add_column_head');
        $this->loader->add_action('manage_posts_custom_column', $plugin_admin, 'add_column_content', 10, 2);
        $this->loader->add_filter('acf/load_field/name=pm_coupon_code', $plugin_admin, 'read_only_field');
        $this->loader->add_filter('acf/update_value/name=pm_coupon_code', $plugin_admin, 'handle_coupon_code');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Production_Manager_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Register all PM custom post types
     *
     * @since    1.0.0
     * @access   private
     */
    public function register_custom_post_types() {
        $labels = array(
            'name' => _x('Production Orders', 'post type general name', 'wp-production-manager'),
            'singular_name' => _x('Production Order', 'post type singular name', 'wp-production-manager'),
            'menu_name' => _x('Production Orders', 'admin menu', 'wp-production-manager'),
            'name_admin_bar' => _x('Production Order', 'add new on admin bar', 'wp-production-manager'),
            'add_new' => _x('Add New', 'order', 'wp-production-manager'),
            'add_new_item' => __('Add New Order', 'wp-production-manager'),
            'new_item' => __('New Order', 'wp-production-manager'),
            'edit_item' => __('Edit Order', 'wp-production-manager'),
            'view_item' => __('View Order', 'wp-production-manager'),
            'all_items' => __('All Orders', 'wp-production-manager'),
            'search_items' => __('Search Orders', 'wp-production-manager'),
            'parent_item_colon' => __('Parent Order:', 'wp-production-manager'),
            'not_found' => __('No orders found.', 'wp-production-manager'),
            'not_found_in_trash' => __('No orders found in Trash.', 'wp-production-manager'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Production Order', 'wp-production-order'),
            'public' => true,
            'publicly_queryable' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'pm_production_order'),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'can_export' => true,
            'capability_type' => 'post',
            'menu_position' => 22,
            'menu_icon' => 'dashicons-admin-generic',
            'rest_base' => 'pm_production_order',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('pm_production_order', $args);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Production_Manager_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version() {
        return $this->version;
    }

}
