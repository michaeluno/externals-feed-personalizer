(function($){
    $( document ).ready( function() {
        $( ".externals-feed-personalizer-actions .dismiss" ).click( function() {
            var _sNonce  = $( this ).closest( '.externals-feed-personalizer-container' ).children( '.nonce' ).first().attr( 'data-nonce' );
            var _sItemID = $( this ).closest( '.externals-feed-personalizer-item' ).attr( 'data-id' );
            $( this ).closest( '.externals-feed-personalizer-item' ).fadeOut( "slow", function() {
                $( this ).remove();
                if ( ! _aEFP.userID ) {
                    return true;
                }
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : _aEFP.AJAXURL,
                    // Data set to $_POSt and $_REQUEST
                    data : {
                        action: 'removed_feed_item_by_user',    // WordPress action hook
                        externals_feed_personalizer_security: _sNonce,
                        user_id: _aEFP.userID,
                        item_id: _sItemID // set it
                    },
                    success: function(response) {
                        if( 'success' == response.type ) {
                            if ( _aEFP.debugMode ) {
                                console.log('Externals Feed Personalizer (Ajax):' + response.message);
                            }
                        }
                        else {
                            if ( _aEFP.debugMode ) {
                                console.error('Externals Feed Personalizer (Ajax): ' + response.message);
                            }
                        }
                   }

                });
            });;
        });

        // Clicking on other action elements
        $( ".externals-feed-personalizer-actions .menu, .externals-feed-personalizer-actions .thumbs-up" ).click(function() {
            alert( "Not implemented yet. Comming soon..." );
        });

    });
}(jQuery));