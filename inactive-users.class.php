<?php


class OKF_Inactive_Users {
    
    const SLUG = 'okf-inactive-users';
    protected $user_list;
    protected $inactive_count;
	
    public static function init() {
        add_action( 'network_admin_menu', array( get_class(), 'network_menu' ) );
    } // end init
	

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
	
            wp_enqueue_style( sprintf( '%s-admin-styles', self::SLUG ), plugin_dir_url( __FILE__ ) . 'css/inactive-users.css' );
	
	} // end register_admin_styles
	
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
        function network_menu() { 
            
            add_submenu_page( 'settings.php', 'Inactive Users', 'Inactive Users', 'manage_network', self::SLUG, array( get_class(), 'network_page' ));
            
        }
        
        function network_page() { 
            include( plugin_dir_path( __FILE__ ) . 'views/inactive-users.php' );
        }
        
        function build_user_list_array($users = false) {
            global $wpdb;

            $this->inactive_count = 0;
            $this->user_list = array();
            $limit = 2000;

            $users = $wpdb->get_col( "SELECT ID FROM $wpdb->users" );
            
            if ($users) {
                foreach ($users as $k => $user) {
                    $user_blogs = get_blogs_of_user($user);

                    if ( empty( $user_blogs ) && !is_super_admin($user) ) {

                        $this->inactive_count++;
                        $this->user_list[] = $user;
                        if ( $this->inactive_count == $limit ) break;
                    }
                }
            }
            
            return $this;
        }
        
        function count_user_list() {
            return $this->inactive_count;
        }
        
        function filter_user_list($per_page, $offset) {
            return array_slice($this->user_list, $offset, $per_page);
        }

        function delete_all() {
            if ($this->build_user_list_array()->user_list) {
                foreach ( $this->user_list as $user ) {
                    wpmu_delete_user($user);
                    update_site_option( sprintf('%s-total-deleted', self::SLUG), ($this->get_total_deleted() + 1) );
                }
            }
        }
        
        function delete_users( $users = false ) {
            if ($users) {
                foreach ( $users as $user ) {
                    wpmu_delete_user($user);
                    update_site_option( sprintf('%s-total-deleted', self::SLUG), ($this->get_total_deleted() + 1) );
                }
            }
        }

        function get_total_deleted() {
            return get_site_option( sprintf('%s-total-deleted', self::SLUG), 0 , false );
        }


        // function user_blog_checker() {
        //     global $wpdb;

        //     $count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
        //     $batch = 200;
        //     $offset = 0;
        //     $inactive_users = 0;


        //     $filename = sanitize_title_with_dashes('Inactive User Export') . "-" . gmdate("Y-m-d", time()) . ".csv";
        //     $charset = get_option('blog_charset');
        //     $lines = chr(239) . chr(187) . chr(191);
        //     $separator = ',';

        //     $fields = array(
        //         'ID',
        //         'user_login',
        //         'user_email'
        //     );

        //     header('Content-Description: File Transfer');
        //     header("Content-Disposition: attachment; filename=$filename");
        //     header('Content-Type: text/plain; charset=' . $charset, true);
        //     ob_clean();

        //     foreach ( $fields as $field_label ) {
        //         $lines .= '"' . str_replace('"', '""', $field_label) . '"' . $separator;
        //     }
        //     $lines.= "\n";
            
            
        //     for ( $i = 1; $i <= ($count/$batch); $i++ ) {
        //     // for ( $i = 1; $i <= 2; $i++ ) {
        //         $users = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->users LIMIT %d, %d", $offset, $batch) );
        //         if ($users) {
        //             foreach ($users as $user) {
        //                 $user_blogs = get_blogs_of_user($user->ID);

        //                 if (empty( $user_blogs )) {
        //                     foreach ($fields as $field) {
        //                         $lines .= '"' . str_replace('"', '""', $user->$field) . '"' . $separator;
        //                     }
        //                     // $inactive_users++;
        //                 }
        //                 $lines = substr($lines, 0, strlen($lines)-1);
        //                 $lines.= "\n";
        //             }
        //         }
                
        //         $offset += $batch;
        //         if ( !seems_utf8( $lines ) )
        //             $lines = utf8_encode( $lines );

        //         echo $lines;
        //         $lines = "";
        //     }
        //     // echo $inactive_users;
        //     die();

        // }
  
} // end class

