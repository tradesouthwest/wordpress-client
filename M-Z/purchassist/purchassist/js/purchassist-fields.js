/* https://makitweb.com/create-duplicate-of-the-elements-with-clone-jquery/ */
jQuery( document ).ready(function() {
	
    //e.preventDefault();
    jQuery('#but_add').click(function(){
        $ = jQuery;
        var lastname_id = $('.patsw-form-field input[type=text]:nth-child(1)').last().attr('id');
        var split_id    = lastname_id.split('_');
        var index       = Number(split_id[1]) + 1;
        // button remove values
        var lastbut_id  = $('.patsw-form-field input[type=button]:nth-child(1)').last().attr('name');
        var split_name  = lastbut_id.split('_');
        var indexx      = Number(split_name[1]) + 1;
        // field remove values
        var lastfld_id  = $('.patsw-form-field').last().attr('id');
        var split_fld   = lastfld_id.split('_');
        var indexf      = Number(split_fld[1]) + 1;

        // clone
        var newel = $(".patsw-form-field").last().clone(true);
        
        // add new values
        $(newel).find('input[type=text]:nth-child(1)').attr("id","purchaseitem_"+index);
        $(newel).find('input[type=text]:nth-child(2)').val("purchaseitem_"+index);
        $(newel).find('input[type=button]:nth-child(1)').attr("name","purchasefield_"+indexx);
        $(newel).find('input[type=button]:nth-child(2)').val("purchasefield_"+indexx);
        $(newel).attr("id","purchasefield_"+indexf);
        $(newel).val("purchasefield_"+indexf);
        
        // copy
        $(newel).insertAfter( $(".patsw-form-field:last") );
            console.log(index);
    });

    jQuery(".but_rmv").click(function(){
        $ = jQuery;
        var tormv = $(this).attr("name");
        $('.patsw-form-field:nth-of-type(1)').attr("id", tormv ).next().remove();

        console.log('removed '+tormv);
    });

});