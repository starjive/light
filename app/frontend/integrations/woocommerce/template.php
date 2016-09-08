<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Related Products
 * Replace the default related products function with our own which displays the correct number of product columns
 *
 * @since	1.0
 */
if (!function_exists('sf_woocommerce_output_related_products')) {
	function sf_woocommerce_output_related_products() {
		$products_max 	= 4;
		$products_cols 	= 4;
		$args = apply_filters( 'sf_related_products_args', array(
			'posts_per_page' => $products_max,
			'columns'        => $products_cols,
		) );
		return $args;
	}
}

if ( ! function_exists( 'loop_columns' ) ) {
	// Change columns in product loop to 4
	function loop_columns() {
		return 4;
	}
}

/**
 * Before Content
 * Wraps all WooCommerce content in wrappers which match the theme markup
 *
 * @since	1.0
 * @return  void
 * @uses  	sf_content_before(), sf_main_before()
 */
if ( ! function_exists( 'sf_woocommerce_before_main_content' ) ) {
	function sf_woocommerce_before_main_content() {
	?>
		<!-- #content Starts -->
		<?php sf_content_before(); ?>
	    <div id="content" class="col-full">

	    	<div id="main-sidebar-container">

	            <!-- #main Starts -->
	            <?php sf_main_before(); ?>
	            <section id="main" class="col-left">
	    <?php
	}
}

/**
 * After Content
 * Closes the wrapping divs
 *
 * @since	1.0
 * @return  void
 * @uses    sf_main_after(), sf_content_after()
 */
if ( ! function_exists( 'sf_woocommerce_after_main_content' ) ) {
	function sf_woocommerce_after_main_content() {
	?>
				</section><!-- /#main -->
	            <?php sf_main_after(); ?>

			</div><!-- /#main-sidebar-container -->

			<?php get_sidebar( 'alt' ); ?>

	    </div><!-- /#content -->
		<?php sf_content_after(); ?>
	    <?php
	}
}

/**
 * Breadcrumbs
 * Remove default breadcrumbs
 *
 * @since	1.0
 * @return  void
 * @uses    sf_breadcrumbs()
 */
if ( ! function_exists( 'woocommercesf_breadcrumb' ) ) {
	function woocommercesf_breadcrumb() {
		global  $sf_options;
		if ( isset($sf_options['sf_breadcrumbs_show']) && $sf_options['sf_breadcrumbs_show'] == 'true' ) {
			sf_breadcrumbs();
		}
	}
}

/**
 * WooCommerce Pagination
 * Replaces WooCommerce pagination with the function in the Framework
 *
 * @uses  woocommercesf_add_search_fragment()
 * @uses  sf_pagination()
 * @since	1.0
 * @return  void
 */
if ( ! function_exists( 'sf_woocommerce_after_main_content' ) ) {
	function sf_woocommerce_after_main_content() {
		if ( is_search() && is_post_type_archive() ) {
			add_filter( 'sf_pagination_args', 'woocommercesf_add_search_fragment', 10 );
		}
		sf_pagenav();
	}
}

/**
 * Search Fragment
 *
 * @param  array $settings Fragments
 * @return array           Fragments
 * @since	1.0
 */
if ( ! function_exists( 'woocommercesf_add_search_fragment' ) ) {
	function woocommercesf_add_search_fragment ( $settings ) {
		$settings['add_fragment'] = '&post_type=product';
		return $settings;
	} // End woocommercesf_add_search_fragment()
}

/**
 * Search Widget
 * Customize output of search widget
 *
 * @since	1.0
 * @return  string			Search Form
 */
if ( ! function_exists( 'sf_custom_wc_search' ) ) {
	function sf_custom_wc_search( $form ) {

		$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
			<div>
				<label class="screen-reader-text" for="s">' . __( 'Search for:', 'woocommerce' ) . '</label>
				<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Search', 'sfwp-locale' ) . '" />
				<button type="submit" id="searchsubmit" class="fa fa-search submit" name="submit" value="' . __( 'Search', 'sfwp-locale' ) . '"></button>
				<input type="hidden" name="post_type" value="product" />
			</div>
		</form>';

		return $form;

	} // End sf_custom_wc_search()
}

