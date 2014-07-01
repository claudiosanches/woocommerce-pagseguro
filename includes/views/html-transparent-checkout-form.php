<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="pagseguro-payment-form">
	<input type="hidden" id="pagseguro-cart-total" value="<?php echo number_format( $cart_total, 2, '.', '' ); ?>" />

	<p id="pagseguro-payment-methods" class="form-row form-row-wide">
		<?php if ( 'yes' == $this->tc_credit ) : ?>
		<label><input id="pagseguro-payment-method-credit-cart" type="radio" name="pagseguro_payment_method" value="credit-card" <?php checked( true, ( 'yes' == $this->tc_credit ), true ); ?> /> <?php _e( 'Credit Card', 'woocommerce-pagseguro' ); ?></label>
		<?php endif; ?>

		<?php if ( 'yes' == $this->tc_transfer ) : ?>
		<label><input id="pagseguro-payment-method-bank-transfer" type="radio" name="pagseguro_payment_method" value="bank-transfer" <?php checked( true, ( 'no' == $this->tc_credit && 'yes' == $this->tc_transfer ), true ); ?> /> <?php _e( 'Bank Transfer', 'woocommerce-pagseguro' ); ?></label>
		<?php endif; ?>

		<?php if ( 'yes' == $this->tc_ticket ) : ?>
		<label><input id="pagseguro-payment-method-banking-ticket" type="radio" name="pagseguro_payment_method" value="banking-ticket" <?php checked( true, ( 'no' == $this->tc_credit && 'no' == $this->tc_transfer && 'yes' == $this->tc_ticket ), true ); ?> /> <?php _e( 'Banking Ticket', 'woocommerce-pagseguro' ); ?></label>
		<?php endif; ?>
	</p>

	<?php if ( 'yes' == $this->tc_credit ) : ?>
		<div id="pagseguro-credit-card-form" class="pagseguro-method-form">
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
	<?php endif; ?>

	<?php if ( 'yes' == $this->tc_transfer ) : ?>
		<div id="pagseguro-bank-transfer-form" class="pagseguro-method-form">
			<p class="form-row form-row-wide">
				<label for="pagseguro-bank-transfer"><?php _e( 'Bank Brand', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<select id="pagseguro-bank-transfer" name="pagseguro_bank_transfer" style="font-size: 1.5em; padding: 4px; width: 100%;">
					<option value="0">--</option>
					<option value="bradesco"><?php _e( 'Bradesco', 'woocommerce-pagseguro' ); ?></option>
					<option value="itau"><?php _e( 'Ita&uacute;', 'woocommerce-pagseguro' ); ?></option>
					<option value="bancodobrasil"><?php _e( 'Banco do Brasil', 'woocommerce-pagseguro' ); ?></option>
					<option value="banrisul"><?php _e( 'Banrisul', 'woocommerce-pagseguro' ); ?></option>
					<option value="hsbc"><?php _e( 'HSBC', 'woocommerce-pagseguro' ); ?></option>
				</select>
			</p>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

	<?php if ( 'yes' == $this->tc_ticket ) : ?>
		<div id="pagseguro-banking-ticket-form" class="pagseguro-method-form">
			<p class="form-row form-row-wide"><?php _e( 'The order will be confirmed only after the payment approval', 'woocommerce-pagseguro' ); ?></p>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

</fieldset>
