<?php

      function swp_abc(){
        add_action( 'manage_shop_order_posts_custom_column' , 'swp_flag_order_columns_content', 10 , 2  );
        //add_action( 'rest_api_init', 'swp_mobile_product_orders' ); 
        add_filter( 'manage_edit-shop_order_columns', 'swp_flag_order_columns' , 11 );
            
        function swp_flag_order_columns($columns)
        {
            $reordered_columns = array();

            // Inserting columns to a specific location
            foreach( $columns as $key => $column){
                $reordered_columns[$key] = $column;
                if( $key ==  '_the_meta_key1' ){
                    // Inserting after "Status" column
                    $reordered_columns['flag_status'] = __( 'Mobile Order','swpappconnector');
                }
            }
            return $reordered_columns;
        }
        
        // Adding custom fields meta data for each new column (example)
        function swp_flag_order_columns_content( $column )
        {   
            global $post, $woocommerce;
	       $order_id = $post->ID;
          switch ( $column )
            {
                case 'flag_status' :
                    // Get custom post meta data
                    $flag_status = get_post_meta( $order_id, '_the_meta_key1', true );
                    if(!empty($flag_status))
                        echo "Yes";

                    // Testing (to be removed) - Empty value case
                    else
                        echo '1';

                    break;

            }
        }
      }
        
    
   
//    $swp_flag_order_meta = new swp_flag_order_meta();
