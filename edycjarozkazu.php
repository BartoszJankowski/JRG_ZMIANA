<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 15:19
 */

//TODO: rozpoczac edycję w bazieDanych
//TODO: dodac uzytkownikowi wpis o rozpoczętej edycji

//TODO: sprawdzić usera który edytuje rozkaz oraz jego czas zalogowania msc aktywność

//TODO: zakonczyc edycje w bazie danych

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
$_SETTINGS->load($user->getJrgId());

if(!$user->isChef()){
    echo 'Musisz posiadac uprawnienia szefa aby edytować rozkaz.';
    echo '<a href="'.$base_url.'">Powrót</a>';
    die;
}

if(isset($_POST)){
	$_POST = test_input($_POST);
}
$ltd = new LocalDateTime($_POST['data']);
$hasSzablon = $dbRozkazy->hasActiveTemplate( $user->getStrazak()->getJrgId());
if( $hasSzablon ){
	$dbJednostki->selectJrg($user->getStrazak()->getJrgId() );
	$rozkaz = $dbRozkazy->selectRozkaz($user->getStrazak()->getJrgId(), $ltd);
	if($rozkaz){
		$szablon = new Szablon($user->getStrazak()->getJrgId());
		$dbRozkazy->getSzablon($user->getStrazak()->getJrgId(), $rozkaz->getSzablonId(), $szablon);
		$rozkaz->setSzablon($szablon->getId(), $szablon->getObiektyHtml() );
	} else {
		$strazacy = $dbStrazacy->getZmianaListStrazacy($user->getStrazak()->getJrgId(),$user->getStrazak()->getZmiana());
		$harmonogramy = $dbharmo->getJrgharmos($user->getStrazak()->getJrgId(),$ltd->getYear() );
		foreach ( $strazacy as $strazak){
			if(array_key_exists($strazak->getStrazakId(), $harmonogramy)){
				$strazak->setHarmonogram( $harmonogramy[$strazak->getStrazakId()] );
			}
		}
		$szablon = $dbRozkazy->selectCurrentOrderTemplate($user->getStrazak()->getJrgId());

		$rozkaz = new Rozkaz();
		$rozkaz->createDane($user->getStrazak()->getJrgId(),$ltd , $dbJednostki, $dbDyzury);
		$rozkaz->setSzablon($szablon['id'], unserialize($szablon['szablon']));
		$rozkaz->setFiremans($strazacy);
	}

	$rozkaz->setObjectData();

	if(isset($_POST['saveRozkaz'])){
		$rozkaz->save($_POST);
		//print_r($_POST);
		//die;
		if($dbRozkazy->saveRozkaz($user->getStrazak()->getJrgId(),$rozkaz)){\
			header('Location: '.$base_url.'/rozkazpodglad.php?data='.$_POST['data'].'&edit=1');
			exit;
        } else {
			$saveInfo = 'Podczas zapisu rozkazu wystapił bład: '.$dbRozkazy->getError();
        }
	}
}



$style = '.elo {width:300px;}';
$title = "Edycja rozkazu";
require 'header.php';
?>
	<main >
		<?php if(!$hasSzablon) :  ?>
		<h1>Brak szablonów</h1>
		<p>Twoja jednostka nie posiada szablonu rozkazu. Aby go utworzyć przejdź <a href="szablonrozkazu.php" title="Twórz szablon rozkazu" >tutaj</a> </p>

        <?php else :
                if(isset($saveInfo)){
                    echo '<h3>'.$saveInfo.'</h3>';
                }
            ?>
            <div class="w3-container"><div class="w3-container w3-border">
        <?php
            if(isset($_POST['edit']) && $user->getStrazak()->getZmiana() == $rozkaz->getZmiana()){
                echo '<form action="" method="post" ><input type="hidden" name="edit" value="1" /><input type="hidden" name="data" value="'.$_POST['data'].'" />';
                $rozkaz->displaySzablon();
                echo '<button class="w3-input  w3-margin-top" type="submit" name="saveRozkaz">Zapisz rozkaz</button></form>';
            }
            ?>
            </div></div>
        <? endif; ?>
	</main>
    <script>
        window.addEventListener("beforeunload", function (event) {
            //TODO: zakonczyc edycje funkcja ajax
            console.log("zakonczyc edycje  ajax ");
        });

    </script>
<?php

require 'footer.php';