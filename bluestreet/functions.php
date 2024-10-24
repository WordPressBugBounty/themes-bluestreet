<?php

// Global variables define
define('BLUESTREET_PARENT_TEMPLATE_DIR_URI', get_template_directory_uri());
define('BLUESTREET_TEMPLATE_DIR_URI', get_stylesheet_directory_uri());
define('BLUESTREET_TEMPLATE_DIR', trailingslashit(get_stylesheet_directory()));

if (!function_exists('wp_body_open')) {

    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action('wp_body_open');
    }

}

// Enqueue Script and Style
function bluestreet_enqueue_scripts() {
    wp_enqueue_style('bluestreet-parent-style', BLUESTREET_PARENT_TEMPLATE_DIR_URI . '/style.css');
    wp_enqueue_style('bluestreet-parent-media-style', BLUESTREET_PARENT_TEMPLATE_DIR_URI . '/css/media-responsive.css');
    wp_enqueue_style('bootstrap', BLUESTREET_PARENT_TEMPLATE_DIR_URI . '/css/bootstrap.css');
    wp_dequeue_style('wallstreet-default', BLUESTREET_PARENT_TEMPLATE_DIR_URI . '/css/default.css');
    wp_enqueue_style('bluestreet-default', BLUESTREET_TEMPLATE_DIR_URI . '/css/default.css');

    require(BLUESTREET_TEMPLATE_DIR . 'functions/script/custom_style.php');
}

add_action('wp_enqueue_scripts', 'bluestreet_enqueue_scripts', 999);

// Theme Setup
add_action('after_setup_theme', 'bluestreet_theme_setup');

function bluestreet_theme_setup() {
    require_once( BLUESTREET_TEMPLATE_DIR . '/theme_setup_data.php' );
    load_child_theme_textdomain('bluestreet', BLUESTREET_TEMPLATE_DIR . '/languages');
    require( BLUESTREET_TEMPLATE_DIR . '/functions/customizer/customizer-copyright.php' );
    require( BLUESTREET_TEMPLATE_DIR . '/functions/customizer/customizer-header-layout.php' );
    require( BLUESTREET_TEMPLATE_DIR . '/functions/customizer/customizer-blog-layout.php' );
    require( BLUESTREET_TEMPLATE_DIR . '/functions/template-tag.php' );
    require_once BLUESTREET_TEMPLATE_DIR . '/class-tgm-plugin-activation.php';

    //About Theme
    $theme = wp_get_theme(); // gets the current theme
    if ('Bluestreet' == $theme->name) {
        if (is_admin()) {
            require BLUESTREET_TEMPLATE_DIR . '/admin/admin-init.php';
        }
    }

  
   
    add_theme_support( 'automatic-feed-links' );

    add_theme_support( 'title-tag' );

}

add_action('tgmpa_register', 'bluestreet_register_required_plugins');

//Set for old user
if (!get_option('leo_user', false)) {
    //detect old user and set value
    $bluestreet_user = get_option('wallstreet_pro_options', array());
    if ((isset($bluestreet_user['service_title']) || isset($bluestreet_user['service_description']) || isset($bluestreet_user['home_blog_heading']) || isset($bluestreet_user['home_blog_description']))) {
        add_option('leo_user', 'old');
    } else {
        add_option('leo_user', 'new');
    }
}

function bluestreet_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
            'name' => esc_html__('Custom FaceBook Feed','bluestreet'),
            'slug' => 'facebook-feed',
            'required' => false,
        ),
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id' => 'bluestreet', // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '', // Default absolute path to bundled plugins.
        'menu' => 'tgmpa-install-plugins', // Menu slug.
        'has_notices' => true, // Show admin notices or not.
        'dismissable' => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message' => '', // Message to output right before the plugins table.
    );

    tgmpa($plugins, $config);
}

add_action('customize_controls_print_styles', 'bluestreet_custmizer_style');

function bluestreet_custmizer_style() {
    wp_enqueue_style('bluestreet-customizer-css', BLUESTREET_TEMPLATE_DIR_URI . '/css/cust-style.css');
}

