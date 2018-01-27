<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 23.01.2018
 * Time: 13:29
 */

require 'php/config.php';

if(isset($_POST['register'])){
	$dbusers = new DBUsers();
	if($dbusers->registerNewUser(
		test_input($_POST['login']),
		test_input($_POST['password']),
		test_input($_POST['confirm_password']),
		test_input($_POST['jrg']),
		test_input($_POST['name']),
		test_input($_POST['surname'])
	) ) {
		header('Location: '.$base_url.'/register.php?succes');
		exit;
	} else {
		echo $dbusers->error;
	}
}

if(isset($_GET['succes'])){
	$infoAdd = '<h3>Twoje konto zostało utworzone. Możesz się zalogować <a href="http://zmiana.bjit.pl/login.php">tutaj</a>. Na Twoją pocztę zostało wysłane potwierdzenie rejestracji.</h3>';

}


$jednostki = new DBJednostki();
$res = $jednostki->getJrgList();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style type="text/css">

	</style>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="/js/jquery.validate.min.js"></script>
</head>

<body>
<div class="w3-container w3-third w3-border w3-margin w3-padding-16">
	<!--
		Formularz do rejestracji uzytkownika.
	-->
	<form method="post" action="" id="formRegister">
		<h2>Rejestracja</h2>
		<?php
		echo $infoAdd;
		?>
		<div>
				<label  class="w3-text-gray"> Email*</label>
				<input type="email" name="login" value="<?php echo $_POST['email'] ?>" class="w3-input" required />
		</div>
		<div>
				<label class="w3-text-gray"> Hasło*</label>
				<input type="password" id="password" name="password" value="" class="w3-input" required />
		</div>
		<div>
				<label class="w3-text-gray"> Powtórz hasło*</label>
				<input type="password" name="confirm_password" value="" class="w3-input" required />
		</div>
		<div>
				<label  class="w3-text-gray"> Imię</label>
				<input type="text" name="name" value="" class="w3-input"  />
		</div>
		<div>
				<label  class="w3-text-gray"> Nazwisko</label>
				<input type="text" name="surname" value="" class="w3-input"  />
		</div>
		<div>
				<label class="w3-text-gray">Wybierz jednostkę</label>
				<select name="jrg" class="w3-select">
					<option disabled selected> Rozwiń listę JRG</option>
					<?php
						if(is_array($res)){
							foreach($res as $id=>$val){
								echo '<option value="'.$id.'"> jrg '.$val.'</option>';
							}
						}
					?>
				</select>
		</div>

		<input class="w3-input w3-margin-top" type="submit" name="register" value="Zarejestruj"/>

	</form>
</div>



<script>
	/**
	 * jqueryValidaton dla formularza #formRegister
	 * jquery Lib required
	 */
    $("#formRegister").validate({
        rules: {
            login : {
                required : true,
	            email : true
            },
            password : {
                required : true,
                minlength : 8
            },
            confirm_password : {
                required : true,
                minlength : 8,
                equalTo : "#password"
            }
        }
    });
</script>
<?php

require 'footer.php';


