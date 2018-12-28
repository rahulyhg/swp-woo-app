<?php

if ( ! defined( 'ABSPATH' ) ) {
	/***** Exit if accessed directly.*****/
    exit; 
}


 /***** Create Api involve with users *****/
if ( ! class_exists( 'SWPuser' )){
    class SWPuser{
        
        /***** Fields fix wordpress *****/
        private $wordpress_fields = array('first_name','last_name','email','user_login','nicename','display_name','url','description','password');

        /***** Fields fix WooCommerce *****/
        private $woocommerce_fields = array('first_name','last_name','company','country','address_1','address_2','city','state','postcode','phone','email');
        
        /***** url for endpoints *****/
        private $endpoint_url = 'swp/v1/user';
        
        /***** MobiConnectorUser construct *****/
        public function __construct() {		
            $this->swp_register_user_routes();
        }

        /***** Hook into actions and filters. *****/
        public function swp_register_user_routes() {
            add_action( 'rest_api_init', array( $this, 'swp_register_user_api_hooks'));
        }
        
        /***** Create Api or add field *****/
        public function swp_register_user_api_hooks() {
            
            /***** forgot password  *****/
            register_rest_route( $this->endpoint_url, '/forgot-password', 
                array(
                    array(
                        'methods'         => 'POST',
                        'callback'        => array( $this, 'swp_forgot_user_password' ),
                        //'permission_callback' => array( $this, 'swp_get_items_permissions_check' ),
                        'args' => array(
                            'username' => array(
                                'required' => true,
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'player_id'	=> array(
                                'sanitize_callback' => 'esc_sql'
                            )
                        ),
                    )
                ) 
            );
           
            /***** New User register  *****/
            register_rest_route( $this->endpoint_url, '/registration', array(
                    array(
                        'methods'         => 'POST',
                        'callback'        => array( $this, 'swp_new_user_registration' ),
                        //'permission_callback' => array( $this, 'swp_get_items_permissions_check' ),
                        'args' => array(
                            'username' => array(
                                'required' => true,
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'password' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'random_password' => array(
                                'sanitize_callback' => 'absint'
                            ),
                            'email' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'user_nicename' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'display_name' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'nickname' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'first_name' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'last_name' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'billing_phone' => array(
                                'sanitize_callback' => 'esc_sql'
                            ),
                            'player_id'	=> array(
                                'sanitize_callback' => 'esc_sql'
                            )
                        ),
                    )
                ) 
            );
        }
        
        /**************************************************
         * Check if a given request has access to read items.
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         *****************************************************/
        public function swp_get_items_permissions_check( $request ) {
            $usekey = get_option('mobiconnector_settings-use-security-key');
            //if ($usekey == 1 && ! bamobile_mobiconnector_rest_check_post_permissions( $request ) ) {
                return new WP_Error( 'mobiconnector_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'mobiconnector' ), array( 'status' => rest_authorization_required_code() ) );
            //}

            return true;
        }
         
        /*************************************************
         * Forgot password with username
         * @param WP_REST_Request $request  current Request
         *************************************************/
        public function swp_forgot_user_password( $request ) {
            global $wpdb;
            $parameters = $request->get_params();
                        
            /***** check username or email exist *****/
            $user_id = username_exists( $parameters['username'] );

            if($user_id == false) {
                return new WP_Error( 'user_not_exists', 'User does not exist', array( 'status' => 404 ) );
            }
            global $wpdb, $wp_hasher;
            $user = get_userdata( $user_id );
            /***** Generate something random for a password reset key. *****/
            $key = wp_generate_password( 20, false );

            /***** This action is documented in wp-login.php *****/
            do_action( 'retrieve_password_key', $user->user_login, $key );

            /***** Now insert the key, hashed, into the DB. *****/
            if ( empty( $wp_hasher ) ) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new PasswordHash( 8, true );
            }
            $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
            $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
            $message = __("Someone requested that the password be reset for the following account:") . "\r\n\r\n";
            $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
            $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
            $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
            $message .= '< ' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . " >\r\n\r\n";
            @mail($user->user_email, __('Password Reset'), $message);
            return __("To reset your password, visit your register email address");
        }
        
        /*************************************************
        * register new user through mobile 
        * @param WP_REST_Request $request  current Request
        **************************************************/
        public function swp_new_user_registration( $request ){
            
            global $wpdb;
		    $parameters = $request->get_params();
		
            /**** if random_password exist. unset password *****/
            if(isset($parameters['random_password'])) {
                $random_password = wp_generate_password( 8, false );
                $parameters['password'] = $random_password;
            }		
            
            /***** check username or email exist *****/
            $user_id = username_exists( $parameters['username'] );

            if($user_id !== false) {
                return new WP_Error( 'username_exists', 'Username already exists', array( 'status' => 401 ) );
            }
            
            /***** check email isset and exist *****/
            if(isset($parameters['email']) && email_exists($parameters['email']) != false) {
                return new WP_Error( 'email_exists', 'Email already exists', array( 'status' => 401 ) );
            }
            
            /***** check isset password *****/
            if(!isset($parameters['password'])) {
                return new WP_Error( 'password_empty', 'Password required', array( 'status' => 401 ) );
            }
            $user_id = wp_create_user( $parameters['username'], $parameters['password'], @$parameters['email'] );
            
            /***** update firstname & lastname *****/
            $new_user = array(
                    'ID' 			=> $user_id,
                    'user_nicename' => @$parameters['user_nicename'],
                    'display_name' 	=> @$parameters['display_name'],
                    'nickname' 		=> @$parameters['nickname'],
                    'first_name' 	=> @$parameters['first_name'],
                    'last_name' 	=> @$parameters['last_name'],
            );
            if(empty($parameters['display_name']) && (!empty($parameters['first_name']) || !empty($parameters['last_name']))){
                $new_user['display_name'] = trim(@$parameters['first_name'].' '.@$parameters['last_name']);
            }
            if(empty($parameters['user_nicename']) && (!empty($parameters['first_name']) || !empty($parameters['last_name']))){
                $new_user['user_nicename'] = trim(@$parameters['first_name'].' '.@$parameters['last_name']);
            }
            if(empty($parameters['nickname']) && (!empty($parameters['first_name']) || !empty($parameters['last_name']))){
                $new_user['nickname'] = trim(@$parameters['first_name'].' '.@$parameters['last_name']);
            }
            $user_id = wp_update_user( $new_user );
//            if(isset($parameters['billing_phone']) && !empty($parameters['billing_phone'])){
//                if(is_plugin_active('woocommerce/woocommerce.php')){
//                    $billing['billing_phone'] = $parameters['billing_phone'];
//                    foreach($billing as $key => $value) {
//                        update_user_meta( $user_id, $key, $value );
//                    }
//                }	
//            }
            
            /***** email to admin and user *****/
            $this->swp_new_user_registration_notification($user_id, null, 'both');
            //return $user_id;
            return "Thank you for register your details";
        }
        
        /**********************************************************************************
         * Email login credentials to a newly-registered user.
         * A new user registration notification is also sent to admin email.
         * @global wpdb         $wpdb      WordPress database object for queries.
         * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
         * @param int    $user_id    User ID.
         * @param null   $deprecated Not used (argument deprecated).
         * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
         * string (admin only), 'user', or 'both' (admin and user). Default empty.
        *****************************************************************************************/
        public function swp_new_user_registration_notification( $user_id, $deprecated = null, $notify = '' ) {
            if ( $deprecated !== null ) {
                _deprecated_argument( __FUNCTION__, '4.3.1' );
            }

            global $wpdb, $wp_hasher;
            $user = get_userdata( $user_id );
            
            /****************************************************************************
            * The blogname option is escaped with esc_html on the way into the database in sanitize_option
            * we want to reverse this for the plain text arena of emails.
            *****************************************************************************/
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            
            /***** email send to admin *****/
            if ( 'user' !== $notify ) {
                $message = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
                $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
                $message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

                @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
            }

            /***** `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notifcation. *****/
            if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
                return;
            }
            $message = sprintf(__('Sitename: %s'), $blogname) . "\r\n\r\n";
            $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
            
            /***** email send to customer *****/
            @mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
         
            return true;
        }

        
    /***** end class swpuser *****/    
    }
    
    $SWPuser = new SWPuser();
}
