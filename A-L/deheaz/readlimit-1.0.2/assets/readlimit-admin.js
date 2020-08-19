
(function($){
'use strict';
$( "input.regular-text" ).blur(function() {
  $( this ).next( "span.inline-mezzage" ).css( "display", "inline" ).fadeOut( 1000 );
});
})(jQuery); 
