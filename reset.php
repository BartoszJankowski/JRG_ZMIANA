<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 23.01.2018
 * Time: 12:52
 */

require 'php/config.php';

if(isset($_GET['success'])){
}

if(isset($_POST['reset'])){
	$dbUsers = new DBUsers();
	if( $dbUsers->resetPass(
		test_input($_POST['email'])

		) ){
		header('Location: '.$base_url);
		exit;
	} else {
		$infoAdd = "<h3>" . $dbUsers->error . "</h3>";
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
          <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
            <link rel="stylesheet" type="text/css" href="css/login.css" />
              <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" /> 
                <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
                  <link rel="stylesheet" type="text/css" href="css/main.css" />
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <script type="text/javascript" src="js/ajaxreset.js?ver=<?php echo time() ?>"></script>
    <script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>
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

	<div id="errorreset"></div>
            <div id="inforeset" name="info" value="info"></div>

	<form id="reset" method="post" action="">
		<input type="hidden" name="action" value="reset" />


		<h2>Zresetuj hasło</h2>

		<label  class="w3-text-gray"> Login / email</label>
		<input type="email" name="email" value="<?php test_input($_POST['email']) ?>" class="w3-input" required />

		<button id="reset" type="submit" name="reset" class="btn btn-danger btn-lg btn__addJrg">Resetuj</button>
	</form>
</div>

<?php

require 'footer.php';

