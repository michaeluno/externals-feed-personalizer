(function($){
    $( document ).ready( function() {

        var _getYouTubeVideoId = function( url ) {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = url.match(regExp);

            if (match && match[2].length == 11) {
                return match[2];
            } else {
                return 'error';
            }
        };

        $( '.externals-feed-personalizer-title a' ).each( function (index, value) {

            var _sYouTubeVideoID = _getYouTubeVideoId( $( this ).attr( 'href' ) );
            if ( 'error' === _sYouTubeVideoID ) {
                return true;    // continue
            }
            // console.log( 'youtube id: ' + _sYouTubeVideoID );
            var _sIframeMarkup = '<iframe width="560" height="315" src="//www.youtube.com/embed/'
                 + _sYouTubeVideoID + '" frameborder="0" allowfullscreen></iframe>';

            $( this ).closest( '.externals-feed-personalizer-item' )
                .children( '.externals-feed-personalizer-description' )
                .first()
                .append( _sIframeMarkup );

        });





    });
}(jQuery));