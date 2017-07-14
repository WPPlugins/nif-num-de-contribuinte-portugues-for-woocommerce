<?php
/**
 * Plugin Name: NIF (Num. de Contribuinte Português) for WooCommerce
 * Plugin URI: http://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/nif-de-contribuinte-portugues-woocommerce-wordpress/
 * Description: This plugin adds the Portuguese VAT identification number (NIF/NIPC) as a new field to WooCommerce checkout and order details, if the billing address is from Portugal.
 * Version: 3.0
 * Author: Webdados
 * Author URI: http://www.webdados.pt
 * Text Domain: woocommerce_nif
 * Domain Path: /lang
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
// Get active network plugins - "Stolen" from Novalnet Payment Gateway
function nif_active_nw_plugins() {
	if (!is_multisite())
		return false;
	$nif_activePlugins = (get_site_option('active_sitewide_plugins')) ? array_keys(get_site_option('active_sitewide_plugins')) : array();
	return $nif_activePlugins;
}
if (in_array('woocommerce/woocommerce.php', (array) get_option('active_plugins')) || in_array('woocommerce/woocommerce.php', (array) nif_active_nw_plugins())) {

	//Languages
	add_action('plugins_loaded', 'woocommerce_nif_init');
	function woocommerce_nif_init() {
		load_plugin_textdomain('woocommerce_nif', false, dirname(plugin_basename(__FILE__)) . '/lang/');
	}
	
	//Add field to billing address fields - Globally
	add_filter('woocommerce_billing_fields', 'woocommerce_nif_billing_fields', 10, 2);
	function woocommerce_nif_billing_fields($fields, $country) {
		if($country=='PT') {
			$fields['billing_nif'] = array(
				'type'			=>	'text',
				'label'			=> apply_filters( 'woocommerce_nif_field_label', __('NIF / NIPC', 'woocommerce_nif') ),
				'placeholder'	=> apply_filters( 'woocommerce_nif_field_placeholder', _x('Portuguese VAT identification number', 'placeholder', 'woocommerce_nif') ),
				'class'			=> apply_filters( 'woocommerce_nif_field_class', array('form-row-first') ), //Should be an option (?)
				'required'		=> apply_filters( 'woocommerce_nif_field_required', false ), //Should be an option (?)
				'clear'			=> apply_filters( 'woocommerce_nif_field_clear', true ), //Should be an option (?)
				'autocomplete'	=> apply_filters( 'woocommerce_nif_field_autocomplete', 'on' ),
				'priority'		=> apply_filters( 'woocommerce_nif_field_priority', 120 ), //WooCommerce should by ths parameter but it doesn't seem to be doing so
				'maxlength'		=> apply_filters( 'woocommerce_nif_field_maxlength', 9 ),
				'validate'		=> ( apply_filters( 'woocommerce_nif_field_validate', false ) ? array('nif_pt') : array() ), //Does nothing so far
			);
		}
		return $fields;
	}

	//Add field to order admin panel
	add_filter('woocommerce_admin_billing_fields', 'woocommerce_nif_admin_billing_fields');
	function woocommerce_nif_admin_billing_fields($billing_fields) {
		global $post;
		if ($post->post_type=='shop_order') {
			$order = new WC_Order($post->ID);
			$countries = new WC_Countries();
			$billing_country = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_billing_country() : $order->billing_country;
			//Customer is portuguese or it's a new order ?
			if ( $billing_country=='PT' || ($billing_country=='' && $countries->get_base_country()=='PT') ) {
				$billing_fields['nif']=array(
					'label' => apply_filters( 'woocommerce_nif_field_label', __( 'NIF / NIPC', 'woocommerce_nif' ) ),
				);
			}
		}
		return $billing_fields;
	}
	//Add field to ajax billing get_customer_details - See https://github.com/woothemes/woocommerce/commit/5c43b340027fc9dea78e15825f12191768f7d2ed
	add_action('admin_init', 'woocommerce_nif_admin_init_found_customer_details');
	function woocommerce_nif_admin_init_found_customer_details() {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			add_filter('woocommerce_ajax_get_customer_details', 'woocommerce_nif_ajax_get_customer_details', 10, 3);
		} else {
			add_filter('woocommerce_found_customer_details', 'woocommerce_nif_found_customer_details_old', 10, 3);
		}
	}
	//Pre 3.0
	function woocommerce_nif_found_customer_details_old($customer_data, $user_id, $type_to_load ) {
		if ($type_to_load=='billing') {
			if (isset($customer_data['billing_country']) && $customer_data['billing_country']=='PT') {
				$customer_data['billing_nif'] = get_user_meta( $user_id, $type_to_load . '_nif', true );
			}
		}
		return $customer_data;
	}
	//3.0 and above - See https://github.com/woocommerce/woocommerce/issues/12654
	function woocommerce_nif_ajax_get_customer_details($customer_data, $customer, $user_id ) {
		if (isset($customer_data['billing']['country']) && $customer_data['billing']['country']=='PT') {
			$customer_data['billing']['nif'] = $customer->get_meta('billing_nif');
		}
		return $customer_data;
	}

	//Add field to the admin user edit screen
	add_action('woocommerce_customer_meta_fields', 'woocommerce_nif_customer_meta_fields');
	function woocommerce_nif_customer_meta_fields($show_fields) {
		if (isset($show_fields['billing']) && is_array($show_fields['billing']['fields'])) {
			$show_fields['billing']['fields']['billing_nif']=array(
				'label' => apply_filters( 'woocommerce_nif_field_label', __( 'NIF / NIPC', 'woocommerce_nif' ) ),
				'description' => '',
			);
		}
		return $show_fields;
	}

	//Add field to customer details on the Thank You page
	add_action( 'woocommerce_order_details_after_customer_details', 'woocommerce_nif_order_details_after_customer_details');
	function woocommerce_nif_order_details_after_customer_details($order) {
		$billing_country = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_billing_country() : $order->billing_country;
		$billing_nif = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_meta( '_billing_nif' ) : $order->billing_nif;
		if ($billing_country=='PT' && $billing_nif) {
			?>
			<tr>
				<th><?php echo apply_filters( 'woocommerce_nif_field_label', __( 'NIF / NIPC', 'woocommerce_nif' ) ); ?>:</th>
				<td><?php echo esc_html( $billing_nif ); ?></td>
			</tr>
			<?php
		}
	}

	//Add field to customer details on Emails
	add_filter('woocommerce_email_customer_details_fields', 'woocommerce_nif_email_customer_details_fields', 10, 3);
	function woocommerce_nif_email_customer_details_fields($fields, $sent_to_admin, $order) {
		$billing_nif = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_meta( '_billing_nif' ) : $order->billing_nif;
		if ($billing_nif) {
			$fields['billing_nif'] = array(
				'label' => apply_filters( 'woocommerce_nif_field_label', __('NIF / NIPC', 'woocommerce_nif') ),
				'value' => wptexturize( $billing_nif )
			);
		}
		return $fields;
	}

	//Validation
	add_action( 'woocommerce_checkout_process', 'woocommerce_nif_checkout_process' );
	function woocommerce_nif_checkout_process() {
		if ( apply_filters( 'woocommerce_nif_field_validate', false ) ) {
			if ( WC()->customer->get_country()=='PT' || (WC()->customer->get_country()=='' && $countries->get_base_country()=='PT') ) {
				$billing_nif = wc_clean( isset( $_POST['billing_nif'] ) ? $_POST['billing_nif'] : '' );
				if ( woocommerce_valida_nif($billing_nif, true) ) {
					//OK
				} else {
					wc_add_notice(
						sprintf( __( 'You have entered an invalid %s for Portugal.', 'woocommerce_nif' ), '<strong>'.apply_filters( 'woocommerce_nif_field_label', __('NIF / NIPC', 'woocommerce_nif') ).'</strong>' ),
						'error'
					);
				}
			} else {
				//Not Portugal
			}
		} else {
			//All good - No validation required
		}
	}
	/* NIF */
	function woocommerce_valida_nif($nif, $ignoreFirst=true) {
		//Limpamos eventuais espaços a mais
		$nif=trim($nif);
		//Verificamos se é numérico e tem comprimento 9
		if (!is_numeric($nif) || strlen($nif)!=9) {
			return false;
		} else {
			$nifSplit=str_split($nif);
			//O primeiro digíto tem de ser 1, 2, 5, 6, 8 ou 9
			//Ou não, se optarmos por ignorar esta "regra"
			if (
				in_array($nifSplit[0], array(1, 2, 5, 6, 8, 9))
				||
				$ignoreFirst
			) {
				//Calculamos o dígito de controlo
				$checkDigit=0;
				for($i=0; $i<8; $i++) {
					$checkDigit+=$nifSplit[$i]*(10-$i-1);
				}
				$checkDigit=11-($checkDigit % 11);
				//Se der 10 então o dígito de controlo tem de ser 0
				if($checkDigit>=10) $checkDigit=0;
				//Comparamos com o último dígito
				if ($checkDigit==$nifSplit[8]) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */
	
}