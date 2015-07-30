<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAC_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH_WC_RAC_Metaboxes
 *
 * @class   YITH_WC_RAC_Metaboxes
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_RAC_Metaboxes' ) ) {

    class YITH_WC_RAC_Metaboxes {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_RAC_Metaboxes
         */

        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_RAC_Metaboxes
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {
            //display info cart
            add_action( 'add_meta_boxes',array($this,  'show_info_cart' ));
            //display cart
            add_action( 'add_meta_boxes',array($this,  'show_cart' ));
            //display cart action metabox
            add_action( 'add_meta_boxes',array($this,  'show_cart_action' ));
            //Remove metabox Publish/Update
            add_action( 'admin_menu', array( $this, 'remove_metabox' ) );
        }

        /**
         * Add the metabox to show the info of the current cart
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_info_cart() {
            add_meta_box( 'ywrac-info-cart', __( 'Cart Info', 'ywrac' ), array( $this, 'show_cart_info_metabox' ), 'ywrac_cart', 'normal', 'default' );
        }

        /**
         * Metabox to show the info of the current cart
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_cart_info_metabox( $post ){

            $user_id      = get_post_meta( $post->ID, '_user_id', true );
            $user_details = get_userdata( $user_id );

            $args         = array(
                'cart_id'     => $post->ID,
                'status'      => get_post_meta( $post->ID, '_cart_status', true ),
                'last_update' => $post->post_modified_gmt,
                'user'        => $user_details,
            );

            wc_get_template( 'admin/metabox_cart_info_content.php', $args );

        }

        /**
         * Add the metabox to show the content of current cart
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_cart() {
            add_meta_box( 'ywrac-cart', __( 'Cart Content', 'ywrac' ), array( $this, 'show_cart_metabox' ), 'ywrac_cart', 'normal', 'default' );
        }

        /**
         * Metabox to show the content of current cart
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_cart_metabox( $post ){
            $cart_content = maybe_unserialize( get_post_meta( $post->ID, '_cart_content', true));
            if( $cart_content != ''){
                wc_get_template( 'admin/metabox_cart_content.php', array( 'cart_content' => $cart_content ));
            }
        }

        /**
         * Add the metabox to show the cart action
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_cart_action() {
            add_meta_box( 'ywrac-cart-action', __( 'Cart Action', 'ywrac' ), array( $this, 'show_cart_action_metabox' ), 'ywrac_cart', 'side', 'default' );
        }


        /**
         * Metabox to show the cart action
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function show_cart_action_metabox( $post ) {
            $email_sent   = get_post_meta( $post->ID, '_email_sent', true );
            wc_get_template( 'admin/metabox_cart_action.php', array( 'cart_id' => $post->ID,  'email_sent'  => ( $email_sent == 'no' || $email_sent == '' ) ? __( 'Not sent', 'ywrac' ) : $email_sent ) );
        }

        /**
         * Remove the metabox update/publish
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        function remove_metabox(){
            remove_meta_box( 'submitdiv', YITH_WC_Recover_Abandoned_Cart()->post_type_name, 'side' );
        }


    }
}

/**
 * Unique access to instance of YITH_WC_RAC_Metaboxes class
 *
 * @return \YITH_WC_RAC_Metaboxes
 */
function YITH_WC_RAC_Metaboxes() {
    return YITH_WC_RAC_Metaboxes::get_instance();
}