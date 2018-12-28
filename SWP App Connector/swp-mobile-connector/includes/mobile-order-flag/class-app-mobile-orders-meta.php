<?php
if( !defined('ABSPATH')){
    
    /****** Exit if file directly access ******/
    exit;
}

/*************************************************
* @param flag order meta is use for adding column in woocommerce orders texonomy
* Mobile Order(column name)
* @return string
**************************************************/
if (! class_exists ('swp_flag_order_meta')){
    class swp_flag_order_meta{
       
        function __construct(){
            add_filter( 'manage_edit-shop_order_columns',  array( $this, 'swp_flag_order_column') , 11 );
            add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'swp_flag_order_column_content' ) , 10,2  );
            add_action( 'rest_api_init', array( $this, 'swp_mobile_product_orders' ) );
        }
        
        function swp_flag_order_column($columns){
        
            $reordered_columns = array();

            /***** Inserting columns to a specific location  *****/
            foreach( $columns as $key => $column){
                $reordered_columns[$key] = $column;
                if( $key ==  'order_status' ){
                    // Inserting after "Status" column
                    $reordered_columns['flag_status'] = __( 'Mobile Order','swpappconnector');
                }
            }
            return $reordered_columns;
        }
        
        /***** Adding custom fields meta data for each new column (example) *****/
        function swp_flag_order_column_content( $column ){
          
           global $post, $woocommerce;
	       $order_id = $post->ID;
              switch ( $column )
                {
                    case 'flag_status' :
                        /***** Get custom post meta data *****/
                        $flag_status = get_post_meta( $order_id, 'swp_mobile_order', true );
                        if(!empty($flag_status))
                            echo "yes";

                        /***** Testing (to be removed) - Empty value case  *****/
                        else
                            echo '-';

                        break;

                }
        }
        
        function swp_mobile_product_orders(){
                
            $swp_mobile_order_schema = array(
                'description' => 'Mobile order flag',
                'type' => 'boolean',
                'context' => array('view','edit'),
                'default' => false
            );
            /***** register product title *****/
               register_rest_field( 'shop_order', 'swp_mobile_order', array(
                'get_callback'    =>  array( $this, 'swp_get_mobile_product_order_callback' ),
                'update_callback' => array( $this, 'swp_update_mobile_product_order_callback' ),
                'schema' => $swp_mobile_order_schema,
            ));
        }

        /***** Get product orders *****/
        function swp_get_mobile_product_order_callback($order, $field_name, $request) {
            
            /***** $order['id'] use for call array of order use only for get_post_meta *****/
            return get_post_meta( $order['id'], $field_name, true );  
        }
        
        /***** Update Product Orders *****/
         function swp_update_mobile_product_order_callback($value, $order, $field_name ) {
            if(!empty($field_name)){
                
            /***** $order->ID use for call object of order use only for update_post_meta *****/
            return update_post_meta( $order->ID, 'swp_mobile_order', $value ); 
            }
             else{
                return ;
             }
            
        }
    }
}
            
        
            
        
    
    

  
