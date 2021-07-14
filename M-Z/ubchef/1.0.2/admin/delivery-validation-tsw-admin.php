<?php
/**
 * @since ver: 1.0.0
 * Author:     Tradesouthwest
 * Author      URI: http://tradesouthwest.com
 * @package    sound_absorption_calc
 * @subpackage admin/sound-absorption-calc-admin
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_shipping', 'delivery_validation_tsw_add_shipping_section' );
function delivery_validation_tsw_add_shipping_section( $sections ) {
	
	$sections['postcode_exclusion'] = __( 'Exclude Postcodes', 'delivery-validation-tsw' );
	return $sections;
	
}
/**
 * Add settings to the specific section we created before
 */
add_filter( 'woocommerce_get_settings_shipping', 'delivery_validation_tsw_shipping_settings', 10, 2 );
function delivery_validation_tsw_shipping_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'postcode_exclusion' ) {
		$settings_postcodes = array();
        $settings_postcodes[] = array( 
                        'name' => __( 'Exclude Postcode Settings', 'delivery-validation-tsw' ), 
						'id'   => 'postcode_exclusion_title',
                        'type' => 'title', 
                        'desc' => __( 'Add postcodes to exclude from Shipping', 'delivery-validation-tsw' )
                         );
        $settings_postcodes[] = array(
			'name'     => __( 'Enter Postal Codes to Exclude', 'delivery-validation-tsw' ),
			'id'       => 'postcode_exclusion_values',
			'desc_tip' => __( 'These MUST be separated by a comma!', 'delivery-validation-tsw' ),
			'type'     => 'textarea',
			'desc'     => __( 'Each code MUST be separated by a comma. Example: 
                            AB25&#44; -Last entry does not need a comma after it.', 'delivery-validation-tsw' )
		);
		$settings_postcodes[] = array(
			'name'     => __( 'Error Message', 'delivery-validation-tsw'),
			'id'       => 'postcode_exclusion_verify',
			'type'     => 'text',
			'desc' => __( 'Delivery error message on the Checkout page.', 'delivery-validation-tsw' )
		);
    $settings_postcodes[] = array( 'type' => 'sectionend', 
								   'id'   => 'postcode_exclusion' 
								   );
		return $settings_postcodes;
    /**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}
/**
 * Get our options for comparing to shipping codes.
 *
 * @param string $defaults         Values can be removed if no longer needed.
 * @param string $exception_values Values
 * @return array                   List of excluded postal codes. 
 */
function delivery_validation_tsw_get_verified()
{
	$testing_postcode  = $value = '';
	//$exception_values  = array();
	
	/*$defaults          = array( 'AB25','AB31','AB33-38','AB41-56','FK17-21', 
                                'HS1-9','IV1-99','KW0-14','PH15-41','PH45-99','TR21-25'); */
	$exception_values  = ( empty( get_option('postcode_exclusion_values') ) )
    				? '' : get_option('postcode_exclusion_values');

	/*if ( empty ( $exception_values ) ) {
		$exception_values = $defaults;

	} else {
	*/
		$arr_values = explode(',', $exception_values );
		//$exceptions = explode(',', $exception_values ); //$values;
	//}

	if( is_array( $arr_values ) ) {
	$testing_postcode  = filter_var('ABaaa25');
		//foreach( $arr_values as $key=>$value ) {

			if ( in_array( $testing_postcode, $arr_values ) ) {

				$result = 'is an excluded postcode';
				//break;
			} else {

				$result = 'This postcode is OK';
				//break;
			}

		//} 				
		unset($arr_values);
	} else {
	
		$value = 'not array';
	}
		return sanitize_text_field( $testing_postcode . ' ' . $result );


			$testing_postcode  = $result = null;
} 
