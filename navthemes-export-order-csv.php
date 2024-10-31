<?php
/**
 */
/*
Plugin Name: NavThemes - Export Single Order Data Woo-commerce
Description: This Plugin Simply Downloads All Data of an Order for shipping Purposes such as Seller Cloud.
Author: NavThemes
Version: 1.0
Author URI: https://navthemes.com
Plugin URI :https://www.navthemes.com/wp-plugins/
*/
 
/*
* Add Meta Box Here
*/

  /** Register Meta Box Here **/

if(!function_exists('add_navthemes_download_csv_meta_box')) :

function add_navthemes_download_csv_meta_box() {
    add_meta_box(
        'navthemes_download_csv', // $id
        'Export Order Details', // $title
        'navthemes_download_csv_meta_box', // $callback
        'shop_order', // $screen
        'normal', // $context
        'high' // $priority
    );
}
add_action( 'add_meta_boxes', 'add_navthemes_download_csv_meta_box' );


// function does not exists
endif; 

    
     /** Meta Box View Here **/

if(!function_exists('navthemes_download_csv_meta_box')) : 

function navthemes_download_csv_meta_box() { ?>

   <?php
        global $wpdb, $post;
        $download_csv_url = admin_url('admin-post.php?orderid='.$post->ID.'&action=download_csv' );
    ?>

<a class="button save_order button-primary" href="<?php echo $download_csv_url; ?>"> Download </a>

<?php }

endif;

/*
* Download CSV Function
*/

if(!function_exists('navthemes_download_csv')) :

add_action( 'admin_post_download_csv', 'navthemes_download_csv' ); 

function navthemes_download_csv() {

    // This is our order id
    $orderid = intval( $_REQUEST['orderid'] );
    
    // This is how we fetch whole data
    $data =  wc_get_order($orderid);
    
    //print_r($data->get_data());

    $order_data = $data->get_data();

    $line1 = array();
    $line2 = array();


    // Lets frame order details here 
    foreach ($order_data as $key => $value) {

        // Only if not emplty
        if(!empty($value)) {

        $parent_key = $key;


        // Only if not meta_data
        if($key!='meta_data' && $key!='number'):

        // if its array or object lets process inside elements too    
        if( ( is_array($value) || is_object($value) ) ){

            foreach ($value as $key => $value) {
                    
                    $key = $parent_key."_".$key;        

                    array_push($line1, $key);
                    array_push($line2, $value);
                
            }
        }

        // If not its just String, Process it Simply
        else{
                array_push($line1, $key);
                array_push($line2, $value);
        }

    endif;


        } // Not empty

    }


    /*
    Let Create CSV Here.
    */

    $filename = 'order-'.$orderid.'.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    header("Cache-Control: no-store, no-cache");

    $df = fopen('php://output', 'w');

    fputcsv($df, $line1);
    fputcsv($df, $line2);

    exit;
    wp_die();    
   //Since now we have order data in array lets frame an CSV using it and then we will pass that to generate CSV and we good.   
    

}

endif;
 


?>
