<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 22:22
 */

session_start();
require 'config.php';
$user = new User();

if(!$user->checkSession()){
	header('Location: '.$base_url.'/login.php');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-main</title>
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
		Formularz aby dodac jednostkę: nr jrg, miasto i email wymagane.
		 Tworzy jrg i uzytkownika. Inforumuje o błedach:
		# istnieje juz nr jednostki dla tego miasta
		# wewnętrzne bł\edy bazy danych
		# problem z wysłaniem email
	-->
	<h1>Witaj, <?php echo $_SESSION['login'] ?></h1>
</div>

</body>
</html>