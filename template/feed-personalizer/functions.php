<?php
// There are cases the the template is remained active but the template extension plugin is deactivated.
// In that case, the main plugin tries to load the `functions.php` file of the deactivated plugin's template.
// Which causes issues if the template PHP code tries to access the extension plugin specific global elements such as classes and functions.
if ( ! class_exists( 'ExternalsFeedPersonalizer_Registry' ) ) {
    return;
}

class ExternalsFeedPersonalizer_Resource extends Externals_WPUtility {

    public function __construct() {
// update_user_meta( get_current_user_id(), '_efp_removed_items', array() );
        add_action( 'wp_enqueue_scripts', array( $this, 'replyToEnqueueScripts' ) );
    }
    public function replyToEnqueueScripts() {

        // YouTube Video Embedder
        wp_enqueue_script(
            'externals-feed-personalizer-youtube-embedder',
            $this->getResolvedSRC( dirname( __FILE__ ) . '/js/youtube-embedder.js' ),
            array( 'jquery' )
        );

        // Personalization
        wp_enqueue_script(
            'externals-feed-personalizer',
            $this->getResolvedSRC( dirname( __FILE__ ) . '/js/feed-personalizer.js' ),
            array( 'jquery' )
        );
        $_aData = array(
            'userID'    => get_current_user_id(),
            'AJAXURL'   => admin_url( 'admin-ajax.php' ),
            'debugMode' => defined( 'WP_DEBUG' ) && WP_DEBUG,
        );
        wp_localize_script( 'externals-feed-personalizer', '_aEFP', $_aData );

        // Dashicons in front-end
        wp_enqueue_style( 'dashicons' );

    }

}

class ExternalsFeedPersonalizer_BlockItems {

    /**
     * Represents the structure of user remove items array.
     * @var array
     */
    private $___aRemoveItemsByUser = array(
        'ids' => array(),
    );

    public function __construct() {
        add_action( 'wp_ajax_' . 'removed_feed_item_by_user', array( $this, 'replyToRemoveFeedItemByUser' ) );
        add_filter(
            'externals_filter_feed_item',
            array( $this, 'replyToBlockItem' ),
            10,
            3
        );
    }

    public function replyToRemoveFeedItemByUser() {
        check_ajax_referer(
            'externals_feed_personalizer_nonce', // the nonce key passed to the `wp_create_nonce()`
            'externals_feed_personalizer_security' // the $_REQUEST key storing the nonce.
        );
        exit( json_encode( $this->___getAjaxResponse() ) );
    }
        /**
         * @return  array
         */
        private function ___getAjaxResponse() {

            $_aResult        = array(
                'type'    => 'success',
                'message' => '',
            );

            try {

                if ( get_current_user_id() !== ( integer ) $_REQUEST[ 'user_id' ] ) {
                    throw new Exception( __( 'The user ID does not match', 'externals-feed-personalizer' ) );
                }

                if ( ! isset( $_REQUEST[ 'item_id' ] ) ) {
                    throw new Exception( __( 'The item ID is not set.', 'externals-feed-personalizer' ) );
                }

                $_aResult[ 'message' ] = $this->___setItemIDForBlocking( $_REQUEST[ 'item_id' ], $_REQUEST[ 'user_id' ] );

            } catch( Exception $_oException ) {
                $_aResult[ 'type' ]    = 'error';
                $_aResult[ 'message' ] = $_oException->getMessage();
            }

            return $_aResult;

        }
            /**
             * @return  string  The message.
             * @throw   exception
             */
            private function ___setItemIDForBlocking( $sItemID, $iUserID ) {
                $_iUserID       = ( integer ) $iUserID;
                $_sItemID       = ( string ) $sItemID;

                if ( ! $_sItemID ) {
                    throw new Exception( __( 'The item to remove does not have an ID.', 'externals-feed-personalizer' ) );
                }
                $_aRemovedItems = $this->___getRemovedItemsByUser( $_iUserID );
                $_aItemIDs      = is_array( $_aRemovedItems[ 'ids' ] ) ? $_aRemovedItems[ 'ids' ] : array();
                if ( in_array( $_sItemID, $_aItemIDs ) ) {
                    throw new Exception( __( 'The remove item has been already set.', 'externals-feed-personalizer' ) );
                }
                array_unshift($_aItemIDs, $_sItemID ); // inset the item at the beginning of the array
                $_aItemIDs      = array_slice( $_aItemIDs, 0, 1000 ); // extract the first 1000 items;
                $_aRemovedItems[ 'ids' ] = $_aItemIDs;
                update_user_meta( $_iUserID, '_efp_removed_items', $_aRemovedItems );
                return __( 'Added the remove item.', 'externals-feed-personalizer' );
            }

    /**
     * @param $aItem
     * @param $oItem
     * @param $oArgument
     *
     * @return array
     * @callback    externals_filter_feed_item
     */
    public function replyToBlockItem( $aItem, $oItem, $oArgument ) {
        static $_aRemovedItems = array();   // should retrieve (access the database) only once in page load
        // possible that the item is already dropped.
        if ( empty( $aItem ) ) {
            return $aItem;
        }
        if ( empty( $_aRemovedItems ) ) {
            $_aRemovedItems = $this->___getRemovedItemsByUser( get_current_user_id() );
        }
        if ( ! isset( $_aRemovedItems[ 'ids' ] ) || ! is_array( $_aRemovedItems[ 'ids' ]  ) ) {
            return $aItem;
        }
        if ( in_array( $aItem[ 'id' ], $_aRemovedItems[ 'ids' ] ) ) {
            return array();    // drop it
        }
        return $aItem;
    }
        /**
         * @param $iUserID
         *
         * @return array
         */
        private function ___getRemovedItemsByUser( $iUserID ) {
            $_aRemovedItems = get_user_meta( $iUserID, '_efp_removed_items', true );
            return ! is_array( $_aRemovedItems ) || empty( $_aRemovedItems )
                ? $this->___aRemoveItemsByUser
                : $_aRemovedItems + $this->___aRemoveItemsByUser;
        }

}

new ExternalsFeedPersonalizer_BlockItems;
new ExternalsFeedPersonalizer_Resource;