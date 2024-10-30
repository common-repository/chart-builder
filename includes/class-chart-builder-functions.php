<?php

if( !class_exists( 'Chart_Builder_Functions' ) ){

	/**
	 * Class Chart_Builder_Functions
	 * Class contains useful functions that uses in common
	 *
	 *
	 * Hooks used in the class
	 * There are not hooks yet
	 * @hooks           @actions
	 *                  @filters
	 *
	 *
	 * @param           $plugin_name
	 *
	 * @since           1.0.0
	 * @package         Chart_Builder
	 * @subpackage      Chart_Builder/includes
	 * @author          Chart Builder Team <info@ays-pro.com>
	 */
    class Chart_Builder_Functions {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

	    /**
	     * The constructor of the class
	     *
	     * @since   1.0.0
	     * @param   $plugin_name
	     */
        public function __construct( $plugin_name ) {
			global $wpdb;
	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
            $this->plugin_name = $plugin_name;
			$this->db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts";
        }


	    /**
	     * Get instance of this class
	     *
	     * @access  public
	     * @since   1.0.0
	     * @param   $plugin_name
	     * @return  Chart_Builder_Functions
	     */
	    public static function get_instance( $plugin_name ){
		    return new self( $plugin_name );
	    }

	    /**
         * Date validation function
         *
         * @accept  two parameters
	     * @param   $date
	     * @param   string $format
	     *
	     * @return  bool
	     */
        public function validateDate( $date, $format = 'Y-m-d H:i:s' ){
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }


	    /**
	     * Version compare function
	     *
	     * @accept  two parameters
	     * @param   $version1
	     * @param   $operator
	     * @param   $version2
	     *
	     * @return  bool|int
	     */
        public function versionCompare( $version1, $operator, $version2 ) {

            $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
            $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );

            if (strlen ( $_fv ) > strlen ( $_sv )) {
                $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
            }

            if (strlen ( $_fv ) < strlen ( $_sv )) {
                $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
            }

            return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
        }

        public function get_all_charts_count() {
			global $wpdb;
            $sql = "SELECT COUNT(id) FROM " . $this->db_table . " WHERE `status`='published'";
			return intval( $wpdb->get_var( $sql ) );
        }


	    /**
	     * Gets the properties of the post type
	     *
	     * @access friendly
	     */
        // public function fetch_post_type_properties() {
        //     $array = $this->get_post_type_properties( $_POST['post_type'] );
        //     $this->_sendResponse(
        //         array(
        //             'success' => true,
        //             'fields'  => $array,
        //         )
        //     );
        //     wp_die();
	    // }

	    /**
	     * Gets the allowed types
	     *
	     * @access private
	     */
	    public function getAllowedTypes() {
		    return array(
			    'string',
			    'number',
			    'boolean',
			    'date',
			    'datetime',
			    'timeofday',
		    );
	    }

	    /**
	     * Gets the properties of the post type
	     *
	     * @access private
	     */
	    public function get_post_type_properties( $post_type ) {
		    $array = null;

		    $query = new WP_Query(
			    array(
				    'post_type'              => $post_type,
				    'no_rows_found'          => false,
				    'post_per_page'          => 1,
				    'orderby'                => 'post_date',
				    'order'                  => 'DESC',
				    'fields'                 => 'ids',
				    'update_post_meta_cache' => false,
				    'update_post_term_cache' => false,
			    )
		    );

		    $array = array(
			    'post_title',
			    'post_status',
			    'comment_count',
			    'post_date',
			    'post_modified',
		    );

		    if ( $query->have_posts() ) {
			    $id   = $query->posts[0];
			    $meta = get_post_meta( $id, '', true );
			    foreach ( $meta as $key => $values ) {
				    $array[] = $key;
			    }
		    }

		    return $array;
	    }

	    /**
	     * Gets all tables and their columns.
	     *
	     * @access public
	     * @return array
	     */
	    public function get_all_db_tables_column_mapping( $chart_id, $use_filter = true ) {
		    $mapping    = array();
		    $tables     = $this->get_db_tables();
		    foreach ( $tables as $table ) {
			    $cols   = $this->get_db_table_columns( $table, true );
			    $names  = wp_list_pluck( $cols, 'name' );
			    $mapping[ $table ] = $names;
		    }

		    return $mapping;
	    }

	    /**
	     * Gets the tables in the database;
	     *
	     * @access public
	     * @return array
	     */
	    public function get_db_tables() {
		    global $wpdb;
		    $tables = get_transient( CHART_BUILDER_DB_PREFIX . 'db_tables' );
		    if ( $tables ) {
			    return $tables;
		    }

		    $sql    = $wpdb->get_col( 'SHOW TABLES', 0 );
		    foreach ( $sql as $table ) {
			    if ( empty( $prefix ) || 0 === strpos( $table, $wpdb->prefix ) ) {
				    $tables[] = $table;
			    }
		    }

		    set_transient( CHART_BUILDER_DB_PREFIX . 'db_tables', $tables, HOUR_IN_SECONDS );
		    return $tables;
	    }

	    /**
	     * Gets the column information for the table.
	     *
	     * @param string $table The table.
	     * @param bool   $prefix_with_table Whether to prefix column name with the name of the table.
	     * @access private
	     * @return array
	     */
	    private function get_db_table_columns( $table, $prefix_with_table = false ) {
		    global $wpdb;
		    $columns    = get_transient( CHART_BUILDER_DB_PREFIX . "db_{$table}_columns" );
		    if ( $columns ) {
			    return $columns;
		    }
		    $columns    = array();
		    // @codingStandardsIgnoreStart
		    $rows       = $wpdb->get_results( "SHOW COLUMNS IN `$table`", ARRAY_N );
		    // @codingStandardsIgnoreEnd
		    if ( $rows ) {
			    // n => numeric, d => date-ish, s => string-ish.
			    foreach ( $rows as $row ) {
				    $col        = ( $prefix_with_table ? "$table." : '' ) . $row[0];
				    $type       = $row[1];
				    if ( strpos( $type, 'int' ) !== false || strpos( $type, 'float' ) !== false ) {
					    $type   = 'n';
				    } elseif ( strpos( $type, 'date' ) !== false || strpos( $type, 'time' ) !== false ) {
					    $type   = 'd';
				    } else {
					    $type   = 's';
				    }
				    $columns[]  = array( 'name' => $col, 'type' => $type );
			    }
		    }

		    set_transient( CHART_BUILDER_DB_PREFIX . "db_{$table}_columns", $columns, DAY_IN_SECONDS );
		    return $columns;
	    }

	    /**
	     * Gets the dependent tables and then gets column information for all the tables.
	     *
	     * @param string $table The table.
	     * @access public
	     * @return array
	     */
	    public function get_table_columns( $table ) {
		    $columns    = array();
		    if ( ! $table ) {
			    return $columns;
		    }

		    $tables = array( $table );
		    $mapping = $this->get_db_table_mapping();
		    if ( array_key_exists( $table, $mapping ) ) {
			    $tables[] = $mapping[ $table ];
		    }
		    foreach ( $tables as $table ) {
			    $cols = $this->get_db_table_columns( $table, count( $tables ) > 1 );
			    $columns = array_merge( $columns, $cols );
		    }
		    return $columns;
	    }

	    /**
	     * Gets the relationship between tables in the database.
	     *
	     * @access public
	     * @return array
	     */
	    public function get_db_table_mapping() {
		    global $wpdb;
		    $mapping = get_transient( CHART_BUILDER_DB_PREFIX . 'db_table_mapping' );
		    if ( $mapping ) {
			    return $mapping;
		    }
		    // no need to provide x=>y and then y=>x as we will flip the array shortly.
		    $mapping = array(
			    $wpdb->prefix . 'posts' => $wpdb->prefix . 'postmeta',
			    $wpdb->prefix . 'users' => $wpdb->prefix . 'usermeta',
			    $wpdb->prefix . 'terms' => $wpdb->prefix . 'termmeta',
			    $wpdb->prefix . 'comments' => $wpdb->prefix . 'commentmeta',
		    );

		    $mapping += array_flip( $mapping );
		    set_transient( CHART_BUILDER_DB_PREFIX . 'db_table_mapping', $mapping, HOUR_IN_SECONDS );
		    return $mapping;
	    }

	    /**
	     * Creates HTML table from passed data
	     *
	     * @access public
	     * @return string
	     */
    }
}

if( ! function_exists( 'CBFunctions' ) ){
	/**
	 * Function for quick access to Chart_Builder_Functions class
	 *
	 * @since   1.0.0
	 * @return  Chart_Builder_Functions
	 */
	function CBFunctions(){

        static $instance = null;

        if( $instance === null ){
            $instance = Chart_Builder_Functions::get_instance( CHART_BUILDER_NAME );
        }

        if( $instance instanceof Chart_Builder_Functions ){
	        return $instance;
        }

		return Chart_Builder_Functions::get_instance( CHART_BUILDER_NAME );
	}
}