<?php
if( !defined( 'ABSPATH' ) ){
    /***** EXIT if direct accessed file ******/ 
    exit;
}
/************************************************
*@popup endpoints for popup
* add image, link ,url for popup
*return array
************************************************/

if( !class_exists( 'swp_endpoint_popup' ) ){
    class swp_endpoint_popup{
        
        public function __construct(){
            //add_action( 'rest_api_init', array( $this, 'swp_endpoint_popup_add_image' ) );
            add_action( 'rest_api_init', array( $this, 'swp_app_register_popup_api' ) );    
        }
        
        /***** function for register api route  *****/
        public function swp_app_register_popup_api(){
            register_rest_route( 'swp/v1', '/popup',
                                array(
                                    'methods'         => 'GET',
                                    'callback'        => array( $this, 'swp_app_get_popup' ),
//                                    'permission_callback' => array( $this, 'swp_app_check_permissions_for_get_items' ),	
                                    					
                                ) 
                            );
        }
        
        /***** create popup function for fetching value  *****/
        public function swp_app_get_popup($request){
            $params = $request->get_params();
            $popup =  get_option('wooconnector-popup-homepage');
            $url = get_option('wooconnector-popup-homepage-link');
            $newurl = '';
            if(isset($url) && $url != ''){            
                $product_id = url_to_postid($url);
                if(!empty($product_id)) {
                    $newurl =  str_replace($url, 'link://product/'.$product_id, $url);
                }elseif(strpos($url,'link://') !== false){
                    $newurl = $url;
                }elseif(strpos($url,'product-category') !== false){
                    $url_split = explode('#', $url);
                    $url = $url_split[0];
                            // Get rid of URL ?query=string
                    $url_split = explode('?', $url);
                    $url = $url_split[0];

                    $scheme = parse_url( home_url(), PHP_URL_SCHEME );
                    $url = set_url_scheme( $url, $scheme );

                    if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
                    $url = str_replace('://', '://www.', $url);

                    if ( false === strpos(home_url(), '://www.') )
                    $url = str_replace('://www.', '://', $url);

                    $url = trim($url, "/");
                    $slugs = explode('/', $url);				
                    $category = $this->swp_get_product_category_by_slug('/'.end($slugs));
                    if(!empty($category)){
                        $newurl =  'link://product-category/'.$category->term_id;
                    }
                }
                elseif(strpos($url,'about-us') != false){
                    $newurl = 'link://about-us';
                }elseif(strpos($url,'bookmark') != false){
                    $newurl = 'link://bookmark';
                }elseif(strpos($url,'term-and-conditions') != false){
                    $newurl = 'link://term-and-conditions';
                }elseif(strpos($url,'privacy-policy') != false){
                    $newurl = 'link://privacy-policy';
                }elseif(strpos($url,'contact-us') != false){
                    $newurl = 'link://contact-us';
                }else{
                    $newurl = $url;
                }
            }
            $check = get_option('wooconnector-popup-homepage-check');
            $dt = false;
            if(!empty($check) && $check == 1){
                $dt = true;

            }
                             
            $return = array(
                'popup' => $popup,
                'link_popup' => $newurl
                );
            if(!empty($popup)){
                return $return;
            }else{
                return null;
            }
        }
        
                
        /***** function for retrive value by product category *****/
        public function swp_get_product_category_by_slug( $slug  ) {
            $category = get_term_by( 'slug', $slug, 'product_cat' );
            if ( $category )
                _make_cat_compat( $category );
            return $category;
        }
        
    /***** end class *****/
    }
    $swp_endpoint_popup = new swp_endpoint_popup();
}