<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 20:52
 */

session_start();
require 'php/config.php';
$dbUsers = new DBUsers();
$user = new User();

if($dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/main.php');
	exit;
}

if(isset($_POST['log_in'])){
     if(!$dbUsers->login($_POST['login'],$_POST['password']))
     {
         echo $dbUsers->error;
     } else {
         header('Location: '.$base_url.'/main.php');
         exit;
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
    <nav>
        <div class="w3-bar w3-border w3-light-grey">
            <a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button w3-green">
                <i class="fa fa-fw fa-home w3-xlarge"></i>
                    <div class="w3-small">Strona główna</div></a>
                    
            <?php if($user->logged):?>

                    <a  href="<?php echo $base_url; ?>/main.php" class="w3-bar-item w3-button">
                        <i class="fa fa-fw fa-user w3-xlarge"></i>
                            <div class="w3-small">Konto</div></a>
                                <a  href="<?php echo $base_url; ?>/main.php?logout=1" class="w3-bar-item w3-button">
                                    <i class="fa fa-fw fa-sign-out w3-xlarge"></i>
                                        <div class="w3-small">Wyloguj się</div></a>
            <?php else : ?>
                                             <a  href="<?php echo $base_url; ?>/login.php" class="w3-bar-item w3-button">
                                                <i class="fa fa-fw fa-sign-in w3-xlarge"></i>
                                                    <div class="w3-small">Zaloguj się</div></a>

            <?php endif; ?>
        </div>
    </nav>
        <h1>Twój harmonogram, rokaz i kalendarz w jednym miejscu</h1>
</header>
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
			<a class="w3-right" href="reset.php" target="_self">Zapomniałeś hasła?</a>
		</div>
	</form>
</div>
<?php

require 'footer.php';

