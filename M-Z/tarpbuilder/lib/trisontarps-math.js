(function( $ ) {
 
    "use strict"; 
     
    $(document).ready(function(){
    
         $(".feetWidth").on("change", function(){
         var a = parseInt($('.feetWidth').val());
         var b = parseInt($('.inchWidth').val());
         var sum = ( 1 * a ) + ( b * .0833336 );
         $("#attribute_pa_custom-tarp-width").attr('value', sum.toFixed(2));
         })
         
         $(".inchWidth").on("change", function(){
         var a = parseInt($('.feetWidth').val());
         var b = parseInt($('.inchWidth').val());
         var sum = ( 1 * a ) + ( b * .0833336 );
         $("#attribute_pa_custom-tarp-width").attr('value', sum.toFixed(2));
         })
        
         $(".feetLength").on("change", function(){
         var a = parseInt($('.feetLength').val());
         var b = parseInt($('.inchLength').val());
         var sum = ( 1 * a ) + ( b * .0833336 );
         $("#attribute_pa_custom-tarp-length").attr('value', sum.toFixed(2));
         })
       
         $(".inchLength").on("change", function(){
         var a = parseInt($('.feetLength').val());
         var b = parseInt($('.inchLength').val());
         var sum = ( 1 * a ) + ( b * .0833336 );
         $("#attribute_pa_custom-tarp-length").attr('value', sum.toFixed(2));
         })

         // calculate total sq ft in input field
         $(".variable_price_calculator input").on("change", function(){
         var a = parseInt($('.feetWidth').val());
         var b = parseInt($('.inchWidth').val());
         var c = parseInt($('.feetLength').val());
         var d = parseInt($('.inchLength').val());
         var sumt = (( 1 * a ) + ( b * .0833336 )) * (( 1 * c ) + ( d * .0833336 ));
         $("#attribute_pa_custom-tarp").attr('value', sumt.toFixed(2));
         })
         // grommets_top

         
    })
 
})(jQuery);
