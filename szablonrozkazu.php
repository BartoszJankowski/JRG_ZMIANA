<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 15:16
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
//$dbJednostki = new DBJednostki();
//$dbStrazacy = new DBStrazacy();
$user = new User();
$dbRozkazy = new DBRozkazy();
$info = '';
if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}


if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}
if(isset($_GET['start'])){
	$szablon = new Szablon($user->getJrgId());
	if($dbRozkazy->utworzSzablon($user, $szablon) ){
		header('Location: '.$base_url.$_SERVER['PHP_SELF'].'?edit='.$szablon->getId());
		exit;
	} else {
		$info = '<h2>Nie udało sie utworzyć szabnlonu</h2><p>'.$dbRozkazy->error.'</p>';
	}
} elseif(isset($_GET['edit'])){
	$szablon = new Szablon($user->getJrgId());
	if(!$dbRozkazy->getSzablon($user->getJrgId(), $_GET['edit'], $szablon) )
	{
		$szablon = false;
	}
}


$title = "Szablon rozkazu";
require 'header.php';
?>
<main>
	<?php
	echo $info;
		if(!isset($_GET['edit'])) :
	?>
	<form action="" method="get">
		<button type="submit" name="start" value="1">Utwórz nowy szablon</button>
	</form>
	<?  elseif($szablon) :
		echo '<p>Edycja szablonu '.$szablon->getDataSzablonu().'/'.$szablon->getId().'</p>';
		$p = new Paragraf('to jest mój paragraf :P');
		$p->print();

	  else : ?>
		<h1>Podczas wykonywania żadania wystapił bład:</h1>
	<?
		echo  '<p>'.$dbRozkazy->error.'</p>';
		endif;
	?>
</main>
<?php

require 'footer.php';