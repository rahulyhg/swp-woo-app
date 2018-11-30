<?php
class SWPsettingaboutus
{   
    function swp_general_settings_about_us_register_field(){
           register_setting(
            'swp_app_about_us_option',
            'swp_app_about_us_option'
        );
        
        /* Header Options Section */
        add_settings_section( 
            'swp_app_about_us_header',
            'About Us',
           array( $this,'swp_app_about_us_header_callback' ),
            'swp_app_about_us_option'
        );

        add_settings_field(  
            'swp_app_about_us_title',                      
            'About Us (Title)',               
            array( $this, 'swp_app_about_us_title_callback' ),   
            'swp_app_about_us_option',                     
            'swp_app_about_us_header',
            array(
                'swp_app_about_us_title' 
            ) 
        );
        add_settings_field(  
            'swp_app_about_us_description',                      
            'About Us (Description)',               
            array( $this, 'swp_app_about_us_description_callback' ),   
            'swp_app_about_us_option',                     
            'swp_app_about_us_header',
            array(
                'swp_app_about_us_description' 
            ) 
        );
    


    }
    
      /* Call Backs
    -----------------------------------------------------------------*/
       
    
    public function swp_app_about_us_header_callback() { 
        echo '<p>Enter Your About Us Details :</p>'; 
    }
    public function swp_app_about_us_title_callback($args) { 

        $options = get_option('swp_app_about_us_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_about_us_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }
        public function swp_app_about_us_description_callback($args) { 

        $options = get_option('swp_app_about_us_option'); 

        echo '<input type="text" id="' . $args[0] . '" name="swp_app_about_us_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }

    public function ch_essentials_featured_post_callback($options) { 

        $options = get_option('swp_app_general_options'); 

        query_posts( $args );


        echo '<select id="featured_post" name="swp_app_general_options[featured_post]">';
        while ( have_posts() ) : the_post();

            $selected = selected($options[featured_post], get_the_id(), false);
            printf('<option value="%s" %s>%s</option>', get_the_id(), $selected, get_the_title());

        endwhile;
        echo '</select>';


    }
    
    public function swp_about_us_setting_page(){
        settings_fields( 'swp_app_about_us_option' );
        do_settings_sections( 'swp_app_about_us_option' ); 

    }

}
    
if( is_admin() )
    $SWPsettingaboutus = new SWPsettingaboutus();