// Add script for sticky header
add_action('wp_head', 'bluestreet_sticky_header');

function bluestreet_sticky_header() {
    $bluestreet_options = wp_parse_args(get_option('wallstreet_pro_options', array()), bluestreet_theme_data_setup());
    if ($bluestreet_options['header_center_layout_setting'] == 'center') {
        ?>
        <script>
            jQuery(document).ready(function (jQuery) {
                jQuery(window).bind('scroll', function () {
                    if (jQuery(window).scrollTop() > 200) {
                        jQuery('.navbar').addClass('stickymenu1');
                        jQuery('.navbar').slideDown();
                    } else {
                        jQuery('.navbar').removeClass('stickymenu1');
                        jQuery('.navbar').attr('style', '');
                    }
                });
            });
        </script>
        <?php

    }
}

/* sidebar */
add_action('widgets_init', 'bluestreet_widgets_init');

function bluestreet_widgets_init() {
  
    register_sidebar(array(
        'name' => esc_html__('Footer widget area', 'bluestreet'),
        'id' => 'footer-widget-area',
        'description' => esc_html__('Footer widget area', 'bluestreet'),
        'before_widget' => '<div class="col-md-3 col-sm-6 footer_widget_column">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="footer_widget_title">',
        'after_title' => '</h2>',
    ));

}

$bluestreet_theme=wp_get_theme();
if( $bluestreet_theme->name == 'Bluestreet' || $bluestreet_theme->name == 'Bluestreet child' || $bluestreet_theme->name == 'Bluestreet Child' ) {
    // Notice to add required plugin
    function bluestreet_admin_plugin_notice_warn() {
        $theme_name = wp_get_theme();
        if ( get_option( 'dismissed-bluestreet_comanion_plugin', false ) ) {
           return;
        }
        if ( function_exists('webriti_companion_activate')) {
            return;
        }?>

        <div class="updated notice is-dismissible bluestreet-theme-notice">

            <div class="owc-header">
                <h2 class="theme-owc-title">               
                    <svg height="60" width="60" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70"><defs><style>.cls-1{font-size:33px;font-family:Verdana-Bold, Verdana;font-weight:700;}</style></defs><title>Artboard 1</title><text class="cls-1" transform="translate(-0.56 51.25)">WC</text></svg>
                    <?php echo esc_html('Webriti Companion','bluestreet');?>
                </h2>
            </div>

            <div class="bluestreet-theme-content">
                <h3><?php printf (esc_html__('Thank you for installing the %1$s theme.', 'bluestreet'), esc_html($theme_name)); ?></h3>

                <p><?php esc_html_e( 'We highly recommend you to install and activate the', 'bluestreet' ); ?>
                    <b><?php esc_html_e( 'Webriti Companion', 'bluestreet' ); ?></b> plugin.
                    <br>
                    <?php esc_html_e( 'This plugin will unlock enhanced features to build a beautiful website.', 'bluestreet' ); ?>
                </p>
                <button id="install-plugin-button-welcome-page" data-plugin-url="<?php echo esc_url( 'https://webriti.com/extensions/webriti-companion.zip');?>"><?php echo esc_html__( 'Install', 'bluestreet' ); ?></button>
            </div>
        </div>
        
        <script type="text/javascript">
            jQuery(function($) {
            $( document ).on( 'click', '.bluestreet-theme-notice .notice-dismiss', function () {
                var type = $( this ).closest( '.bluestreet-theme-notice' ).data( 'notice' );
                $.ajax( ajaxurl,
                  {
                    type: 'POST',
                    data: {
                      action: 'dismissed_notice_handler',
                      type: type,
                    }
                  } );
              } );
          });
        </script>
    <?php

    }
    add_action( 'admin_notices', 'bluestreet_admin_plugin_notice_warn' );
    add_action( 'wp_ajax_dismissed_notice_handler', 'bluestreet_ajax_notice_handler');

    function bluestreet_ajax_notice_handler() {
        update_option( 'dismissed-bluestreet_comanion_plugin', TRUE );
    }
    function bluestreet_notice_style(){?>
        <style type="text/css">
            label.tg-label.breadcrumbs img {
                width: 6%;
                padding: 0;
            }
            .bluestreet-theme-notice .theme-owc-title{
                display: flex;
                align-items: center;
                height: 100%;
                margin: 0;
                font-size: 1.5em;
            }
            .bluestreet-theme-notice p{
                font-size: 14px;
            }
            .updated.notice.bluestreet-theme-notice h3{
                margin: 0;
            }
            div.bluestreet-theme-notice.updated {
                border-left-color: #22a1c4;
            }
            .bluestreet-theme-content{
                padding: 0 0 1.2rem 3.57rem;
            }
        </style>
    <?php
    }
    add_action('admin_enqueue_scripts','bluestreet_notice_style');
}
// Hook the AJAX action for logged-in users
add_action('wp_ajax_bluestreet_check_plugin_status', 'bluestreet_check_plugin_status');

