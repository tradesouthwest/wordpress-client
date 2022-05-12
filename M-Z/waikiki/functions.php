
/** Added by Larry @codeable May 2022 
 * Backup at line 76 +
 */
add_action( 'wp_enqueue_scripts', 'mobile_child_register_stylesheet', 999 );
function mobile_child_register_stylesheet() {

    $ver = time();
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array(),
        $ver
    );
}
include( 'functions-extend.php' );
