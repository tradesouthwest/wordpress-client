/*
width / 2  = top_bottom
length / 2 = left_right
grommets_top    = top_bottom / _approx_spacing 
grommets_bottom = top_bottom / _approx_spacing 
grommets_left   = left_right / _approx_spacing
grommets_right  = left_right / _approx_spacing
 */
(function( $ ) {
 
    "use strict";
     
    $(document).ready(function(){
        $("#pa_approx-spacing").on("change", function(){
        var a = parseInt($("#pa_approx-spacing").val());
        var b = parseInt($("#attribute_pa_custom-tarp-width").val());
        var sumt = ( 12 * b ) / ( 1 * a );
        var sumb = ( 12 * b ) / ( 1 * a );
        $("#grommets_top").val(sumt.toFixed(2));
        $("#grommets_bottom").val(sumb.toFixed(2));
        })

        $("#pa_approx-spacing").on("change", function(){
        var a = parseInt($("#pa_approx-spacing").val());
        var b = parseInt($("#attribute_pa_custom-tarp-length").val());
        var sumt = ( 12 * b ) / ( 1 * a );
        var sumb = ( 12 * b ) / ( 1 * a );
        $("#grommets_left").val(sumt.toFixed(2));
        $("#grommets_right").val(sumb.toFixed(2));
        })

        // tarp_ display under images
        $("#pa_approx-spacing").on("change", function(){
        var a = parseInt($("#pa_approx-spacing").val());
        var b = parseInt($("#attribute_pa_custom-tarp-width").val());
        var sumt = ( 12 * b ) / ( 1 * a );
        var sumb = ( 12 * b ) / ( 1 * a );
        $("#tarp_grommets_top").val(sumt.toFixed(2));
        $("#tarp_grommets_bottom").val(sumb.toFixed(2));
        })
        
        $("#pa_approx-spacing").on("change", function(){
        var a = parseInt($("#pa_approx-spacing").val());
        var b = parseInt($("#attribute_pa_custom-tarp-length").val());
        var sumt = ( 12 * b ) / ( 1 * a );
        var sumb = ( 12 * b ) / ( 1 * a );
        $("#tarp_grommets_left").val(sumt.toFixed(2));
        $("#tarp_grommets_right").val(sumb.toFixed(2));
        })

        // tarp_gromtext
        /*
        $("#pa_approx-spacing").on("change", function(){
        var a = parseInt($("#pa_approx-spacing").val());
        var b = parseInt($("#width_needed").val());
        var sumt = ( 12 * b ) / ( 1 * a );
        var sumb = ( 12 * b ) / ( 1 * a );
        $("#tarp_gromtext_top").val(sumt.toFixed(2));
        $("#tarp_gromtext_bottom").val(sumb.toFixed(2));
        })
       
        $(".approx_spacing").on("change", function(){
        var a = $(".approx_spacing option:selected").val();
        var b = 'yes';
        if(a > 1){
        $("#hemmed option:selected").removeAttr("selected");
        $(`#hemmed option[value='${b}']`).attr('selected', 'selected');  
        }
        })
             */    
        // tarpsim background
        $("#color-of-tarp").on("change",function(){
        var preVal; //= $("#tarpsim-table").attr("class");
        var a = $("#color-of-tarp").find("option:selected").val();
        $("#tarpsim-table-grommets").removeClass(`bkgrnd-${preVal}`).addClass(`bkgrnd-${a}`);
        })
    })
 
})(jQuery);
