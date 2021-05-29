<?php 
/**
 * @package    tarpbuilder
 * @subpackage includes/tarpbuilder-quantity
 * @since      2.0.1
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// PA-1
add_action( 'woocommerce_before_add_to_cart_button', 'tarpbuilder_plus_quantity_numberof', 10 );
// PA-3
add_action( 'woocommerce_single_product_summary', 'tarpbuilder_plus_quantity_numberof_label', 12 );

/** HELPER
 * Validate if product has a unitized field value
 * 
 * @since 1.0.2
 * @param string $value Title option must be set.
 * @return Bool
 */
function tarpbuilder_plus_is_unitized($post)
{
    global $post;

    $ppday = get_post_meta( $post->ID, '_tarpbuilder_plus_fee', true );
    
    if( empty ( $ppday  ) ) { 
        $unitized = false; 
        }
        else { 
            $unitized = true; 
    }
        return (bool)$unitized;
}


// tarp simulator (replaced inline below)
//add_action('woocommerce_product_thumbnails', 'tarpbuilder_plus_display_tarp_simulation');
/* **************** PUBLIC SIDE DISPLAY **************** */

/** PA-1
 * Display numberof input and label on single product page
 * Label in front of numberof field
 * 
 * @param  string $wndtext Product Page Increment Name
 * @param  string $wndwdth Style in put fields
 * @param  string $value   Price 
 * @return print  HTML
 */

