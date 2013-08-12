<?php

class OKFN_Utility {
    
    const SLUG = 'okfn-utility';

    var $plugin_dir;
	
    public static function init() {
        
        // Load plugin text domain
        add_action( 'init', array( get_class(), 'plugin_textdomain' ) );

        // Transparency for Cookie notification bar
        add_action( 'init', array( get_class(), 'cookie_notification_styles' ) );
//        add_action( 'init', array( get_class(), 'force_cookie_policy_page_creation' ) );
        add_filter( 'catapult_cookie_content', array( get_class(), 'cookie_policy_global_page' ), 10, 2 );

        add_action( 'wpmu_options', array( get_class(), 'lock_login_network_options_display' ) );
        add_action( 'update_wpmu_options' , array( get_class(), 'lock_login_network_options_save' ) );
        add_filter( 'authenticate' , array( get_class(), 'lock_login_action' ), 100, 3 );

        add_filter( 'login_message', array ( get_class(),  'password_reset_login_notice') );

        add_filter( 'wp_footer', array ( get_class(),  'pagely_footer_notice') );
        // add_filter( 'allow_password_reset', array ( get_class(),  'disable_reset_lost_password') );
        
        
    } // end init

    function __construct() {
        $this->plugin_dir = ( WPMU_PLUGIN_DIR == dirname(__FILE__) ) ? WPMU_PLUGIN_DIR . '/okfn-utility' : dirname(__FILE__);

        return $this;
    }

    /*--------------------------------------------*
     * Core Functions
     *---------------------------------------------*/
	

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
            
            $locale = apply_filters( 'plugin_locale', get_locale(), self::SLUG );
            load_textdomain( self::SLUG, WP_LANG_DIR.'/'.self::SLUG.'/'.self::SLUG.'-'.$locale.'.mo' );
            load_plugin_textdomain( self::SLUG, FALSE, plugin_dir_url( __FILE__ ) . 'lang/' );

	} // end plugin_textdomain
	
	
    function cookie_notification_styles() {
        if ( function_exists( catapult_add_cookie_bar ) ) {
            wp_enqueue_style( 'okf-transparent-cookie-bar', trailingslashit(WPMU_PLUGIN_URL) . self::SLUG . '/css/cookie-bar.css', false, false);
        }
    }
    
    // Currently the http://wordpress.org/extend/plugins/uk-cookie-consent/ plugin only creates the policy page when you view the plugin settings page itself. 
    // As I don't want to do this on every site, this function will check for policy page existance and add it if necessary. 
    // This would be better handled on plugin activation - http://wordpress.org/support/topic/suggestion-create-policy-page-on-plugin-activation?replies=1#post-4117511
    function force_cookie_policy_page_creation() {
        if ( function_exists('catapult_cookie_options_page') 
                && ! get_page_by_title( __( 'Cookie Policy', 'uk-cookie-consent' ) ) ) {
            
            include ABSPATH . '/wp-admin/includes/plugin.php';
            include ABSPATH . '/wp-admin/includes/template.php';
            ob_get_clean();
            ob_start();
            catapult_cookie_options_page();
            ob_end_clean();
        }
    }
    
    function cookie_policy_global_page($content, $options) {
        $regex = '/https?\:\/\/[^\" ]+/i';
        $content = str_replace('<a', '<a target="_blank"', $content);
        return preg_replace($regex, network_home_url() . 'cookie-policy' , $content);
    }

    function lock_login_network_options_display() {
        global $okfn_utility;
        wp_nonce_field( plugin_basename( __FILE__ ), 'okfn_login_lock_nonce' );
        $okf_login_lock = get_site_option('okf_login_lock');
        include( $okfn_utility->plugin_dir . '/views/lock-login.php' );
    }

    function lock_login_network_options_save() {


        if ( ( ! current_user_can( 'manage_network_options' ) )
            || ( ! isset( $_POST['okfn_login_lock_nonce'] ) )
            || ( ! wp_verify_nonce( $_POST['okfn_login_lock_nonce'], plugin_basename( __FILE__ ) ) )
            ) return;
        
        update_site_option( 'okf_login_lock', $_POST['okf_login_lock'] );

    }

    function lock_login_action( $user, $username, $password ) {
        $okf_login_lock = get_site_option('okf_login_lock');

        if ( $okf_login_lock && is_a($user, 'WP_User') ) { 
            if ( !is_super_admin( $user->ID ) ) 
                $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Login is currently disabled.'));
            
        }

        return $user;
    }

    function password_reset_login_notice( $message ) {
        if ( empty($message) ){
            return "<p style='margin-bottom: 10px;'>Be advised, due to a server migration all passwords on this system were reset on July 27th, 2013. If you haven't done so yet, please use the 'Lost Your Password' link below to set your own password.</p>";
        } 
        else {
            return $message;
        }

    }

    function pagely_footer_notice() {
        if ( DB_NAME == 'db_dom4659' ) {
            global $okfn_utility;
            include( $okfn_utility->plugin_dir . '/views/pagely-footer-notice.php' );
        }
    }

    function disable_reset_lost_password() {
        if ( DB_NAME == 'db_dom4659' ) {
            return false;
        }
        else return true;
    } 
        
  
} // end class
