/* script for topprice 
 * version: 1.0.0 
 */
jQuery(function ($) {
    "use strict";
    $(document).ready(function(){ 
    
        $(window).blur( function(){    
        var upprice = $(".bundle_price").text();
            $(".bndl_prc").html(upprice);
                console.log( upprice + "page loaded" );
        })

        $(".bundled_qty").change(function(){
                    var uppprice = $(".bundle_price").text();
                    $("#bpTop").html(uppprice);
                    $(".bndl_prc").css("display", "none");
                        console.log( uppprice );   
        })
    });    
});
