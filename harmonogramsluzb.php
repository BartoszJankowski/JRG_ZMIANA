<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 01:05
 */
session_start();
require 'php/config.php';
$dbUsers = new DBUsers();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$dbharmo = new DBHarmonogramy();
$user = new User();
$localDateTime = new LocalDateTime();

if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
$_SETTINGS->load($user->getJrgId());


if(isset($_POST)){
	$_POST = test_input($_POST);
}

/**
 * Odpowiada za ustawienie msc zmiane miesiąca
 */
if(isset($_GET)){
	$_GET = test_input($_GET);
	if(isset($_GET['month'],$_GET['year']))
	    $localDateTime = new LocalDateTime($_GET['year'].'-'.$_GET['month'].'-1');
	if(isset($_GET['mscAction'])){
	    if($_GET['mscAction'] > 0){
		    $localDateTime->add(new DateInterval('P'.$_GET['mscAction'].'M'));
        } else {
		    $localDateTime->sub(new DateInterval('P'.abs($_GET['mscAction']).'M'));
	    }
		header('Location: '.$base_url.$_SERVER['PHP_SELF'].'?month='.$localDateTime->getMonth().'&year='.$localDateTime->getYear());
		exit;
    }


}

$month= $localDateTime->getMonth() ;
$year = $localDateTime->getYear() ;

$harmo = new Harmonogram($year);
$harmo->genHarmonogram();

$strazacy = $dbStrazacy->getZmianaListStrazacy($user->getJrgId(),$user->getStrazak()->getZmiana());
$harmonogramy = $dbharmo->getJrgharmos($user->getJrgId(),$year );

if(isset($_GET['createHarmo'])){
	$harmonogram = new Harmonogram($year);
	foreach ($strazacy as $strazak){
	    if( $strazak->getStrazakId() == $_GET['createHarmo'] ){
		    $harmonogram->genHarmoForStrazak($strazak, $_GET['typ']);
		    $dbharmo->saveHarmonogram($user->getJrgId(),$year,$strazak->getStrazakId(),$harmonogram);
		    $harmonogramy[$strazak->getStrazakId()] = $harmonogram;
		}
    }
} elseif(isset($_POST['editHarmoType'])){
    $idStr = $_POST['editHarmoType'];
    if(is_numeric($idStr)){
        if(array_key_exists($idStr, $harmonogramy)){
	        foreach ($strazacy as $strazak){
		        if( $strazak->getStrazakId() == $idStr ){
			        $harmonogramy[$idStr]->changeHarmoType($strazak, $_POST['typ']);
			        $dbharmo->changeHarmo($year,$idStr,$harmonogramy[$idStr]);
		        }
	        }

        }
    }
}

if(isset($_POST['harmoVal'])){
	$daneDoZapisu = array();
	foreach ($_POST as $nr=>$harmoChanges){
		if(is_numeric($nr)){
			if(array_key_exists($nr, $harmonogramy)){
				$harmonogramy[$nr]->putChanges($_POST['month'],$harmoChanges,  $_POST['harmoVal']);
				$daneDoZapisu[$nr] = array('harmonogram'=>$harmonogramy[$nr],'exists'=>true);
			}
		}
	}
	$dbharmo->saveHarmos($user->getStrazak()->getJrgId(),$year,$daneDoZapisu);
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
} else {
	foreach ( $strazacy as $strazak ){
		if(array_key_exists($strazak->getStrazakId(), $harmonogramy)){
			$strazak->setHarmonogram( $harmonogramy[$strazak->getStrazakId()] );
		} else {
		    $harm = new Harmonogram($year);
		   // $harm->genHarmoForStrazak($strazak);
			$strazak->setHarmonogram( $harm );
        }
	}
}

$style ='
td:nth-of-type(2) {
    min-width:200px;
}
.harmoCell input {
  border:none;
  background-color:transparent;
  width:100%;
}
';

$title = "Harmonogram służb";
require 'header.php';
?>
<main class="" xmlns="http://www.w3.org/1999/html">
    <form action="" method="get">
        <input type="hidden" name="month" value="<?php echo $month?>"> <input type="hidden" name="year" value="<?php echo $year ?>">
        <h1 class="w3-center"><button name="mscAction" value="-1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-left"></i></button><span style="width: 25%;display: inline-block"><?php echo get_moth_name($month).' '.$year; ?></span><button name="mscAction" value="1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-right"></i></button></h1>
    </form>

    <div class="w3-conteiner w3-row w3-row-padding w3-margin">
        <?php
        foreach (get_harmo_values() as $v=>$tab){
            echo '<div class="w3-col l2 w3-small "><span class=" w3-padding-small" style="width: 20px;height: 20px;background-color: '.$tab['col'].'" >'.$v.'</span> - '.$tab['n'].'</div>';
        }
        ?>
    </div>
	<form action="" method="post">
        <div class="w3-row">

            <select class="w3-select w3-border w3-col l3 w3-margin harmoValSelect" name="harmoVal">
				<?php

				foreach (get_harmo_values() as $val=>$tab){
					echo '<option class="'.$tab['col'].'" value="'.$val.'">'.$tab['n'].'</option>';
				}
				echo '<option class="" value="">Usuń</option>';
				?>
            </select>

        </div>
		<table class="w3-table-all w3-hoverable w3-small">
		<?php
		echo '<input type="hidden" name="year" value="'.$year.'"/>';
		echo '<input type="hidden" name="month" value="'.$month.'"/>';
		$harmo->printHarmoHeader($month);
		 foreach ($strazacy as $str){
		     $str->getHarmonogram()->printHarmoRow($str, $month);
		 }
		?>
		</table>
        <div class="w3-row w3-container w3-padding">
            <input class="w3-input " type="submit" value="Zapisz" />
        </div>


	</form>
</main>


<?php

require 'footer.php';

