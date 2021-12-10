/* ajax calls for fdoe adjustments */
(function( $ ) {
    "use strict";
$(document).ready( function(){
(function($){
        
    $('.qty').focus(function(){
        $(this).addClass("enabled");
        var id = $(this).attr('id');
        var vl = $(this).attr('value');
        console.log(id + vl);
            
    });

});
}); 
})(jQuery);
