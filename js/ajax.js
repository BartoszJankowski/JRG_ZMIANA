
var debug = true;

$(function(){
	$.ajaxSetup({

		url:'callback.php',
		error : OnError,
		timeout : 15000
	});

	$("#addUserInfo").on("submit", function(){
        logD('->event onsubmit start');
		submitForm();
        logD('->event onsubmit end');
		
		return false;
	})
});

function OnError(xhr){
    logD(xhr.statusText);
}

function submitForm() {

	var postData = getPostData('addUserInfo');
	

	$.ajax({
		type: 'POST',
		data: postData,

        beforeSend : function() {
            $("#errorlog").fadeOut();
            $('#log_in').html('<span class="glyphicon-transfer"></span> &nbsp; Logowanie ...');

        },
		
		}).done(function(response) {
				logD(response);
			if(response.result){

				 $('#log_in').html('<img src="img/btn-ajax-loader.gif" /> &nbsp; Logowanie ...');
					setTimeout('window.location.href = "../main.php"; ');
				}

					else{
						$('#errorlog').fadeIn(1000, function() {
							$('#errorlog').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.errorMsg+' !</div>');
								$('#log_in').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Zaloguj się');

					});
				}

			var jsonResponse = null;
	  });	

	return false;
};

function getPostData(formIdString){

    var postData = {};
    var form = $("#"+formIdString).get(0);
    var formElements = form.elements;
    for(x in formElements){

        var el = formElements[x];

        if(el.disabled || el.name === undefined || el.value === undefined || el.name.length<=0 || el.value.length <=0){
            continue;
        }
		 postData[el.name] = el.value;


    }
    logD(postData);
    return postData;
}

function logD(str){
	if(debug){
		console.log(str);
	}
}

