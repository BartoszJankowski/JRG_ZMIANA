<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 22:50
 */
session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
//$dbJednostki = new DBJednostki();
$dbSettings = new DBJrgSettings();
$dbRozkazy = new DBRozkazy();
$dbStrazacy = new DBStrazacy();
$dbharmo = new DBHarmonogramy();
$dbDyzury = new DbDyzuDomowy();
$user = new User();

$info = '';
if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
$_SETTINGS->load($user->getJrgId());

$ldt = new LocalDateTime();

if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
	if(isset($_GET['month'],$_GET['year'])){
		$ldt = new LocalDateTime($_GET['year'].'-'.$_GET['month'].'-1');
    }
	if(isset($_GET['mscAction'])){
		if($_GET['mscAction'] > 0){
			$ldt->add(new DateInterval('P'.$_GET['mscAction'].'M'));
		} else {
			$ldt->sub(new DateInterval('P'.abs($_GET['mscAction']).'M'));
		}
		header('Location: '.$base_url.$_SERVER['PHP_SELF'].'?month='.$ldt->getMonth().'&year='.$ldt->getYear());
		exit;
	}
}


$ddomowe = $dbDyzury->loadDyzuryZmianyNaMsc($user->getJrgId(),$user->getStrazak()->getZmiana(),$ldt->getYear(), $ldt->getMonth());

if($ddomowe==null){
	$ddomowe = new DyzuryDomowe(null, $user->getStrazak()->getZmiana(), $ldt->getYear(), $ldt->getMonth());
	$ddomowe->createEmpty();
	$dbDyzury->addNewDD($ddomowe, $user->getJrgId() );
}


$ddomowe->setHarmo($dbharmo->getJrgharmos($user->getJrgId(),$ldt->getYear() ));
$ddomowe->setStrazacy($dbStrazacy->getZmianaListStrazacy($user->getJrgId(), $user->getStrazak()->getZmiana()));

if(isset($_POST['saveDD'])){
	$ddomowe->fillWithPostData($_POST);
	if($dbDyzury->updateDD($user->getJrgId(), $user->getStrazak()->getZmiana(), $ddomowe)){
		header('Location: '.$base_url.$_SERVER['PHP_SELF'].'?month='.$ldt->getMonth().'&year='.$ldt->getYear());
	exit;
	} else {
	    $info = '<h2>Błąd podczas zapisu dyżurów domowych. '.$dbDyzury->error.'</h2>';
    }

} else if(isset($_POST['fill'])){
	$ddomowe->autoUzupelnienie($_POST);
}

$title = "Dyżury domowe";
require 'header.php';


//TODO: js editing
?>
	<main class="w3-container" >
        <form action="" method="get" >
            <input type="hidden" name="month" value="<?php echo $ldt->getMonth(); ?>"> <input type="hidden" name="year" value="<?php echo $ldt->getYear(); ?>">
            <h1 class="w3-center w3-border-bottom"><button name="mscAction" value="-1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-left"></i></button><span style="width: 25%;display: inline-block"><?php echo get_moth_name($ldt->getMonth()).' '.$ldt->getYear(); ?></span><button name="mscAction" value="1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-right"></i></button></h1>
            <div class="w3-center w3-large" style="font-variant: small-caps;">
                <label class="switch">
                    <input id="slide_type" type="checkbox"><span class="slider"></span><span class="rightLabel">Grafik</span><span class="leftLabel">Lista</span>
                </label>
            </div>
        </form>
		<?php
            echo $info;
		?>
        <div class="w3-row">

        </div>
        <div id="dyzury_grafik" class="w3-container  w3-threequarter" style="display: none">
            <form action="" method="post" >
			<?php
			$ddomowe->printDyzuryDiff();
			?>
                <button type="submit" name="saveDD" class="w3-input">Zapisz</button>
            </form>
        </div>
        <div id="dyzury_list" class="w3-container  w3-threequarter">
            <div class="w3-container w3-twothird w3-center">
                <form action="" method="post" >
                    <table class="w3-table-all w3-small table-grafik">
				        <?php
				        PozycjaDD::printNaglowek();
				        $ddomowe->printDyzury();
				        ?>
                    </table>
                    <button type="submit" name="saveDD" class="w3-input">Zapisz</button>
                </form>
            </div>
            <div class="w3-container w3-third">

                <table class="w3-table-all w3-hoverable table-grafik">

			        <?php
			        $ddomowe->printStrazacyInRows();
			        ?>
                </table>
            </div>

        </div>

        <div class="w3-container w3-quarter">
            <h2>Uzupełnij automatycznie</h2>
            <form action="" method="post">
                <p>Liczba dyżurów na dzień</p>
                <select name="maxDD" class="w3-select w3-border">
                    <option value="1" >1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                <p>Wybierz uprawnienia wymagane na każdy dzień dyżuru: </p>
                <?php
                    foreach (DBJrgSettings::getUprawnienia() as $uprawnienie){
                        echo '<div><label><input type="checkbox" class="w3-check" name="wymagane[]" '.((isset($_POST['wymagane']) && in_array($uprawnienie->getId(),$_POST['wymagane'] ))? "checked":"").' value="'.$uprawnienie->getId().'" ><msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></msc>  '.$uprawnienie->getName().'</label></div>';
                    }
                ?>
                <p>Zaznacz aby ustawić strażaka zawsze na dyżurze przed WS</p>
                <label><input class="w3-check" type="checkbox" name="przedWS" value="1"  <?php echo isset($_POST['przedWS'])? "checked":""; ?>  >Ustaw dyżur przed każdą WS </label>

                <p>Zaznacz aby uzupełnić pozostałe puste miejsca</p>
                <label><input class="w3-check" type="checkbox" name="fillUp" value="1"  <?php echo isset($_POST['fillUp'])? "checked":""; ?>  >Uzupełnij puste pola</label>

                <div class="w3-margin-top">
                    <button type="submit" class="w3-input" name="fill" ><i class="fas fa-magic"></i> Uzupełnij</button>
                </div>
            </form>
        </div>




	</main>
    <script>

         var strazacyIn =<?php echo $ddomowe->getStrazacyInPrev();?>;
    </script>
<script type="text/javascript" src="js/dyzurydomowe.js?ver=<?php echo time(); ?>" />

<?php

require 'footer.php';