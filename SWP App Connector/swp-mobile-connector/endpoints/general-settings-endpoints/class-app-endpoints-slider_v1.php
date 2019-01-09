<?php
if( !defined( 'ABSPATH' )){
    /****** EXITE if file directly access ******/
    exit;
} 
/******************************************************
* @param create slider endpoints
* return array
*
*******************************************************/

if( !class_exists('swp_endpoint_slider') ){
    class swp_endpoint_slider{
        
        private $endpoint_url = 'swp/v1/product';

        /***** create construct function *****/
        public function __construct(){
             add_action( 'rest_api_init', array( $this, 'swp_app_register_slider_api'));
            require_once(ABSPATH.'/wp-content/plugins/swp-mobile-connector/settings/slider/class-app-connector-slider.php');
         }
        
        /***** slider Add slider - endpoint *****/    
        
        public function swp_app_register_slider_api() {
            register_rest_route( $this->endpoint_url, '/slider', array(
                        'methods'         => 'GET',
                        'callback'        => array( $this, 'swp_app_get_slider' ),
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
        
        public function swp_app_get_slider($request){	
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
                'post_type'        => 'slider_slide',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'	       => '',
                'author_name'	   => '',
                'post_status'      => 'publish',
                'suppress_filters' => true 
            );
            $sliders = get_posts($args);
            $wp_upload_dir = wp_upload_dir();	
            $listimages = array();
            $home = get_home_url();	
            foreach($sliders as $slider){
                $imageid = get_post_thumbnail_id($slider->ID);
                
                $oldurls = get_post_meta($slider->ID, 'app_slider_url', true);	
                if (strrpos($oldurls, 'https') !== false) {
                        $oldurl = trim($oldurls,'https:');
                    }
                    else{
                        $oldurl = trim($oldurls,'http:');
                }
                
                $slider_large = get_post_meta($imageid, 'swp_app_slider_large', true);
                if(empty($slider_large))
                {
                    $post_ID = $slider->ID;
                    $post = get_post($post_ID);
                    $SWPappslider = new SWPappslider();
                   $SWPappslider->swp_app_update_thumnail_slider($post_ID,$post);
                }			
                $oldurl = apply_filters('post_title',trim($oldurl,'/'));
                if(!empty($oldurl)) {		
                    $post_id = url_to_postid($oldurl);
                    $get_url = get_site_url();
                    
                    if (strpos($get_url, 'https') !== false) {
                        $site_url = trim($get_url,'https:');
                    }
                    else{
                        $site_url = trim($get_url,'http:');
                    }
                    
                    if(strpos($oldurl, 'http') !== false){
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => $oldurl
                        );			
                    }						
                    elseif(!empty($post_id) && get_post_type($post_id) == 'product') {					
                        $newurl =  str_replace($oldurl, $site_url.'/product/'.$post_id, $oldurl);							
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
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
                            $newurl =  str_replace($oldurl, 'link://product-category/'.$category['term_id'], $oldurl);
                            $listimages[] = array(
                                'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                                'caption' => apply_filters('post_title',$slider->post_excerpt),
                                'url' => $newurl
                            );	
                        }
                        else{
                            continue;
                        }
                    }
                    elseif($oldurl == $home.'/contact-us'){					
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => 'link://contact-us'
                        );	
                    }elseif($oldurl == $home.'/about-us'){					
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => 'link://about-us'
                        );	
                    }elseif($oldurl == $home.'/bookmark'){					
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => 'link://bookmark'
                        );	
                    }elseif($oldurl == $home.'/term-and-conditions'){					
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => 'link://term-and-conditions'
                        );	
                    }elseif($oldurl == $home.'/privacy-policy'){					
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => 'link://privacy-policy'
                        );	
                    }else{
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                            'caption' => apply_filters('post_title',$slider->post_excerpt),
                            'url' => $oldurl
                        );
                    }					
                }
                else{
                    $listimages[] = array(
                        'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_slider_large', true),
                        'caption' => apply_filters('post_title',$slider->post_excerpt),
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
    $swp_endpoint_slider = new swp_endpoint_slider();
}