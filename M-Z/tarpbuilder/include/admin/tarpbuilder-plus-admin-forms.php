<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    tarpbuilder-plus
 * @subpackage /include
 * @author     Larry Judd <tradesouthwest@gmail.com>
 * TODO add a field in the order table (admin side)
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* **************** FORM FIELDS **************** */
/** c0.)
 * name for 'label' field
 * @since 2.0.1
 */
if( !function_exists( 'tarpbuilder_plus_cstitle_field_cb' ) ) :
function tarpbuilder_plus_cstitle_field_cb($args)
{  
    printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b><br/>
    <span class="wndspan">%5$s </span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c1.)
 * name for 'text' field
 * @since 2.0.11
 */
if( !function_exists( 'tarpbuilder_plus_csdescription_field_cb' ) ) :
function tarpbuilder_plus_csdescription_field_cb($args)
{  
    printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b> <em class="untzr-em"> *</em><br/>
    <span class="wndspan">%5$s </span>',
        esc_attr($args['type']),
        esc_attr($args['option_group']),
        esc_attr($args['name']),
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c2.)
 * name for 'display' field
 * @since 2.0.11
 */
if( !function_exists( 'tarpbuilder_plus_wndproduct_field_cb' ) ) :
function tarpbuilder_plus_wndproduct_field_cb($args)
{  
    printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b> <em class="untzr-em"> *</em><br/>
    <span class="wndspan">%5$s </span>',
        esc_attr($args['type']),
        esc_attr($args['option_group']),
        esc_attr($args['name']),
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c3.)
 * callback for 'text' field
 * @since 2.0.11
 */
if( !function_exists( 'tarpbuilder_plus_begin_label_cb' ) ) :
function tarpbuilder_plus_begin_label_cb($args)
{  
    printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b> <em class="untzr-em"> *</em><br/>
    <span class="wndspan">%5$s </span>',
        esc_attr($args['type']),
        esc_attr($args['option_group']),
        esc_attr($args['name']),
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c4.)
 * 'style' field
 * @since 2.0.11
 */
if( !function_exists( 'tarpbuilder_plus_wndinwidth_field_cb' ) ) :
function tarpbuilder_plus_wndinwidth_field_cb($args)
{  
    printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b><br/>
    <span class="wndspan">%5$s </span>',
        esc_attr($args['type']),
        esc_attr($args['option_group']),
        esc_attr($args['name']),
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c5.)
 * inline style field 2
 * @since 2.0.110
 */
if( !function_exists( 'tarpbuilder_plus_wndinppd_field_cb' ) ) :
function tarpbuilder_plus_wndinppd_field_cb($args)
{  
   printf(
    '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" value="%4$s" 
    class="regular-text" /><b class="wntip" data-title="%6$s"> ? </b><br/>
    <span class="wndspan">%5$s </span>',
        esc_attr($args['type']),
        esc_attr($args['option_group']),
        esc_attr($args['name']),
        $args['value'],
        esc_html($args['description']),
        esc_attr($args['tip'])
    );
}
endif;
/** c6.)
 * 'select' field
 * @since 2.0.11
 */
if( !function_exists( 'tarpbuilder_plus_wndtaxbase_field_cb' ) ) :
function tarpbuilder_plus_wndtaxbase_field_cb($args)
{  
    print('<label for="tarpbuilder_plus_wndtaxbase_field">');
    if( ! empty ( $args['options'] && is_array( $args['options'] ) ) )
    { 
        $options_markup = '';
        $value = $args['value'];
        foreach( $args['options'] as $key => $label )
        {
            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', 
            $key, selected( $value, $key, false ), $label );
        }
        printf( '<select name="%1$s[%2$s]" id="%1$s-%2$s">%3$s</select>',  
                esc_attr($args['option_group']),
                esc_attr($args['name']),
                $options_markup );
    }
    $tip = $args['tip'];
    print('<b class="wntip" data-title="' . esc_attr($tip) . '"> ? </b></label>'); 
}
endif;
/* **************** CHECKBOXES **************** */
/** c7.)
 * switch for 'allow zero qnty' field
 * @since 2.0.11
 * @input type checkbox
 */
if( !function_exists( 'tarpbuilder_plus_wndnada_field_cb' ) ) : 
function tarpbuilder_plus_wndnada_field_cb($args)
{ 
    $checked = '';
    $options = get_option($args['option_group']);
    $value   = ( !isset( $options[$args['name']] ) ) 
                ? null : $options[$args['name']];
    if($value) { $checked = ' checked="checked" '; }

        $html  = '';
        $html .= '<input id="' . esc_attr( $args['name'] ) . '" 
        name="' . esc_attr( $args['option_group'] . '['.$args['name'].']') .'" 
        type="checkbox" ' . $checked . ' />';
    $html .= '<span class="wndspan">' . esc_html( $args['description'] ) .'</span>';
    $html .= '<b class="wntip" data-title="'. esc_attr( $args['tip'] ) .'"> ? </b>';
    
    echo $html;
}   
endif;
/** c8.)
 * switch for 'allow match' field
 * @since 2.0.11
 * @input type checkbox
 */
