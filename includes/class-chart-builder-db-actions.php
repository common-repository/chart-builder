<?php
global $chart_builder_db_actions_notices;
$chart_builder_db_actions_notices = 0;

if( !class_exists( 'Chart_Builder_DB_Actions' ) ){
    ob_start();

	/**
	 * Class Chart_Builder_DB_Actions
	 * Class contains functions to interact with chart database
	 *
	 * Main functionality belong to inserting, updating and deleting of
	 * Also chart settings and options
	 *
	 * Hooks used in the class
	 * @hooks           @filters        ays_chart_item_save_options
	 *                                  ays_chart_item_save_settings
     *
     * Database tables without prefixes
     * @tables          charts
     *                  charts_meta
	 *
	 * @param           $plugin_name
     *
	 * @since           1.0.0
	 * @package         Chart_Builder
	 * @subpackage      Chart_Builder/includes
	 * @author          Chart Builder Team <info@ays-pro.com>
	 */
    class Chart_Builder_DB_Actions {

        /**
         * The ID of this plugin.
         *
         * @since       1.0.0
         * @access      private
         * @var         string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The name of table in the database.
         *
         * @since       1.0.0
         * @access      private
         * @var         string    $db_table    The name of database table.
         */
        private $db_table;

	    /**
	     * The name of meta table in the database.
	     *
	     * @since       1.0.0
	     * @access      private
	     * @var         string    $db_table_meta    The name of database table.
	     */
        private $db_table_meta;

	    /**
	     * The constructor of the class
	     *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $plugin_name
	     */
        public function __construct( $plugin_name ) {

	        global $wpdb;
	        global $chart_builder_db_actions_notices;

	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
            $this->plugin_name = $plugin_name;

	        /**
	         * Assigning database @charts table full name to the @db_table property
	         */
            $this->db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts";

	        /**
	         * Assigning database @charts metas table full name to the @db_table_meta property
	         */
            $this->db_table_meta = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts_meta";


	        /**
	         * Adding action to admin_notices hook
             * Will work when there is some notice after some action
	         */
            if( $chart_builder_db_actions_notices === 0 ) {
	            add_action( 'admin_notices', array( $this, 'chart_notices' ) );
	            $chart_builder_db_actions_notices++;
            }

        }

	    /**
	     * Get instance of this class
	     *
	     * @since       1.0.0
         * @access      public
         *
	     * @param       $plugin_name
         *
	     * @return      Chart_Builder_DB_Actions
	     */
        public static function get_instance( $plugin_name ){
            return new self( $plugin_name );
        }

	    /**
         * Get records form database
         * Applying filters like per page and ordering
         *
         * @since       1.0.0
	     * @access      public
         *
	     * @return      array
	     */
        public function get_items(){
            global $wpdb;

            $per_page = $this->get_pagination_count();

            $page_number = 1;
            if ( ! empty( $_REQUEST['paged'] ) ) {
                $page_number = absint( sanitize_text_field( $_REQUEST['paged'] ) );
            }

            $sql = "SELECT * FROM " . $this->db_table;

            $sql .= self::get_where_condition();

            if ( ! empty( $_REQUEST['orderby'] ) ) {
                $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
                $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

                $sql_orderby = sanitize_sql_orderby( $order_by );

                if ( $sql_orderby ) {
                    $sql .= ' ORDER BY ' . $sql_orderby;
                } else {
                    $sql .= ' ORDER BY id DESC';
                }
            }else{
                $sql .= ' ORDER BY id DESC';
            }

            $p_page = ($page_number - 1) * $per_page;
            $sql .= " LIMIT $p_page , $per_page";
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );

            return $result;
        }

	    /**
	     * @return mixed
	     */
        public function get_pagination_count(){
            $per_page = get_user_meta( get_current_user_id(), 'cb_charts_per_page', true );
            if( $per_page == '' ){
                $per_page = 5;
            }
            $per_page = absint( $per_page );
            return $per_page;
        }

	    /**
         * Get WHERE condition for SQL queries that trying to get records
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @return      string
	     */
        public static function get_where_condition(){
            global $wpdb;
            $where = array();
            $sql = '';

//            $search = ( isset( $_REQUEST['s'] ) ) ? sanitize_text_field( $_REQUEST['s'] ) : false;
//            if( $search ){
//                $s = array();
//                $s[] = sprintf( "`user_name` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
//                $s[] = sprintf( "`user_email` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
//                $s[] = sprintf( "POSITION( '%%%s%%' IN `submission_date` )", esc_sql( $wpdb->esc_like( $search ) ) );
//                $s[] = sprintf( "`unique_code` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
//                // $s[] = ' `score` LIKE \'%'.$search.'%\' ';
//                $where[] = ' ( ' . implode(' OR ', $s) . ' ) ';
//            }

            if ( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != ''){
                $where[] = ' `status` = "' . esc_sql( sanitize_text_field( $_GET['fstatus'] ) ) . '" ';
            }else{
	            $where[] = ' `status` != "trashed" ';
            }

//            if( isset( $_REQUEST['wpuser'] ) ){
//                $user_id = absint( sanitize_text_field( $_REQUEST['wpuser'] ) );
//                $where[] = ' `user_id` = '.$user_id.' ';
//            }

//            if( isset( $_REQUEST['form'] ) ){
//                $quiz_id = absint( sanitize_text_field( $_REQUEST['form'] ) );
//                $where[] = ' `form_id` = '.$quiz_id.' ';
//            }



            if( ! empty($where) ){
                $sql = " WHERE " . implode( " AND ", $where );
            }

            return $sql;
        }

	    /**
         * Get record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      array|false
	     */
        public function get_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            $sql = "SELECT * FROM ". $this->db_table ." WHERE id = '". $id ."'";
            $result = $wpdb->get_row( $sql, ARRAY_A );

            if( $result ){
                return $result;
            }

            return false;
        }

	    /**
	     * Insert or update record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
         * @redirect    to specific page based on clicked button
	     * @param       $id
         *
	     * @return      false|void
	     */
        public function add_or_edit_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( isset( $_POST["chart_builder_action"] ) && wp_verify_nonce( $_POST["chart_builder_action"], 'chart_builder_action' ) ){
                $success = 0;
                $name_prefix = 'ays_';

                // Save type
                $save_type = isset( $_POST['save_type'] ) && $_POST['save_type'] != '' ? sanitize_text_field( $_POST['save_type'] ) : '';

	            // Author_id
	            $author_id = get_current_user_id();

	            // Title
                $title = isset( $_POST[ $name_prefix . 'title' ] ) && $_POST[ $name_prefix . 'title' ] != '' ? stripslashes( sanitize_text_field( $_POST[ $name_prefix . 'title' ] ) ) : 'Untitled chart';

                // if( $title == '' ){
                //     $message = 'empty-title';
                //     $url = esc_url_raw( remove_query_arg( false ) );
                //     $url = esc_url_raw( add_query_arg( array(
                //         'status' => 'empty-title'
                //     ), $url ) );
                //     wp_redirect( $url );
                //     exit();
                // }


                // Description
                $description = isset( $_POST[ $name_prefix . 'description' ] ) && $_POST[ $name_prefix . 'description' ] != '' ? stripslashes( sanitize_text_field($_POST[ $name_prefix . 'description' ]) ) : '';

                // Type
                $type = 'google-charts'; //isset( $_POST[ $name_prefix . 'type' ] ) && $_POST[ $name_prefix . 'type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'type' ] ) : '';

                // Source chart type
                $source_chart_type = isset( $_POST[ $name_prefix . 'source_chart_type' ] ) && $_POST[ $name_prefix . 'source_chart_type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'source_chart_type' ] ) : 'pie_chart';

                // Source type
                $source_type = isset( $_POST[ $name_prefix . 'source_type' ] ) && $_POST[ $name_prefix . 'source_type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'source_type' ] ) : 'manual';

                // Manual data
                // $manual_data = isset( $_POST[ $name_prefix . 'manual_data' ] ) && $_POST[ $name_prefix . 'manual_data' ] != '' ? stripslashes( sanitize_textarea_field( $_POST[ $name_prefix . 'manual_data' ] ) ) : '';

                $chart_source_data_add = isset($_POST[ $name_prefix . 'chart_source_data' ]) && !empty( $_POST[ $name_prefix . 'chart_source_data' ] ) ? sanitize_text_field(json_encode($_POST[ $name_prefix . 'chart_source_data' ])) : array();
                $chart_source_data_add = json_decode($chart_source_data_add, true);
                
                $chart_source_filtered_data = array();
                foreach($chart_source_data_add as $chart_source_data_key => $chart_source_data_value){
                    if(!empty($chart_source_data_value) && (isset($chart_source_data_value[0]) && trim($chart_source_data_value[0]) != '')){
                        foreach($chart_source_data_value as $s_data_key => $s_data_value){
                            $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && $s_data_value != '') ? stripslashes( sanitize_text_field( $s_data_value ) ) : '';
                        }

                    }
                }
                $chart_source_filtered_data = json_encode( $chart_source_filtered_data );

                // Source
                switch ( $source_type ){
                    case 'manual':
                    default:
                        $source = $chart_source_filtered_data;
                        break;
                }

                // Status
                $status = isset( $_POST[ $name_prefix . 'status' ] ) && $_POST[ $name_prefix . 'status' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'status' ] ) : 'draft';

                // Date created
                $date_created = isset( $_POST[ $name_prefix . 'date_created' ] ) && CBFunctions()->validateDate( $_POST[ $name_prefix . 'date_created' ] ) ? sanitize_text_field($_POST[ $name_prefix . 'date_created' ]) : current_time( 'mysql' );

                // Date modified
                $date_modified = isset( $_POST[ $name_prefix . 'date_modified' ] ) && CBFunctions()->validateDate( $_POST[ $name_prefix . 'date_modified' ] ) ? sanitize_text_field($_POST[ $name_prefix . 'date_modified' ]) : current_time( 'mysql' );

                // Options
                $options = array();
                if( isset( $_POST[ $name_prefix . 'options' ] ) && !empty( $_POST[ $name_prefix . 'options' ] )) {
                    foreach($_POST[ $name_prefix . 'options' ] as $each_option_key => $each_option_value){
                        $each_option_value = isset($each_option_value) && $each_option_value != '' ? sanitize_text_field($each_option_value) : '';
                        $each_option_key   = isset($each_option_key) && $each_option_key != '' ? sanitize_text_field($each_option_key) : '';
                        $options[$each_option_key] = sanitize_text_field($each_option_value);
                    }
                }

                $options = apply_filters( 'ays_chart_item_save_options', $options );

                // Settings
                // == Sanitize in the loop all values except checkboxes, radios (only for settings array). The latter sanitize separately (out of loop) ==
                $settings = array();
                if( isset( $_POST[ $name_prefix . 'settings' ] ) && !empty( $_POST[ $name_prefix . 'settings' ] )) {
                    foreach($_POST[ $name_prefix . 'settings' ] as $each_setting_key => $each_setting_value){
                        $each_setting_value = isset($each_setting_value) && $each_setting_value != '' ? sanitize_text_field($each_setting_value) : '';
                        $each_setting_key   = isset($each_setting_key) && $each_setting_key != '' ? sanitize_text_field($each_setting_key) : '';
                        $settings[$each_setting_key] = $each_setting_value;
                    }
                }
                // == Sanitize checkboxes, radios here (only for settings array) ==
	            $settings['show_color_code'] = ( isset( $settings['show_color_code'] ) && $settings['show_color_code'] != '' ) ? sanitize_text_field($settings['show_color_code']) : 'off';

                $settings = apply_filters( 'ays_chart_item_save_settings', $settings );

                $message = '';
                if( $id == 0 ){
                    $result = $wpdb->insert(
                        $this->db_table,
                        array(
	                        'author_id'         => $author_id,
                            'title'             => $title,
                            'description'       => $description,
                            'type'              => $type,
                            'source_chart_type' => $source_chart_type,
                            'source_type'       => $source_type,
                            'source'            => $source,
                            'status'            => $status,
                            'date_created'      => $date_created,
                            'date_modified'     => $date_modified,
                            'options'           => json_encode( $options ),
                        ),
                        array(
	                        '%s', // author_id
                            '%s', // title
                            '%s', // description
                            '%s', // type
                            '%s', // source_chart_type
                            '%s', // source_type
                            '%s', // source
                            '%s', // status
                            '%s', // date_created
                            '%s', // date_modified
                            '%s', // options
                        )
                    );

                    $inserted_id = $wpdb->insert_id;

                    if( is_array( $settings ) && ! empty( $settings ) ){
                        foreach ( $settings as $key => $setting ){
                            $this->add_meta( $inserted_id, $key, $setting );
                        }
                    }

                    $message = 'created';
                }else{
                    $result = $wpdb->update(
                        $this->db_table,
                        array(
	                        'author_id'         => $author_id,
                            'title'             => $title,
                            'description'       => $description,
                            'type'              => $type,
                            'source_chart_type' => $source_chart_type,
                            'source_type'       => $source_type,
                            'source'            => $source,
                            'status'            => $status,
                            'date_modified'     => $date_modified,
                            'options'           => json_encode( $options ),
                        ),
                        array( 'id' => $id ),
                        array(
	                        '%s', // author_id
                            '%s', // title
                            '%s', // description
                            '%s', // type
                            '%s', // source_chart_type
                            '%s', // source_type
                            '%s', // source
                            '%s', // status
                            '%s', // date_modified
                            '%s', // options
                        ),
                        array( '%d' )
                    );

                    $inserted_id = $id;


                    if( is_array( $settings ) && ! empty( $settings ) ){
                        foreach ( $settings as $key => $setting ){
                            if( $this->get_meta( $inserted_id, $key ) ) {
                                $this->update_meta( $inserted_id, $key, $setting );
                            }else{
                                $this->add_meta( $inserted_id, $key, $setting );
                            }
                        }
                    }

                    $message = 'updated';
                }

                if( $result >= 0  ) {
                    if( $save_type == 'apply' ){
                        if($id == 0){
                            $url = esc_url_raw( add_query_arg( array(
                                "action"    => "edit",
                                "id"        => $inserted_id,
                                "status"    => $message
                            ) ) );
                        }else{
                            $url = esc_url_raw( add_query_arg( array(
                                "status" => $message
                            ) ) );
                        }
                        wp_redirect( $url );
                        exit;
                    }elseif( $save_type == 'save_new' ){
                        $url = remove_query_arg( array('id') );
                        $url = esc_url_raw( add_query_arg( array(
                            "action" => "add",
                            "status" => $message
                        ), $url ) );
                        wp_redirect( $url );
                        exit;
                    }else{
                        $url = remove_query_arg( array('action', 'id') );
                        $url = esc_url_raw( add_query_arg( array(
                            "status" => $message
                        ), $url ) );
                        wp_redirect( $url );
                        exit;
                    }
                }

            }else{
                return false;
            }

        }

	    /**
	     * Delete record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function delete_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            $wpdb->delete(
                $this->db_table_meta,
                array( 'chart_id' => absint( $id ) ),
                array( '%d' )
            );

            $wpdb->delete(
                $this->db_table,
                array( 'id' => absint( $id ) ),
                array( '%d' )
            );

	        return true;
        }

	    /**
	     * Move to trash record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function trash_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

	        $result = $wpdb->update(
		        $this->db_table,
		        array( 'status' => 'trashed' ),
		        array( 'id' => absint( $id ) ),
		        array( '%s' ),
		        array( '%d' )
	        );

            if( $result >= 0 ){
                return true;
            }

	        return false;
        }

	    /**
	     * Restore record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function restore_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

	        $result = $wpdb->update(
		        $this->db_table,
		        array( 'status' => 'draft' ),
		        array( 'id' => absint( $id ) ),
		        array( '%s' ),
		        array( '%d' )
	        );

            if( $result >= 0 ){
                return true;
            }

	        return false;
        }

	    /**
	     * Get record metadata by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      array
	     */
        public function get_metadata( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return array();
            }

            $sql = "SELECT * FROM " . $this->db_table_meta . " WHERE chart_id = " . $id;

            $results = $wpdb->get_results($sql, ARRAY_A);

            if( count( $results ) > 0 ){
                return $results;
            }else{
                return array();
            }
        }

	    /**
	     * Convert record metadata to useful format
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $settings
         *
	     * @return      array
	     */
        public function convert_metadata( $settings ){

            if( ! is_array( $settings ) || empty( $settings ) ){
                return array();
            }

            $data = array();
            foreach ( $settings as $k => $setting ) {
                $data[ $setting['meta_key'] ] = $setting['meta_value'];
            }

            return $data;
        }

	    /**
	     * Add default values to record metadata of the chart
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $settings
         *
	     * @return      array
	     */
        public function apply_default_metadata( $settings ){

            if( ! is_array( $settings ) || empty( $settings ) ){
                return array();
            }

            $defaults = array(
                'width' => '',
                'height' => '',
                'font_size' => '',
                'title_color' => '',
            );

            foreach ( $defaults as $key => $default ) {
                if( ! isset( $settings[ $key ] ) ) {
                    $settings[ $key ] = $default;
                }
            }

            return $settings;
        }

	    /**
	     * Get record meta by record id and meta key
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key
         *
	     * @return      false|array
	     */
        public function get_meta( $id, $meta_key ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $sql = "SELECT meta_value FROM ". $this->db_table_meta ." WHERE meta_key = '".$meta_key."'";
            $result = $wpdb->get_var($sql);

            if( $result != "" ){
                return $result;
            }

            return false;
        }

	    /**
	     * Insert record meta by record id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key         @accept string
	     * @param       $meta_value       @accept JSON|serialized array|string|number
	     * @param       string $note      @accept string|number
	     * @param       string $options   @accept JSON
	     *
	     * @return      bool
	     */
        public function add_meta( $id, $meta_key, $meta_value, $note = "", $options = "" ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $result = $wpdb->insert(
                $this->db_table_meta,
                array(
                    'chart_id'    => absint( $id ),
                    'meta_key'    => $meta_key,
                    'meta_value'  => $meta_value,
                    'note'        => $note,
                    'options'     => $options
                ),
                array( '%s', '%s', '%s', '%s' )
            );

            if($result >= 0){
                return true;
            }

            return false;
        }

	    /**
	     * Update record meta by record id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key         @accept string
	     * @param       $meta_value       @accept JSON|serialized array|string|number
	     * @param       string $note      @accept string|number
	     * @param       string $options   @accept JSON
	     *
	     * @return      bool
	     */
        public function update_meta( $id, $meta_key, $meta_value, $note = "", $options = "" ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $value = array(
                'meta_value'  => $meta_value,
            );

            $value_s = array( '%s' );
            if($note != null){
                $value['note'] = $note;
                $value_s[] = '%s';
            }

            if($options != null){
                $value['options'] = $options;
                $value_s[] = '%s';
            }

            $result = $wpdb->update(
                $this->db_table_meta,
                $value,
                array(
                    'chart_id' => absint( $id ),
                    'meta_key' => $meta_key,
                ),
                $value_s,
                array( '%d', '%s' )
            );

            if($result >= 0){
                return true;
            }

            return false;
        }

	    /**
	     * Delete record meta by record id and meta key
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key
	     *
	     * @return      bool
	     */
        public function delete_meta( $id, $meta_key ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $wpdb->delete(
                $this->db_table_meta,
                array(
                    'chart_id' => absint( $id ),
                    'meta_key' => $meta_key,
                ),
                array( '%d', '%s' )
            );

            return true;
        }

	    /**
	     * Display notice based on action that happened to record
	     *
	     * @since       1.0.0
         * @access      public
         *
         * @return      void|html
	     */
        public function chart_notices(){
            $page = (isset($_REQUEST['page'])) ? sanitize_text_field( $_REQUEST['page'] ) : '';
            if ( !($page == "chart-builder") )
                return;

            $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

            if ( empty( $status ) )
                return;

            $error = false;
            switch ( $status ) {
                case 'created':
                    $updated_message =  __( 'Chart created.', "chart-builder" ) ;
                    break;
                case 'updated':
                    $updated_message =  __( 'Chart saved.', "chart-builder" ) ;
                    break;
                case 'duplicated':
                    $updated_message =  __( 'Chart duplicated.', "chart-builder" ) ;
                    break;
                case 'deleted':
                    $updated_message =  __( 'Chart deleted.', "chart-builder" ) ;
                    break;
                case 'trashed':
                    $updated_message =  __( 'Chart moved to trash.', "chart-builder" ) ;
                    break;
                case 'restored':
                    $updated_message =  __( 'Chart restored.', "chart-builder" ) ;
                    break;
                case 'all-duplicated':
                    $updated_message =  __( 'Charts are duplicated.', "chart-builder" ) ;
                    break;
                case 'all-deleted':
                    $updated_message =  __( 'Charts are deleted.', "chart-builder" ) ;
                    break;
                case 'all-trashed':
                    $updated_message =  __( 'Charts are moved to trash.', "chart-builder" );
                    break;
                case 'all-restored':
                    $updated_message =  __( 'Charts are restored.', "chart-builder" );
                    break;
                case 'empty-title':
                    $error = true;
                    $updated_message =  __( 'Error: Chart title can not be empty.', "chart-builder" ) ;
                    break;
                default:
                    break;
            }

            if ( empty( $updated_message ) )
                return;

            $notice_class = 'success';
            if( $error ){
                $notice_class = 'error';
            }
            ?>
            <div class="notice notice-<?php echo esc_attr( $notice_class ); ?> is-dismissible">
                <p> <?php echo esc_html($updated_message); ?> </p>
            </div>
            <?php
        }

    }
}