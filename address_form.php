<!-- 
	Form for inputting address information
	Set variable $address_type for each control to have a unique id (for example, displaying both billing and shipping info on same page and using this form for both)
-->

<!-- Country -->
<div class="form-group">
	<label for="country">Country *</label>
	<select class="form-control" id="country<?php echo '_'.$address_type ?>" style="width:auto;">
		<option value="United States">United States</option>
	</select>
	<p style="font-size:10px;"><i>*currently only supporting United States residents at this time!</i></p>
</div>
<!-- End Country -->

<!-- Address Line 1 -->
<div class="form-group">
	<label for="address_1">Address Line 1 *</label>
	<input style="width:auto;" type="text" class="form-control" id="address_1<?php echo '_'.$address_type ?>" placeholder="Address Line 1">
	<p id="address_1_error<?php echo '_'.$address_type ?>" style="display:none;color:red">Address 1 is required!</p>
</div>
<!-- End Address Line 1 -->

<!-- Address Line 2 -->
<div class="form-group">
	<label for="address_2">Address Line 2</label>
	<input style="width:auto;" type="text" class="form-control" id="address_2<?php echo '_'.$address_type ?>" placeholder="Address Line 2">
</div>
<!-- End Address Line 2 -->

<!-- City -->
<div class="form-group">
	<label for="city">City *</label>
	<input style="width:auto;" type="text" class="form-control" id="city<?php echo '_'.$address_type ?>" placeholder="City">
	<p id="city_error<?php echo '_'.$address_type ?>" style="display:none;color:red">City is required!</p>
</div>
<!-- End City -->

<!-- State -->
<div class="form-group">
	<label for="state">State *</label>
	<select class="form-control" id="state<?php echo '_'.$address_type ?>" style="width:auto;">
		<option value="AL">AL</option>
		<option value="AK">AK</option>
		<option value="AZ">AZ</option>
		<option value="AR">AR</option>
		<option value="CA">CA</option>
		<option value="CO">CO</option>
		<option value="CT">CT</option>
		<option value="DE">DE</option>
		<option value="DC">DC</option>
		<option value="FL">FL</option>
		<option value="GA">GA</option>
		<option value="HI">HI</option>
		<option value="ID">ID</option>
		<option value="IL">IL</option>
		<option value="IN">IN</option>
		<option value="IA">IA</option>
		<option value="KS">KS</option>
		<option value="KY">KY</option>
		<option value="LA">LA</option>
		<option value="ME">ME</option>
		<option value="MD">MD</option>
		<option value="MA">MA</option>
		<option value="MI">MI</option>
		<option value="MN">MN</option>
		<option value="MS">MS</option>
		<option value="MO">MO</option>
		<option value="MT">MT</option>
		<option value="NE">NE</option>
		<option value="NV">NV</option>
		<option value="NH">NH</option>
		<option value="NJ">NJ</option>
		<option value="NM">NM</option>
		<option value="NY">NY</option>
		<option value="NC">NC</option>
		<option value="ND">ND</option>
		<option value="OH">OH</option>
		<option value="OK">OK</option>
		<option value="OR">OR</option>
		<option value="PA">PA</option>
		<option value="RI">RI</option>
		<option value="SC">SC</option>
		<option value="SD">SD</option>
		<option value="TN">TN</option>
		<option value="TX">TX</option>
		<option value="UT">UT</option>
		<option value="VT">VT</option>
		<option value="VA">VA</option>
		<option value="WA">WA</option>
		<option value="WV">WV</option>
		<option value="WI">WI</option>
		<option value="WY">WY</option>
	</select>
</div>
<!-- End State -->

<!-- Zip Code -->
<div class="form-group">
	<label for="zip_code">Zip Code *</label>
	<input style="width:auto;" type="text" class="form-control" id="zip_code<?php echo '_'.$address_type ?>" placeholder="Zip Code" maxlength="5" inputmode="numeric" pattern="[0-9]{3}">
	<p id="zip_code_error<?php echo '_'.$address_type ?>" style="display:none;color:red">Zip Code is required and must be 5 digits long!</p>
</div>
<!-- End Zip Code -->