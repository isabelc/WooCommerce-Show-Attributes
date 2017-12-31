<?php
/*
Plugin Name: WooCommerce Show Attributes
Plugin URI: https://isabelcastillo.com/docs/woocommerce-show-attributes
Description: Show WooCommerce custom product attributes on the Product, Shop and Cart pages, admin Order Details page and emails.
Version: 1.6.4
Author: Isabel Castillo
Author URI: https://isabelcastillo.com
License: GPL2
Text Domain: woocommerce-show-attributes
Domain Path: languages

Copyright 2014-2018 Isabel Castillo

WooCommerce Show Attributes is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

WooCommerce Show Attributes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNSES FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WooCommerce Show Attributes; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
if ( ! defined( 'ABSPATH' ) ) exit;
class WooCommerce_Show_Attributes {
	private static $instance = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'show_atts_on_product_page' ), 25 );
		add_filter( 'woocommerce_product_tabs', array( $this, 'additional_info_tab' ), 98 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'show_atts_on_cart' ), 10, 3 );
		add_filter( 'woocommerce_order_item_name', array( $this, 'show_atts_on_customer_order' ), 99, 2 );
		add_filter( 'woocommerce_order_item_name', array( $this, 'show_admin_new_order_email' ), 99, 2 );
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'show_atts_in_admin_order'), 10, 3 );
		add_action( 'woocommerce_admin_order_item_headers', array( $this, 'admin_order_item_header' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
		add_action( 'init', array( $this, 'if_show_atts_on_shop' ) );
		add_action( 'woocommerce_grouped_product_list_before_price', array( $this, 'show_atts_grouped_product' ) );
		add_filter( 'woocommerce_email_styles', array( $this, 'email_style' ) );

	}

	/**
	* Load plugin's textdomain
	*/
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-show-attributes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Get the non-variation attributes of product.
	 *
	 * @param WC_Product_Simple|WC_Product_Variation $product Product instance.
	 * @param mixed $single_product true when on single product page
	 * @return array of attributes, each with label and value
	 * @since 1.6.4
	 */
	private function get_attributes( $product, $single_product ) {
		$out = array();

		// If this is a Product_Variation object, the non-variation attributes are not returned with $product->get_attributes().
		// So, we get the non-variation attributes with get_post_meta.

		if ( is_a( $product, 'WC_Product_Variation' ) ) {

			$attributes = get_post_meta( $product->get_parent_id(), '_product_attributes', true );

			if ( ! $attributes ) {
				return $out;
			}

			foreach ( $attributes as $attribute ) {

				if ( $attribute['is_visible'] && empty( $attribute['is_variation'] ) ) {

					if ( $attribute['is_taxonomy'] ) {

						$out[] = $this->get_global_taxonomy_attribute_data( $attribute['name'], $product->get_parent_id(), $single_product );

					} else {

						$out[] = array(
									'label' => $attribute['name'],
									'value' => $attribute['value']
						);
					}

				}
			}

		} else {

			$attributes = $product->get_attributes();
			if ( ! $attributes ) {
				return;
			}

			foreach ( $attributes as $attribute ) {

				// skip variations
				if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
					continue;
				}
				if ( $attribute->get_variation() ) {
					continue;
				}

				// honor the visibility setting
				if ( ! $attribute->get_visible() ) {
					continue;
				}

				$name = $attribute->get_name();

				if ( $attribute->is_taxonomy() ) {

					// global attributes

					$out[] = $this->get_global_taxonomy_attribute_data( $name, $product, $single_product );
				} else {

					// custom, non-global attributes

					$out[] = array(
								'label' => $name,
								'value' => esc_html( implode( ', ', $attribute->get_options() ) )
					);

				}

			} // end foreach attributes

		}

		return $out;

	}

	/**
	* Returns the HTML string for the product attributes.
	* This does not affect nor include attributes which are used for Variations.
	*
	* @param WC_Product_Simple|WC_Product_Variation $product Product instance. Default null to avoid errors.
	* @param string $element HTML element to wrap each attribute with, accepts span or li.
	* @param boolean $show_weight whether to show the product weight
	* @param boolean $show_dimensions whether to show the product dimensions
	* @param boolean $skip_atts whether to skip the attributes and only honor weight and dimensions
	* @param mixed $single_product true when on single product page
	*/
	public function the_attributes( $product = null, $element, $show_weight = null, $show_dimensions = null, $skip_atts = null, $single_product = null ) {

		$out = '';
		$out_middle = '';

		if ( isset( $product ) ) {
			if ( is_object( $product ) ) {

				if ( $show_weight ) {
					if ( $product->has_weight() ) {
						$weight = $product->get_weight();
						$unit = esc_attr( get_option( 'woocommerce_weight_unit' ) );
					}
				}

				if ( $show_dimensions ) {
					if ( $product->has_dimensions() ) {
						$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );
					}
				}

		   		$colon = get_option( 'wcsa_remove_semicolon' ) == 'yes' ? ' ' : ': ';
				$hide_labels = get_option( 'woocommerce_show_attributes_hide_labels' );
				// check if they choose span element over li
				if ( get_option( 'woocommerce_show_attributes_span' ) == 'yes' ) {
					$element = 'span';
				}

		   		if ( ! $skip_atts ) {

					$attributes = $this->get_attributes( $product, $single_product );

					if ( ! $attributes ) {
						return;
					}

					foreach ( $attributes as $attribute ) {

						$class_string = sanitize_title( strip_tags( $attribute['value'] ) );

						$out_middle .= '<' . esc_attr( $element ) . ' class="' . esc_attr( sanitize_title( $attribute['label'] ) ) . ' ' . esc_attr( $class_string ) . '">';


						// Hide labels if they want to
						if ( 'yes' != $hide_labels ) {
								
							// PolyLang translation support
							if ( function_exists( 'pll__' ) ) {
								$translation = pll__( $attribute['label'] );
								$attribute['label'] = ( ! empty( $translation ) ) ? $translation : $attribute['label'];
							}
							// WPML translation support
							if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
								$translation = apply_filters( 'wpml_translate_single_string', $attribute['label'], 'WordPress', 'taxonomy singular name: '. $attribute['label'] );
							    $attribute['label'] = ( ! empty( $translation ) ) ? $translation : $attribute['label'];
							}										

							$out_middle .= '<span class="attribute-label"><span class="attribute-label-text">' . sprintf( __( '%s', 'woocommerce-show-attributes' ), esc_html( $attribute['label'] ) ) . '</span>' . $colon . ' </span> ';
							
						}
							
						$out_middle .= '<span class="attribute-value">';
						$out_middle .= $attribute['value'];
						$out_middle .= '</span></' . esc_attr( $element ) . '>';

						if ('span' == $element) {
							$out_middle .= '<br />';
						}
					

					} // ends foreach attribute
				}

				// Add weight and dimensions if they opted in

				if ( ! empty( $weight ) ) {
					$unit = empty( $unit ) ? '' : $unit;
					// weight
					$out_middle .= '<' . esc_attr( $element ) . ' class="show-attributes-weight">';
					// Hide labels if they want to
					if ( $hide_labels != 'yes' ) {
						$out_middle .= '<span class="attribute-label">' . __( 'Weight', 'woocommerce-show-attributes' ) . $colon . ' </span> ';
					}
					$out_middle .= '<span class="attribute-value">' . esc_html( $weight ) . ' ' . esc_html( $unit ) . ' </span></' . esc_attr( $element ) . '>';
					if ( 'span' == $element ) {
						$out_middle .= '<br />';
					}
				}

				if ( ! empty( $dimensions ) ) {
					// dimensions
					$out_middle .= '<' . esc_attr( $element ) . ' class="show-attributes-dimensions">';
					// Hide labels if they want to
					if ( $hide_labels != 'yes' ) {
						$out_middle .= '<span class="attribute-label">' . __( 'Dimensions', 'woocommerce-show-attributes' ) . $colon . ' </span> ';
					}
					$out_middle .= '<span class="attribute-value">' . esc_html( $dimensions ) . '</span></' . esc_attr( $element ) . '>';
					if ('span' == $element) {
						$out_middle .= '<br />';
					}
				}

				if ( $out_middle ) {
					$out = ('li' == $element) ? '<ul ' : '<span ';
					$out .= 'class="custom-attributes">' . $out_middle;
					$out .= ('li' == $element) ? '</ul>' : '</span>';
				}

		

			}
		}
		return $out;
	}

	/**
	 * Get the attribute label and value for a global attribute.
	 * 
	 * Global attributes are those which are stored as taxonomies and created on the Products > Attributes page.
	 *
	 * @param string $name Name of the attribute
	 * @param int|WC_Product_Simple|WC_Product_Variation $product Product id or instance.
	 * @param mixed $single_product true when on single product page
	 * @since 1.6.4
	 */
	private function get_global_taxonomy_attribute_data( $name, $product, $single_product ) {
		$out = array();

		$product_id = is_numeric( $product ) ? $product : $product->get_id();
		$terms = wp_get_post_terms( $product_id, $name, 'all' );

		if ( ! empty( $terms ) ) {
			if ( ! is_wp_error( $terms ) ) {

				// Get the label

				$tax = $terms[0]->taxonomy;
				$tax_object = get_taxonomy($tax);
				// get tax label
				if ( isset ( $tax_object->labels->singular_name ) ) {
					$out['label'] = $tax_object->labels->singular_name;
				} elseif ( isset( $tax_object->label ) ) {
					$out['label'] = $tax_object->label;
					// Trim label prefix
					$label_prefix = __( 'Product', 'woocommerce-show-attributes' ) . ' ';
					if ( 0 === strpos( $out['label'], $label_prefix ) ) {
						$out['label'] = substr( $out['label'], strlen( $label_prefix ) );
					}										
				}

				// Get the term value

				$tax_terms = array();
				foreach ( $terms as $term ) {
					$single_term = sprintf( __( '%s', 'woocommerce-show-attributes' ), esc_html( $term->name ) );

					// Show terms as links?
					if ( $single_product ) {
						if ( get_option( 'wcsa_terms_as_links' ) == 'yes' ) {
							$term_link = get_term_link( $term );
							if ( ! is_wp_error( $term_link ) ) {
								$single_term = '<a href="' . esc_url( $term_link ) . '">' . sprintf( __( '%s', 'woocommerce-show-attributes' ), esc_html( $term->name ) ) . '</a>';
							}
						}
					}

					array_push( $tax_terms, $single_term );
									
				}

				$out['value'] = implode(', ', $tax_terms);
			}
		}

		return $out;
	}

	/**
	* Show product attributes on the product page.
	*
	* Show product attributes above the Add to Cart button on the single product page
	* and on the Parent of Grouped products.
	*/

	public function show_atts_on_product_page() {
		$show_weight = null;
		if ( get_option( 'wcsa_weight_product' ) == 'yes' ) {
			$show_weight = true;
		}

		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_product' ) == 'yes' ) {
			$show_dimensions = true;
		}

		// add a flag to skip the attributes.
		// this way i'll know to only honor weight and dimensions
		if ( get_option( 'wcsa_product' ) != 'no') {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}

		global $product;
		echo wp_kses_post( $this->the_attributes( $product, 'li', $show_weight, $show_dimensions, $skip_atts, true ) );

	}

	/**
	* Show attributes on Customer emails and View Order page.
	*
	* Show product attributes below the item on the View Order page,
	* and on Order emails that go to customer.
	*
	* @param string $item_name the product title
	* @param object $item the product object
	* @since 1.4.0
	*/
	public function show_atts_on_customer_order( $item_name, $item ) {
		// Do not show this on the admin main Orders page
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! empty( $screen ) && isset( $screen->id ) && 'edit-shop_order' == $screen->id ) {
				return $item_name;
			}
		}

		$show_weight = null;
		if ( get_option( 'wcsa_weight_customer_order_emails' ) == 'yes' ) {
			$show_weight = true;
		}
		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_customer_order_emails' ) == 'yes' ) {
			$show_dimensions = true;
		}

		$out = $item_name;

		if ( get_option( 'wcsa_customer_order_emails' ) != 'no' ) {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}

		// get the name of templates used for this email call
		$templates_used = array();
		foreach ( debug_backtrace() as $called_file ) {
			foreach ( $called_file as $index ) {
				if ( is_array( $index ) ) { // avoid errors
					if ( ! empty( $index[0] ) ) {
						if ( is_string( $index[0] ) ) { // eliminate long arrays
							$templates_used[] = $index[0];
						}
					}
				}
			}
		}


		// Do not add atts to admin emails

		$is_customer_email = true;

		foreach ( $templates_used as $template_name ) {

			// check each file name for '/emails/admin-'
			if ( strpos( $template_name, '/emails/admin-' ) !== false ) {

				// admin email so do not add atts
				$is_customer_email = false;
			}
		}

		// Only add atts to customer emails, and included here is the customer View Order page.

		if ( $is_customer_email ) {
			$product = wc_get_product( $item['product_id'] );

			


			$atts = $this->the_attributes( $product, 'span', $show_weight, $show_dimensions, $skip_atts );
			if ( $atts ) {
				$out = $item_name . '<br />' . wp_kses_post( $atts ) . '<br />';
			}
		}

		return $out;
	}

	/**
	* Show product attributes on the Cart page.
	*/
	public function show_atts_on_cart( $name, $cart_item, $cart_item_key ) {
		$show_weight = null;
		if ( get_option( 'wcsa_weight_cart' ) == 'yes' ) {
			$show_weight = true;
		}
		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_cart' ) == 'yes' ) {
			$show_dimensions = true;
		}
		if ( get_option( 'wcsa_cart' ) != 'no' ) {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}

		$product = $cart_item['data'];
		$out = $name . '<br />' . wp_kses_post( $this->the_attributes( $product, 'span', $show_weight, $show_dimensions, $skip_atts ) );

		return $out;

	}

	/**
	* Show product attributes on the admin Order Details page.
	*
	* @param object, the product object
	* @param $item
	* @param integer, product id
	*/
	public function show_atts_in_admin_order( $product, $item, $item_id ) {
		$show_weight = null;
		if ( get_option( 'wcsa_weight_admin_order_details' ) == 'yes' ) {
			$show_weight = true;
		}
		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_admin_order_details' ) == 'yes' ) {
			$show_dimensions = true;
		}

		if ( get_option( 'wcsa_admin_order_details' ) != 'no' ) {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}
		if ( is_object($product) ) {
			echo '<td><div class="view">' . wp_kses_post( $this->the_attributes( $product, 'span', $show_weight, $show_dimensions, $skip_atts ) ) . '</div></td>';
		}
	}

	/**
	* Add Attributes to the Order Items header on the admin Order Details page.
	*/
	public function admin_order_item_header() {
		if ( get_option( 'wcsa_admin_order_details' ) != 'no' ) {
			echo '<th class="wsa-custom-attributes">' . __( 'Attributes', 'woocommerce-show-attributes' ) . '</th>';
		}
	}

	/**
	* Show product attributes on the child products of a Grouped Product page.
	*
	* @param object, the product object
	* @since 1.2.4
	*/
	public function show_atts_grouped_product( $product ) {
		$show_weight = null;
		if ( get_option( 'wcsa_weight_product' ) == 'yes' ) {
			$show_weight = true;
		}
		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_product' ) == 'yes' ) {
			$show_dimensions = true;
		}
		if ( get_option( 'wcsa_product' ) != 'no') {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}
		echo '<td class="grouped-product-custom-attributes">' . wp_kses_post( $this->the_attributes( $product, 'span', $show_weight, $show_dimensions, $skip_atts ) ) . '</td>';
	}

	/**
	 * Show the attributes on the main shop page.
	 * @since 1.2.3
	 */
	public function show_atts_on_shop() {
			global $product;
			echo wp_kses_post( $this->the_attributes( $product, 'li' ) );
	}

	/**
	 * Check if option to show attributes on main shop is enabled.
 	 * @since 1.2.3
	 */
	public function if_show_atts_on_shop() {
		$show = get_option( 'woocommerce_show_attributes_on_shop' );

		// if option to show on shop page is enabled, do it
		if ( 'above_price' == $show ) {
			add_action ( 'woocommerce_after_shop_loop_item_title', array( $this, 'show_atts_on_shop' ), 4 );

		} elseif ( 'above_add2cart' == $show ) {
			add_action ( 'woocommerce_after_shop_loop_item', array( $this, 'show_atts_on_shop' ), 4 );

		}
	}

 	/**
	* Customize the Additional Information tab to NOT show our custom attributes
	*/
	public function additional_info_tab( $tabs ) {
		global $product;

		if ( ! is_object( $product ) ) {
			return $tabs;
		}

		if ( $product->has_attributes() ) {

			// check if any of the attributes are variations
			$attributes = $product->get_attributes();

			$need_tab = array();
			foreach ( $attributes as $attribute ) {
				if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
					continue;
				}

				if ( $attribute->get_variation() && $attribute->get_visible() ) {
					$need_tab[] = 1;					
				} else {
					$need_tab[] = '';
				}
			}

			// if all $need_tab array values are empty, none of the attributes are visible variations
			// so we would not need the tab except for dimensions or weight
			if ( count(array_filter($need_tab)) == 0 ) {
				// if no dimensions & no weight, unset the tab
				if ( ! $product->has_dimensions() && ! $product->has_weight() ) {
					unset( $tabs['additional_information'] );
				// if dimensions and weight have both been moved up by option, unset the tab
				} elseif ( get_option( 'wcsa_weight_product' ) == 'yes' && get_option( 'wcsa_dimensions_product' ) == 'yes' ) {
						unset( $tabs['additional_information'] );
				} else {

					// we have to show weight and/or height so do tab
					$tabs['additional_information']['callback'] = 'additional_info_tab_content';
				}
			} else {
				// we have visible variations so do tab
				$tabs['additional_information']['callback'] = 'additional_info_tab_content';
			}
		} else {

			// we have no attributes
			if ( $product->has_dimensions() || $product->has_weight() ) {

				if ( get_option( 'wcsa_weight_product' ) == 'yes' && get_option( 'wcsa_dimensions_product' ) == 'yes' ) {

					// weight and dimensions have been moved up so unset tab
					unset( $tabs['additional_information'] );
				} else {
					// we have to show weight and/or height so do tab
					$tabs['additional_information']['callback'] = 'additional_info_tab_content';
				}

			}
		}
		return $tabs;
	}

	/**
	 * Add settings to the Show Attributes section.
	 * @since 1.4.0
	 */
	public function add_settings( $settings, $current_section ) {
		if ( 'wc_show_attributes' == $current_section ) {
			return wcsa_all_settings();

			// If not, return the standard settings
		} else {
			return $settings;

		}

	}

	/**
	 * Add our settings section under the Products tab.
	 * @since 1.4.0
	 */
	public function add_section( $sections ) {
		$sections['wc_show_attributes'] = __( 'Show Attributes', 'woocommerce-show-attributes' );
		return $sections;
	}

	/**
	 * Show attributes on the Admin New Order email.
	 *
	 * Add the custom product attributes to the New Order email
	 * which goes to the Admin.
	 *
	 * @param string $item_name the product title
	 * @param object $item the product object
	 * @since 1.4.0
	 */

	public function show_admin_new_order_email( $item_name, $item ) {
		$show_weight = null;
		if ( get_option( 'wcsa_weight_admin_email' ) == 'yes' ) {
			$show_weight = true;
		}
		$show_dimensions = null;
		if ( get_option( 'wcsa_dimensions_admin_email' ) == 'yes' ) {
			$show_dimensions = true;
		}
		$out = $item_name;
		if ( get_option( 'wcsa_admin_email' ) != 'no' ) {
			$skip_atts = null;
		} else {
			$skip_atts = true;
		}

		// get the name of templates used for this email call
		$templates_used = array();
		foreach ( debug_backtrace() as $called_file ) {
			foreach ( $called_file as $index ) {
				if ( is_array( $index ) ) { // avoid errors
					if ( ! empty( $index[0] ) ) {
						if ( is_string( $index[0] ) ) { // eliminate long arrays
							$templates_used[] = $index[0];
						}
					}
				}
			}
		}

		// Only add atts to admin emails.
		$is_admin_email = false;
		foreach ( $templates_used as $template_name ) {
			// check each file name for '/emails/admin-'
			if ( strpos( $template_name, '/emails/admin-' ) !== false ) {
				// Eureka! We have an admin email
				$is_admin_email = true;
			}
		}

		if ( $is_admin_email ) {
			$product = wc_get_product( $item['product_id'] );
			$atts = $this->the_attributes( $product, 'span', $show_weight, $show_dimensions, $skip_atts );
			if ( $atts ) {
				$out = $item_name . '<br />' . $atts . '<br />';
			}

		}

		return $out;

	}

	/**
	 * Add CSS for emails.
	 */
	public function email_style( $css ) {
		$css .= '.custom-attributes {display: table}.custom-attributes > span {display: table-row}.custom-attributes .attribute-label{white-space: nowrap}.custom-attributes .attribute-value {padding-left: 8px;display: table-cell}.custom-attributes br {display: none}';
		return $css;
	}
	/**
	 * Save default options upon plugin activation
	 */
	static function install() {
		$settings = wcsa_all_settings();
		foreach ( $settings as $option ) {
			if ( ! empty( $option['default'] ) ) {// Only if we have any defaults
				$db_option = get_option( $option['id'] );
				if ( empty( $db_option ) ) {// If option is empty, set the default value
					update_option( $option['id'], $option['default'] );
				}
			}
		}

	}

} // end class

