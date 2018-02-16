<?php
$activePage = "main";
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 22:22
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$user = new User();

if(isset($_POST)){
    $_POST = test_input($_POST);
}

if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}



$title = "Panel główny";
require 'header.php';
?>
<main>

    <div>
        <h4>Witaj, <?php echo $user->getName() != null ? $user->getName() . ' ' . $user->getSurname() : $user->login; echo ' [' . $user->getPrevilages() . ']'; ?>
        </h4>
            <div>
            </div>
    </div>


</main>



<?php

require 'footer.php';
