<?php

if(! function_exists( 'swp_flag_order_meta' )){
   function swp_flag_order_meta(){

        function swp_flag_order_column($columns)
        {
            $reordered_columns = array();

            // Inserting columns to a specific location
            foreach( $columns as $key => $column){
                $reordered_columns[$key] = $column;
                if( $key ==  'order_status' ){
                    // Inserting after "Status" column
                    $reordered_columns['flag_status'] = __( 'Mobile App Flag','theme_domain');
                }
            }
            return $reordered_columns;
        }
        add_filter( 'manage_edit-shop_order_columns', 'swp_flag_order_column',20);
        
        // Adding custom fields meta data for each new column (example)
        function swp_flag_order_column_content( $column, $post_id )
        {
          switch ( $column )
            {
                case 'flag_status' :
                    // Get custom post meta data
                    $flag_status = get_post_meta( $post_id, '_the_meta_key1', true );
                    if(!empty($flag_status))
                        echo $flag_status;

                    // Testing (to be removed) - Empty value case
                    else
                        echo '1';

                    break;

            }
        }
        add_action( 'manage_shop_order_posts_custom_column' , 'swp_flag_order_column_content', 20,2  );

    }

}