if( !function_exists( 'tarpbuilder_plus_wndmatch_field_cb' ) ) :
function tarpbuilder_plus_wndmatch_field_cb($args)
{ 
    $checked = '';
    $options = get_option($args['option_group']);
    $value   = ( !isset( $options[$args['name']] ) ) 
                ? null : $options[$args['name']];
    if($value) { $checked = ' checked="checked" '; }

        $html  = '';
        $html .= '<input id="' . esc_attr( $args['name'] ) . '" 
        name="' . esc_attr( $args['option_group'] . '['.$args['name'].']') .'" 
        type="checkbox" ' . $checked . '/>';
    $html .= '<span class="wndspan">' . esc_html( $args['description'] ) .'</span>';
    $html .= '<b class="wntip" data-title="'. esc_attr( $args['tip'] ) .'"> ? </b>';

    echo $html;
} 
endif;  
/** c9.)
 * switch for 'remove styles' field
 * @since 2.0.1
 * @input type checkbox
 */
function tarpbuilder_plus_removestyles_field_cb($args)
{ 
    $checked = '';
    $options = get_option($args['option_group']);
    $value   = ( !isset( $options[$args['name']] ) ) 
                ? null : $options[$args['name']];
    if($value) { $checked = ' checked="checked" '; }

        $html  = '';
        $html .= '<input id="' . esc_attr( $args['name'] ) . '" 
        name="' . esc_attr( $args['option_group'] . '['.$args['name'].']') .'" 
        type="checkbox" ' . $checked . '/>';
    $html .= '<span class="wndspan">' . esc_html( $args['description'] ) .'</span>';
    $html .= '<b class="wntip" data-title="'. esc_attr( $args['tip'] ) .'"> ? </b>';

    echo $html;
}   

/* **************** COLOR PICKERS **************** */
/**
 * Render page branding colors option
 * @string $args = array()
 * @since  2.0.110
 */
function tarpbuilder_plus_inwoo_background_cb($args) 
{ 
    //be safe default
    if($args['default'] == '') $args['default'] = sanitize_text_field('#aa8200');

    printf( '<label>%1$s </label> 
        <em> %7$s</em><b class="wntip" data-title="%9$s">?</b>
        <br/>
        <input type="%2$s" name="%3$s[%4$s]" value="%5$s" 
        class="%6$s tarpbuilder-color-picker-1" id="%3$s-%4$s"
        data-default-color="%8$s"/>',
            esc_html( $args['label'] ),
            esc_attr( $args['type'] ),
            esc_attr( $args['option_group'] ),
            esc_attr( $args['name'] ),        
            sanitize_hex_color( $args['value'] ),
            esc_attr( $args['class'] ),
            esc_attr( $args['description'] ),
            sanitize_hex_color( $args['default'] ),
            esc_attr( $args['tip'] )
    ); 
}
/**
 * Render page branding colors option
 * @string $args = array()
 * @since  2.0.110
 */
function tarpbuilder_plus_inwoo_color_cb($args) 
{ 
    //be safe default
    if($args['default'] == '') $args['default'] = sanitize_text_field('#ffffff');

    printf( '<label>%1$s </label> 
        <em> %7$s</em><b class="wntip" data-title="%9$s">?</b>
        <br/>
        <input type="%2$s" name="%3$s[%4$s]" value="%5$s" 
        class="%6$s tarpbuilder-color-picker-2" id="%3$s-%4$s"
        data-default-color="%8$s"/>',
            esc_html( $args['label'] ),
            esc_attr( $args['type'] ),
            esc_attr( $args['option_group'] ),
            esc_attr( $args['name'] ),        
            sanitize_hex_color( $args['value'] ),
            esc_attr( $args['class'] ),
            esc_attr( $args['description'] ),
            sanitize_hex_color( $args['default'] ),
            esc_attr( $args['tip'] )
     ); 
} 
/**
 * textarea before content
 */
function tarpbuilder_content_before_html_cb($args)
{

printf('<textarea id="%1$s-%2$s" class="textarea-field" name="%1$s[%2$s]" rows="5" cols="35">%3$s</textarea>',
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        $args['tip']
    );
}
/**
 * textarea after content
 */
function tarpbuilder_content_after_html_cb($args)
{

printf('<textarea id="%1$s-%2$s" class="textarea-field" name="%1$s[%2$s]" rows="5" cols="35">%3$s</textarea>',
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        $args['tip']
    );
}
