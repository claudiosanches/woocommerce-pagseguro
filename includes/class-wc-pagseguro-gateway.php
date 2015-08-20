<?php
/**
 * WC PagSeguro Gateway Class.
 *
 * Built the PagSeguro method.
 */
class WC_PagSeguro_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'pagseguro';
		$this->icon               = apply_filters( 'woocommerce_pagseguro_icon', plugins_url( 'assets/images/pagseguro.png', plugin_dir_path( __FILE__ ) ) );
		$this->method_title       = __( 'PagSeguro', 'woocommerce-pagseguro' );
		$this->method_description = __( 'Accept payments by credit card, bank debit or banking ticket using the PagSeguro.', 'woocommerce-pagseguro' );
		$this->order_button_text  = __( 'Proceed to payment', 'woocommerce-pagseguro' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title             = $this->get_option( 'title' );
		$this->description       = $this->get_option( 'description' );
		$this->email             = $this->get_option( 'email' );
		$this->token             = $this->get_option( 'token' );
		$this->method            = $this->get_option( 'method', 'direct' );
		$this->tc_credit         = $this->get_option( 'tc_credit', 'yes' );
		$this->tc_transfer       = $this->get_option( 'tc_transfer', 'yes' );
		$this->tc_ticket         = $this->get_option( 'tc_ticket', 'yes' );
		$this->tc_ticket_message = $this->get_option( 'tc_ticket_message', 'yes' );
		$this->send_only_total   = $this->get_option( 'send_only_total', 'no' );
		$this->invoice_prefix    = $this->get_option( 'invoice_prefix', 'WC-' );
		$this->sandbox           = $this->get_option( 'sandbox', 'no' );
		$this->debug             = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Set the API.
		$this->api = new WC_PagSeguro_API( $this );

		// Main actions.
		add_action( 'woocommerce_api_wc_pagseguro_gateway', array( $this, 'ipn_handler' ) );
		add_action( 'valid_pagseguro_ipn_request', array( $this, 'update_order_status' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Transparent checkout actions.
		if ( 'transparent' == $this->method ) {
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'checkout_scripts' ) );
		}

		if ( is_admin() ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * Displays notifications when the admin has something wrong with the configuration.
	 */
	public function admin_notices() {
		if ( 'yes' == $this->get_option( 'enabled' ) ) {
			// Checks if email is not empty.
			if ( empty( $this->email ) ) {
				include_once 'views/html-notice-email-missing.php';
			}

			// Checks if token is not empty.
			if ( empty( $this->token ) ) {
				include_once 'views/html-notice-token-missing.php';
			}

			if ( 'transparent' == $this->method && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
				include_once 'views/html-notice-ecfb-missing.php';
			}

			// Checks that the currency is supported
			if ( ! $this->using_supported_currency() && ! class_exists( 'woocommerce_wpml' ) ) {
				include_once 'views/html-notice-currency-not-supported.php';
			}
		}
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency() {
		return in_array( get_woocommerce_currency(), array( 'BRL' ) );
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available() {
		// Test if is valid for use.
		$available = ( 'yes' == $this->get_option( 'enabled' ) ) && ! empty( $this->email ) && ! empty( $this->token ) && $this->using_supported_currency();

		if ( 'transparent' == $this->method && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			$available = false;
		}

		return $available;
	}

	/**
	 * Has fields.
	 *
	 * @return bool
	 */
	public function has_fields() {
		return ( 'transparent' == $this->method ) ? true : false;
	}

	/**
	 * Admin scripts.
	 *
	 * @param string $hook Page slug.
	 */
	public function admin_scripts( $hook ) {
		if ( in_array( $hook, array( 'woocommerce_page_wc-settings', 'woocommerce_page_woocommerce_settings' ) ) && ( isset( $_GET['section'] ) && 'wc_pagseguro_gateway' == strtolower( $_GET['section'] ) ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'pagseguro-admin', plugins_url( 'assets/js/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_PagSeguro::VERSION, true );
		}
	}

	/**
	 * Checkout scripts.
	 */
	public function checkout_scripts() {
		if ( is_checkout() && $this->is_available() ) {
			if ( ! get_query_var( 'order-received' ) ) {
				$session_id = $this->api->get_session_id();
				$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_style( 'pagseguro-checkout', plugins_url( 'assets/css/transparent-checkout' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_PagSeguro::VERSION );
				wp_enqueue_script( 'pagseguro-library', $this->api->get_direct_payment_url(), array(), null, true );
				wp_enqueue_script( 'pagseguro-checkout', plugins_url( 'assets/js/transparent-checkout' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'pagseguro-library', 'woocommerce-extra-checkout-fields-for-brazil-front' ), WC_PagSeguro::VERSION, true );

				wp_localize_script(
					'pagseguro-checkout',
					'wc_pagseguro_params',
					array(
						'session_id'         => $session_id,
						'interest_free'      => __( 'interest free', 'woocommerce-pagseguro' ),
						'invalid_card'       => __( 'Invalid credit card number.', 'woocommerce-pagseguro' ),
						'invalid_expiry'     => __( 'Invalid expiry date, please use the MM / YYYY date format.', 'woocommerce-pagseguro' ),
						'expired_date'       => __( 'Please check the expiry date and use a valid format as MM / YYYY.', 'woocommerce-pagseguro' ),
						'general_error'      => __( 'Unable to process the data from your credit card on the PagSeguro, please try again or contact us for assistance.', 'woocommerce-pagseguro' ),
						'empty_installments' => __( 'Select a number of installments.', 'woocommerce-pagseguro' ),
					)
				);
			}
		}
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'woocommerce-pagseguro' ) . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable PagSeguro', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-pagseguro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-pagseguro' ),
				'desc_tip'    => true,
				'default'     => __( 'PagSeguro', 'woocommerce-pagseguro' )
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-pagseguro' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-pagseguro' ),
				'default'     => __( 'Pay via PagSeguro', 'woocommerce-pagseguro' )
			),
			'email' => array(
				'title'       => __( 'PagSeguro Email', 'woocommerce-pagseguro' ),
				'type'        => 'text',
				'description' => __( 'Please enter your PagSeguro email address. This is needed in order to take payment.', 'woocommerce-pagseguro' ),
				'desc_tip'    => true,
				'default'     => ''
			),
			'token' => array(
				'title'       => __( 'PagSeguro Token', 'woocommerce-pagseguro' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Please enter your PagSeguro token. This is needed to process the payment and notifications. Is possible generate a new token %s.', 'woocommerce-pagseguro' ), '<a href="https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml">' . __( 'here', 'woocommerce-pagseguro' ) . '</a>' ),
				'default'     => ''
			),
			'method' => array(
				'title'       => __( 'Integration method', 'woocommerce-pagseguro' ),
				'type'        => 'select',
				'description' => __( 'Choose how the customer will interact with the PagSeguro. Redirect (Client goes to PagSeguro page) or Lightbox (Inside your store)', 'woocommerce-pagseguro' ),
				'desc_tip'    => true,
				'default'     => 'direct',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					'redirect'    => __( 'Redirect (default)', 'woocommerce-pagseguro' ),
					'lightbox'    => __( 'Lightbox', 'woocommerce-pagseguro' ),
					'transparent' => __( 'Transparent Checkout', 'woocommerce-pagseguro' )
				)
			),
			'transparent_checkout' => array(
				'title'       => __( 'Transparent Checkout Options', 'woocommerce-pagseguro' ),
				'type'        => 'title',
				'description' => ''
			),
			'tc_credit' => array(
				'title'   => __( 'Credit Card', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Credit Card for Transparente Checkout', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'tc_transfer' => array(
				'title'   => __( 'Bank Transfer', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bank Transfer for Transparente Checkout', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'tc_ticket' => array(
				'title'   => __( 'Banking Ticket', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Banking Ticket for Transparente Checkout', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'tc_ticket_message' => array(
				'title'   => __( 'Banking Ticket Tax Message', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Display a message alerting the customer that will be charged R$ 1,00 for payment by Banking Ticket', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'behavior' => array(
				'title'       => __( 'Integration Behavior', 'woocommerce-pagseguro' ),
				'type'        => 'title',
				'description' => ''
			),
			'send_only_total' => array(
				'title'   => __( 'Send only the order total', 'woocommerce-pagseguro' ),
				'type'    => 'checkbox',
				'label'   => __( 'If this option is enabled will only send the order total, not the list of items.', 'woocommerce-pagseguro' ),
				'default' => 'no'
			),
			'invoice_prefix' => array(
				'title'       => __( 'Invoice Prefix', 'woocommerce-pagseguro' ),
				'type'        => 'text',
				'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PagSeguro account for multiple stores ensure this prefix is unqiue as PagSeguro will not allow orders with the same invoice number.', 'woocommerce-pagseguro' ),
				'desc_tip'    => true,
				'default'     => 'WC-'
			),
			'testing' => array(
				'title'       => __( 'Gateway Testing', 'woocommerce-pagseguro' ),
				'type'        => 'title',
				'description' => ''
			),
			'sandbox' => array(
				'title'       => __( 'PagSeguro Sandbox', 'woocommerce-pagseguro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable PagSeguro Sandbox', 'woocommerce-pagseguro' ),
				'default'     => 'no',
				'description' => sprintf( __( 'PagSeguro Sandbox can be used to test the payments. <strong>Note:</strong> you must use the development token that can be found in %s.', 'woocommerce-pagseguro' ), '<a href="https://sandbox.pagseguro.uol.com.br/comprador-de-testes.html" target="_blank">' . __( 'PagSeguro Sandbox', 'woocommerce-pagseguro' ) .'</a>' )
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-pagseguro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-pagseguro' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log PagSeguro events, such as API requests, inside %s', 'woocommerce-pagseguro' ), $this->get_log_view() )
			)
		);
	}

	/**
	 * Admin page.
	 */
	public function admin_options() {
		include 'views/html-admin-page.php';
	}

	/**
	 * Send email notification.
	 *
	 * @param string $subject Email subject.
	 * @param string $title   Email title.
	 * @param string $message Email message.
	 */
	protected function send_email( $subject, $title, $message ) {
		$mailer = WC()->mailer();

		$mailer->send( get_option( 'admin_email' ), $subject, $mailer->wrap_message( $title, $message ) );
	}

	/**
	 * Payment fields.
	 *
	 * @return string
	 */
	public function payment_fields() {
		wp_enqueue_script( 'wc-credit-card-form' );

		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}

		$cart_total = $this->get_order_total();

		if ( 'transparent' == $this->method ) {
			wc_get_template( 'transparent-checkout-form.php', array(
				'cart_total'        => $cart_total,
				'tc_credit'         => $this->tc_credit,
				'tc_transfer'       => $this->tc_transfer,
				'tc_ticket'         => $this->tc_ticket,
				'tc_ticket_message' => $this->tc_ticket_message,
			), 'woocommerce/pagseguro/', WC_PagSeguro::get_templates_path() );
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return array            Redirect.
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( 'lightbox' != $this->method ) {
			if ( isset( $_POST['pagseguro_sender_hash'] ) && 'transparent' == $this->method ) {
				$response = $this->api->do_payment_request( $order, $_POST );

				if ( $response['data'] ) {
					$this->update_order_status( $response['data'] );
				}
			} else {
				$response = $this->api->do_checkout_request( $order, $_POST );
			}

			if ( $response['url'] ) {
				// Remove cart.
				WC()->cart->empty_cart();

				return array(
					'result'   => 'success',
					'redirect' => $response['url']
				);
			} else {
				foreach ( $response['error'] as $error ) {
					wc_add_notice( $error, 'error' );
				}

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}
		} else {
			$use_shipping = isset( $_POST['ship_to_different_address'] ) ? true : false;

			return array(
				'result'   => 'success',
				'redirect' => add_query_arg( array( 'use_shipping' => $use_shipping ), $order->get_checkout_payment_url( true ) )
			);
		}
	}

	/**
	 * Output for the order received page.
	 *
	 * @param  $order_id Order ID.
	 *
	 * @return string    PagSeguro lightbox.
	 */
	public function receipt_page( $order_id ) {
		$order        = new WC_Order( $order_id );
		$request_data = $_POST;
		if ( isset( $_GET['use_shipping'] ) && true == $_GET['use_shipping'] ) {
			$request_data['ship_to_different_address'] = true;
		}

		$response = $this->api->do_checkout_request( $order, $request_data );

		if ( $response['url'] ) {
			// Lightbox script.
			wc_enqueue_js( '
				$( "#browser-has-javascript" ).show();
				$( "#browser-no-has-javascript, #cancel-payment, #submit-payment" ).hide();
				var isOpenLightbox = PagSeguroLightbox({
						code: "' . esc_js( $response['token'] ) . '"
					}, {
						success: function ( transactionCode ) {
							window.location.href = "' . str_replace( '&amp;', '&', esc_js( $this->get_return_url( $order ) ) ) . '";
						},
						abort: function () {
							window.location.href = "' . str_replace( '&amp;', '&', esc_js( $order->get_cancel_order_url() ) ) . '";
						}
				});
				if ( ! isOpenLightbox ) {
					window.location.href = "' . esc_js( $response['url'] ) . '";
				}
			' );

			wc_get_template( 'lightbox-checkout.php', array(
				'cancel_order_url'    => $order->get_cancel_order_url(),
				'payment_url'         => $response['url'],
				'lightbox_script_url' => $this->api->get_lightbox_url(),
			), 'woocommerce/pagseguro/', WC_PagSeguro::get_templates_path() );
		} else {
			$html = '<ul class="woocommerce-error">';
				foreach ( $response['error'] as $message ) {
					$html .= '<li>' . $message . '</li>';
				}
			$html .= '</ul>';

			$html .= '<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Click to try again', 'woocommerce-pagseguro' ) . '</a>';

			echo $html;
		}
	}

	/**
	 * IPN handler.
	 */
	public function ipn_handler() {
		@ob_clean();

		$ipn = $this->api->process_ipn_request( $_POST );

		if ( $ipn ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_pagseguro_ipn_request', $ipn );
			exit();
		} else {
			$message = __( 'PagSeguro Request Unauthorized', 'woocommerce-pagseguro' );
			wp_die( $message, $message, array( 'response' => 401 ) );
		}
	}

	/**
	 * Update order status.
	 *
	 * @param array $posted PagSeguro post data.
	 */
	public function update_order_status( $posted ) {

		if ( isset( $posted->reference ) ) {
			$order_id = (int) str_replace( $this->invoice_prefix, '', $posted->reference );
			$order    = new WC_Order( $order_id );

			// Checks whether the invoice number matches the order.
			// If true processes the payment.
			if ( $order->id === $order_id ) {

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'PagSeguro payment status for order ' . $order->get_order_number() . ' is: ' . intval( $posted->status ) );
				}

				// Order details.
				$order_details = array(
					'type'         => '',
					'method'       => '',
					'installments' => '',
					'link'         => ''
				);

				if ( isset( $posted->code ) ) {
					update_post_meta(
						$order->id,
						__( 'PagSeguro Transaction ID', 'woocommerce-pagseguro' ),
						(string) $posted->code
					);
				}
				if ( isset( $posted->sender->email ) ) {
					update_post_meta(
						$order->id,
						__( 'Payer email', 'woocommerce-pagseguro' ),
						(string) $posted->sender->email
					);
				}
				if ( isset( $posted->sender->name ) ) {
					update_post_meta(
						$order->id,
						__( 'Payer name', 'woocommerce-pagseguro' ),
						(string) $posted->sender->name
					);
				}
				if ( isset( $posted->paymentMethod->type ) ) {
					$order_details['type'] = intval( $posted->paymentMethod->type );
					update_post_meta(
						$order->id,
						__( 'Payment type', 'woocommerce-pagseguro' ),
						$this->api->get_payment_name_by_type( $order_details['type'] )
					);
				}
				if ( isset( $posted->paymentMethod->code ) ) {
					$order_details['method'] = $this->api->get_payment_method_name( intval( $posted->paymentMethod->code ) );
					update_post_meta(
						$order->id,
						__( 'Payment method', 'woocommerce-pagseguro' ),
						$order_details['method']
					);
				}
				if ( isset( $posted->installmentCount ) ) {
					$order_details['installments'] = (string) $posted->installmentCount;
					update_post_meta(
						$order->id,
						__( 'Installments', 'woocommerce-pagseguro' ),
						$order_details['installments']
					);
				}
				if ( isset( $posted->paymentLink ) ) {
					$order_details['link'] = (string) $posted->paymentLink;
					update_post_meta(
						$order->id,
						__( 'Payment url', 'woocommerce-pagseguro' ),
						$order_details['link']
					);
				}

				// Save/update payment information for transparente checkout.
				if ( 'transparent' == $this->method ) {
					update_post_meta( $order->id, '_wc_pagseguro_payment_data', $order_details );
				}

				switch ( intval( $posted->status ) ) {
					case 1 :
						$order->update_status( 'on-hold', __( 'PagSeguro: The buyer initiated the transaction, but so far the PagSeguro not received any payment information.', 'woocommerce-pagseguro' ) );

						break;
					case 2 :
						$order->update_status( 'on-hold', __( 'PagSeguro: Payment under review.', 'woocommerce-pagseguro' ) );

						break;
					case 3 :
						$order->add_order_note( __( 'PagSeguro: Payment approved.', 'woocommerce-pagseguro' ) );

						// For WooCommerce 2.2 or later.
						add_post_meta( $order->id, '_transaction_id', (string) $posted->code, true );

						// Changing the order for processing and reduces the stock.
						$order->payment_complete();

						break;
					case 4 :
						$order->add_order_note( __( 'PagSeguro: Payment completed and credited to your account.', 'woocommerce-pagseguro' ) );

						break;
					case 5 :
						$order->update_status( 'on-hold', __( 'PagSeguro: Payment came into dispute.', 'woocommerce-pagseguro' ) );
						$this->send_email(
							sprintf( __( 'Payment for order %s came into dispute', 'woocommerce-pagseguro' ), $order->get_order_number() ),
							__( 'Payment in dispute', 'woocommerce-pagseguro' ),
							sprintf( __( 'Order %s has been marked as on-hold, because the payment came into dispute in PagSeguro.', 'woocommerce-pagseguro' ), $order->get_order_number() )
						);

						break;
					case 6 :
						$order->update_status( 'refunded', __( 'PagSeguro: Payment refunded.', 'woocommerce-pagseguro' ) );
						$this->send_email(
							sprintf( __( 'Payment for order %s refunded', 'woocommerce-pagseguro' ), $order->get_order_number() ),
							__( 'Payment refunded', 'woocommerce-pagseguro' ),
							sprintf( __( 'Order %s has been marked as refunded by PagSeguro.', 'woocommerce-pagseguro' ), $order->get_order_number() )
						);

						break;
					case 7 :
						$order->update_status( 'cancelled', __( 'PagSeguro: Payment canceled.', 'woocommerce-pagseguro' ) );

						break;

					default :
						// No action xD.
						break;
				}
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error: Order Key does not match with PagSeguro reference.' );
				}
			}
		}
	}

	/**
	 * Thank You page message.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string
	 */
	public function thankyou_page( $order_id ) {
		$data = get_post_meta( $order_id, '_wc_pagseguro_payment_data', true );

		if ( isset( $data['type'] ) ) {
			wc_get_template( 'payment-instructions.php', array(
				'type'         => $data['type'],
				'link'         => $data['link'],
				'method'       => $data['method'],
				'installments' => $data['installments'],
			), 'woocommerce/pagseguro/', WC_PagSeguro::get_templates_path() );
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 * @param  bool   $plain_text    Plain text or HTML.
	 *
	 * @return string                Payment instructions.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || 'on-hold' !== $order->status || $this->id !== $order->payment_method ) {
			return;
		}

		$data = get_post_meta( $order->id, '_wc_pagseguro_payment_data', true );

		if ( isset( $data['type'] ) ) {
			if ( $plain_text ) {
				wc_get_template( 'emails/plain-instructions.php', array(
					'type'         => $data['type'],
					'link'         => $data['link'],
					'method'       => $data['method'],
					'installments' => $data['installments'],
				), 'woocommerce/pagseguro/', WC_PagSeguro::get_templates_path() );
			} else {
				wc_get_template( 'emails/html-instructions.php', array(
					'type'         => $data['type'],
					'link'         => $data['link'],
					'method'       => $data['method'],
					'installments' => $data['installments'],
				), 'woocommerce/pagseguro/', WC_PagSeguro::get_templates_path() );
			}
		}
	}
}
