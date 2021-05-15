<?php 
/**
 * BP Profilex Admin Screens
 * @see http://wpsettingsapi.jeroensormani.com/
 * @param int $option_id The ID of the option being edited.
 */
add_action( 'admin_menu', 'bppx_profilex_add_admin_menu' );
add_action( 'admin_init', 'bppx_profilex_settings_init' );


function bppx_profilex_add_admin_menu(  ) { 

	add_options_page( 'BP Profilex', 'BP Profilex', 'manage_options', 'bp_profilex', 'bppx_profilex_options_page' );

}

/**
 * TODO current_user = 554 to generator as default
 */
function bppx_profilex_settings_init(  ) { 

	register_setting( 'pluginPage', 'bppx_profilex_settings' );

	add_settings_section(
		'bppx_profilex_pluginPage_section', 
		__( 'Location tag serializes data for visits', 'bppx-profilex' ), 
		'bppx_profilex_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'bppx_location_tag_generator', 
		__( 'Location Tag Generator', 'bppx-profilex' ), 
		'bppx_location_tag_generator_field_render', 
		'pluginPage', 
		'bppx_profilex_pluginPage_section' 
	);
	add_settings_field( 
		'bppx_location_checkbox_field_1', 
		__( 'Check to turn off Location Tag', 'bppx-profilex' ), 
		'bppx_profilex_checkbox_field_1_render', 
		'pluginPage', 
		'bppx_profilex_pluginPage_section' 
	);


}


function bppx_location_tag_generator_field_render(  ) { 

	$options = get_option( 'bppx_profilex_settings' );
	?>
	<input type='text' name='bppx_profilex_settings[bppx_location_tag_generator]' 
        value='<?php echo $options['bppx_location_tag_generator']; ?>'>
	<?php

}

function bppx_profilex_checkbox_field_1_render(  ) { 

	$options = get_option( 'bppx_profilex_settings' );
	?>
	<input type='checkbox' name='bppx_profilex_settings[bppx_location_checkbox_field_1]' 
        <?php checked( $options['bppx_location_checkbox_field_1'], 1 ); ?> value='1'>
	<?php

}


function bppx_profilex_settings_section_callback(  ) { 

	echo __( 'Location Tag options', 'bppx-profilex' );

}


function bppx_profilex_options_page(  ) { 

		?>
		<form action='options.php' method='post'>

			<h2>BP Profilex</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<?php

}
