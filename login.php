<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 20:52
 */

session_start();
require 'config.php';
$user = new User();

if($user->checkSession()){
	header('Location: '.$base_url.'/main.php');
	exit;
}

if(isset($_POST['log_in'])){
     if(!$user->login($_POST['login'],$_POST['password'])){
         echo $user->error;
     } else {
         header('Location: '.$base_url.'/main.php');
         exit;
     }
}

//TODO: rejestracja
//TODO: reset hasła


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
</head>

<body>
<div class="w3-container w3-third w3-border w3-margin w3-padding-16">
	<!--
		Formularz do logowania uzytkownika.
	-->
	<form method="post" action="">
		<h2>Zaloguj się</h2>
		<?php
		echo $infoAdd;
		?>

		<label  class="w3-text-gray"> Login / email</label>
		<input type="email" name="login" value="<?php echo $_POST['email'] ?>" class="w3-input" required />

		<label class="w3-text-gray"> Hasło</label>
		<input type="password" name="password" value="" class="w3-input" required />

		<input class="w3-input w3-margin-top" type="submit" name="log_in" value="Zaloguj"/>
		<div class="w3-container">
			<a class="w3-left" href="register.php" target="_self">Rejestracja</a>
			<a class="w3-right" href="?" target="_self">Zapomniałeś hasła?</a>
		</div>
	</form>
</div>

</body>
</html>
