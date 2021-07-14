 <div style="display:block;width:100%;height:800px;background:#eee; color:#000;">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" value="<?php echo $rand; ?>" name="randcheck" />
            <p><input type="text" name="test" value="<?php echo $_POST['test']; ?>"></p>
            <p><input type="submit" name="submit" value="test"></p>
        </form>
        <hr>
        <?php 

        
    if(isset($_POST['submit']) ){
            
            $test = $_POST['test'];
            $array = array( 'HB33', 'JD99-99', 'HD25', 'LA77-31', '7H2B' );
            //$array = explode(",", $arr );
            if ( is_array( $array ) ) {            
        // find any value before a white space
        $substring = substr($test, 0, strpos($test, ' '));
                if ( $substring ) {
            
                    echo 'found' . ' ' . $substring . '<br>';
                    if ( in_array( $substring, $array ) ) {
                        echo 'in array';
                    } else {
                        echo 'not in array';
                    }

                } else {
                        echo 'only found string without whitespace';
                }
                
            
                } else {
                    echo 'not an array';
            }
            echo '<p>This is the array we are looking through</p>';
        print_r($array);
        }
        
        ?>
        <p>test results</p>
        <p>test results</p>
        
        
    </div>
