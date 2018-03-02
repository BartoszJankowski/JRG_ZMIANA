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


$output = array('result'=>false,'action'=>null,'error'=>null,'errorMsg'=>null);

try {
	$output['action'] = $_POST['action'];
// logowanie, wylogowanie, tworzenie ejdnostki, rejestracja usersa
	switch ($_POST['action']){
		case 'log_in':
			if($dbUsers->login($_POST['login'],$_POST['password'])) {
				$output['result'] = true;
			} else {
				throw new UserErrors($dbUsers->error);
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