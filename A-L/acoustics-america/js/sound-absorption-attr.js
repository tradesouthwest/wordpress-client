jQuery(document).ready(function ($) {
	"use strict";

    var pa_thickness = $("select#pa_thickness").val();
    $('#sac_nrc_units').val(pa_thickness);
    console.log( "pa_thickness= " + pa_thickness );

    $("select#pa_thickness").change(function(){
    var sp_thickness = $("select#pa_thickness").children("option:selected").val();
        $('#sac_nrc_units').val(sp_thickness);
        console.log( "sp_thickness= " + sp_thickness );
    });

    $( "#sac_check" ).click( function() {
        //event.preventDefault();
            
        var sac_thickness = $('#pa_thickness').val();
        var qty = $('input[name="quantity"]').val();
        var sac_nrc_units = sac_thickness * qty * 0.85;
        
            console.log( "sac_thickness= " + sac_thickness );
            $('#sac_nrc_units').val(sac_nrc_units);

    });
});
