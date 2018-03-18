<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 08.02.2018
 * Time: 15:38
 */


//PLIK do obsługi żadań asynchronicznych JS.
//pobiera dane GET/POST
//zwraca odpowiedź JSON

session_start();

require 'php/config.php';

$dbUsers = new DBUsers();

if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}


$output = array('result'=>false,'action'=>null,'info'=>null,'error'=>null,'errorMsg'=>null);

try {
	$output['action'] = $_POST['action'];
	switch ($_POST['action']){
			/*
			 * LOGOWANIE UZYTKOWNIKA
			 */
		case 'log_in':
			if($dbUsers->login(
				$_POST['login'],
				$_POST['password']
				)) {
				$output['result'] = true;
			} else {
				throw new UserErrors($dbUsers->getError());
			}
			break;
			/*
			 * TWORZENIE NOWEJ JRG
			 * WAZNE ! ! ! !
			 * zwróć uwage że w typ przypadku możesz dodastac dodatkowy element 'info' ktory oznacza ze zostało utworzone konto administratora.
			 */
		case 'addJrg':

			$db = new DBJednostki();
			if($db->createJrg(
				$_POST['jrg'], 
				$_POST['city'],
				$_POST['street'],
				$_POST['nr'],
				$_POST['email']
				)){
				$output['result'] = true;
					if($dbUsers->createJrgAdmin(
						$_POST['email']
						)){
						$output['info'] = 'Na podany adres email zostały wysłane dane dostępowe do konta.';
						};
			} else {
				throw new UserErrors($db->getError());
			}
			break;
			/*
			 * RESET HASLA
			 */
		case 'reset':
			if( $dbUsers->resetPass(
				$_POST['email']
				)){
				$output['result'] = true;
					$output['info'] = 'Twoje hasło zostało zresetowane. Sprawdź skrzynkę email.';
			}  else {
				throw new UserErrors($dbUsers->getError());
			}
			break;
			/*
			 * RESJESTRACJA UZYTKOWNIKA
			 */
		case 'register':
			if($dbUsers->registerNewUser(
				$_POST['login'],
				$_POST['password'],
				$_POST['confirm_password'],
				$_POST['jrg'],
				$_POST['name'],
				$_POST['surname']
			)){
				$output['result'] = true;
					$output['info'] = 'Twoje konto zostało utworzone. Możesz się zalogować <a href="http://zmiana.bjit.pl/login.php">Tutaj</a>. Na Twoją pocztę zostało wysłane potwierdzenie rejestracji.';
			
			} else {
				throw new UserErrors($dbUsers->getError());
			}
			break;
		default:
			throw new UserErrors('Nieznana akcja. System nie mógł wykonać polecenia: \"'.$_POST['action'].'\"');
	}
} catch (UserErrors $user_errors){
	$output['error'] = true;
	$output['errorMsg'] = $user_errors->getMessage();
}


header("Content-Type: application/json; charset=UTF-8");
echo json_encode($output);
die;

?>