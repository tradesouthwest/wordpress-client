jQuery(function ($) {
		"use strict";
    
    $(document).ready(function(){

        $(".bundled_qty").change(function(e){
            e.preventDefault();

        var upprice = $(".bundle_price").text();
        
        $("#bpTop").html(upprice);
        $(".bndl_prc").css("display", "none");
        console.log( upprice );   
        })
    })
});