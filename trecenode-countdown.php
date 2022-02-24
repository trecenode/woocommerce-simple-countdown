<?php
/**
 * Plugin Name: Simple Sale Countdown by 13Node
 * Plugin URI:  https://13node.com/informatica/wordpress/simple-woocommerce-countdown-plugin/
 * Description: Show time left till the sale ends.
 * Version: 1.3
 * Author: Danilo Ulloa
 * Author URI: https://13node.com
 * Text Domain: simple-sale-countdown-by-13node
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'TRECE_TEXT_DOMAIN' ) ) {
	define( 'TRECE_TEXT_DOMAIN', 'simple-sale-countdown-by-13node' );
}
add_action( 'after_setup_theme', 'trece_language_setup' );
function trece_language_setup(){
	load_plugin_textdomain( TRECE_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_filter( 'woocommerce_get_price_html', 'trece_price_html', 100, 2);
add_filter( 'woocommerce_cart_item_price', 'trece_price_html', 100, 2);
function trece_price_html( $price, $product )
{
    global $post;
    $sales_price_to = get_post_meta($post->ID, '_sale_price_dates_to', true);
    if($sales_price_to != "")
    {
        $sales_price_date_to = date("Y/m/d", $sales_price_to);
		$today = date("Y/m/d");
		if ($today < $sales_price_date_to)
		{
			return $price.'<br />
				<div class="timer" style="background-color:#eff1f2; text-align:center; font-size:14px;">
					<i class="fa fa-clock-o fa-spin" aria-hidden="true"></i> '.__("Sale ends &hellip;", "simple-sale-countdown-by-13node").'<br />
					<div data-countdown="'.$sales_price_date_to.'"></div>
				</div>
			';
		} else {
			return $price;
		}
        
    }
    else
    {
        return apply_filters( 'woocommerce_get_price', $price );
    }
}
/* Put JS in the footer and CSS in the header */
add_action('wp_footer', 'trece_add_script_wp_footer');
add_action('wp_header', 'trece_add_css_wp_footer');
function trece_add_css_wp_footer(){
?>
    <style>
		.timer {
			margin:auto;
		}
		.price {
			max-width: 250px;
			min-width: 150px!important;
			text-align: center;
		}
</style>
<?php
}
add_action('wp_enqueue_scripts','trece_js_init');
function trece_js_init() {
	wp_register_script( 'trece-countdown', plugins_url().'/simple-sale-countdown-by-13node/js/jquery.countdown.min.js', array('jquery'), '2.2.0', true);
	wp_enqueue_script( 'trece-countdown' );
}
function trece_add_script_wp_footer() {
?>
	<script>
		jQuery(document).ready(function($){
			$('[data-countdown]').each(function() {
			var $this = $(this), finalDate = $(this).data('countdown');
			$this.countdown(finalDate, function(event) {
				$this.html(event.strftime('%D d. %H:%M:%S'));
			});
			});
		});
	</script>
<?php
}
