<?php 
<?php echo var_export(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']))); ?></div>
    
    <?php require_once('geoplugin.class.php');
    
    $geoplugin = new geoPlugin(); 
    $geoplugin->locate(); 
    echo 'City: ' . $geoplugin->city; 
?><?php



global $post, $current_user;
$data = unserialize(get_post_meta($post->ID, '_list', true));
if( count($data) != 0 ) {
    if ( !in_array( $current_user->ID, $data ) ) {
        $data[] = $current_user->ID;
    }
    $data = array_unique($data); // remove duplicates
    sort( $data ); // sort array
    //$data = serialize($data);
    update_post_meta($post->ID, '_list', $data);
} else {
    $data = array();
    $data[0] = $current_user->ID;
    //$data = serialize($data);
    update_post_meta($post->ID, '_list', $data);  
}
// ------------------------ https://wpengineer.com/968/wordpress-working-with-options/
$myOptions = get_option('MyOptions');
foreach($myOptions as $option => $value) {
    echo $option . " => " . $value . "<br />";
}
/* Output:
firstOption => 2
secondOption => my second Option
*/
 
echo $myOptions['firstOption'];
/* Output:
2
*/
// echo var_export(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR'])));
