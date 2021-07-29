/* booking-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
(function($){
	$(document).ready(function(){
	    
	    $('.daterangepicker .applyBtn').click(function(e){
	        e.preventDefault();
	        var additional_rental_fees = $('#additional_rental_fees').val();
	        //$('booking-estimated-cost span').data(price', Number(additional_rental_fees * 1));
	        console.log('added est prc '+additional_rental_fees);
	        
	    });
	   	$('a.booking-confirmation-btn').on('click', function(e){
        		e.preventDefault();
        		var tswchk   = $('input#tsw-agreeto');
        	   	var tswvalid = $('.tsw-valid');
        	   	var tswerror = $('.tsw-error');
            
    	    //tswchk.attr("checked", !tswchk.attr("checked"));
	        
	        if( tswchk.attr( "checked" ) ) {
	            tswvalid.addClass('tsw-valid');
	            tswerror.addClass('tsw-error');
            } else {
                tswvalid.removeClass('tsw-valid');
                tswvalid.addClass('tsw-invalid');
                tswerror.addClass('tsw-error_show');
                alert( 'You must agree to additional fees.' );
            }
	   	});
		     
    	$('#tsw-agreeto').click(function(){
    	    
    	    var chk = $(this);
    	    chk.attr("checked", !chk.attr("checked"));
    	    var totalcost = 0;
    	    totalcost = $('.total-costs').data("price");
            var totalfees = $('#totalfees').val();
            
            var total_cost = Number( totalcost * 1 )+Number( totalfees * 1 );
            var reset_cost = Number( totalcost * 1 )-Number( totalfees * 1 );
            
    	    if( chk.attr( "checked" ) ) {
    	        $(chk).attr('checked', 'checked');
    	        
    	        
    	        $('.total-costs').data("price", total_cost);
                $('.total-costs span').html( '$'+total_cost);

                //$('total-discounted_costs span').html( "$"+total_cost);

                    console.log( "a= " + totalcost + ' ' + totalfees );    
                    totalcost = 0;
    	    } 
    	    else if( !chk.attr( "checked" ) ) {
                $(chk).attr('checked', false);
                
                $('.total-costs').data("price", reset_cost);
                $('.total-costs span').html( '$'+reset_cost);

                //$('total-discounted_costs span').html( "$"+reset_cost);
                
                    console.log( "b= " + totalcost + ' ' + totalfees );    
                    reset_cost = 0;
    	    }
        });
            
        });
     //return false;
})(jQuery); 
