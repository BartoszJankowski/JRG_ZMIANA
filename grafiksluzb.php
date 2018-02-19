<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 20:50
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


$grafik = new Grafik($localDateTime->getYear(),$localDateTime->getMonth(),$user->getStrazak()->getZmiana());
$strazacy = $dbStrazacy->getZmianaListStrazacy($user->getStrazak()->getJrgId(),$user->getStrazak()->getZmiana());
$harmonogramy = $dbharmo->getJrgharmos($user->getStrazak()->getJrgId(),$localDateTime->getYear() );
foreach ( $strazacy as $strazak){
	if(array_key_exists($strazak->getStrazakId(), $harmonogramy)){
		$strazak->setHarmonogram( $harmonogramy[$strazak->getStrazakId()] );
	}
}

if(isset($_POST['saveGraf']) && $user->isChef()){
	$daneDoZapisu = array();
	foreach ($_POST as $str_id=>$harmoChangesTab){
		if(is_numeric($str_id)){
			if(array_key_exists($str_id, $harmonogramy)){
				$harmonogramy[$str_id]->putGrafChanges($localDateTime->getMonth(),$harmoChangesTab);
				$daneDoZapisu[$str_id] = array( 'harmonogram' =>$harmonogramy[$str_id], 'exists' =>true);
			} else {
				$harmonogram = new Harmonogram($localDateTime->getYear());
				$harmonogram->genHarmoForStrazak($dbStrazacy->getStrazak($str_id),get_harmo_types()[0]);
				$harmonogram->putGrafChanges($localDateTime->getMonth(),$harmoChangesTab);
				$daneDoZapisu[$str_id] = array( 'harmonogram' =>$harmonogram, 'exists' =>false); ;
			}
		}
	}
	$dbharmo->saveHarmos($user->getStrazak()->getJrgId(),$localDateTime->getYear(),$daneDoZapisu);
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}


$title = "Grafik";
require 'header.php';
?>

	<main class="" xmlns="http://www.w3.org/1999/html">
		<form action="" method="get" >
			<input type="hidden" name="month" value="<?php echo $localDateTime->getMonth(); ?>"> <input type="hidden" name="year" value="<?php echo $localDateTime->getYear(); ?>">
			<h1 class="w3-center">
                <button name="mscAction" value="-1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-left"></i></button>
                <span style="width: 25%;display: inline-block"><?php echo get_moth_name($localDateTime->getMonth()).' '.$localDateTime->getYear(); ?></span><button name="mscAction" value="1" type="submit" class="w3-button w3-xlarge"><i class="fa fa-fw fa-chevron-right"></i></button></h1>
		</form>

		<?php
            if($user->isChef() || $user->isAdmin())
			    $grafik->printMiesiac($strazacy);
            else
                $grafik->printMiesiacForUser($user, $strazacy);
		?>

		<div class="w3-conteiner w3-row w3-row-padding w3-margin">
            <h3>Legenda: </h3>
			<?php
			foreach (get_grafik_values() as $v=>$tab){
				echo '<div class="w3-col l2 w3-small "><span class="w3-border w3-padding-small" style="width: 45px;height: 30px;display: inline-block" >'.$v.'</span> - '.$tab['n'].'</div>';
			}
			?>
		</div>

	</main>
    <script>

    </script>

<?php

require 'footer.php';
