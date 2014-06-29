<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="pagseguro-payment-form">
	<input type="hidden" id="pagseguro-cart-total" value="<?php echo number_format( $cart_total, 2, '.', '' ); ?>" />

	<p class="form-row form-row-wide">
		<label><input id="pagseguro-payment-method-credit-cart" type="radio" name="pagseguro_payment_method" value="credit-card" checked="checked" /> <?php _e( 'Credit Card', 'woocommerce-pagseguro' ); ?></label>
		<label><input id="pagseguro-payment-method-banking-ticket" type="radio" name="pagseguro_payment_method" value="banking-ticket" /> <?php _e( 'Banking Ticket', 'woocommerce-pagseguro' ); ?></label>
		<label><input id="pagseguro-payment-method-online-debit" type="radio" name="pagseguro_payment_method" value="online-debit" /> <?php _e( 'Online Debit', 'woocommerce-pagseguro' ); ?></label>
	</p>

	<div id="pagseguro-credit-cart-form">
		<p class="form-row form-row-first">
			<label for="pagseguro-card-holder-name"><?php _e( 'Card Holder Name', 'woocommerce-pagseguro' ); ?> <small>(<?php _e( 'as recorded on the card', 'woocommerce-pagseguro' ); ?>)</small> <span class="required">*</span></label>
			<input id="pagseguro-card-holder-name" name="pagseguro_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<p class="form-row form-row-last">
			<label for="pagseguro-card-number"><?php _e( 'Card Number', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<div class="clear"></div>
		<p class="form-row form-row-first">
			<label for="pagseguro-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<p class="form-row form-row-last">
			<label for="pagseguro-card-cvc"><?php _e( 'Security Code', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="<?php _e( 'CVC', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<div class="clear"></div>
		<p class="form-row form-row-first">
			<label for="pagseguro-card-installments"><?php _e( 'Installments', 'woocommerce-pagseguro' ); ?> <small>(<?php _e( 'the minimum value of the installment is R$ 5,00', 'woocommerce-pagseguro' ); ?>)</small> <span class="required">*</span></label>
			<select id="pagseguro-card-installments" name="pagseguro_card_installments" style="font-size: 1.5em; padding: 4px; width: 100%;" disabled="disabled">
				<option value="0">--</option>
			</select>
		</p>
		<p class="form-row form-row-last">
			<label for="pagseguro-card-holder-cpf"><?php _e( 'Card Holder CPF', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-holder-cpf" name="pagseguro_card_holder_cpf" class="input-text wecfb-cpf-field" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<div class="clear"></div>
		<p class="form-row form-row-first">
			<label for="pagseguro-card-holder-birth-date"><?php _e( 'Card Holder Birth Date', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-holder-birth-date" name="pagseguro_card_holder_birth_date" class="input-text" type="text" autocomplete="off" placeholder="<?php _e( 'DD / MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<p class="form-row form-row-last">
			<label for="pagseguro-card-holder-phone"><?php _e( 'Card Holder Phone', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
			<input id="pagseguro-card-holder-phone" name="pagseguro_card_holder_phone" class="input-text" type="text" autocomplete="off" placeholder="<?php _e( '(xx) xxxx-xxxx', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
		</p>
		<div class="clear"></div>
	</div>
</fieldset>
