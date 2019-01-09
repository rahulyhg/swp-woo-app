<?php
if( !defined( 'ABSPATH' )){
    /****** EXITE if file directly access ******/
    exit;
} 
/******************************************************
* @param create deals endpoints
* return array
*
*******************************************************/

if( !class_exists('SWPappendpointsdeals') ){
    class SWPappendpointsdeals{
        
        private $endpoint_url = 'swp/v1/product';

        /***** create construct function *****/
        public function __construct(){
             add_action( 'rest_api_init', array( $this, 'swp_app_register_deals_api'));
         }
        
        /*****  Add deals - endpoint *****/    
        
        public function swp_app_register_deals_api() {
            register_rest_route( $this->endpoint_url, '/deals', array(
                        'methods'         => 'GET',
                        'callback'        => array( $this, 'swp_app_get_deals' ),
                       // 'permission_callback' => array( $this, 'get_items_permissions_check' ),	
                        'args'            => array(
                            'post_per_page' => array(
                                'default' => 10,
                                'sanitize_callback' => 'absint',
                            ),
                            'post_num_page' => array(
                                'default' => 1,
                                'sanitize_callback' => 'absint',
                            ),
                        ),					
                ) 
            );
        }
        
        public function swp_app_get_deals($request){	
            require_once(ABSPATH.'/wp-content/plugins/swp-mobile-connector/settings/deals/class-app-connector-deals.php');
            $parameters = $request->get_params();
            $post_per_page = $parameters['post_per_page'];
            $post_num_page = $parameters['post_num_page'];
            $args = array(
                'posts_per_page'   => $post_per_page,
                'page'             => $post_num_page,
                'offset'           => 0,
                'category'         => '',
                'category_name'    => '',
                'orderby'          => 'menu_order',
                'order'            => 'ASC',			
                'post_type'        => 'app_deals',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'	       => '',
                'author_name'	   => '',
                'post_status'      => 'publish',
                'suppress_filters' => true 
            );
            $deals = get_posts($args);
            $wp_upload_dir = wp_upload_dir();	
            $listimages = array();
            $home = get_home_url();	
            foreach($deals as $deal){
                $imageid = get_post_thumbnail_id($deal->ID);		
                $oldurl = get_post_meta($deal->ID, 'app_deals_link', true);	
                $deal_large = get_post_meta($imageid, 'swp_app_deals_large', true);
                if(empty($deal_large))
                {
                    $post_ID = $deal->ID;
                    $post = get_post($post_ID);
                   $SWPappdeal = new SWPappdeal();
                   $SWPappdeal->swp_app_update_thumbnail_deals($post_ID,$post);
                }			
                $oldurl = apply_filters('post_title',trim($oldurl,'/'));
                if(!empty($oldurl)) {		
                    $post_id = url_to_postid($oldurl);	
                    if(strpos($oldurl, 'shop://') !== false){
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => $oldurl
                        );			
                    }						
                    elseif(!empty($post_id) && get_post_type($post_id) == 'product') {					
                        $newurl =  str_replace($oldurl, 'shop://product/'.$post_id, $oldurl);							
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => $newurl
                        );						
                    }
                    elseif(strpos($oldurl,'product-category') !== false){
                        $url_split = explode('#', $oldurl);
                        $oldurl = $url_split[0];

                                // Get rid of URL ?query=string
                        $url_split = explode('?', $oldurl);
                        $oldurl = $url_split[0];

                        $scheme = parse_url( home_url(), PHP_URL_SCHEME );
                        $oldurl = set_url_scheme( $oldurl, $scheme );

                        if ( false !== strpos(home_url(), '://www.') && false === strpos($oldurl, '://www.') )
                        $oldurl = str_replace('://', '://www.', $oldurl);

                        if ( false === strpos(home_url(), '://www.') )
                        $oldurl = str_replace('://www.', '://', $oldurl);

                        $oldurl = trim($oldurl, "/");
                        $slugs = explode('/', $oldurl);				
                        $category = $this->swp_app_get_product_category_by_slug('/'.end($slugs));
                        if(!empty($category))
                        {
                            $newurl =  str_replace($oldurl, 'shop://product-category/'.$category['term_id'], $oldurl);
                            $listimages[] = array(
                                'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                                'url' => $newurl
                            );	
                        }
                        else{
                            continue;
                        }
                    }
                    elseif($oldurl == $home.'/contact-us'){					
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => 'shop://contact-us'
                        );	
                    }elseif($oldurl == $home.'/about-us'){					
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => 'shop://about-us'
                        );	
                    }elseif($oldurl == $home.'/bookmark'){					
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => 'shop://bookmark'
                        );	
                    }elseif($oldurl == $home.'/term-and-conditions'){					
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => 'shop://term-and-conditions'
                        );	
                    }elseif($oldurl == $home.'/privacy-policy'){					
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => 'shop://privacy-policy'
                        );	
                    }else{
                        $listimages[] = array(
                            'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => $oldurl
                        );
                    }					
                }
                else{
                    $listimages[] = array(
                        'deals_image' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                        'url' => null
                    );	
                }
            }			
            return $listimages;
        }
        
        public function swp_app_get_product_category_by_slug($cat_slug){
            $category = get_term_by('slug', $cat_slug, 'product_cat', 'ARRAY_A');
            return $category;
        }

    }
    $SWPappendpointsdeals = new SWPappendpointsdeals();
}