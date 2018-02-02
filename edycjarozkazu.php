<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 15:19
 */


session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
//$dbJednostki = new DBJednostki();
//$dbStrazacy = new DBStrazacy();
$user = new User();
$dbRozkazy = new DBRozkazy();

if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}

$szablony = $dbRozkazy->FselectFinSzablony($user->getJrgId());

if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}




$title = "Rozkaz";
require 'header.php';
?>
	<main>
		<?php if(!$szablony) :  ?>
		<h1>Brak szablonów</h1>
		<p>Twoja jednostka nie posiada szablonu rozkazu. Aby go utworzyć przejdź <a href="szablonrozkazu.php" title="Twórz szablon rozkazu" >tutaj</a> </p>
		<? endif; ?>
	</main>
<?php

require 'footer.php';