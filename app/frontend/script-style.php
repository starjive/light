<?php

	/**
	 * Theme Frontend JavaScript.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'sf_add_javascript' ); }
	
	if ( ! function_exists( 'sf_add_javascript' ) ) {
		function sf_add_javascript() {
		
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
			wp_enqueue_script( 'third-party', get_template_directory_uri() . '/app/frontend/assets/js/third-party' . $suffix . '.js', array( 'jquery' ) );
			//wp_register_script( 'flexslider', get_template_directory_uri() . '/app/frontend/assets/js/jquery.flexslider' . $suffix . '.js', array( 'jquery' ) ); - deprecated for cdn
			wp_register_script( 'flexslider', '//cdnjs.cloudflare.com/ajax/libs/flexslider/2.6.1/jquery.flexslider' . $suffix . '.js', array( 'jquery' ) );
			//wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/app/frontend/assets/js/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ) ); - deprecated for cdn
			wp_register_script( 'prettyPhoto', '//cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/js/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ) );
			wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/app/frontend/assets/js/modernizr' . $suffix . '.js', array( 'jquery' ), '2.6.2' );
	
			// Conditionally load the Slider JavaScript, where needed.
			$load_slider_js = false;
	
			if (
				( get_option( 'sf_slider_magazine' ) == 'true' && is_page_template( 'templates/template-magazine.php' ) ) ||
				( get_option( 'sf_slider_biz' ) == 'true' && is_page_template( 'templates/template-business.php' ) ) ||
				is_page_template( 'templates/template-widgets.php' )
			) {
				$load_slider_js = true;
			}

			if ( is_page_template( 'templates/template-contact-form.php' ) ) {
				$google_maps_api_key = get_option( 'sf_maps_api_key' );
				wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '', array(), '1.60818', true );
			}
	
			// Allow child themes/plugins to load the slider JavaScript when they need it.
			$load_slider_js = apply_filters( 'sf_load_slider_js', $load_slider_js );
	
			if ( $load_slider_js ) { wp_enqueue_script( 'flexslider' ); }
	
			do_action( 'sf_add_javascript' );
	
			wp_enqueue_script( 'general', get_template_directory_uri() . '/app/frontend/assets/js/general' . $suffix . '.js', array( 'jquery', 'third-party' ) );
	
		}
	}


	/**
	 * Theme Frontend CSS.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! is_admin() ) { add_action( 'wp_print_styles', 'sf_add_css' ); }
	
	if ( ! function_exists( 'sf_add_css' ) ) {
		function sf_add_css() {
			global $sf_options;
	
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	
			//wp_register_style( 'prettyPhoto', get_template_directory_uri() . '/app/frontend/assets/css/vendor/prettyphoto/prettyPhoto.css' ); - deprecated for cdn
			wp_register_style( 'prettyPhoto', '//cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/css/prettyPhoto' . $suffix . '.css', '3.1.6' );
			wp_register_style( 'non-responsive', get_template_directory_uri() . '/app/css/non-responsive.css' );
	
			// Disable prettyPhoto css if WooCommerce is activated and user is on the product page
			$woocommerce_activated 	= is_woocommerce_activated();
			$woocommerce_lightbox	= get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;
			$woocommerce_product 	= false;
			if ( $woocommerce_activated ) {
				$woocommerce_product = is_product();
			}
	
			if ( $woocommerce_activated && $woocommerce_product && $woocommerce_lightbox ) {
				wp_deregister_style( 'prettyPhoto' );
			}
	
			do_action( 'sf_add_css' );
		}
	}


	/**
	 * Theme Admin JavaScript.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( is_admin() ) { add_action( 'admin_print_scripts', 'sf_add_admin_javascript' ); }
	
	if ( ! function_exists( 'sf_add_admin_javascript' ) ) {
		function sf_add_admin_javascript() {
			global $pagenow;
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && ( get_post_type() == 'page' ) ) {
				wp_enqueue_script( 'sf-postmeta-options-custom-toggle', get_template_directory_uri() . '/app/frontend/assets/js/meta-options-custom-toggle' . $suffix . '.js', array( 'jquery' ), '1.0.0' );
			}
		}
	}


	/**
	 * Enqueue Javascript postMessage handlers for the Customizer.
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since	1.0
	 * @return	void
	 */
	add_action( 'customize_preview_init', 'sf_customize_preview_js' );
	
	if ( ! function_exists( 'sf_customize_preview_js' ) ) {
		function sf_customize_preview_js() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'sf-customizer', get_template_directory_uri() . '/app/frontend/assets/js/theme-customizer' . $suffix . '.js', array( 'customize-preview' ), '20140801', true );
		}
	}

?>