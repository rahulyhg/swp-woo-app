<?php
class SWPappsettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        $this->init_hooks();
    }

    /**
	 * Hook into actions and filters.
	 */
    public function init_hooks(){
        //require_once('class-app-connector-backend-about-us.php');
        require_once('general-settings/general-settings.php');
        require_once( 'class-app-connector-backend-slider-settings.php' );
        require_once( 'class-app-connector-backend-popup-settings.php' );
        //require_once( 'class-app-connector-backend-footer.php' );
    }
    }

if( is_admin() )
    $SWPappsettings = new SWPappsettings();