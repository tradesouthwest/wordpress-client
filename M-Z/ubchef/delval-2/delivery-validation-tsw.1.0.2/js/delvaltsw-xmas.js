/** delevery validation jq version: 1.0.21 */
(function( $ ) {
    "use strict";
 		$(document).ready( function(){
// open

    $(".reset_variations").click(function () {
            $(".reset_variations").html("Thanks");
    });


    $(".variations_form select").change(function (e) {
        e.preventDefault();
        
        var optsel = $(this).children("option:selected").val();
        var seltwo = $(".variations_form select option:selected[value!='']").length;
        
        var starter = $("#starter-two-courses option:selected").val();
        var mainc   = $("#main-course-two-courses option:selected").val();
        var dessert = $("#dessert-two-courses option:selected").val();

        if ( seltwo > 2 ) {

//alert( "Only Two courses per order. Please click the Clear button below and select again. Thanks." );
$(".reset_variations").html("<br>Only Two courses per order. Please her to Clear and Select again. Thanks.");
    seltwo = undefined;
           //return false; 

    
        }
        else {
            console.log(seltwo + " OK27 " + optsel);
            return true;
            
        }
        return false;
    });	

        
// close
        
    	}); 
})(jQuery);
