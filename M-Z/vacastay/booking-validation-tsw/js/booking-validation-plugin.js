/* booking-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
(function($){
	$(document).ready(function(){
    	$('#tsw-agreeto').click(function(){
    	    
    	    var chk = $(this);
    	    chk.attr("checked", !chk.attr("checked"));
    	    
    	    var totalcost = $('.total-costs').data("price");
            var totalfees = $('#totalfees').val();
            
            var total_cost = Number( totalcost * 1 )+Number( totalfees * 1 );
            var reset_cost = Number( totalcost * 1 )-Number( totalfees * 1 );
            
    	    if( chk.attr( "checked" ) ) {
    	        $(chk).attr('checked', true);
    	        $('.total-costs').data("price", total_cost);
                $('.total-costs span').html( '$'+total_cost);

                    console.log( "a= " + totalcost + ' ' + totalfees );    
                    totalcost = 0;
    	    } 
    	    else if( !chk.attr( "checked" ) ) {
                $(chk).attr('checked', false);
                $('.total-costs').data("price", reset_cost);
                $('.total-costs span').html( '$'+reset_cost);

                    console.log( "b= " + totalcost + ' ' + totalfees );    
    	    }
        });
    
	}); //return false;
})(jQuery); 
