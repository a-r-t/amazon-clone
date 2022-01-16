// checks if input data for checkout.php is valid
function isCheckoutInputDataValid() {
	let _isCardInputValid = isCardInputValid()
	let _isBillingAddressInputValid = isBillingAddressInputValid()
	let _isShippingAddressInputValid = isShippingAddressInputValid();
	return _isCardInputValid && _isBillingAddressInputValid && _isShippingAddressInputValid;
}

// is card input valid
function isCardInputValid() {
	var isValid = true;
	
	// if new payment information entered, validate card info
	if ($("#paymentInfo option:last").is(":selected")) {
		
		// name on card
		if ($("#nameOnCard").val() === "") {
			$("#card_name_error").css('display', 'block');
			isValid = false;
		}
		else {
			$("#card_name_error").css('display', 'none');
		}
		
		// card number
		if (!/^[0-9]{16}$/.test($("#cardNumber").val())) {
			$("#card_name_error").css('display', 'block');
			isValid = false;
		}
		else {
			$("#card_name_error").css('display', 'none');
		}
		
		// cvv
		if ($("#cvv").val() === "") {
			$("#cvv_error").css('display', 'block');
			isValid = false;
		}
		else {
			$("#cvv_error").css('display', 'none');
		}
	}
	return isValid;
}

// is billing address input valid
function isBillingAddressInputValid() {
	var isValid = true;

	// if new payment information entered, validate billing address
	if ($("#paymentInfo option:last").is(":selected")) {
		
		// address 1
		if ($("#address_1_billing").val() === "") {
			$("#address_1_error_billing").css('display', 'block');
			isValid = false;
		}
		else {
			$("#address_1_error_billing").css('display', 'none');
		}
		
		// city
		if ($("#city_billing").val() === "") {
			$("#city_error_billing").css('display', 'block');
			isValid = false;
		}
		else {
			$("#city_error_billing").css('display', 'none');
		}
		
		// zipcode
		if (!/^[0-9]{5}$/.test($("#zip_code_billing").val())) {
			$("#zip_code_error_billing").css('display', 'block');
			isValid = false;
		}
		else {
			$("#zip_code_error_billing").css('display', 'none');
		}
	}
	return isValid;
}

// is shipping address input valid
function isShippingAddressInputValid() {
	var isValid = true;
	
	// if new shipping information entered, validate shipping address	
	if ($("#shippingInfo option:last").is(":selected")) {
		// address 1
		if ($("#address_1_shipping").val() === "") {
			$("#address_1_error_shipping").css('display', 'block');
			isValid = false;
		}
		else {
			$("#address_1_error_shipping").css('display', 'none');
		}
		
		// city
		if ($("#city_shipping").val() === "") {
			$("#city_error_shipping").css('display', 'block');
			isValid = false;
		}
		else {
			$("#city_error_shipping").css('display', 'none');
		}
		
		// zipcode
		if (!/^[0-9]{5}$/.test($("#zip_code_shipping").val())) {
			$("#zip_code_error_shipping").css('display', 'block');
			isValid = false;
		}
		else {
			$("#zip_code_error_shipping").css('display', 'none');
		}
		
		// name
		if ($("#shipping_name_input").val() === "" || !isShippingNameUnique()) {
			$("#shipping_name_error").css('display', 'block');
			isValid = false;
		}
		else {
			$("#shipping_name_error").css('display', 'none');
		}
	}
	return isValid;
}

// checks if shipping nickname input is unique (cannot have two nicknames the same)
function isShippingNameUnique() {
	let shippingNameInput = $("#shipping_name_input").val();
	
	$("#shippingInfo > option").each(() => {
		if (shippingNameInput === $(this).text()) {
			return false;
		}
	});
	return true;
}

// update input tags on page with appropriate values based on user input
function updatePostInputValues() {
	updatePostBillingInputValues();
	updatePostShippingInputValues();
}

// update billing related input tag values
function updatePostBillingInputValues() {
	if ($("#paymentInfo option:last").is(":selected")) {
		$("#input_card_type").attr('value', $("#cardType :selected").text());
		$("#input_card_name").attr('value', $("#nameOnCard").val());
		$("#input_card_last_four_digits").attr('value', $("#cardNumber").val().substring(12, 16));
		
		
		let cardHash = ($("#cardNumber").val() + $("#expirationMonth option:selected").text() + $("#expirationYear option:selected").text() + $("#cvv").val()).hashCode();
		$("#input_card_hash").attr('value', cardHash);
		
		$("#input_billing_information_country").attr('value', $("#country_billing :selected").text());
		$("#input_billing_information_address_1").attr('value', $("#address_1_billing").val());
		$("#input_billing_information_address_2").attr('value', $("#address_2_billing").val());
		$("#input_billing_information_city").attr('value', $("#city_billing").val());
		$("#input_billing_information_state").attr('value', $("#state_billing :selected").text());
		$("#input_billing_information_zip_code").attr('value', $("#zip_code_billing").val());
	}
	else {
		var billing_info_option = $("#paymentInfo option:selected");
		var billing_information_id = billing_info_option.attr('data-billing_information_id');
		$("#input_billing_information_id").attr('value', billing_information_id);
	}
}