function tarpbuilder_plus_quantity_numberof()
{ 
    global $product, $post;

    $tpconting = tarpbuilder_plus_contingencies();
    if( !$tpconting ) return;

    $plchld   = $wndtext = $wndwdth = $untzrtxt = '';
    $id       = $product->get_id();
    
    // Option
    $untzrtxt = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndproduct_field'];
    $wndwdth  = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndinwidth_field'];
    $valid_hemmed = 'If you selected grommets, validate if "Hemmed" was selected above.';
    // Post Meta
    $wndtext  = ( empty( get_post_meta( $id, '_tarpbuilder_plus_numberof_label', true)))
           ? $untzrtxt : get_post_meta( $id, '_tarpbuilder_plus_numberof_label', true);
    /* Option if Value is set 
     * @input type = number 
     * $value is the post meta
     
    $value     = isset( $_POST['_attribute_pa_custom-tarp'] ) 
                 ? sanitize_text_field( $_POST['_attribute_pa_custom-tarp'] ) : 0; */
    $valwidth  = isset( $_POST['_attribute_pa_custom-tarp-width'] ) 
                 ? sanitize_text_field( $_POST['_attribute_pa_custom-tarp-width'] ) : '';
    $vallength = isset( $_POST['_attribute_pa_custom-tarp-length'] ) 
                 ? sanitize_text_field( $_POST['_attribute_pa_custom-tarp-length'] ) : '';
    //$val_notes = $product->get_meta('woocommerce_custom_fields');
    $valnotes  = isset( $_POST['_tarpbuilder_plus_product_notes'] ) 
                 ? sanitize_text_field( $_POST['_tarpbuilder_plus_product_notes'] ) : '';
    
    $tarpbuilder_plus_before_inputs = (empty(get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_before_html']))
          ? '' : get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_before_html'];

    ob_start();

    printf( '<div class="untzclearfix"></div>
    <div class="tarpbuilder-before-inputs">
    %s
    </div>',
        wp_kses_post(balanceTags($tarpbuilder_plus_before_inputs, true))
    );
    ?>
    <div class="custom-tarp-order-form" id="custom-tarp">    
        <div class="tarpbuild-table-section"> 

            <table id="price_calculator" class="variable_price_calculator tarpbuilder-variations">
            <tbody>
            <tr><td colspan="6"><h4>Enter Width and Length of Tarp<h4></td></tr>
        <?php 
        /* 
        * Inputs for Feet and Inches to determine number of grommets
        */ 
        ?>
            <tr>
            <td class="form_label">
                <label for="number_tarp_width_major" 
                      id="number_tarp_width_major_label">
                      Tarp Width:</label>
            <!--<input type="hidden" value="1" name="number_quantity" id="number_quantity" />-->
            </td>
            <td class="form_field">
    <?php /* 
          * Width feet inches 
          */ 
          ?>
                <input type="number" min="" max="9999" step="1" style="width: 4em" 
                      title="width" onchange="sizeChange(this.form)" 
                      name="tarp_width_major" id="number_tarp_width_major" 
                      class="feetWidth" value="0" /></td>

            <td class="form_field">
                <input name="tarp_width_units_major" onchange="sizeChange(this.form)" 
                        id="select_tarp_width_units_major" class="max-wsel"
                        value="feet" readonly>
                </select></td>
            <td>&</td>
            <td class="form_field">
                <input type="number" min="" max="9999" step="" style="width:4em;" 
                      title="width" onchange="sizeChange(this.form)" 
                      name="tarp_width_minor" id="number_tarp_width_minor" 
                      class="inchWidth" value="0" /></td>

            <td class="form_field">
                <input name="tarp_width_units_minor" onchange="sizeChange(this.form)" 
                        id="select_tarp_width_units_minor" class="max-wsel" 
                        value="inches" readonly>
                </select>
                
                <input type="hidden" data-unit="ft" data-common-unit="ft" 
                      name="_attribute_pa_custom-tarp-width" 
                      id="attribute_pa_custom-tarp-width" 
                      value="0" placeholder="sq. ft." 
                      class="width_needed" readonly>
                </td>
            </tr>

            <tr>
            <td colspan="6" class="form_field">
                <input type="hidden" name="width_error" id="hidden_width_error" /></td>
            </tr>

            <tr>
            <td class="form_label">
                <label for="number_tarp_length_major" 
                        id="number_tarp_length_major_label">
                Tarp Length:</label>
            </td>
            <td class="form_field">
    <?php /* 
          * Length feet inches 
          */ ?>
                <input type="number" min="" max="9999" step="1" style="width:4em" 
                        title="length" onchange="sizeChange(this.form)"  
                        name="tarp_length_major"  id="number_tarp_length_major" 
                        class="feetLength" value="0" /></td>
            <td class="form_field">
                <input name="tarp_length_units_major" onchange="sizeChange(this.form)"   
                        id="select_tarp_length_units_major" class="max-wsel"
                        value="feet" readonly>
                </td>
            <td>&</td>
            <td class="form_field">

                <input type="number" min="" max="9999" step="" style="width:4em;" 
                        onchange="sizeChange(this.form)" title="length" 
                        name="tarp_length_minor" id="number_tarp_length_minor" 
                        class="inchLength" value="0" /></td>
            <td class="form_field">
                <input name="tarp_length_units_minor" onchange="sizeChange(this.form)" 
                        id="select_tarp_length_units_minor" class="max-wsel" 
                        value="inches" readonly>
                

                <input type="hidden" data-unit="ft" data-common-unit="ft" 
                      name="_attribute_pa_custom-tarp-length" id="attribute_pa_custom-tarp-length" 
                      value="0" placeholder="sq. ft." class="length_needed" readonly>
                </td>
            </tr>

            <tr class="total-amount">
                <td colspan="5">Total Area (sq. ft.)</td>
                <td>
                    <span class="trison-inline" data-unit="sq. ft.">
                    <input type="number" id="attribute_pa_custom-tarp" 
                            name="_attribute_pa_custom-tarp" 
                            value="0" 
                            class="tarpbuilder-numberof">
                    </span>
                </td>
            </tr>
            </tbody></table>
        </div>




        <div class="grommet-spacing-table">
            
        <table id="grommet_calculator" class="tarpbuilder-variations">
        <tbody>
        <tr><td colspan="2" style="text-align: center">
        <h4>Select Grommet Spacing</h4>
        <p><strong>Seatbelt Hemmed Grommets per inch<strong></p></td>
        </tr>
        <tr>
        <?php 
        /* 
        * Select dropdown to determine Grommet Spacing
        * Must contain attribute_pa_approx-spacing
        */ 
        ?>
        <td style="padding-top:1em;padding-bottom:1em; text-align:left" class="form_label">
            <label for="select_approx_spacing" id="select_approx_spacing_label">
                  Approx. # per inch</label>
        </td>
        <td style="padding-top:1em;padding-bottom:1em;" class="form_field">
        <!--onchange="set_grommet_spacing(this.form,this);"<select name="approx_spacing" required="required" onchange="set_grommet_spacing(this.form,this);" id="select_approx_spacing"> -->
        <select id="pa_approx-spacing" onchange="set_grommet_spacing(this.form,this);" 
                name="attribute_pa_approx-spacing" class="approx_spacing" 
                
                data-attribute_name="attribute_pa_approx-spacing" data-show_option_none="">
                    <option value="0">Grommets per inch</option>
                    <option value="-1">Set Manually</option>
                    <option value="6" class="attached enabled">Every 6 inches</option>
                    <option value="12" class="attached enabled">Every 12 inches</option>
                    <option value="18" class="attached enabled">Every 18 inches</option>
                    <option value="24" class="attached enabled">Every 24 inches</option>
        </select><p style="color:green;font-weight:bold">
        <?php esc_html_e('For Corners ONLY, Set manually below.', 'woocommerce'); ?></p>
        </td>
        </tr>
        </tbody></table>

        <?php 
        /* *************************************
        * Inputs to display number of grommets
        */ 
        ?>    
      <div class="tarpbuilder-canvas"> 
        <table id="tarpsim-table-grommets" class="bkgrnd-Blue"><tbody>
        <tr>
            <td rowspan="3" style="text-align:right;vertical-align:middle" class="form_field">
                <input type="number" min="0" max="999" step="1" style="width:5em;" 
                        required="required" onchange="showSpecs(this.form)" 
                        name="grommets_left" id="number_grommets_left" 
                        class="tarp_grommets_left" value="0" />
            </td>
            <td style="text-align:center" class="form_field">
                <input type="number" min="0" max="999" step="1" style="width:5em;" 
                        required="required" onchange="showSpecs(this.form)" 
                        name="grommets_top" id="number_grommets_top" 
                        class="tarp_grommets_top" value="0" />
            </td>
            <td rowspan="3" style="text-align:left;vertical-align:middle" class="form_field">
                <input type="number" min="0" max="999" step="1" style="width:5em;" 
                        required="required" onchange="showSpecs(this.form)" 
                        name="grommets_right" id="number_grommets_right" 
                        class="tarp_grommets_right" value="0" />
            </td>
        </tr>

        <tr>
            <td style="text-align:center" colspan="1">
                <div class="tarpsim-color" style="width:320px">
                    <p id="tarp-color-name" class="tarp-color-name">waterproof</p>
                </div>
            </td>
        </tr>

        <tr>
            <td style="text-align:center" colspan="1" class="form_field">
                <input type="number" min="0" max="999" step="1" style="width:5em;" 
                required="required" onchange="showSpecs(this.form)" 
                name="grommets_bottom" id="number_grommets_bottom" 
                class="tarp_grommets_bottom" value="0" />
            </td>
        </tr>

            </tbody></table>
      </div>


        <div class="grommet-spacing-notes">
        <table id="custom_notes" class="tarpbuilder-variations">
            <tbody>

        <tr class="product-purchase-note">
            <?php 
            /* 
            * Readonly field to display tallied data about grommets and tarp
            */ 
            ?>
            <td class="form_field">
                <textarea id="textarea_spacing" readonly="readonly" 
                rows="9" cols="70" style="border:none;" 
                name="spacing" ></textarea>
                <input type ="hidden" name="spacing_unique_id" value="spacing_unique_id_">
            </td>
        </tr>        
            
            <tr>
                <td class="grom-foot">
                <?php esc_html_e($valid_hemmed); ?>
                
                </td>
            </tr>

        </tbody></table>
        </div>
    </div>
    <?php 
        $output = ob_get_clean();
        echo $output;

}


/** PA-3
 * Display price on single product page
 * 
 * @param str $label Label for Checkout Field
 * @param str $ppday Increment price value
 * @param str @currency_symbol $ il8n
 * @return html
 */
function tarpbuilder_plus_quantity_numberof_label()
{ 
    global $product, $post;
    $label     = $ppday = $untzrsing = $wndinppd ='';
    $id        = $product->get_id();
    
    // Options
    $untzrsing = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_cstitle_field'];
    $wndinppd  = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndinppd_field'];
    // Post Meta
    $label     = (empty ( get_post_meta( $id, '_tarpbuilder_plus_single_label', true)))
                 ? $untzrsing : get_post_meta( $id, '_tarpbuilder_plus_single_label', true); 
    $ppday     = get_post_meta( $product->get_id(), '_tarpbuilder_plus_fee', true ); 
    // il8n
    $currency_symbol = get_woocommerce_currency_symbol();

    if( tarpbuilder_plus_is_unitized($post) === true ) {

        printf( '<div id="tarpbuilderFee" class="wndfee title-text" style="%s">
                <label>%s 
                <span>%s</span><span>%s</span></label></div>',
                esc_attr( $wndinppd ),
                esc_html( $label ), 
                esc_attr( $currency_symbol ),
                esc_html( $ppday ) 
                ); 
        } else { 
            return false; 
    }
} 

// will only add javascript to footer if is on page
add_action( 'wp_footer', 'tarpbuilder_plus_datepicker_onpage'); 
function tarpbuilder_plus_datepicker_onpage()
{

    $tpconting = tarpbuilder_plus_contingencies();
    if( $tpconting ) : 
?>

<script type="text/javascript" language="javascript">// <![CDATA[
function convInches()
{
tmpinch = document.calc.inches.value;
if (tmpinch.indexOf(',') > -1) tmpinch = tmpinch.substring(0,tmpinch.indexOf(','))+'.'+tmpinch.substring(tmpinch.indexOf(',')+1);
inch = parseNumber(tmpinch);
feet = parseInt(document.calc.feet.value);
if (!(feet > 0))
{
feet = 0;
document.calc.feet.value = 0;
}
if (!(inch > 0))
{
inch = 0;
document.calc.inches.value = 0;
}
newfeet = inch/12;
if (newfeet > 0) feet += newfeet;
feetstr = feet+" ";
decimal = feetstr.indexOf('.', 0) ;
feetstr = feetstr.substr(0, decimal + 4);
document.calc.decfeet.value = feetstr;
return false;
}
function convDecfeet()
{
tmpft = document.calc.decfeet.value;
if (tmpft.indexOf(',') > -1) tmpft = tmpft.substring(0,tmpft.indexOf(','))+'.'+tmpft.substring(tmpft.indexOf(',')+1);
decfeet = parseNumber(tmpft);
if (decfeet > 0) newfeet = Math.floor(parseFloat(decfeet));
else
{
decfeet = 0;
newfeet = 0;
document.calc.decfeet.value = 0;
}
decpart = decfeet-newfeet;
newinches = decpart/0.0833333;
document.calc.inches.value = makeFraction(newinches);
document.calc.feet.value = newfeet;
return false;
}
function parseNumber(number) {
var space, slant, num, denom, whole, fraction;
slant = number.indexOf('/');
space = number.indexOf(' ');
if (slant != -1 && space != -1) {
num = number.substr(space, slant - space);
denom = number.substr(slant + 1, number.length - slant);
whole = 1 * number.substr(0, space);
fraction = num / denom;
number = whole + fraction;
}
return number;
}
function makeFraction(number) {
var whole, fraction, decimal, denominator, numerator;
whole = Math.floor(number);
number = "/" + number;
decimal = number.indexOf('.', 0) ;
if (decimal == -1)
{
number = whole;
}
else
{
fraction = number.substr(decimal, 12);
numerator = Math.round(fraction / .03125);
denominator = 32;
if (numerator == 32)
{
whole++;
numerator = 0;
}
if (numerator > 0)
{
while ((numerator / 2) == Math.floor(numerator/2) )
{
numerator = numerator / 2;
denominator = denominator / 2;
}
number = whole + '  ' + numerator + '/' + denominator;
if (numerator == denominator)
{
number = whole++;
}
}
else
{
number = whole;
}
}
return number;
}
// ]]></script>
<script type="text/javascript" id="grommets">
// Generated by javascript model. Do not modify by hand. was approx-_spacing
//custom function execute body {
function sizeChange(frm){
	spacing = parseInt(frm['attribute_pa_approx-spacing'].value);
	if (spacing > 0) {
		set_grommet_spacing(frm,frm['attribute_pa_approx-spacing']);
	}
	else {
		showSpecs(frm);
	}
}
function showSpecs(frm) {
width_feet = get_width(frm,'feet');
if (width_feet === false) {
	return false
}
length_feet = get_length(frm,'feet');
if (length_feet === false) {
	return false
}
frm.spacing.value = 'Tarp Size\n'+user_entered(frm.tarp_width_major.value,frm.tarp_width_units_major.value,frm.tarp_width_minor.value,frm.tarp_width_units_minor.value)+' x '+user_entered(frm.tarp_length_major.value,frm.tarp_length_units_major.value,frm.tarp_length_minor.value,frm.tarp_length_units_minor.value)+"\n";
if (frm.spacing.value.indexOf(',') != -1) {  // Convert mixed units into pure inches
  frm.spacing.value += get_width(frm,'inches')+'" x '+get_length(frm,'inches')+'"\n';
}
grommets_left = frm['grommets_left'].value;
grommets_top = frm['grommets_top'].value;
grommets_right = frm['grommets_right'].value;
grommets_bottom = frm['grommets_bottom'].value;
spacing = get_grommet_spacing(width_feet,length_feet,grommets_top,grommets_bottom,grommets_left,grommets_right);
frm.spacing.value += spacing;
}
function user_entered(major,major_units,minor,minor_units) {
  var size1 = '';
  var size2 = '';
  if (major != '' && major != '0') {
    size1 = major+' '+major_units;
  }
  if (minor != '' && major != '0') {
    size2 = minor+' '+minor_units;
  }
  if (size1 == '' || size2 == '') {
    comma = '';
  }
  else {
    comma = ', ';
  }
  return size1+comma+size2;
}
function make_float(num) {
	if (num.length == 0 || isNaN(num)) {
      num = 0;
    }
	return parseFloat(num);
}
function get_width(frm,units,required=false) {
	major = make_float(frm.tarp_width_major.value);
	minor = make_float(frm.tarp_width_minor.value);
	if (major == 0 && minor == 0) {
      if (required) {
	  	alert('Please enter a valid tarp width');
	  	frm.tarp_width_major.focus();
      }
	  return false;
	}
  	if (units == '') {
      if (minor != 0) {
        units = frm.tarp_width_units_minor.value;
      }
      else {
        units = frm.tarp_width_units_major.value;
      }
    }
    total = convert_units(major,frm.tarp_width_units_major.value,units) + convert_units(minor,frm.tarp_width_units_minor.value,units);
  return total;
}
function get_length(frm,units,required=false) {
	major = make_float(frm.tarp_length_major.value);
	minor = make_float(frm.tarp_length_minor.value);
	if (major == 0 && minor == 0) {
      if (required) {
	 	 alert('Please enter a valid tarp length');
	  	frm.tarp_length_major.focus();
      }
	  return false;
	}
  	if (units == '') {
      if (minor != 0) {
        units = frm.tarp_length_units_minor.value;
      }
      else {
        units = frm.tarp_length_units_major.value;
      }
    }
    total = convert_units(major,frm.tarp_length_units_major.value,units) + convert_units(minor,frm.tarp_length_units_minor.value,units);
  return roundNumber(total,2);
}
function convert_units(length,length_units,desired_units) {
  if (length_units == desired_units) {
    return length;
  }
  if (length == 0) {
    return length;
  }
  conversion = length_units+' to '+desired_units;
  switch (conversion) {
    case 'feet to meters':
      return (length * 0.3048);
      break;
    case 'feet to inches':
      return (length * 12);
      break;
    case 'feet to cm':
      return (length * 30.48);
      break;
    case 'inches to feet':
      return (length / 12);
      break;
    case 'inches to meters':
      return (length * 0.0254);
      break;
    case 'inches to cm':
      return (length * 2.54);
      break;
    case 'cm to feet':
      return (length / 30.48);
      break;
    case 'cm to meters':
      return (length / 100);
      break;
    case 'cm to inches':
      return (length / 2.54);
      break;
    case 'meters to feet':
      return (length * 3.28084);
      break;
    case 'meters to inches':
      return (length * 39.37008);
      break;
    case 'meters to cm':
      return (length * 100);
      break;
  }
  return false;
}
function get_spacing(edge_length,grommets)
   {
    if (grommets.length === 0) {
     return 'Enter grommets on this edge';
    }
    if (grommets == 0) {
     return 'No grommets for this edge';
    }
    if (grommets == 1) {
     return '1 Grommet - location must be provided in notes';
    }
    if (grommets == 2) {
     return '2 Grommets - Outside edges assumed';
    }
   inner_grommets = grommets - 2;
    // Remove the space that will be taken up by the inner grommets
   inch = 0.0833333; // feet
    edge_length = edge_length - (inner_grommets * inch);
    inner_spaces = inner_grommets + 1;
    feet_between_grommets = (edge_length / inner_spaces) + inch;  // Add inch to get center to center
    inches_between_grommets = roundNumber(feet_between_grommets * 12,2);
    cm_between_grommets = roundNumber(feet_between_grommets * 30.48,2);
    return grommets+' Grommets; '+inches_between_grommets+'" apart'; // ('+cm_between_grommets+' cm)';
   }
function get_grommet_spacing(width_in_feet,length_in_feet,grommets_w1,grommets_w2,grommets_l1,grommets_l2)
{
    // Each grommet is 1 inch around. Remove 1/2 inch on the outside of the corner grommets plus 2 inches for the corner grommets
 inch = .0833333; // feet
 usable_width = width_in_feet - (inch * 3);
 usable_length = length_in_feet - (inch * 3);
 spacing_width_edge1 = get_spacing(usable_width,grommets_w1);
 spacing_width_edge2 = get_spacing(usable_width,grommets_w2);
 spacing_length_edge1 = get_spacing(usable_length,grommets_l1);
 spacing_length_edge2 = get_spacing(usable_length,grommets_l2);

    return '\nCenter-to-center grommet spacing: \nTop:         '+spacing_width_edge1+'\nBottom: '+spacing_width_edge2+'\nLeft:        '+spacing_length_edge1+'\nRight:     '+spacing_length_edge2;
}

function set_grommet_spacing(frm,fld){
  switch (fld.value) {
	case '-1': return true; // Please Select
	case '0': 
		set_grommets(frm,'0','0','0','0');
		return true;
	default:
		width_inches = get_width(frm,'inches');
		if (width_inches === false) {
			return false
		}
		length_inches = get_length(frm,'inches');
		if (length_inches === false) {
			return false
		}
		spacing_inches = parseInt(fld.value);
		wgrommets = get_number_grommets(spacing_inches,width_inches);
		lgrommets = get_number_grommets(spacing_inches,length_inches);
		set_grommets(frm,lgrommets,wgrommets,lgrommets,wgrommets);
	}
}
function get_number_grommets(spacing,distance){
	distance = distance - 1; // Outside edges
	grommets = Math.round(distance/spacing) + 1;
	return grommets;
}
function set_grommets(frm,left,top,right,bottom){
	frm['grommets_top'].value = top;
	frm['grommets_right'].value = right;
	frm['grommets_bottom'].value = bottom;
	frm['grommets_left'].value = left;
	showSpecs(frm);
}
function set_colours(frm) {
  	var fld = frm.material;
	var material = fld.options[fld.selectedIndex].value;
	var radios = frm.colour; // Colour radio buttons
	var radio_colour;
	var radio_button = new Array(); //  Keyed by colour
	for (var i=0, iLen=radios.length; i<iLen; i++) {
	  radios[i].disabled = true;
	  id = radios[i].id;
      radio_colour = radios[i].value;
      radio_button[radio_colour] = radios[i];
	  document.getElementById(id+'_suffix').setAttribute("style","color:#DCDAD1");
	}
  	var clear_message = true;
	var colors = new Array();
	switch (material){
	    //case 'Please Select' : colors = ['black','blue','brown','clear','green','grey','maroon','orange','red','tan','white','yellow']; break;
		case '14 Oz FR Curtain' : colors = ['black','blue','green','red','white','yellow']; break;
		case '14 Oz Vinyl' : colors = ['black','blue','green','grey','red','white']; break;
		case '18 Oz Vinyl' : colors = ['black','blue','brown','green','grey','maroon','orange','red','tan','white','yellow']; break;
		case '18 Oz Vinyl FR' : colors = ['black','blue','grey','red','white','yellow']; break;
		case '22 Oz Vinyl' : colors = ['black','blue','green','grey','red','white']; break;
        case '40 Oz Vinyl' : colors = ['black']; break; 
        case '50 Oz Vinyl' : colors = ['black','grey','white']; break;
		case 'Welding Curtain' : colors = ['green','red','yellow']; break;
		case 'Clear' : colors = ['clear']; break;
		case 'Reinforced Clear' : colors = ['clear']; break;
		case 'Insulated' : colors = ['black','blue','brown','green','grey','maroon','orange','red','tan','white','yellow']; break;
		case '90% Super HD Mesh' : colors = ['black']; break;
		case '90% Premium Mesh' : colors = ['black','green']; break;
		case '70% Economy Mesh' : colors = ['black','blue','green','red']; break;
		case '60% Premium Mesh' : colors = ['black','blue','green','red','yellow','orange']; break;
        case 'Sawdust Mesh' : colors = ['white']; break;
        case 'Chip Mesh' : colors = ['white']; break;
        case 'Canvas' : colors = ['tan']; break;
        case 'Canvas Untreated' : colors = ['white']; break;
        case 'Canvas FR' : colors = ['tan']; break;
      	default:
        	clear_message = false;
	}

	for (i=0; i < colors.length; i++) {
		light(radio_button[colors[i]]);
	}
	// If there is only one option, set it 
	if (colors.length == 1) {radio_button[colors[0]].checked = true; }
	// 
	// If the currently selected radio button is hidden, clear it.
	var set_colour = 'white';
	for (var i=0, iLen=radios.length; i<iLen; i++) {
		  if (radios[i].checked == true &&  radios[i].disabled == true) {
			  radios[i].checked= false;
			  break;
		  }
		  if (radios[i].checked) {
			  var set_colour = radios[i].value;
			  break;
		  }
	}
	switch (set_colour) {
	  case 'white': set_colour = '#FEFCFF'; break;
	  case 'clear': set_colour = 'white'; break;
	  case 'green': set_colour = '#0f9787'; break;
	  case 'brown': set_colour = '#625147'; break;
	  case 'blue': set_colour = '#3269cc'; break;
	  case 'grey': set_colour = '#acb9c2'; break;
	  case 'yellow': set_colour = '#f3bc19'; break;
	  case 'orange': set_colour = '#ed3d27'; break;
	  case 'red': set_colour = '#dd2a26'; break;
	}
        //
        // Make other colours more true to actual tarp colours
    var colour_cell = document.getElementById('show_colour');
    var get_locale_label =  document.getElementById('tarp_colour_fieldset');
    var message = clear_message ? '&nbsp;' : 'Choose material to see available '+get_locale_label.firstChild.innerText+'s';
	colour_cell.innerHTML = message;
	colour_cell.setAttribute("style","width:5em;border:solid 1px black; background-color:"+set_colour);

}
function light(elem){
	 elem.disabled = false;
	 label = document.getElementById(elem.id+'_suffix');
	 label.setAttribute("style",'color:black;');
}
function roundNumber(num, dec) {
  return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
}


//custom }
</script>
<?php 
    
    endif;
    return false;
}
