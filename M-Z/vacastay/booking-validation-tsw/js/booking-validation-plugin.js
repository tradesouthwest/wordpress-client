/* booking-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
(function($){
	$(document).ready(function(){
	$('#tsw-agreeto').change(function(e){
	    e.preventDefault();
	    
	    var totalcost = $('.total-costs').data("price");
        var totalfees = $('#totalfees').val();
        var total_cost = Number( totalcost * 1 )+Number( totalfees * 1 );
	    

        
            console.log( "added fees= " + totalfees + ' ' + totalcost );
            $('.total-costs').data("price", total_cost);
            $('.total-costs span').html(total_cost);
            totalcost = totalfees = total_cost = null;
	});
	}); return false;
})(jQuery); 
