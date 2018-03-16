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
$_SETTINGS->load($user->getJrgId());

if(isset($_POST)){$_POST = test_input($_POST);}
if(isset($_GET)){$_GET = test_input($_GET);}
try {
	if($user->getStrazak() == null){
		throw new UserErrors('Nie zostałeś jeszcze przypisany do zmiany aby móc przegladać rozkazy dzienne.');
	}
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
	} else {
	    $info = '<h1>Brak szablonów</h1>
	<p>Twoja jednostka nie posiada szablonu rozkazu.</p>';
    }
} catch (UserErrors $user_errors){
	$info = '<h3>'.$user_errors->getMessage().'</h3>';
}




$title = "Rozkaz dzienny";
require 'header.php';
?>
<main>
	<?php if(isset($info)) :

        echo $info;

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
	<form action="" class="noprint" method="get" >
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
        <div id="printable" class="w3-container w3-border podglad-rozkazu w3-margin">
			<?php
			$rozkaz->displayRozkaz();

			if($user->getStrazak()->getZmiana() == $rozkaz->getZmiana() && $user->isChef()) : ?>
			<form class="noprint" action="edycjarozkazu.php" method="post" >
				<input type="hidden" name="data" value="<? echo $ltd->getMySqlDate(); ?>">
				<button  class="w3-input w3-margin-top"  type="submit" name="edit">Rozpocznij edycję</button>
			</form>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</main>

<?php

require 'footer.php';
