
(function( $ ) {
 
    "use strict"; 
     
    $(document).ready(function(){
    
        $("input.edd_license_prices").click( function(){
            var amnt = $("input.edd_license_prices:checked").val();
            $("input#edd_license_price").attr("value", amnt);
            $("input#edd_license_price").attr("data-price", amnt);
        })
            //localStorage.setItem("edd_license_upgraded", amnt ); 
        $("input[name='edd_license_prices']").click( function(){
            var txt = $("input[name='edd_license_prices']:checked").attr("data-default-text");
            $("#defaultText").html(txt);
            $(".lp-position").show();
        })
        /* localStorage.removeItem("edd_cp_price");
        localStorage.removeItem("edd_license_upgraded");
        sessionStorage.removeItem("edd_price");
        sessionStorage.removeItem("edd_license_upgraded"); */
    })
        
})(jQuery);