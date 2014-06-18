<?php

if(!class_exists('WP_List_Table'))
    require_once ABSPATH . '/wp-admin/includes/class-wp-list-table.php';


class OKF_Inactive_Users_List extends WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular'      => 'wp_list_inactive_user', // Singular label
            'plural'        => 'wp_list_inactive_users', // Plural label
            'ajax'          => false
        ) );
    }


    /**
    * Define the columns that are going to be used in the table
    * @return array $columns, the array of columns to use with the table
    */
    function get_columns() {
        return $columns= array(
            'cb'                => '<input type="checkbox" />',
            'col_user_id'       => __('User ID'),
            'col_user_login'    => __('User Login'),
            'col_user_email'    => __('User Email'),
            'col_reg_date'      => __('Registration Date')
        );
    }

    /**
    * Prepare the table with different parameters, pagination, columns and table elements
    */
    function prepare_items() {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();

        $this->process_bulk_action();

        $GLOBALS['okf_inactive_users']->build_user_list_array();
        $perpage = 500;

        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

        $totalpages = ceil( $GLOBALS['okf_inactive_users']->count_user_list() / $perpage );
        if( !empty($paged) && !empty($perpage) ){
            $offset = ( $paged-1 ) * $perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $GLOBALS['okf_inactive_users']->count_user_list(),
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );

        // /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // /* -- Fetch the items -- */
        $this->items = $GLOBALS['okf_inactive_users']->filter_user_list( (int)$perpage, (int)$offset);

    }

    function get_bulk_actions() {
        return $actions = array(
            'delete-all' => __( 'Delete All' ),
            'delete-selected' => __( 'Delete Selected' )
        );
    }

    function process_bulk_action() {

        switch($this->current_action()) {
            case "delete-all":
                $GLOBALS['okf_inactive_users']->delete_all();
                break;

            case "delete-selected":
                $GLOBALS['okf_inactive_users']->delete_users($_POST[$this->_args['singular']]);
                break;
        }
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item
        );
    }


    function column_default($item, $column_name) {
        $user = get_userdata( $item );
        switch($column_name) {
            case "col_user_id":
                echo stripslashes( $item );
                break;

            case "col_user_login":
                echo stripslashes( $user->user_login );
                break;

            case "col_user_email":
                echo stripslashes( $user->user_email );
                break;

            case "col_reg_date":
                echo stripslashes( $user->user_registered );
                break;
        }
    }


}
