<?php
/**
 * REST API WC MailChimp settings
 *
 * Handles requests to the /payment_gateways endpoint.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce/API
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

#TODO:
# * permission check


/**
 * @package WooCommerce/API
 */
class WC_REST_MC_Store_Settings_Controller extends WC_REST_Payment_Gateways_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'mailchimp';

	/**
	 * Prepare a payment gateway for response.
	 *
	 * @param  WC_Payment_Gateway $gateway    Payment gateway object.
	 * @param  WP_REST_Request    $request    Request object.
	 * @return WP_REST_Response   $response   Response data.
	 */

	/**
	 * Register the route for /payment_gateways and /payment_gateways/<id>
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_settings' ),
			)
		) );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/api_key', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_api_key' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
		'schema' => array( $this, 'get_api_key_schema' ),
		) );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/store_info', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_store_info' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
		'schema' => array( $this, 'get_store_info_schema' ),
		) );
	}

	/**
	 * Check whether a given request has permission to view payment gateways.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		// if ( ! wc_rest_check_manager_permissions( 'payment_gateways', 'read' ) ) {
		// 	return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		// }
		return true;
	}

	/**
	 * Get payment gateways.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_settings( $request ) {
		$options = get_option('mailchimp-woocommerce', array() );
		$options['active_tab'] = isset($options['active_tab']) ? $options['active_tab'] : "api_key";
		return rest_ensure_response( $options );
	}

	public function update_api_key( $request ) {
		$parameters     = $request->get_params();
		$handler        = MailChimp_Woocommerce_Admin::connect();
		$data           = $handler->validatePostApiKey( $parameters );
		$options        = get_option('mailchimp-woocommerce', array());
    $merged_options = (isset($data) && is_array($data)) ? array_merge($options, $data) : $options;
		update_option('mailchimp-woocommerce', $merged_options);
		return rest_ensure_response( $merged_options );
	}

	/**
	 * Get the payment gateway schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_api_key_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'store_settings',
			'type'       => 'object',
			'properties' => array(
				'api_key' => array(
					'description' => __( 'MailChimp api key.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	public function update_store_info( $request ) {
		$parameters     = $request->get_params();
		$handler        = MailChimp_Woocommerce_Admin::connect();
		$parameters['mailchimp_active_tab'] = 'store_info';
		$data           = $handler->validate( $parameters );
		update_option('mailchimp-woocommerce', $mSSata);
		return rest_ensure_response( $data );
	}

	/**
	 * Get the payment gateway schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_store_info_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'store_info',
			'type'       => 'object',
			'properties' => array(
				'store_name' => array(
					'description' => __( 'Store name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_street' => array(
					'description' => __( 'Store street.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_city' => array(
					'description' => __( 'Store city.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_state' => array(
					'description' => __( 'Store state.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_postal_code' => array(
					'description' => __( 'Store postal code.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_country' => array(
					'description' => __( 'Store country.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_phone' => array(
					'description' => __( 'Store_phone', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_locale' => array(
					'description' => __( 'Store locale', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_currency_code' => array(
					'description' => __( 'Store currency code', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'store_phone' => array(
					'description' => __( 'Store phone', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get any query params needed.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

}