function bluestreet_check_plugin_status() {
    if (!current_user_can('install_plugins')) {
        wp_send_json_error('You do not have permission to manage plugins.');
        return;
    }

    if (!isset($_POST['plugin_slug'])) {
        wp_send_json_error('No plugin slug provided.');
        return;
    }

    $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
    $plugin_main_file = $plugin_slug . '/' . $plugin_slug . '.php'; // Adjust this based on your plugin structure

    // Check if the plugin exists
    $plugins = get_plugins();
    if (isset($plugins[$plugin_main_file])) {
        if (is_plugin_active($plugin_main_file)) {
            wp_send_json_success(array('status' => 'activated'));
        } else {
            wp_send_json_success(array('status' => 'installed'));
        }
    } else {
        wp_send_json_success(array('status' => 'not_installed'));
    }
}

// Existing AJAX installation function for installing and activating
add_action('wp_ajax_bluestreet_install_activate_plugin', 'bluestreet_install_and_activate_plugin');

function bluestreet_install_and_activate_plugin() {
    if (!current_user_can('install_plugins')) {
        wp_send_json_error('You do not have permission to install plugins.');
        return;
    }

    if (!isset($_POST['plugin_url'])) {
        wp_send_json_error('No plugin URL provided.');
        return;
    }

    // Include necessary WordPress files for plugin installation
    include_once(ABSPATH . 'wp-admin/includes/file.php');
    include_once(ABSPATH . 'wp-admin/includes/misc.php');
    include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    $plugin_url = esc_url($_POST['plugin_url']);
    $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
    $plugin_main_file = $plugin_slug . '/' . $plugin_slug . '.php'; // Ensure this matches your plugin structure

    // Download the plugin file
    WP_Filesystem();
    $temp_file = download_url($plugin_url);

    if (is_wp_error($temp_file)) {
        wp_send_json_error($temp_file->get_error_message());
        return;
    }

    // Unzip the plugin to the plugins folder
    $plugin_folder = WP_PLUGIN_DIR;
    $result = unzip_file($temp_file, $plugin_folder);
    
    // Clean up temporary file
    unlink($temp_file);

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
        return;
    }

    // Activate the plugin if it was installed
    $activate_result = activate_plugin($plugin_main_file);

    

    // Return success with redirect URL
    wp_send_json_success(array('redirect_url' => admin_url('admin.php?page=bluestreet-welcome')));
}

// Enqueue JavaScript for the button functionality
add_action('admin_enqueue_scripts', 'bluestreet_enqueue_plugin_installer_script');

function bluestreet_enqueue_plugin_installer_script() {
    global $hook_suffix;
    wp_enqueue_script('bluestreet-plugin-installer-js', BLUESTREET_TEMPLATE_DIR_URI . '/admin/assets/js/plugin-installer.js', array('jquery'), null, true);
    wp_localize_script('bluestreet-plugin-installer-js', 'pluginInstallerAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'hook_suffix' => $hook_suffix,
        'nonce' => wp_create_nonce('plugin_installer_nonce'),

    ));
}