// only if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	* The custom HTML for the Additional Information tab which now excludes our custom attributes.
	*/
	function additional_info_tab_content() { ?>
		<h2><?php _e( 'Additional Information', 'woocommerce-show-attributes' ); ?></h2>
		<table class="shop_attributes">
		<?php
		global $product;
		$attributes = $product->get_attributes();
		$has_weight = $product->has_weight();
		$has_dimensions = $product->has_dimensions();
		$display_dimensions = apply_filters( 'wc_product_enable_dimensions_display', $has_weight || $has_dimensions );

		if ( get_option( 'wcsa_weight_product' ) != 'yes' ) {
				if ( $display_dimensions && $has_weight ) : ?>
					<tr>
					<th><?php _e( 'Weight', 'woocommerce-show-attributes' ) ?></th>
					<td class="product_weight"><?php echo esc_html( wc_format_weight( $product->get_weight() ) ); ?></td>
					</tr>
				<?php endif;
		}

		if ( get_option( 'wcsa_weight_dimensions' ) != 'yes' ) {


			if ( $display_dimensions && $product->has_dimensions() ) : ?>
				<tr>
				<th><?php _e( 'Dimensions', 'woocommerce-show-attributes' ) ?></th>
				<td class="product_dimensions"><?php echo esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) ); ?></td>
				</tr>
			<?php endif;
		}


		foreach ( $attributes as $attribute ) :
			$name = $attribute->get_name();
			// Skip atts that are shown above add to cart
			if ( get_option( 'wcsa_product', 'no' ) == 'yes') {
				continue;
			}
		?>
			<tr>
			<th><?php echo esc_html( wc_attribute_label( $name ) ); ?></th>
			<td><?php
				$values = array();
				if ( $attribute->is_taxonomy() ) {
					global $wc_product_attributes;
					$product_terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
					foreach ( $product_terms as $product_term ) {
						$product_term_name = esc_html( $product_term->name );
						$link = get_term_link( $product_term->term_id, $name );
						if ( ! empty ( $wc_product_attributes[ $name ]->attribute_public ) ) {
							$values[] = '<a href="' . esc_url( $link  ) . '" rel="tag">' . $product_term_name . '</a>';
						} else {
							$values[] = $product_term_name;
						}
					}
				} else {
					$values = $attribute->get_options();
					foreach ( $values as &$value ) {
						$value = esc_html( $value );
					}
				}

				echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
			?></td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php
	}
	$WooCommerce_Show_Attributes = WooCommerce_Show_Attributes::get_instance();
	register_activation_hook(__FILE__, array( $WooCommerce_Show_Attributes, 'install' ) );
}

