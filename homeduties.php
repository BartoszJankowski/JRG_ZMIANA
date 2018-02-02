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
$dbStrazacy = new DBStrazacy();
$user = new User();
$dbRozkazy = new DBRozkazy();
$info = '';
if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
$ldt = new LocalDateTime();
$ddomowe = new DyzuryDomowe(null, $user->getStrazak()->getZmiana(), $ldt->getYear(), $ldt->getMonth());
$ddomowe->createEmpty();
$ddomowe->setStrazacy($dbStrazacy->getZmianaListStrazacy($user->getStrazak()->getJrgId(), $user->getStrazak()->getZmiana()));


$title = "Dyżury domowe";
require 'header.php';
?>
	<main class="w3-container">
		<?php
		$ddomowe->printNaglowek();
		?>
        <div class="w3-half w3-center">

            <table class="w3-table-all table-grafik">
		        <?php
		        PozycjaDD::printNaglowek();
		        $ddomowe->printDyzury();
		        ?>
            </table>
        </div>
        <div class="w3-quarter">
            <table class="w3-table-all table-grafik">
		        <?php
		        $ddomowe->printStrazacyInRows();
		        ?>
            </table>
        </div>




	</main>
<?php

require 'footer.php';