<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 15:19
 */


session_start();
require 'php/config.php';
require 'php/Szablon.class.php';
$dbUsers     = new DBUsers();

$user = new User();
$dbRozkazy = new DBRozkazy();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$dbharmo = new DBHarmonogramy();
$dbDyzury = new DbDyzuDomowy();


if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
$ltd = new LocalDateTime('2018-01-18');
$dbJednostki->selectJrg($user->getStrazak()->getJrgId() );
$szablony = $dbRozkazy->FselectFinSzablony($user->getStrazak()->getJrgId());





if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}

$strazacy = $dbStrazacy->getZmianaListStrazacy($user->getStrazak()->getJrgId(),$user->getStrazak()->getZmiana());
$harmonogramy = $dbharmo->getJrgharmos($user->getStrazak()->getJrgId(),$ltd->getYear() );
foreach ( $strazacy as $strazak){
	if(array_key_exists($strazak->getStrazakId(), $harmonogramy)){
		$strazak->setHarmonogram( $harmonogramy[$strazak->getStrazakId()] );
	}
}



$title = "Rozkaz";
require 'header.php';
?>
	<main>
		<?php if(!$szablony) :  ?>
		<h1>Brak szablonów</h1>
		<p>Twoja jednostka nie posiada szablonu rozkazu. Aby go utworzyć przejdź <a href="szablonrozkazu.php" title="Twórz szablon rozkazu" >tutaj</a> </p>

        <?php else :
            echo '<div class="w3-half">';
            $rozkaz = new Rozkaz($user->getStrazak()->getJrgId(), unserialize($szablony[0]['szablon']), $ltd , $dbJednostki);
            $rozkaz->setFiremans($strazacy);
            //print_r($szablony[0]);
            $rozkaz->displaySzablon();
			echo '</div>';
            ?>

        <? endif; ?>
	</main>
<?php

require 'footer.php';