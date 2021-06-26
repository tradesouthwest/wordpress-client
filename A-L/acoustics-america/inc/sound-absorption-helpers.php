<?php 
/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin helper scripts
 *
 * @since 1.0.0
 */

function sound_absorption_calc_addtosite_scripts() {
/*$cstitle = get_option( 'sound_absorption_calc_options' )['sound_absorption_calc_cstitle_field'];
$csdescription = get_option( 'sound_absorption_calc_options' )['sound_absorption_calc_csdescription_field'];
*/
    if ( !is_singular() ) return false;
    $ver    = time();
    $content = '';
    $content .= '.woocommerce .single_variation_wrap .nrc_final_value{display:none}.sac-block:before,.sac-block:after{content: " ";}
    .sac-block:before{clear:left;display:block;width:100%;height:1px;position: relative;top18px;}
    .sac-block:after{clear:both;display:table;}.sac-block{position:relative;top:93px;left: 15px;display:block;width:100%;background:transparent;}
    .absorption-units-title{display:block;color: #3a3a3a;position:relative;bottom:-1em;left:-1em;clear:right}
    .sac_check{padding:2px 0;display:inline-block;background-image: linear-gradient(#f1f1f1,#fefefe,#f3f3f3);}'; 

    //let WP handle ver and loading
    wp_enqueue_style(  'sound-absorption-calc-style' );
    wp_register_style( 'sound-absorption-calc-entry-set', false );
    wp_enqueue_style(  'sound-absorption-calc-entry-set' );
    wp_add_inline_style( 'sound-absorption-calc-entry-set', $content );
/*
    wp_register_script( 'sound-absorption-attr', 
                        SOUND_ABSORPTION_CALC_URL . 'js/sound-absorption-attr.js', 
                        array( 'jquery' ), $ver, true 
                    );
    wp_localize_script( 'sound-absorption-attr', 'sac_ajax_object', 
                        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
                                'action' => 'soundAbsoptionCalc_find_nrc_thickness',
                                'security' => wp_create_nonce( 'sac-nonce-string' ) 
                        ) 
                    );
  */  
    wp_enqueue_script( 'sound-absorption-attr' );

}
//add_action( 'wp_ajax_nopriv_soundAbsoptionCalc_find_nrc_thickness', 'soundAbsoptionCalc_find_nrc_thickness' );
add_action( 'wp_footer', 'soundAbsoptionCalc_find_nrc_thickness' );
function soundAbsoptionCalc_find_nrc_thickness()
{   
/* 1/8", 1", 1 7/16", 2 1/4", 2", 3", 2 mm, 5 mm, 10 mm, 12 mm */
    ?>
<script id="sound-absorption-attr">
jQuery(document).ready(function ($) {
	"use strict";
        
    var pa_thickness = $("select#pa_thickness").val();
    $('#sac_nrc_units').val(pa_thickness);
    console.log( "pa_thickness= " + pa_thickness );

    $("select#pa_thickness").change(function(){
    var sp_thickness = $("select#pa_thickness").children("option:selected").val();
        //$('#sac_nrc_units').val(sp_thickness);
        console.log( "sp_thickness= " + sp_thickness );
    });

    $( "#sac_check" ).click( function() {
        //event.preventDefault();
            
        var sac_thickness = $("select#pa_thickness").val();
        var qty        = $('input[name="quantity"]').val();
        var nrc_multi = $('#nrc_mutilplier').val();

        if ( sac_thickness == 1 ) { nrc_multi = '.85'; }
        else if ( sac_thickness == 2 ) {  nrc_multi = '1.15'; }
        var sac_nrc_units = sac_thickness * qty * nrc_multi;
        
            console.log( "sac_thickness= " + sac_thickness );
            $('#sac_nrc_units').val(sac_nrc_units);

    });
});</script>
    <?php 
}
/**
 * Get each value from plugin NRCs
 * @param  string $opt Value of field, last two characters
 * @return string
 */
function soundAbsoptionCalc_get_nrc_value($opt)
{
    $opt     = ( '' != $opt ) ? $opt : '';
    $options = get_option('sound_absorption_calc');

    $option  = $options["sound_absorption_calc_mat_{$opt}"];
    $rtrn    = ( '' != $option ) ? $option : '';

        return $rtrn;
        $opt = null;
} 
