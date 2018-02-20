// $(document).ready(function() {
// 	$('#add').click(function() {
// 		var login = escape($('#login').val());
// 		var password = escape($('#password').val());
// 		var password2 = escape($('#password2').val());
// 		var name = escape($('#name').val());
// 		var name = escape($('#name2').val());
// 		var jrg = escape($('#jrg').val());
// 		var url = "jrg_zmiana/register.php" + login + "&password=" + password + "&password2=" + password2 + "&name=" + name + "&name2=" + name2 + "&jrg=" + jrg;
// 		$.post(url);

// 	});

// });

function redirect(to, delay){
	window.setTimeout(function(){
	window.location.href = to;
	}, delay);
}

$('.form').before('<div id="info"></div>');

$('.form').on('submit', function (e){

	var login = $('#login').val(),
		password = $('#password').val(),
		string = '&login=' + login + '&password=' + password + '&action=login';

	var request = $.ajax(
		{
			url: 'main.php',
			type: 'POST',
			dataType: 'json',
			data: string
	});

	request.done(function(html) {
		var array = $.parseJSON('html');

		if (array[0] == true){
			$('#info').addClass('alert alert-succes').text(array[1]);
				redirect('main.php', 1000);
		}
			else if (array[0] == false){
				$('#info').addClass('alert alert-succes').text(array[1])
			}
	});
	
	e.preventDefault();

});