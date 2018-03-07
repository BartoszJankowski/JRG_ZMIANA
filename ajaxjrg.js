
var debug = true;

$(function(){
	$.ajaxSetup({

		url:'callback.php',
		error : OnError,
		timeout : 15000
	});

	$("#Jrg").on("submit", function() {
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

	var postJrg = getPostData('Jrg');

	$.ajax({
		type: 'POST',
		data: postJrg,

        beforeSend : function() {
            $("#error").fadeOut();
            $("#success").fadeOut();
             $('#addJrg').html('<span class="glyphicon-transfer"></span> &nbsp; Wysłanie ...');
             alert('beforeSend !');
        },
		
		success : function(response){

			if(response.result){
				
					$('#success').fadeIn(1000, function() {
							$('#success').html('<div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign">kurwa wa mac</span> &nbsp;'+response.result.info+' !</div>');
								$('#addJrg').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Dodano');

					});
				}

				//  $('#addJrg').html('<span class="glyphicon-transfer"></span> &nbsp; Dodano!');
				// 		$('#success').html('<div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+result.info+' !</div>');
				// }
					else{

						$('#error').fadeIn(1000, function() {
							$('#error').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign">hi hi hi</span> &nbsp; '+response.errorMsg+' !</div>');
								$('#addJrg').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Dodaj');
								logD(response.errorMsg);

					});
				}
			 
			var jsonResponse = null;

            logD(response.reault);
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

    var postJrg = {};
    var form = $("#"+formIdString).get(0);
    var formElements = form.elements;
    for(x in formElements){

        var el = formElements[x];

        if(el.disabled || el.name === undefined || el.value === undefined || el.name.length<=0 || el.value.length <=0){
            continue;
        }
		 postJrg[el.name] = el.value;


    }
    logD(postJrg);
    return postJrg;
}

function logD(str){
	if(debug){
		console.log(str);

}
}