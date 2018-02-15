<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 13.02.2018
 * Time: 22:50
 */

session_start();
require 'php/config.php';
require 'php/Szablon.class.php';
$dbUsers     = new DBUsers();
$user = new User();
$dbRozkazy = new DBRozkazy();
if(!$dbUsers->checkSession($user)){header('Location: '.$base_url.'/login.php');exit;}


if(isset($_POST)){$_POST = test_input($_POST);}
if(isset($_GET)){$_GET = test_input($_GET);}
$hasSzablon = $dbRozkazy->hasActiveTemplate( $user->getStrazak()->getJrgId());

if($hasSzablon){
	$ltd = new LocalDateTime($_GET['data']);

	$dbJednostki = new DBJednostki();
	$dbJednostki->selectJrg($user->getStrazak()->getJrgId() );

	$rozkaz = $dbRozkazy->selectRozkaz($user->getStrazak()->getJrgId(), $ltd);

	if($rozkaz){
		$szablon = new Szablon($user->getStrazak()->getJrgId());
		$dbRozkazy->getSzablon($user->getStrazak()->getJrgId(), $rozkaz->getSzablonId(), $szablon);
		$rozkaz->setSzablon($szablon->getId(), $szablon->getObiektyHtml() );
	} else {
		$rozkaz = new Rozkaz();
		$szablon = $dbRozkazy->selectCurrentOrderTemplate($user->getStrazak()->getJrgId());
		$rozkaz->createDane($user->getStrazak()->getJrgId(),$ltd , $dbJednostki);
		$rozkaz->setSzablon($szablon['id'], unserialize($szablon['szablon']));
	}

	$rozkaz->setObjectData();
}



$title = "Rozkaz dzienny";
require 'header.php';
?>
<main>
	<?php if(!$hasSzablon) :  ?>
		<h1>Brak szablonów</h1>
	<p>Twoja jednostka nie posiada szablonu rozkazu.</p>
	<?php

	echo ($user->isChef() ? '<p>Aby go utworzyć przejdź <a href="szablonrozkazu.php" title="Twórz szablon rozkazu" >tutaj</a> </p>': '');

	else :
			$ltd->addDays(-3)->getDayOfMsc();
			$poprzedniaSluzba  = $ltd->getMySqlDate();
			$ltd->addDays(2)->getDayOfMsc();
            $dzienPoprzedni  = $ltd->getMySqlDate();

			$ltd->addDays(2)->getDayOfMsc();
			$dziennastepny  = $ltd->getMySqlDate();
			$ltd->addDays(2);
			$nastepnaSluzba  = $ltd->getMySqlDate();
			$ltd->addDays(-3);
            ?>
	<form action="" method="get" >
		<h1 class="w3-center">
			<button name="data" value="<?php echo $poprzedniaSluzba ?>" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-angle-double-left"></i></button>
			<button name="data" value="<?php echo $dzienPoprzedni ?>" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-angle-left"></i></button>
			<span><?php echo $ltd->getDate() ?></span>
			<button name="data" value="<?php echo $dziennastepny; ?>" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-angle-right"></i></button>
			<button name="data" value="<?php echo $nastepnaSluzba; ?>" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-angle-double-right"></i></button>
		</h1>
	</form>
	<?php
	if(isset($_GET['edit']) && $_GET['edit']==1){
		echo '<h2>Poprawnie zapisano rozkaz dzienny</h2>';
	}
	?>
	<div class="w3-container">
		<div class="w3-container w3-border podglad-rozkazu">
			<?php
			$rozkaz->displayRozkaz();

			if($user->getStrazak()->getZmiana() == $rozkaz->getZmiana() && $user->isChef()) : ?>
			<form action="edycjarozkazu.php" method="post" >
				<input type="hidden" name="data" value="<? echo $ltd->getMySqlDate(); ?>">
				<button  class="w3-input w3-margin-top"  type="submit" name="edit">Rozpocznij edycję</button>
			</form>
			<? endif; ?>
		</div>
	</div>
	<? endif; ?>
</main>

<?php

require 'footer.php';
