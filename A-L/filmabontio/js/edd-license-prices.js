/* @since v. 1.0.51  
 jQuery.post( url [, data ] [, success ] [, dataType ] ) 
 Data(key-value pairs) 
 Success(result,status,xhr)
 Type type of data returned from the AJAX request (xml, json, script, or html)
 */
jQuery(document).ready(function ($) 
{

    $('input.edd_license_prices').click(function() {

        var elp_price = $('.edd_license_prices:checked').val();

        $.ajax({
            
            url: license_upgrades.admin_ajax,
           
            type: 'GET',
            var data = {
            action: 'license_upgraded'
            value: elp_price,
            security:  license_upgrades.elpnonce,
            post_data: $( form.edd_purchase_form ).serialize()
            },
            $.post( license_upgrades.admin_ajax, data, function( response ){
               $( 'body' ).trigger( 'edd_add_to_cart' );
            });
               success: function(response) {
                if(response) {
                //get permalink for post from php and go to it
                
                console.log("set as " + elp_price);
                }
            }
        });
               
 
    });
    //return false;
});
