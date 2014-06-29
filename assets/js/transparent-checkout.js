/*jshint devel:true */
/*global wc_pagseguro_params, PagSeguroDirectPayment */
(function ( $ ) {
	'use strict';

	$( function () {

		/**
		 * Set credit card brand.
		 *
		 * @param {string} brand
		 */
		function pagSeguroSetCreditCardBrand( brand ) {
			$( '#pagseguro-credit-cart-form' ).attr( 'data-credit-card-brand', brand );
		}

		/**
		 * Format price.
		 *
		 * @param  {int|float} price
		 *
		 * @return {string}
		 */
		function pagSeguroGetPriceText( price ) {
			return 'R$ ' + parseFloat( price, 10 ).toFixed( 2 ).replace( '.', ',' ).toString();
		}

		/**
		 * Get installment option.
		 *
		 * @param  {object} installment
		 *
		 * @return {string}
		 */
		function pagSeguroGetInstallmentOption( installment ) {
			var interestFree = ( true === installment.interestFree ) ? ' ' + wc_pagseguro_params.interest_free : '';

			return '<option value="' + installment.quantity + '" data-installment-value="' + installment.installmentAmount + '">' + installment.quantity + 'x ' + pagSeguroGetPriceText( installment.installmentAmount ) + interestFree + '</option>';
		}

		/**
		 * Add error message
		 *
		 * @param  {string} error
		 *
		 * @return {void}
		 */
		function pagSeguroAddErrorMessage( error ) {
			var wrapper = $( '#pagseguro-credit-cart-form' );

			$( '.woocommerce-error', wrapper ).remove();
			wrapper.prepend( '<div class="woocommerce-error" style="margin-bottom: 0.5em !important;">' + error + '</div>' );
		}

		// Transparent checkout actions.
		if ( wc_pagseguro_params.session_id ) {
			// Initialize the transparent checkout.
			PagSeguroDirectPayment.setSessionId( wc_pagseguro_params.session_id );

			// Get the credit card brand.
			$( 'body' ).on( 'focusout', '#pagseguro-card-number', function () {
				var bin = $( this ).val().replace( /[^\d]/g, '' ).substr( 0, 6 ),
					instalmments = $( 'body #pagseguro-card-installments' );

					// Reset the installments.
					instalmments.empty();
					instalmments.attr( 'disabled', 'disabled' );

				PagSeguroDirectPayment.getBrand({
					cardBin: bin,
					success: function ( data ) {
						$( 'body' ).trigger( 'pagseguro_credit_card_brand', data.brand.name );
						pagSeguroSetCreditCardBrand( data.brand.name );
					},
					error: function () {
						$( 'body' ).trigger( 'pagseguro_credit_card_brand', 'error' );
						pagSeguroSetCreditCardBrand( 'error' );
					}
				});
			});

			// Set the errors.
			$( 'body' ).on( 'focus', '#pagseguro-card-number, #pagseguro-card-expiry', function () {
				$( '#pagseguro-credit-cart-form .woocommerce-error' ).remove();
			});

			// Get the installments.
			$( 'body' ).on( 'pagseguro_credit_card_brand', function ( event, brand ) {
				if ( 'error' !== brand ) {
					PagSeguroDirectPayment.getInstallments({
						amount: $( 'body #pagseguro-cart-total' ).val(),
						brand: brand,
						success: function ( data ) {
							var instalmments = $( 'body #pagseguro-card-installments' );

							if ( false === data.error ) {
								instalmments.empty();
								instalmments.removeAttr( 'disabled' );
								instalmments.append( '<option value="0">--</option>' );

								$.each( data.installments[brand], function ( index, installment ) {
									instalmments.append( pagSeguroGetInstallmentOption( installment ) );
								});
							} else {
								pagSeguroAddErrorMessage( wc_pagseguro_params.invalid_card );
							}
						},
						error: function () {
							pagSeguroAddErrorMessage( wc_pagseguro_params.invalid_card );
						}
					});
				} else {
					pagSeguroAddErrorMessage( wc_pagseguro_params.invalid_card );
				}
			});

			// Process the credit card data when submit the checkout form.
			$( 'body' ).on( 'click', '#place_order', function () {
				if ( ! $( '#payment_method_pagseguro' ).is( ':checked' ) ) {
					return true;
				}

				if ( 'radio' === $( 'body li.payment_method_pagseguro input[name=pagseguro_payment_method]' ).attr( 'type' ) ) {
					if ( 'credit-card' !== $( 'body li.payment_method_pagseguro input[name=pagseguro_payment_method]:checked' ).val() ) {
						return true;
					}
				} else {
					if ( 'credit-card' !== $( 'body li.payment_method_pagseguro input[name=pagseguro_payment_method]' ).val() ) {
						return true;
					}
				}

				var form = $( 'form.checkout, form#order_review' ),
					creditCardForm  = $( '#pagseguro-credit-cart-form', form ),
					error           = false,
					errorHtml       = '',
					brand           = creditCardForm.attr( 'data-credit-card-brand' ),
					cardNumber      = $( '#pagseguro-card-number', form ).val().replace( /[^\d]/g, '' ),
					cvv             = $( '#pagseguro-card-cvc', form ).val(),
					expirationMonth = $( '#pagseguro-card-expiry', form ).val().replace( /[^\d]/g, '' ).substr( 0, 2 ),
					expirationYear  = $( '#pagseguro-card-expiry', form ).val().replace( /[^\d]/g, '' ).substr( 2 ),
					installments    = $( '#pagseguro-card-installments', form ),
					today           = new Date();

				// Validate the credit card data.
				errorHtml += '<ul>';

				// Validate the card brand.
				if ( typeof brand === 'undefined' || 'error' === brand ) {
					errorHtml += '<li>' + wc_pagseguro_params.invalid_card + '</li>';
					error = true;
				}

				// Validate the expiry date.
				if ( 2 !== expirationMonth.length || 4 !== expirationYear.length ) {
					errorHtml += '<li>' + wc_pagseguro_params.invalid_expiry + '</li>';
					error = true;
				}

				if ( ( 2 === expirationMonth.length && 4 === expirationYear.length ) && ( expirationMonth > 12 || expirationYear <= ( today.getFullYear() - 1 ) || expirationYear >= ( today.getFullYear() + 20 ) || ( expirationMonth < ( today.getMonth() + 2 ) && expirationYear.toString() === today.getFullYear().toString() ) ) ) {
					errorHtml += '<li>' + wc_pagseguro_params.expired_date + '</li>';
					error = true;
				}

				// Installments.
				if ( '0' === installments.val() ) {
					errorHtml += '<li>' + wc_pagseguro_params.empty_installments + '</li>';
					error = true;
				}

				errorHtml += '</ul>';

				// Create the card token.
				if ( ! error ) {
					PagSeguroDirectPayment.createCardToken({
						brand:           brand,
						cardNumber:      cardNumber,
						cvv:             cvv,
						expirationMonth: expirationMonth,
						expirationYear:  expirationYear,
						success: function ( data ) {
							// Remove any old hash input.
							$( 'input[name=pagseguro_card_hash], input[name=pagseguro_sender_hash]', form ).remove();

							// Add the hash input.
							form.append( $( '<input name="pagseguro_card_hash" type="hidden" />' ).val( data.card.token ) );
							form.append( $( '<input name="pagseguro_sender_hash" type="hidden" />' ).val( PagSeguroDirectPayment.getSenderHash() ) );
							form.append( $( '<input name="pagseguro_installment_value" type="hidden" />' ).val( $( 'option:selected', installments ).attr( 'data-installment-value' ) ) );

							// Submit the form.
							form.submit();
						},
						error: function () {
							pagSeguroAddErrorMessage( wc_pagseguro_params.general_error );
						}
					});

				// Display the error messages.
				} else {
					pagSeguroAddErrorMessage( errorHtml );
				}

				return false;
			});

			// Input masks.
			$( 'body' ).on( 'updated_checkout', function () {
				// CPF.
				$( '#pagseguro-card-holder-cpf' ).mask( '999.999.999-99', { placeholder: ' ' } );

				// Birth Date.
				$( '#pagseguro-card-holder-birth-date' ).mask( '99 / 99 / 9999', { placeholder: ' ' } );

				// Phone.
				$( '#pagseguro-card-holder-phone' ).focusout( function () {
					var phone, element;
					element = $( this );
					element.unmask();
					phone = element.val().replace( /\D/g, '' );

					if ( phone.length > 10 ) {
						element.mask( '(99) 99999-999?9', { placeholder: ' ' } );
					} else {
						element.mask( '(99) 9999-9999?9', { placeholder: ' ' } );
					}
				}).trigger( 'focusout' );
			});

		}
	});

}( jQuery ));
