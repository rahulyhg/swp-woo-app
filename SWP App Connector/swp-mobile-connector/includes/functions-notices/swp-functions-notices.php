<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/******************************************************************
 * Add a notice.
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 ******************************************************************/
function swp_app_add_notice( $message, $notice_type = 'success' ) {
    $notices = mc()->session->get( 'mobiconnector_notices', array() );
    $notices[ $notice_type ][] =  $message;
    mc()->session->set( 'mobiconnector_notices', $notices );
}

/********************************************************************
 * Returns all queued notices, optionally filtered by a notice type.
 * @param  string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 * @return array|mixed
 *******************************************************************/
function swp_app_get_notices( $notice_type = '' ) {	
    $all_notices = mc()->session->get( 'mobiconnector_notices', array() );

    if ( empty( $notice_type ) ) {
        $notices = $all_notices;
    } elseif ( isset( $all_notices[ $notice_type ] ) ) {
        $notices = $all_notices[ $notice_type ];
    } else {
        $notices = array();
    }

    return $notices;
}

/********************************************************************
 * Set all notices at once.
 * @param mixed $notices Array of notices.
 ******************************************************************/
function swp_app_set_notices( $notices ) {
    mc()->session->set( 'mobiconnector_notices', $notices );
}

/********************************************************************
 * Prints messages and errors which are stored in the session, then clears them.
 ********************************************************************/
function swp_app_print_notices() {

    $all_notices  = mc()->session->get( 'mobiconnector_notices', array() );   
   
    $notice_types = array('success', 'error', 'notice' , 'successonesignal', 'erroronesignal' );

    foreach ( $notice_types as $notice_type ) {
        if ( swp_app_notice_count( $notice_type ) > 0 ) {
            bamobile_mobiconnector_get_template( "notices/{$notice_type}.php", array(
                'messages' => array_filter( $all_notices[ $notice_type ] ),
            ) );
        }
    }

    swp_app_clear_notices();
}

/*****************************************************************
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 *******************************************************************/
function swp_app_notice_count( $notice_type = '' ) {
    $notice_count = 0;
    $all_notices  = mc()->session->get( 'mobiconnector_notices', array() );

    if ( isset( $all_notices[ $notice_type ] ) ) {

        $notice_count = count( $all_notices[ $notice_type ] );

    } elseif ( empty( $notice_type ) ) {

        foreach ( $all_notices as $notices ) {
            $notice_count += count( $notices );
        }
    }

    return $notice_count;
}

/************************************************************
 * Unset all notices.
 ************************************************************/
function swp_app_clear_notices() {    
    mc()->session->set('mobiconnector_notices', array() );    
}

?>