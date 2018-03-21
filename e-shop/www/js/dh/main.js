/**
 * Author: Dominik Harmim <harmim6@gmail.com>
 */

if (typeof dhMain === "undefined") {
	var dhMain = {};
}


/**
 * Plus-Minus number input control.
 */
dhMain.plusMinusNumberInput = {
	/**
	 * Initialization.
	 */
	init: function () {
		var $btnNumber = $(".plus-minus-number .btn-number");
		var $inputNumber = $(".plus-minus-number .input-number");

		if ($btnNumber.length > 0 && $inputNumber.length > 0) {
			this.click($btnNumber);
			this.focusin($inputNumber);
			this.change($inputNumber, $btnNumber);
			this.keydown($inputNumber);

			$inputNumber.trigger("change");
		}
	},


	/**
	 * Plus and minus number buttons click event.
	 *
	 * @param $btnNumber plus and minus number button elements
	 */
	click: function ($btnNumber) {
		$btnNumber.click(function (e) {
			e.preventDefault();

			var type = $(this).data("type");
			var $input = $("input[name='" + $(this).data("field") + "']");
			var currentVal = parseInt(String($input.val()));

			if (!isNaN(currentVal)) {
				if (type === "minus") {
					if (currentVal > $input.attr("min")) {
						$input.val(currentVal - 1).trigger("change");
					}
					if (parseInt(String($input.val())) === parseInt(String($input.attr("min")))) {
						$(this).attr("disabled", "true");
					}
				} else if (type === "plus") {
					if (currentVal < $input.attr("max")) {
						$input.val(currentVal + 1).trigger("change");
					}
					if (parseInt(String($input.val())) === parseInt(String($input.attr("max")))) {
						$(this).attr("disabled", "true");
					}
				}
			} else {
				$input.val(0).trigger("change");
			}
		});
	},


	/**
	 * Input number focusin event.
	 *
	 * @param $inputNumber input number element
	 */
	focusin: function ($inputNumber) {
		$inputNumber.focusin(function () {
			$(this).data("oldValue", $(this).val());
		});
	},


	/**
	 * Input number change event.
	 *
	 * @param $inputNumber input number element
	 * @param $btnNumber plus and minus number button elements
	 */
	change: function ($inputNumber, $btnNumber) {
		$inputNumber.change(function () {
			var minValue = parseInt(String($(this).attr("min")));
			var maxValue = parseInt(String($(this).attr("max")));
			var valueCurrent = parseInt($(this).val());
			var name = $(this).attr("name");

			if (valueCurrent > minValue) {
				$btnNumber.parent().find("[data-type='minus'][data-field='" + name + "']").removeAttr("disabled");
			} else if (valueCurrent === minValue) {
				$btnNumber.parent().find("[data-type='minus'][data-field='" + name + "']").attr("disabled", true);
			} else {
				$(this).val(String($(this).data("oldValue")));
			}

			if (valueCurrent < maxValue) {
				$btnNumber.parent().find("[data-type='plus'][data-field='" + name + "']").removeAttr("disabled");
			} else if (valueCurrent === maxValue) {
				$btnNumber.parent().find("[data-type='plus'][data-field='" + name + "']").attr("disabled", true);
			} else {
				$(this).val(String($(this).data("oldValue")));
			}
		});
	},


	/**
	 * Input number keydown event.
	 *
	 * @param $inputNumber input number element
	 */
	keydown: function ($inputNumber) {
		$inputNumber.keydown(function (e) {
			if (
				$.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 // allow: backspace, delete, tab, escape, enter
				|| (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) // allow: Ctrl/Cmd+A
				|| (e.keyCode >= 35 && e.keyCode <= 39) // allow: home, end, left, right
			) {
				// let it happen, don't do anything
				return;
			}

			// ensure that it is a number and stop the key press
			if ((e.shiftKey === true && (e.keyCode < 96 || e.keyCode > 105)) || e.keyCode < 48 || e.keyCode > 57) {
				e.preventDefault();
			}
		});
	}
};


/**
 * Form validation using jQuery validation plugin.
 */
dhMain.formValidation = {
	/**
	 * Default validation options.
	 */
	defaultOptions: {
		errorClass: "invalid-feedback",
		validClass: "valid-feedback",
		errorElement: "div",
		highlight: function (element) {
			$(element).parent().addClass("was-validated");
			$(element).addClass("invalid").removeClass("valid");
		},
		unhighlight: function (element) {
			$(element).removeClass("invalid").addClass("valid");
		}
	},


	/**
	 * Initialization.
	 */
	init: function () {
		$.validator.setDefaults(this.defaultOptions);
		this.customValidators();
		this.default();
		this.registration();
	},


	/**
	 * Custom validation functions registration.
	 */
	customValidators: function () {
		$.validator.addMethod("containLettersAndNumbers", function (value, element) {
			return this.optional(element)
				|| (
					(new RegExp(".*\\d.*")).test(value)
					&& (new RegExp(".*[a-z].*", "i")).test(value)
				)
		}, "Field must contain letters and numbers.");
	},


	/**
	 * Default form validation all form with class needs-validation.
	 */
	default: function () {
		$("form.needs-validation").validate();
	},


	/**
	 * Registration form validation (form with ID registration).
	 */
	registration: function () {
		$("form#registration").validate({
			rules: {
				password: {
					containLettersAndNumbers: true
				},
				confirmPassword: {
					equalTo: "#password"
				}
			}
		});
	}
};


/**
 * Executes when the DOM is fully loaded.
 */
$(function () {
	dhMain.plusMinusNumberInput.init();
	dhMain.formValidation.init();
});
