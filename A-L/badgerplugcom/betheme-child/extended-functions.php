<?php 
/**
 * Exclusions for quote cart button
 * @uses   no value - only to validate the function exists
 *
 * @see    https://developer.wordpress.org/reference/functions/is_page/
 * @author Larry @codeable
 */ 
function betheme_child_request_aquote_echo()
{   
    return false;   
}

/**
 * Get product attributes to display on yith-quote table
 * @param string $attribute_name Taxonomy name
 * @param string $product_id     Global value
 *
 * @return array                 Attribute value
 */
function betheme_child_get_product_terms_carton( $attribute_name, $product_id )
{

    $object_id = wc_attribute_taxonomy_id_by_name($attribute_name); 

    $attribute_key = wc_attribute_taxonomy_name($attribute_name);
    $product_terms = wc_get_product_terms($product_id, $attribute_key, array('fields' => 'names'));

     // check if not empty, then display
     if (!empty($product_terms)) {

         $attribute = array_shift($product_terms);
         return $attribute;
     } else {

         // no attribute under this name
         return '';
     }
} 
