<?php 
defined('ABSPATH') or die('Denied');
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class WooconnectorTable extends WP_List_Table{
	
	public function __construct() {

		$args = array(
			'singular' => __( 'Notification','woocommerce'), //singular name of the listed records
			'plural'   => __( 'Notifications','woocommerce'), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		);
		parent::__construct($args);
	}
	
	public static function getItemsList( $per_page = 10, $page_number = 1 ) {
		$api = get_option('swp_settings-api');
		$api = esc_sql($api);
		global $wpdb;
		$table_name = $wpdb->prefix . "swp_data_api";
		$datas = $wpdb->get_results(
			"
			SELECT * 
			FROM $table_name
			WHERE api_key = '$api'
			"
		);
		$idswpapi = 0;
		if(!empty($datas)){
			foreach($datas as $data){
				$idswpapi = $data->api_id;
			}
		}		
		
		$ordervalue = isset($_REQUEST['orderby']) ?  $_REQUEST['orderby'] : 'create_date';
		
		$sql = "SELECT * FROM {$wpdb->prefix}swp_data_notification";
		
		$sql .= " WHERE api_id = $idswpapi";
		
		$sql .= ' ORDER BY ' . $ordervalue;
		
		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		
		$datas = $wpdb->get_results( $sql, 'ARRAY_A' );
		
		return $datas;
	}
	/**
	 * Delete a swp_data record.
	 *
	 * @param int $id swp_data ID
	 */
	public static function delete_notification( $id ) {
		global $wpdb;

		$wpdb->delete(
				"{$wpdb->prefix}swp_data_notification",
				array(
					'id' => $id,
			 		'%d' 
				)
		);
	}	
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count() {
	  global $wpdb;

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}swp_data_notification";

	  return $wpdb->get_var( $sql );
	}
	
	/** Text displayed when no swp_data data is available */
	public function no_items() {
		_e( 'No notification avaliable.', 'woocommerce' );
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_notification_id( $item ) {

	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'swp_delete_nonce' );

	  $title = '<strong><a class="row-title" href="?page=swp-one-signal&wootab=viewnotification&notification='. $item['notification_id'] .'">' . $item['notification_id'] . '</a></strong>';
		
	  $actions = array(
			'delete' => sprintf( '<a href="?page=%s&wootab=list&action=%s&id=%s&_wpnonce=%s">Delete</a>', 'swp-one-signal', 'delete',  $item['id'] , $delete_nonce )
	  );

	  return $title . $this->row_actions( $actions );
	}
	
	function column_actions( $item ) {
		return '<a class="button swpview-btn" href="?page=swp-one-signal&wootab=viewnotification&notification='. $item['notification_id'] .'" title="View"></a>';
	}
	
	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
	  switch ( $column_name ) {
		case 'notification_id':			
		case 'recipients':
		case 'create_date':
		case 'successful':
		case 'converted':
		case 'remaining':
		case 'failed':
		case 'total':
		case 'actions':
		  return $item[ $column_name ];
		default:
		  return print_r( $item, true ); //Show the whole array for troubleshooting purposes
	  }
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
	  return sprintf(
		'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	  );
	}
	
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = array(
			'cb'      => '<input type="checkbox" />',
			'notification_id'  => __( 'Notification', 'woocommerce' ),
			'create_date' => __( 'Create date', 'woocommerce' ),
			'recipients' => __( 'Recipients', 'woocommerce' ),
			'successful' => __( 'Successful', 'woocommerce' ),
			'converted' => __('Clicked','woocommerce'),
			'remaining' => __( 'Remaining', 'woocommerce' ),
			'failed' => __( 'Failed', 'woocommerce' ),
			'total' => __( 'Total', 'woocommerce' ),
			'actions' => __( 'Actions', 'woocommerce' ),
	  );

	  return $columns;
	}
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  $sortable_columns = array(
			'notification_id' => array( 'notification_id', true ),
			'recipients' => array( 'recipients', false ),
			'create_date' => array('create_date',true)
	  );

	  return $sortable_columns;
	}
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
	  $actions = array(
			'bulk-delete' => 'Delete'
	  );

	  return $actions;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

	  $columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

	  /** Process bulk action */
	  $this->process_bulk_action();

	  $per_page     = $this->get_items_per_page( 'swp_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ) );


	  $this->items = self::getItemsList( $per_page, $current_page );
	}
		
	
	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( isset($_REQUEST['action']) && 'delete' === $_REQUEST['action'] ) {

		// In our file that handles the request, verify the nonce.
		$nonce = isset($_REQUEST['_wpnonce']) ? esc_attr( $_REQUEST['_wpnonce']) : '';

		if ( ! wp_verify_nonce( $nonce, 'swp_delete_nonce' ) ) {
		  die( 'Go get a life script kiddies' );
		}
		else {
			$idnoti  = isset($_REQUEST['id']) ? absint( $_REQUEST['id']) : '';
			self::delete_notification( $idnoti );

			wp_redirect("?page=swp-one-signal&wootab=onesignal&onesignal=list");
		  
		}

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		   || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

		$delete_ids = esc_sql( $_POST['bulk-delete'] );

		// loop over the array of record IDs and delete them
		foreach ( $delete_ids as $id ) {
		  self::delete_notification( $id );

		}

		wp_redirect("?page=swp-one-signal&wootab=onesignal&onesignal=list");
		
	  }
	}
	
}
?>