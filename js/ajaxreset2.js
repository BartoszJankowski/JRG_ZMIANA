
var debug = true;

$(function(){
	$.ajaxSetup({

		url:'callback.php',
		error : OnError,
		timeout : 15000
	});

	$("#reset").on("submit", function() {
		logD('->event onsubmit start');
		submitFormReset();
        logD('->event onsubmit end');
		
		return false;
	})
});

function OnError(xhr){
    logD(xhr.statusText);
}

function submitFormReset() {

	var postReset = getPostData('reset');

	$.ajax({
		type: 'POST',
		data: postReset,

        beforeSend : function() {
            $("#errorreset").fadeOut();
            // $("#info").fadeOut();
             $('#reset').html('<span class="glyphicon-transfer"></span> &nbsp; Wysłanie ...');
        },
	  }).done(function(response) {
	  	// debugger
			if(response.result){
					// debugger
					$('#inforeset').fadeIn(1000, function() {
							$('#inforeset').html('<div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;'+response.info+' !</div>');
								$('#reset').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Zresetowano');
					});
			}
				else{

						$('#errorreset').fadeIn(1000, function() {
							$('#errorreset').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.errorMsg+' !</div>');
								$('#reset').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; <button class="btn btn-danger btn-lg btn__register" data-toggle="modal" data-target="#myModal1">Zarejestruj się</button>');

					});
				}
			 
			var jsonResponse = null;

			logD(response.result);
					try {
						if(response){
							logD('udalo się!');
						} 
						else {
		                    logD('coś poszło nie tak '+response.info+' - sprawdzmy teraz response.error oraz inne zmienne z odpowiedzi');
						}
					} 
					catch(e){

						logD(e.message)
					}
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

    var postReset = {};
    var form = $("#"+formIdString).get(0);
    var formElements = form.elements;
    for(x in formElements){

        var el = formElements[x];

        if(el.disabled || el.name === undefined || el.value === undefined || el.name.length<=0 || el.value.length <=0){
            continue;
        }
		 postReset[el.name] = el.value;
    }

    logD(postReset);
    return postReset;
}

function logD(str){
	if(debug){
		console.log(str);

}
}