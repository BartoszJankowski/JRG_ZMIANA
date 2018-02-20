
$(document).ready(function() {
		// alert('załadowane kurwa ! ! ! ! !');
	
	// $("#login-form").validate({
	// 	rules:
	// 	{
	// 		password: {
	// 			required: true,
	// 		},
	// 		login: {
	// 			required: true,
	// 			email: true
	// 			},
	// 	},
	// 	messages:
	// 	{
	// 		password:{
	// 					required: "proszę wprowadzić hasło"
	// 				},
	// 		login: "proszę wprwadzić login/email",
	// 	},
	// 	submitHandler: submitForm
	// 	});

function submitForm() {
	
	var data = $("#login-form").serialize();

	$.ajax({

		url: 'callback.php',
		type: 'POST',
		dataType: 'json',
		data: data,
		beforeSand: function() {
			$("#error").fadeOut();
			$('#log_in').html('<span class="glyphicon-transfer"></span> &nbsp; sanding ...');
				},
		success : function(response)
		{
			if(response=='ok'){

				 $('#log_in').html('<img src="btn-ajax-loader.gif" /> &nbsp; Signin In ...');
				 setTimeout(' window.location.href = "main.php"; ', 40000);
			}
			else{
				$('#error').fadeIn(1000, function() {
					
					$('#error').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response+' !</div>');

						$('#log_in').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');

				});
			}
		}
	  });	
				return false;
		}
	});
