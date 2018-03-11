
var debug = true;

$(function(){
	$.ajaxSetup({

		url:'callback.php',
		error : OnError,
		timeout : 15000
	});

	$("#reset").on("submit", function() {
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

	var postreset = getPostData('reset');

	$.ajax({
		type: 'POST',
		data: postreset,

        beforeSend : function() {
            $("#error").fadeOut();
            $("#info").fadeOut();
             $('#reset').html('<span class="glyphicon-transfer"></span> &nbsp; Wysłanie ...');
        },
	  }).done(function(response) {
	  	debugger
			if(response.result){
					debugger
					$('#info').fadeIn(1000, function() {
							$('#info').html('<div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;'+response.info+' !</div>');
								$('#reset').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Zresetowano');
					});
			}
				else{

						$('#error').fadeIn(1000, function() {
							$('#error').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.errorMsg+' !</div>');
								$('#reset').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; <a href="../register.php">Zarejestruj się</a>');

					});
				}
			 
			var jsonResponse = null;
		});

	return false;
}


/**
 * Funkcja przegląda formularz i zapisuje dane z formularza do żądania AJAX [name]=value
 * funkcja pomija wartości puste, pola formularza zablokowane i niezdeklarowane
 * @param formIdString
 * @returns dane do wysłania do serwera
 */
function getPostData(formIdString){

    var postreset = {};
    var form = $("#"+formIdString).get(0);
    var formElements = form.elements;
    for(x in formElements){

        var el = formElements[x];

        if(el.disabled || el.name === undefined || el.value === undefined || el.name.length<=0 || el.value.length <=0){
            continue;
        }
		 postreset[el.name] = el.value;
    }

    // logD(postreset);
    return postreset;
}

function logD(str){
	if(debug){
		console.log(str);

}
}