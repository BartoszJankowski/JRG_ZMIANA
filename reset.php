<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 23.01.2018
 * Time: 12:52
 */

require 'config.php';

if(isset($_GET['succes'])){
	$infoAdd = "<h3>Twoje hasło zostało zresetowane. Sprawdź skrzynkę email.</h3>";
}

if(isset($_POST['reset'])){
	$user = new User();
	if( $user->resetPass(test_input($_POST['email'])) ){
		header('Location: '.$base_url.'/reset.php?succes=1');
		exit;
	} else {
		$infoAdd = "<h3>".$user->error."</h3>";
	}
}
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
<header>
	<div class="w3-bar w3-border w3-light-grey">
		<a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button w3-green"><i class="fa fa-fw fa-home w3-xlarge"></i><div class="w3-small">Strona główna</div></a>
	</div>
</header>
<div class="w3-container w3-third w3-border w3-margin w3-padding-16">
	<!--
		Formularz do logowania uzytkownika.
	-->
	<form method="post" action="">
		<h2>Zresetuj hasło</h2>
		<?php
		echo $infoAdd;
		?>

		<label  class="w3-text-gray"> Login / email</label>
		<input type="email" name="email" value="<?php test_input($_POST['email']) ?>" class="w3-input" required />

		<input class="w3-input w3-margin-top" type="submit" name="reset" value="Resetuj"/>
	</form>
</div>

</body>
</html>