// update shipping related input tag values
function updatePostShippingInputValues() {
	if ($("#shippingInfo option:last").is(":selected")) {
		$("#input_shipping_information_country").attr('value', $("#country_shipping :selected").text());
		$("#input_shipping_information_address_1").attr('value', $("#address_1_shipping").val());
		$("#input_shipping_information_address_2").attr('value', $("#address_2_shipping").val());
		$("#input_shipping_information_city").attr('value', $("#city_shipping").val());
		$("#input_shipping_information_state").attr('value', $("#state_shipping :selected").text());
		$("#input_shipping_information_zip_code").attr('value', $("#zip_code_shipping").val());
		$("#input_shipping_information_name").attr('value', $("#shipping_name_input").val());
	}
	else {
		var shipping_info_option = $("#shippingInfo option:selected");
		var shipping_information_id = shipping_info_option.attr('data-shipping_information_id');
		$("#input_shipping_information_id").attr('value', shipping_information_id);

	}
}

// on process_checkout submit, check input validity and update input value tags as necessary
$('#process_checkout').submit(() => {
	if (isCheckoutInputDataValid()) {
		updatePostInputValues();
		return true;
	}
	else {
		alert("There are field errors! Fix before submitting!");
		return false;
	}
});

// on payment info dropdown changed, show or hide enter payment info form
function selectPaymentChange() {
	if ($("#paymentInfo option:last").is(":selected")) {
		$("#enterPaymentInfo").css('display','block');
	}
	else {
		clearPaymentErrorMessages();
	}
}

// hide all payment related error messages on page
function clearPaymentErrorMessages() {
	$("#card_name_error").css('display', 'none');
	$("#card_name_error").css('display', 'none');
	$("#cvv_error").css('display', 'none');
	$("#enterPaymentInfo").css('display','none');
	$("#address_1_error_billing").css('display', 'none');
	$("#city_error_billing").css('display', 'none');
	$("#zip_code_error_billing").css('display', 'none');
}

// on shipping info dropdown changed, show or hide enter shipping info form
function selectShippingChange() {
	if ($("#shippingInfo option:last").is(":selected")) {
		clearShippingInfoForm();
	}
	else {
		fillInShippingInfoForm();
	}
}

// clear shipping information form
function clearShippingInfoForm() {
	$("#country_shipping").val($("#country_shipping option:first").val());
	$("#country_shipping").attr('disabled', false);
	
	$("#address_1_shipping").val("");
	$("#address_1_shipping").attr('disabled', false);
	
	$("#address_2_shipping").val("");
	$("#address_2_shipping").attr('disabled', false);
	
	$("#city_shipping").val("");
	$("#city_shipping").attr('disabled', false);

	$("#state_shipping").val($("#state_shipping option:first").val());
	$("#state_shipping").attr('disabled', false);
	
	$("#zip_code_shipping").val("");
	$("#zip_code_shipping").attr('disabled', false);
	
	$("#shipping_name").css("display", "block");
}

// fill in shipping information form with selected shipping information nickname from dropdown
// uses data attributes filled in by php in the dropdown
function fillInShippingInfoForm() {
	var option = $("#shippingInfo option:selected");
		
	var country = option.attr("data-country");
	$("#country_shipping").val(country);
	$("#country_shipping").attr('disabled', true);

	var address1 = option.attr('data-address_1');
	$("#address_1_shipping").val(address1);
	$("#address_1_shipping").attr('disabled', true);
	$("#address_1_error_shipping").css('display', 'none');

	var address2 = option.attr('data-address_2');
	$("#address_2_shipping").val(address2);
	$("#address_2_shipping").attr('disabled', true);

	var city = option.attr('data-city');
	$("#city_shipping").val(city);
	$("#city_shipping").attr('disabled', true);
	$("#city_error_shipping").css('display', 'none');

	var state = option.attr('data-state');
	$("#state_shipping").val(state);
	$("#state_shipping").attr('disabled', true);

	var zip_code = option.attr('data-zip_code');
	$("#zip_code_shipping").val(zip_code);
	$("#zip_code_shipping").attr('disabled', true);
	$("#zip_code_error_shipping").css('display', 'none');
	
	$("#shipping_name").css("display", "none");
	$("#shipping_name_error").css('display', 'none');
}