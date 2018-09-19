<?php
/*
 * Available variables:
 * 
 * $aOptions - the plugin options
 * $aItems - the fetched product links
 * $aArguments - the user defined arguments such as image size and count etc.
 */

$_sNonce = getFeedPersonalizerTemplateNonce();
?>

<?php if ( empty( $aItems ) ) : ?>
    <div><p><?php _e( 'No external item found.', 'externals' ); ?></p></div>  
    <?php return true; ?>
<?php endif; ?>    
        
<div class="externals-feed-personalizer-container">
    <input type="hidden" class="nonce" data-nonce="<?php echo $_sNonce;?>" />
    <?php foreach( $aItems as $_aItem ) : ?>
    <div class="externals-feed-personalizer-item" data-id="<?php echo esc_attr( $_aItem[ 'id' ] ); ?>">
        <div class="externals-feed-personalizer-head">
            <h2 class="externals-feed-personalizer-title"><a href="<?php echo esc_url( $_aItem[ 'permalink' ] ); ?>" target="_blank" rel="nofollow"><?php echo $_aItem[ 'title' ]; ?></a></h2>
            <div class="externals-feed-personalizer-actions text-align-right">
                <span class="dashicons dashicons-dismiss dismiss"></span>
            </div>
        </div>
        <div class="externals-feed-personalizer-side">
            <div class="externals-feed-personalizer-actions">
                <span class="dashicons dashicons-thumbs-up thumbs-up"></span>
            </div>
            <div class="externals-feed-personalizer-images">
                <?php
                foreach( $_aItem[ 'images' ] as $_iIndex => $_sIMGURL ) {
                    echo "<div class='externals-feed-personalizer-image'>"
                            . "<img src='" . esc_url( $_sIMGURL ) .  "' alt='" . esc_attr( basename( $_sIMGURL ) ) . "'/>"
                        . "</div>";
                } ?>
            </div>
        </div>
        <div class="externals-feed-personalizer-body">
            <div class="externals-feed-personalizer-meta">            
                <span class="externals-feed-personalizer-date"><?php echo human_time_diff( strtotime( $_aItem[ 'date' ] ), current_time( 'timestamp' ) ) . " " . __( 'ago' ); ?></span>
                <span class="externals-feed-personalizer-author"><?php echo $_aItem[ 'author' ]; ?></span>
                <span class="externals-feed-personalizer-source"><a href="<?php echo esc_attr( esc_url( $_aItem[ 'source' ] ) ); ?>" target="_blank"><span class="dashicons dashicons-admin-links"></span></a></span>
                <span class="externals-feed-personalizer-id"><span class="dashicons dashicons-info"></span></span>
                <span class="externals-feed-personalizer-feed"><a href="<?php echo esc_attr( $_aItem[ 'source_feed' ] ); ?>" target="_blank"><span class="dashicons dashicons-rss"></span></a></span>

            </div>
            <div class="externals-feed-personalizer-actions text-align-right">
                <span class="dashicons dashicons-menu menu"></span>
            </div>
            <div class='externals-feed-personalizer-description'><?php echo "" . strip_tags( $_aItem[ 'description' ] ); ?></div>
        </div>
    </div>
<?php endforeach; ?>    
</div>

<?php
/**
 * Ensures only one time to create a nonce for this particular template.
 * @return bool|string
 */
function getFeedPersonalizerTemplateNonce() {
    static $_sNonce;
    if ( $_sNonce ) {
        return $_sNonce;
    }
    $_sNonce = wp_create_nonce('externals_feed_personalizer_nonce' );
    return $_sNonce;
}