// SURELY THERE'S A BETTER WAY OF DOING THIS

if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Register = {
	pattern: /^[a-zA-Z0-9]{3,20}$/,
	
	IsUsernameValid: function(input) {
		return input.trim().length != 0 && ANORRL.Register.pattern.test(input);
	},

	CheckUsername: function(element, input) {
		if(input.length != 0) {
			if(ANORRL.Register.IsUsernameValid(input)) {
				$("#v_username").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_username").html("a-z A-Z 0-9 and 3-20 characters only!");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
		} else {
			$("#v_username").html("");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	},
	CheckMainPassword: function(element, input) {

		var confirmPasswordElement = $("#ANORRL_Signup_ConfirmPassword");
	
		if(input.length != 0) {
			if(input.length >= 7) {
				$("#v_password").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_password").html("Password must be minimum 7 characters!");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
	
			if(input != confirmPasswordElement.val()) {
				$("#v_confirmpassword").html("Passwords do not match!");
				confirmPasswordElement.addClass("Invalid");
				confirmPasswordElement.removeClass("Valid");
			} else {
				$("#v_confirmpassword").html("");
				confirmPasswordElement.removeClass("Invalid");
				confirmPasswordElement.addClass("Valid");
			}
		} else {
			$("#v_password").html("");
			confirmPasswordElement.removeClass("Invalid");
			confirmPasswordElement.removeClass("Valid");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	},

	CheckSecondPassword: function(element, input) {
		if(input.length != 0) {
			if(input == $("#ANORRL_Signup_Password").val()) {
				$("#v_confirmpassword").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_confirmpassword").html("Passwords do not match!");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
		} else {
			$("#v_confirmpassword").html("");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	},
	CheckAccessKey: function(element, input) {
		if(input.length != 0) {
			if(input.length == 36) {
				$("#v_access").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_access").html("Invalid access key.");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
		} else {
			$("#v_access").html("");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	}
}

ANORRL.Login = {
	pattern: /^[a-zA-Z0-9]{3,20}$/,
	
	IsUsernameValid: function(input) {
		return $.trim(input).length != 0 && ANORRL.Login.pattern.test($.trim(input));
	},

	CheckUsername: function(element, input) {
		
		if(input.length != 0) {
			if(ANORRL.Login.IsUsernameValid(input)) {
				$("#v_username").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_username").html("a-z A-Z 0-9 and 3-20 characters only!");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
		} else {
			$("#v_username").html("");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	},
	CheckPassword: function(element, input) {
		if(input.length != 0) {
			if(input.length >= 7) {
				$("#v_password").html("");
				$(element).removeClass("Invalid");
				$(element).addClass("Valid");
			} else {
				$("#v_password").html("Password must be minimum 7 characters!");
				$(element).addClass("Invalid");
				$(element).removeClass("Valid");
			}
		} else {
			$("#v_password").html("");
			$(element).removeClass("Valid");
			$(element).removeClass("Invalid");
		}
	}
}