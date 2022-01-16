<!-- Form for inputting card information -->

<!-- Card Type -->
<div class="form-group">
	<label for="cardType">Card Type *</label>
	<select class="form-control" id="cardType" style="width:auto;">
	  <option>Visa</option>
	  <option>MasterCard</option>
	  <option>American Express</option>
	  <option>Discover</option>
	</select>
</div>
<!-- End Card Type -->

<!-- Name on Card -->
<div class="form-group">
	<label for="nameOnCard">Name on card *</label>
	<input style="width:auto;" type="text" class="form-control" id="nameOnCard" placeholder="Name on card">
	<p id="card_name_error" style="display:none;color:red">Name on card is required!</p>
</div>
<!-- End Name on Card -->

<!-- Card Number -->
<div class="form-group">
	<label for="cardNumber">Card Number *</label>
	<input style="width:auto;" type="password" class="form-control" id="cardNumber" placeholder="Card Number" maxlength="16" inputmode="numeric" pattern="[0-9]{16}">
	<p id="card_number_error" style="display:none;color:red">Card number is required and must be 16 digits long!</p>
</div>
<!-- End Card Number -->

<!-- Expiration Month -->
<div class="form-group">
	<label for="expirationYear">Expiration Month *</label>
	<select class="form-control" id="expirationMonth" style="width:auto;">
		<?php for ($i = 1; $i < 13; $i++): ?>
			<option><?php echo $i ?></option>
		<?php endfor; ?>
	</select>
</div>
<!-- End Expiration Month -->

<!-- Expiration Year -->
<div class="form-group">
	<label for="expirationMonth">Expiration Year *</label>
	<select class="form-control" id="expirationYear" style="width:auto;">
		<?php for ($i = 0; $i < 5; $i++): ?>
			<option><?php echo date('Y', strtotime('+'.$i.' year')); ?></option>
		<?php endfor; ?>
	</select>
</div>
<!-- End Expiration Year -->

<!-- CVV -->
<div class="form-group">
	<label for="cvv">CVV *</label>
	<input style="width:auto;" type="password" class="form-control" id="cvv" placeholder="CVV" maxlength="3" inputmode="numeric" pattern="[0-9]{3}">
	<p id="cvv_error" style="display:none;color:red">CVV number is required!</p>
</div>	
<!-- End CVV -->
