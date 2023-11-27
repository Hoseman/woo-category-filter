<?php
/*
Plugin Name: Woo Category Filter
Description: A category filter for WooCommerce.
Version: 1.0
Author: Andrew Hosegood
*/

// Function to enqueue stylesheets
function ah65_category_filter_enqueue_styles() {
   // Enqueue your stylesheet
   wp_enqueue_style('ah65_category_enqueue_styles', plugin_dir_url(__FILE__) . 'css/style.css');
   wp_enqueue_script('ah65_category_filter_script', plugin_dir_url(__FILE__) . 'js/scripts.js', array('jquery'), '1.0', true);

   // Pass the Ajax URL to script.js
   wp_localize_script('ah65_category_filter_script', 'ah65_category_filter_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}

// Hook into WordPress enqueue scripts action
add_action('wp_enqueue_scripts', 'ah65_category_filter_enqueue_styles');




function ah_woo_category_filter() {

       $output = '
        <h3 class="category-filter__heading">Filter by Product</h3>

        <form id="category-filter-form">

            <label class="category-filter__label" for="all-radio">
               <input class="category-filter" type="radio" id="all-radio" name="category_filter[]" value="all" checked> View All
            </label>

            <label class="category-filter__label" for="clothing-radio">
                  <input class="category-filter" type="radio" id="clothing-radio" name="category_filter[]" value="clothing"> Clothing
            </label>

            <label class="category-filter__label" for="tshirts-radio">
               <input class="category-filter" type="radio" id="tshirts-radio" name="category_filter[]" value="tshirts"> T-shirts
            </label>

            <label class="category-filter__label" for="accessories-radio">
                <input class="category-filter" type="radio" id="accessories-radio" name="category_filter[]" value="accessories"> Accessories
            </label>

            <label class="category-filter__label" for="hoodies-radio">
               <input class="category-filter" type="radio" id="hoodies-radio" name="category_filter[]" value="hoodies"> Hoodies
            </label>

            <br>

            <h4 class="color-filter__heading">Filter by Color</h4>

            <label class="category-filter__label" for="color-red">
                <input type="radio" id="color-red" name="color_filter" value="red"> Red
            </label>

            <label class="category-filter__label" for="color-blue">
                <input type="radio" id="color-blue" name="color_filter" value="blue"> Blue
            </label>

            <label class="category-filter__label" for="color-green">
               <input type="radio" id="color-green" name="color_filter" value="green"> Green
            </label>

            <!-- Add more color options as needed -->

            <br>


            <h4 class="price-filter__heading">Filter by Price</h4>
                <label id="min-price-label" class="price-filter__label" for="price-range">Min Price (0):</label><br>
                <input class="category-filter__input" type="range" id="min-price" name="min_price" min="0" max="100" step="1" value="0"><br>
                <label id="max-price-label" class="price-filter__label" for="price-range">Max Price (100):</label><br>
                <input class="category-filter__input" type="range" id="max-price" name="max_price" min="0" max="100" step="1" value="100"> 
            
    
            <br>


            <div class="category-filter__submit">

            <input class="category-filter__btn" type="submit" value="reset" id="reset-filter">
            </div>
        </form>
    ';

   return $output;


}



// Register the shortcode
add_shortcode('woo_category_filter', 'ah_woo_category_filter');


// Ajax handler for filtering products
function ah65_category_filter_ajax_handler() {
   $selected_category = isset($_POST['selected_category']) ? sanitize_text_field($_POST['selected_category']) : '';
   $selected_color = isset($_POST['selected_color']) ? sanitize_text_field($_POST['selected_color']) : '';
   $selected_min_price = isset($_POST['selected_min_price']) ? floatval($_POST['selected_min_price']) : 0;
   $selected_max_price = isset($_POST['selected_max_price']) ? floatval($_POST['selected_max_price']) : PHP_FLOAT_MAX;


if ($selected_color === '') {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1, // -1 to retrieve all posts
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $selected_category,
                ),
            ),
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_price',
                    'value'   => array( $selected_min_price, $selected_max_price ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                )
            ),
        );
   } else {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1, // -1 to retrieve all posts
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $selected_category,
                ),
                array(
                    'taxonomy' => 'pa_color',
                    'field'    => 'slug',
                    'terms'    => $selected_color,
                ),
            ),
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_price',
                    'value'   => array( $selected_min_price, $selected_max_price ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                )
            ),
        );
   }
   


   $query = new WP_Query($args);

   // Output the results
   if ($query->have_posts()) {
      echo '<div class="database__spinner overlay hidden"></div>';
       while ($query->have_posts()) {
           $query->the_post();
           global $product;
            
           wc_get_template_part('content', 'product');
       }
   } else {
       echo 'No products found.';
   }

   wp_reset_postdata();

   // Always die in functions echoing ajax content
   die();
}

// Hook for the Ajax action
add_action('wp_ajax_ah65_category_filter', 'ah65_category_filter_ajax_handler');
add_action('wp_ajax_nopriv_ah65_category_filter', 'ah65_category_filter_ajax_handler');


?>