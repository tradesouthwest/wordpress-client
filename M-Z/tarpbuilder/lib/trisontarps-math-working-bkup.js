(function( $ ) {
 
    "use strict";
     
    $(document).ready(function(){
        
        $("#feetWidth").on("change", function(){
        var a = parseInt($('#feetWidth').val());
        var b = parseInt($('#inchWidth').val());
           var sum = ( 1 * a ) + ( b * .0833336 );
           $("#width_needed").val(sum.toFixed(2));
        })
        $("#inchWidth").on("change", function(){
        var a = parseInt($('#feetWidth').val());
        var b = parseInt($('#inchWidth').val());
           var sum = ( 1 * a ) + ( b * .0833336 );
           $("#width_needed").val(sum.toFixed(2));
        })

        $("#feetLength").on("change", function(){
        var a = parseInt($('#feetLength').val());
        var b = parseInt($('#inchLength').val());
           var sum = ( 1 * a ) + ( b * .0833336 );
           $("#length_needed").val(sum.toFixed(2));
        })
        $("#inchLength").on("change", function(){
        var a = parseInt($('#feetLength').val());
        var b = parseInt($('#inchLength').val());
           var sum = ( 1 * a ) + ( b * .0833336 );
           $("#length_needed").val(sum.toFixed(2));
        })
        $(".variable_price_calculator input").on("change", function(){
        var a = parseInt($('#feetWidth').val());
        var b = parseInt($('#inchWidth').val());
        var c = parseInt($('#feetLength').val());
        var d = parseInt($('#inchLength').val());
        var sumt = (( 1 * a ) + ( b * .0833336 )) * (( 1 * c ) + ( d * .0833336 ));
           $(".tarpbuilder-numberof").val(sumt.toFixed(2));
        })
    })
 
})(jQuery);
