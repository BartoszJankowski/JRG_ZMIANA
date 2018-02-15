<?php
$activePage = "mojkalendarz";
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 28.01.2018
 * Time: 14:22
 */

session_start();
require 'php/config.php';
$dbUsers = new DBUsers();
$dbHarmo = new DBHarmonogramy();
$dbDyzury = new DbDyzuDomowy();
$user = new User();

/** Sprawdza sesje i przekierwouje uzytkownika gdy nie zalogowany */
if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
$_SETTINGS->load($user->getJrgId());


if($user->getStrazak()){
	$czas = new LocalDateTime();
	if(isset($_GET)){
		$_GET = test_input($_GET);
		if(isset($_GET['month'],$_GET['year']))
			$czas = new LocalDateTime($_GET['year'].'-'.$_GET['month'].'-1');
	}

	$dbDyzury->loadDyzuryNaRok($user->getJrgId(), $user->getStrazak()->getZmiana(),$czas->getYear() );
	$kalendarz = new Kalendarz($czas->getYear());
	$harmonogram = $dbHarmo->getHarmo($user->getStrazak(), $czas->getYear());
} else {
	$info = '<div class="w3-container"><h1>Nie zostałeś przypisany !</h1><p>Twoje konto nie zostało powiązane jeszcze z istniejącym profilem strażaka. Skontaktuj się z administratorem JRG lub swoim szefem zmiany aby to zrobił.</p></div>';
}



$title = "Kalendarz";
require 'header.php';
?>

<main>
	<?php if($user->getStrazak()) : ?>
	<div class="w3-container">
		<h1 class="w3-hide-small w3-center">
			<div style="display: inline-block;">
				<form class=" w3-left" id="prevYear" action="" method="get" >
					<input type="hidden" name="month" value="12">
					<input type="hidden" name="year" value="<?php  echo $czas->getYear()-1; ?>">
					<button class="w3-btn w3-large" type="submit"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
				</form>
				<form class="w3-hide-small w3-right" id="nextYear" action="" method="get" >
					<input type="hidden" name="month" value="1">
					<input type="hidden" name="year" value="<?php echo $czas->getYear()+1; ?>">
					<button class="w3-btn w3-large" type="submit"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
				</form>
				<?php echo $czas->getYear(); ?>
			</div>
		</h1>


	<?php

		for($i=1; $i<=12 ;$i++){
			if($i%4==1){
				echo '<div class="w3-cell-row">';
			}
			$inn = '';
			$monthIterator = (new ArrayObject($kalendarz->getDayForMonth($i)))->getIterator();
			$tygIterator = get_dni_tyg_iterator();
			while ($monthIterator->valid()){
				$inn .= '<tr>';
				while ($tygIterator->valid()){
					if($monthIterator->valid() && $tygIterator->current() === $monthIterator->current()['t']){
					    $dyzurBool = $dbDyzury->hasFiremanHomeduty($user->getStrazak()->getStrazakId(),$i, $monthIterator->current()['nr_0']);
					    $dayVal = $harmonogram->getDayVal($i, $monthIterator->current()['nr_0']);
					    $val = $dyzurBool ? 'Dd':$dayVal;
                        //$val = $dayVal;
						$inn .= '<td class="zmiana-'.$monthIterator->current()['z'].' w3-display-container"><span class="w3-tiny w3-display-topleft">'.$monthIterator->current()['nr'].'</span><span class="w3-medium w3-display-bottomright">'.$val.'</span></td>';
						$monthIterator->next();
					} else {
						$inn .= '<td></td>';
					}
					$tygIterator->next();
				}
				$tygIterator->rewind();
				$inn .= '</tr>';
			}

			echo '<div id="kal-'.$czas->getYear().'-'.$i.'" class="w3-container kalendar-div w3-cell w3-quarter '.($i==$czas->getMonth()? '':'w3-hide-small').' " style=" min-height: 300px;"><h3 class="w3-border-bottom w3-center "><button class="w3-left w3-btn w3-large prevMsc w3-hide-large"><i class="fa fa-chevron-left" aria-hidden="true"></i></button><button class="w3-btn w3-right w3-large nextMsc w3-hide-large"><i class="fa fa-chevron-right" aria-hidden="true"></i></button><div>'.get_moth_name($i).'</div><div class="w3-small w3-hide-large">'.$czas->getYear().'</div></h3><table class="table-calendar "><tr><td>Pn</td><td>Wt</td><td>Śr</td><td>Cz</td><td>Pt</td><td>Sb</td><td>Nd</td></tr>'.$inn.'</table></div>';
			if($i%4==0){
				echo '</div>';
			}
		}
	?>
	</div>
	<div class="w3-padding w3-center" style="width: 300px;"><div class="w3-third w3-padding-small"><div class="zmiana-1">I</div></div><div class="w3-third w3-padding-small"><div class="zmiana-2">II</div></div><div class="w3-third w3-padding-small"><div class="zmiana-3">III</div></div></div>
	<?php else: echo $info ; endif; ?>   <!-- dodałem na początku przed ? php ; nie odpalało kalendarza na serwerze lokalnym  -->
</main>
<?php

require 'footer.php';