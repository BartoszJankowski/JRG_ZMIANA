
var debug = true;

$(function(){
	$.ajaxSetup({

		url:'callback.php',
		error : OnError,
		timeout : 15000
	});

	$("#Jrg").on("submit", function() {
		logD('->event onsubmit start');
		submitFormJrg();
        logD('->event onsubmit end');
		
		return false;
	})
});

function OnError(xhr){
    logD(xhr.statusText);
}

function submitFormJrg() {

	var postJrg = getPostData('Jrg');

	$.ajax({
		type: 'POST',
		data: postJrg,

        beforeSend : function() {
            $("#erroradd").fadeOut();
            $("#infoadd").fadeOut();
             $('#addJrg').html('<span class="glyphicon-transfer"></span> &nbsp; Wysłanie ...');

        },
	  }).done(function(response) {
	  	// debugger
			if(response.result){
					$('#infoadd').fadeIn(1000, function() {
							$('#infoadd').html('<div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;'+response.info+' !</div>');
								$('#addJrg').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Dodano');
									$('#addJrg').attr('disabled', '1');

					});
			}
				else{

						$('#erroradd').fadeIn(1000, function() {
							$('#erroradd').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.errorMsg+' !</div>');
								$('#addJrg').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Dodaj');

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