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
$dbSettings->load($user->getStrazak()->getJrgId());
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


$ddomowe = $dbDyzury->loadDyzuryZmianyNaMsc($user->getStrazak()->getJrgId(),$user->getStrazak()->getZmiana(),$ldt->getYear(), $ldt->getMonth());

if($ddomowe==null){
	$ddomowe = new DyzuryDomowe(null, $user->getStrazak()->getZmiana(), $ldt->getYear(), $ldt->getMonth());
	$ddomowe->createEmpty();
	$dbDyzury->addNewDD($ddomowe, $user->getStrazak()->getJrgId() );
}



$ddomowe->setSettings($dbSettings);
$ddomowe->setHarmo($dbharmo->getJrgharmos($user->getStrazak()->getJrgId(),$ldt->getYear() ));
$ddomowe->setStrazacy($dbStrazacy->getZmianaListStrazacy($user->getStrazak()->getJrgId(), $user->getStrazak()->getZmiana()));

if(isset($_POST['saveDD'])){
	$ddomowe->fillWithPostData($_POST);
	if($dbDyzury->updateDD($user->getStrazak()->getJrgId(), $user->getStrazak()->getZmiana(), $ddomowe)){
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
	<main class="w3-container">
        <form action="" method="get" >
            <input type="hidden" name="month" value="<?php echo $ldt->getMonth(); ?>"> <input type="hidden" name="year" value="<?php echo $ldt->getYear(); ?>">
            <h1 class="w3-center w3-border-bottom"><button name="mscAction" value="-1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-left"></i></button><span style="width: 25%;display: inline-block"><?php echo get_moth_name($ldt->getMonth()).' '.$ldt->getYear(); ?></span><button name="mscAction" value="1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-right"></i></button></h1>
            <div class="w3-center w3-large" style="font-variant: small-caps;">dyżury domowe</div>
        </form>
		<?php
            echo $info;
		?>
        <div class="w3-container w3-half w3-center">
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
        <div class="w3-container w3-quarter">

            <table class="w3-table-all w3-hoverable table-grafik">

		        <?php
		        $ddomowe->printStrazacyInRows();
                ?>
            </table>
        </div>
        <div class="w3-container w3-quarter">
            <h2>Uzupełnij automatycznie</h2>
            <form action="" method="post">
                <p>Wybierz uprawnienia wymagane na każdy dzień dyżuru: </p>
                <?php
                    foreach ($dbSettings->getUprawnienia() as $uprawnienie){
                        echo '<div><label><input type="checkbox" class="w3-check" name="wymagane[]" '.((isset($_POST['wymagane']) && in_array($uprawnienie->getId(),$_POST['wymagane'] ))? "checked":"").' value="'.$uprawnienie->getId().'" ><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></i>  '.$uprawnienie->getName().'</label></div>';
                    }
                ?>
                <!--
                <p>Wybierz minimalny i maksymalny stan dyżuru: </p>
                <label>Minimalnie: </label><span></span>
                <input class="w3-input range" type="range" name="range[]" min="1" max="<?php echo DyzuryDomowe::LP_POL ?>" value="1">
                <label>Maksymalnie: </label><span></span>
                <input class="w3-input range" type="range" name="range[]" min="1" max="<?php echo DyzuryDomowe::LP_POL ?>" value="<?php echo DyzuryDomowe::LP_POL ?>">
                -->
                <p>Zaznacz aby ustawić strażaka zawsze na dyżurze przed WS</p>
                <label><input class="w3-check" type="checkbox" name="przedWS" value="1"  <?php echo isset($_POST['przedWS'])? "checked":""; ?>  >Ustaw dyżur przed każdą WS </label>

                <p>Zaznacz aby uzupełnić pozostałe puste miejsca</p>
                <label><input class="w3-check" type="checkbox" name="fillUp" value="1"  <?php echo isset($_POST['fillUp'])? "checked":""; ?>  >Uzupełnij puste pola</label>

                <div class="w3-margin-top">
                    <button type="submit" name="fill" >Uzupełnij</button>
                </div>
            </form>
        </div>
        <div class="w3-container  w3-threquarter">
	        <?php
	        //$ddomowe->printDyzuryDiff();
	        ?>

        </div>



	</main>
<script>
    $(".range").each(function(){
        $(this).prev().html("( "+$(this).val()+" )" );
    }).on("input",function(){
        $(this).prev().html("( "+$(this).val()+" )" );
    })

    $(".highlightFireman").on({
        mouseenter: function(){
            $("."+$(this).attr("id")).addClass("w3-gray");
            //$(this).css("background-color", "lightgray");
        },
        mouseleave: function(){
            $("."+$(this).attr("id")).removeClass("w3-gray");
           // $(this).css("background-color", "lightblue");
        },
        click: function(){
            $("."+$(this).attr("id")).toggleClass("w3-text-blue");

        }
    });

</script>
<?php

require 'footer.php';