/**
 * Return an array of all our settings
 * @since 1.6.1
 */
function wcsa_all_settings() {
	$settings = array(
		array(
			'name'	=> __( 'WooCommerce Show Attributes Options', 'woocommerce-show-attributes' ),
			'type'	=> 'title',
			'desc'	=> __( 'Where would you like to show your custom product attributes?', 'woocommerce-show-attributes' ),
			'id'	=> 'wc_show_attributes' ),
		array(
			'name'		=> __( 'Show Attributes on Product Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_product',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show attributes on the single product above Add To Cart, and on Grouped products.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Attributes on Shop Pages', 'woocommerce-show-attributes' ),
			'desc'		=> __( 'Whether to show attributes on the main shop page and shop category pages.', 'woocommerce-show-attributes' ),
			'id'		=> 'woocommerce_show_attributes_on_shop',
			'css'		=> '',
			'default'	=> 'no',
			'type'		=> 'select',
			'options'	=> array(
							''					=> __( 'No', 'woocommerce-show-attributes' ),
							'above_price'		=> __( 'Show them above the price', 'woocommerce-show-attributes' ),
							'above_add2cart'	=> __( 'Show them above "Add to Cart"', 'woocommerce-show-attributes' ),
				),
				'desc_tip'	=> true,
				),
		array(
			'name'		=> __( 'Show Attributes on Cart Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_cart',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show attributes on the cart and checkout pages.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Attributes on Customer Order Emails', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_customer_order_emails',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show attributes on customer order, invoice, and receipt emails, and on the customer\'s View Order page.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Attributes on Admin New Order Email', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_admin_email',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show attributes on the New Order email which goes to the Administrator.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Attributes on Admin Order Details Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_admin_order_details',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show attributes on the Order Details page on the back end, listed under "Order Items".', 'woocommerce-show-attributes' )
				),
		array( 'type' => 'sectionend', 'id' => 'wc_show_attributes' ),
			// style
		array(
			'title'		=> __( 'Style Options', 'woocommerce-show-attributes' ),
			'desc'		=> __( 'These options affect the style or appearance of the attributes.', 'woocommerce-show-attributes' ),
			'type'		=> 'title',
			'id'		=> 'wcsa_style'
				),
		array(
			'name'		=> __( 'Hide the Labels When Showing Product Attributes', 'woocommerce-show-attributes' ),
			'id'		=> 'woocommerce_show_attributes_hide_labels',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Check this box to hide the attribute labels and only show the attribute values.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Attributes in a span Element', 'woocommerce-show-attributes' ),
			'id'		=> 'woocommerce_show_attributes_span',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Check this box to use a span element instead of list bullets when showing product attributes on the single product page.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Remove Colon From Attribute Labels', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_remove_semicolon',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Check this box to remove the colon from the attribute labels. Useful for RTL languages.', 'woocommerce-show-attributes' )
				),
		array( 'type' => 'sectionend', 'id' => 'wcsa_style' ),
			// weight and Dimensions
		array(
			'title'		=> __( 'Show Weight and Dimensions', 'woocommerce-show-attributes' ),
			'desc'		=> __( 'These options let you show the product weight and dimensions in various places.', 'woocommerce-show-attributes' ),
			'type'		=> 'title',
			'id'		=> 'wc_show_weight_dimensions'
				),
		array(
			'name'		=> __( 'Show Weight on Product Page Above Add To Cart', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_weight_product',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product weight on the single product pages, and Grouped products, above Add To Cart instead of in the Additional Information tab.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Dimensions on Product Page Above Add To Cart', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_dimensions_product',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product dimensions on the single product pages, and Grouped products, above Add To Cart instead of in the Additional Information tab.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Weight on Cart Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_weight_cart',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product weight on the cart and checkout pages.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Dimensions on Cart Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_dimensions_cart',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product dimensions on the cart and checkout pages.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Weight on Customer Order Emails', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_weight_customer_order_emails',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product weight on customer order, invoice, and receipt emails, and on the customer\'s View Order page.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Dimensions on Customer Order Emails', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_dimensions_customer_order_emails',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product dimensions on customer order, invoice, and receipt emails, and on the customer\'s View Order page.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Weight on Admin New Order Email', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_weight_admin_email',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product weight on the New Order email which goes to the Administrator.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Dimensions on Admin New Order Email', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_dimensions_admin_email',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product dimensions on the New Order email which goes to the Administrator.', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Weight on Admin Order Details Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_weight_admin_order_details',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product weight on the Order Details page on the back end, listed under "Order Items".', 'woocommerce-show-attributes' )
				),
		array(
			'name'		=> __( 'Show Dimensions on Admin Order Details Page', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_dimensions_admin_order_details',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'Show product dimensions on the Order Details page on the back end, listed under "Order Items".', 'woocommerce-show-attributes' )
				),
		array( 'type' => 'sectionend', 'id' => 'wc_show_weight_dimensions' ),
			// Extra Options
		array(
			'title'		=> __( 'Extra Options', 'woocommerce-show-attributes' ),
			'type'		=> 'title',
			'id'		=> 'wcsa_extra_options'
				),
		array(
			'name'		=> __( 'Show Attribute Terms as Links', 'woocommerce-show-attributes' ),
			'id'		=> 'wcsa_terms_as_links',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'desc'		=> __( 'On the single product page, show the attribute terms as links. They will link to their archive pages. This only works with Global Attributes. Global Attributes are created in Products -> Attributes.', 'woocommerce-show-attributes' )
				),
			array( 'type' => 'sectionend', 'id' => 'wcsa_extra_options' ),
	);

	return $settings;
}
