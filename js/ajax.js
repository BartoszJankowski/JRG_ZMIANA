/*
 * Zmienna która odpowiada za to czy w konsoli sa wypisywane zdarzenia z f.kcji LogD
 * @type {boolean}
 */
var debug = true;

$(function(){
	$.ajaxSetup({
		/*
			bedziemy wysyłac dane zawsze pod ten adres, więc warto go sobie w setupie ustawić raz na stałe
		 */
		url:'callback.php',
		/*
		do obsługi zdarzenia błędu połaczenia równiez bedziemy uzywać stałej funkcji na wszystkich podstronach
		 */
		error : OnError,
		/*
		ustalamy timeout aby nie kazac czekac uzytkownikowi zbyt długo na odpowiedź z serwera,
		jesli serwer nie odpowie w ciagu tych 15 sec js wywoła funkcje onError
		 */
		timeout : 15000
	});

	$("#login_form").on("submit", function(){
        logD('->event onsubmit start');
		submitForm();
        logD('->event onsubmit end');
		//zawsze zwracamy FALSE aby formularz nie został wysłany - zapobiegamy zdarzeniu "onsubmit"
		return false;
	})
});

function OnError(xhr){
    logD(xhr.statusText);
}

function submitForm() {


	var postData = getPostData('login_form');

	$.ajax({
		/*  [url: 'callback.php'],  poniewaz w ajaxSetup zdefiniowalismy juz url tutaj nie musimy tego robic, chyba ze chcemy go zmienic poprzez nadpisanie */
		type: 'POST',
		/*dataType: 'json', /*=> jest to prawidłowe podejscie , ale na chwilę obecna\nie ograniczajmy się, dodatkowo jak juz bedziemy wiedziec ze tylko w ten sposób serwer 
										nam odpowiada to zdefinujemy tą wartośc w ajaxSetup   */
		data: postData, /* obiekt przygotowany przez funkcję czytającą formularz - nic juz nie trzeba z nim robic */

        beforeSend : function() {
            $("#error").fadeOut();
            $('#log_in').html('<span class="glyphicon-transfer"></span> &nbsp; Logowanie ...');
        },
		//ta funkcja ma 3 parametry - wykorzystaj je
		// success(result,status,xhr)	A function to be run when the request succeeds
		success : function(response)
		{

			
			/*
			w js warto sprawdzać wartości przy uzyciu '==='
			==	equal to
			===	equal value and equal type
			w ponizszej sytuacji powinno działać poprawnie
			ale sprawy sie komplikuja jak sprawdzamy liczby i typy boolean */

			if(response.result){

			/*tutaj więcej: https://www.w3schools.com/js/js_mistakes.asp

			ta funkcja w domysle zwraca wartosc udaną - czyli otrzymała odpowiedx z serwera
			aby teraz sprawdzić co pokazac uzytkownikowi nalezy odczytac odpowiedź*/
				// if(response){

				 $('#log_in').html('<img src="img/btn-ajax-loader.gif" /> &nbsp; Logowanie ...');
	
				 // ? jaki jest/był zamysł poniższego kodu ? juz pomijam fakt ze wypadało by napisac do tego oddzielna funkcję, to czemu czekac 40 sekund???
            	setTimeout('window.location.href = "../main.php"; ');
				}
					else{
						$('#error').fadeIn(1000, function() {

							$('#error').html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.errorMsg+' !</div>');

								$('#log_in').html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Zaloguj się');

					});
				}
	// >>>>	ZATEM:
			 

			var jsonResponse = null;
            //kazdorazowo wystwietlmy sobie odpowiedź zawsze zeby poprawnie napisac .js pod odpowiedź
            logD(response);
			try {
				if(response.result){
					logD('udalo się!');
				} else {
                    logD('coś poszło nie tak - sprawdzmy teraz response.error oraz inne zmienne z odpowiedzi');
                    /*
                    tip >>do formularzy trzeba dodac jakis input z name='action' i...
                     */
				}
               // jsonResponse = JSON.parse(response);

			} catch(e){
				//musimy obsłuzyc błąd niepoprawnego odczytywania odpowiedzi itp który moze sie pojawić gdy
				// np. serwer php oprócz odpowiedzi json wyslie jakis inny string
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
	//mozemy zrobic serialize ale w tym przypadku dodatkowo mozemy obsłużyć obiekt form
    var postData = {}; //tworzymy pusty obiekt
    var form = $("#"+formIdString).get(0); //zwraca form element juz nie w instancji jQuerry
    var formElements = form.elements;
    for(x in formElements){
        //el = element HTML DOM javaScript
        var el = formElements[x];
		//pomijanie zbędnych wartości
        if(el.disabled || el.name === undefined || el.value === undefined || el.name.length<=0 || el.value.length <=0){
            continue;
        }
        //jesli wartosci nie zostały pominięte zostaną automatycznie przypisane do zwracanego obiektu
        postData[el.name] = el.value;


    }
    logD(postData);
    return postData;
}


/*
Debug
jedna fukncja do obsługi wszystkich powiadomien które nas interesują w konsoli
 */
function logD(str){
	if(debug){
		console.log(str);
	}
}
