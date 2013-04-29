<?php

class OKFN_Utility {
    
    const SLUG = 'okfn-utility';
	
    public static function init() {
        
        // Load plugin text domain
        add_action( 'init', array( get_class(), 'plugin_textdomain' ) );

        // Transparency for Cookie notification bar
        add_action( 'init', array( get_class(), 'cookie_notification_styles' ) );
//        add_action( 'init', array( get_class(), 'force_cookie_policy_page_creation' ) );
        add_filter( 'catapult_cookie_content', array( get_class(), 'cookie_policy_global_page' ), 10, 2 );
        
    } // end init
	

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
            
            $locale = apply_filters( 'plugin_locale', get_locale(), self::SLUG );
            load_textdomain( self::SLUG, WP_LANG_DIR.'/'.self::SLUG.'/'.self::SLUG.'-'.$locale.'.mo' );
            load_plugin_textdomain( self::SLUG, FALSE, plugin_dir_url( __FILE__ ) . 'lang/' );

	} // end plugin_textdomain
	
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
        function cookie_notification_styles() {
            if ( function_exists( catapult_add_cookie_bar ) ) {
                wp_enqueue_style( 'okf-transparent-cookie-bar', plugin_dir_url( __FILE__ ) . 'css/cookie-bar.css', false, false);
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
        
  
} // end class
