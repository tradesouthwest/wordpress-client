(function( $ ) {
 
    "use strict"; 
     
    $(document).ready(function(){

        $(".tbselect-variable").change( function(){
//var a = $(".tbselect-variable").find("option:selected").val();
               var b = $('input[name="variation_id"]').val();
            
			//var mult = $("#attribute_pa_custom-tarp").val();
               $('#tbselect-price').html(b);          
               //$('#tarpbuilder-variation-price').attr('value', var_id);           
             
        })

/* tbselect-variable
if( jQuery( ".variations_form select" ).length  ){

	// get json value from woocomerce from
	var product_attr=jQuery.parseJSON( 
                    $(".variations_form").attr("data-product_variations") ),
    	obj_attr	= "";
    jQuery( ".variations_form select" ).on( "change", function () {        
       // Create New Array by selecting variations
        jQuery( ".variations_form select" ).each(function( index ) { 
             obj_attr[ $(this).attr("name") ] = $(this).val();
            
        });
        // Get Variations
        jQuery.each( product_attr, function( index, loop_value ) {
        
            if( JSON.stringify( obj_attr ) === JSON.stringify( loop_value.attributes)){
                $('#tarpbuilderVariationDisplay').html( loop_value.price_html );   
            } 
        }); 
    });
} */
  })
 
})(jQuery);
