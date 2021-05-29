<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Callback for help content
 * tarpbuilder_admin_help_content_cb
 */
//add_action( 'tarpbuilder_help_content', 'tarpbuilder_render_admin_help_content' );
function tarpbuilder_admin_help_content_field()
{
    $stylaa = "text-input";
    ob_start();
    echo '
    <h4>'. esc_html__( 'Help and Information', 'tarpbuilder' ) .'</h4>
    <h5>'. esc_html__( 'General Notes', 'tarpbuilder' ) . '</h5>
    <p>'. esc_html__( 'To make a Variable Product you are required to only allow one product to be added to customer cart at a time to avoid the calculations being incorrect in Checkout totals. When creating a Variable Product you must tick the box ', 'tarpbuilder' ) 
    . '<em>' . esc_html( 'Enable this to only allow one of this item to be bought in a single order ', 'tarpbuilder' ) . '</em><sup>i1</sup>' . esc_html( 'that is in the Inventory tab of the products editor.', 'tarpbuilder' ) 
    . '<h5>'. esc_html__( 'Notations Guide', 'tarpbuilder' ) . '</h5><dl>
    <dt>'. esc_html__( '1. Label for Checkout Field', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( ' also appears on the customers order invoice and email.', 'tarpbuilder' ) .'</dd>
    <dt>'. esc_html__( '2. Admin Order Text', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'What the administrator text will be on the Orders page or Woocommerce Orders.', 'tarpbuilder' ) .'</dd>
    <dt>'. esc_html__( '3. Display Increment', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'Text to display on the product page. Text will show above the add-to-cart button to the left of the quantity field. Could be Days, Hours, Tours....', 'tarpbuilder' ) .'</dd>
    <dt>'. esc_html__( '4. Text Before Begin Date', 'tarpbuilder' ) . '</dt>
    <dd>'. esc_html__( 'Leaving this field blank in the admin Configuration and Settings page will force the title to not be displayed at all, unless the Product editor page has a value. Leaving both fields on both pages blank will result in the DatePicker not being displayed or saved.', 'tarpbuilder' ) . '</dd>
    <dt>'. esc_html__( '5. Style', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'Styling should be done as if you were writing style properties between the inline style element. For single-product page use', 'tarpbuilder' ) . '<code>'. esc_attr( $stylaa ) .'</code>'. esc_html__( 'class selector styles.', 'tarpbuilder'  ) .'</dd>
    <dt>'. esc_html__( '6. Tax Options', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'This adjust the Additional Fee tax rate only - not the product tax rate.', 'tarpbuilder' ) .'</dd>
    <dd>'. esc_html__( 'Choices are: standard | reduced | zero. See Woocommerce Settings to set taxes', 'tarpbuilder' ) .'</dd>
    <dt>'. esc_html__( '7. Zero Entry', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'Check box to allow customers to press the Add-To-Cart button WITHOUT requiring them to select a duration from the tarpbuilder quantity field. This gives the option to add rental price of product as the same price of the rental per day price.', 'tarpbuilder' ) .'</dd>
    <dt>'. esc_html__( '8. Allow Product Price to Match Fee', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'Check to have product price the same as increment. You still must add fees price to product data. All this really does is count the number of increments the person selected in the product page and subtracts one (day) from the increments so the first (day) is the price of the product.', 'tarpbuilder' ) .'</small></dd>
    <dt>'. esc_html__( '9. Remove Unitizr styles that affect Woocommerce', 'tarpbuilder' ) .'</dt>
    <dd>'. esc_html__( 'Check to have the default Unitizr plugin styles that control the Woocommerce text and number fields removed. This is helpful for themes that over-ride Woocommerce styles or for situations where Unitizr may be inteferring with Woocommerce design or layout issues.', 'tarpbuilder' ) .'</small></dd>
    </dl>
    <div class="below-instr">
    <p>&nbsp;</p>
    <sub>i1</sub> <img src="' . esc_url( TARPBUILDER_PLUS_URL . '/tarpbuilder/include/admin/inventory-tab.png') .'" alt="Inventory tab in products editor" height="172"/><hr>';
    ?>
    <h4><?php esc_html_e( 'Instructions: ', 'tarpbuilder' ); ?></h4>
    <pre>
1. Only products with the Category of "custom-tarps" will display this plugin options.
2. You must create Attributes Terms of 
    * attribute_pa_custom-tarp (final price on tarp caclulated by price per sq.ft.)
    * attribute_pa_custom-tarp-width
    * attribute_pa_custom-tarp-length
3. You must create a Custom Field in the product of
    * Center to center spacing (used for adding textarea with grommet spacing context)

- build product using category "custom tarps"
- add attributes Material, Color, Hemmed, Custom Tarp Dimensions, Trison Custom Tarps
- only use for variations Material, Color and Hemmed

</pre>    
    </div>
    
<?php 
        $htmls = ob_get_clean();
        echo $htmls;
}