/**
 * Optionally display a header cart link next to the navigation menu.
 *
 * @since	1.0
 * @return	void
 */
if ( ! function_exists( 'sf_nav_cart_contents_link' ) ) {
function sf_nav_cart_contents_link () {
	$settings = array( 'header_cart_total' => 'false' );
	$settings = sf_get_dynamic_values( $settings );
?>
	<a class="cart-contents" href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'sfwp-locale' ); ?>">
		<?php if ( 'true' == $settings['header_cart_total'] ) { ?>
		<span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'sfwp-locale' ), WC()->cart->get_cart_contents_count() ) );?></span> - <?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?>
		<?php } ?>
	</a>
<?php
} // End sf_nav_cart_contents_link()
}

if ( ! function_exists( 'sf_add_nav_cart_link' ) ) {
function sf_add_nav_cart_link () {
	global $woocommerce;
	$settings = array( 'header_cart_link' => 'false', 'nav_rss' => 'false', 'header_cart_total' => 'false' );
	$settings = sf_get_dynamic_values( $settings );

	$class = 'cart fr';
	if ( 'false' == $settings['nav_rss'] ) { $class .= ' no-rss-link'; }
	if ( is_woocommerce_activated() && 'true' == $settings['header_cart_link'] ) { ?>
    	<ul class="<?php echo esc_attr( $class ); ?>">
    		<li>
    			<?php sf_nav_cart_contents_link(); ?>
    			<ul>
	    			<li><?php the_widget( 'WC_Widget_Cart', 'title=' ); ?></li>
				</ul>
    		</li>
   		</ul>
    <?php }
} // End sf_add_nav_cart_link()
}

/**
 * Inserts HTML5 shiv
 *
 * @since	1.0
 * @return	void
 */
if ( ! function_exists( 'woocommerce_html5' ) ) {
	function woocommerce_html5() {
		echo '<!--[if lt IE 9]><script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->';
	}
}

/**
 * If theme lightbox is enabled, disable the WooCommerce lightbox and make product images prettyPhoto galleries
 *
 * @since	1.0
 * @return	void
 */
function woocommerce_prettyphoto() {
	global $sf_options;
	if ( isset($sf_options[ 'sf_enable_lightbox' ]) && $sf_options[ 'sf_enable_lightbox' ] == "true" && is_product() ) {
		?>
			<script>
				jQuery(document).ready(function(){
					jQuery('.images a').attr('rel', 'prettyPhoto[product-gallery]');
				});
			</script>
		<?php
	}
}

/**
 * Star Rating (Sidebar)
 *
 * @since	1.0
 * @return	void
 */
if ( ! function_exists( 'woostore_star_sidebar' ) ) {
	function woostore_star_sidebar() {
		return 12;
	}
}

/**
 * Adjust the star rating in the recent reviews
 *
 * @since	1.0
 * @return	void
 */
if ( ! function_exists( 'woostore_star_reviews' ) ) {
	function woostore_star_reviews() {
		return 12;
	}
}

/**
 * Changes default image placeholder
 *
 * @since	1.0
 * @return	void
 */
if ( ! function_exists( 'sf_wc_placeholder_img_src' ) ) {
	function sf_wc_placeholder_img_src( $src ) {
		$settings = array( 'placeholder_url' => get_template_directory_uri() . '/app/backend/assets/images/placeholder.png' );
		$settings = sf_get_dynamic_values( $settings );

		if( $settings['placeholder_url'] == '' ) {
			$settings['placeholder_url'] = $src;
		}

		return esc_url( $settings['placeholder_url'] );
	} // End sf_wc_placeholder_img_src()
}