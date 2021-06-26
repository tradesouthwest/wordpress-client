jQuery(document).ready(function ($) {
	"use strict";
    $( "#pa_thickness" ).change( function() {
        //event.preventDefault();
            
        var sac_thickness = $('#pa_thickness').val();
        //$('#sacAus').html(sac_thickness);
          
        var data = { 
            'security' : sac_ajax_object.security,
            'action'   : sac_ajax_object.action,
            'sac_thickness' : sac_thickness
        };

        $.ajax({
        url: sac_ajax_object.ajaxurl,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
        
            console.log( "sac_thickness= " + sac_thickness );
            $('#sacAus').html(sac_thickness);
            console.log(data);
        
        }, 
        });
    });
});
