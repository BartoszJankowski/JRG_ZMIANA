<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 22:22
 */

session_start();
require 'config.php';
$dbUsers     = new DBUsers();
$dbJednostki = new DBJednostki();
$user = new User();

if(isset($_GET['logout'])){
	$dbUsers->destroySession($user);
	header('Location: '.$base_url.'/index.php');
	exit;
}


if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}

if(isset($_POST['changePass'])) {
    if($dbUsers->changePass($user, test_input($_POST['oldpass']),test_input($_POST['newpass']),test_input($_POST['newpass2'])) ){
        echo 'Poprawnie zmieniono hasło.';
    } else {
        echo $dbUsers->error;
    }
}
if(isset($_POST['changeuserData'])){
	if($dbUsers->changeUserData($user,test_input($_POST['name']),test_input($_POST['surname'])) ){
		echo 'Poprawnie zmieniono dane.';
	} else {
		echo $dbUsers->error;
	}
}

if( $user->isAdmin() && isset($_POST['manage_jrg'])) {
	$dbJednostki->selectJrg( $_POST['manage_jrg'] );
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
    .error  {
        color:red;
    }
        #settings {
            display: none;
        }
	</style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
</head>

<body>
<header>

    <div class="w3-bar w3-border w3-light-grey">
        <a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button"><i class="fa fa-fw fa-home w3-xlarge"></i><div class="w3-small">Strona główna</div></a>
        <?php if($user->isAdmin()): ?>
        <a id="btnManage" href="#" class="w3-bar-item w3-button"><i class="fa fa-fw fa-users w3-xlarge"></i><div class="w3-small">Zarządzaj</div></a>
        <?php
            endif;

            if($user->isChef() ):
        ?>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-fw fa-calendar w3-xlarge"></i><div class="w3-small">Grafik</div></a>
        <?php
            endif;
        ?>
        <a id="btnSett" href="#" class="w3-bar-item w3-button"><i class="fa fa-fw fa-cog w3-xlarge"></i><div class="w3-small">Ustawienia</div></a>
        <a href="?logout=1" class="w3-bar-item w3-button"><i class="fa fa-fw fa-sign-out w3-xlarge"></i><div class="w3-small">Wyloguj</div></a>
    </div>
</header>
<main>
    <div>
        Witaj, <?php echo $user->getName() != null ? $user->getName() . ' ' . $user->getSurname() : $user->login; echo ' [' . $user->getPrevilages() . ']'; ?>
    </div>
	<?php if($user->isAdmin()): ?>
    <div id="manage">
        <div id="list_jrg" class="w3-border-bottom w3-row-padding">
            <h5>Zarządzaj jednostką: </h5>
            <?php
              $list = $dbJednostki->getJrgListForAdmin($user);
              foreach ($list as $jrg){
	              $dbJednostki->printJrgBtn($jrg);
              }
            ?>
        </div>
        <div>
            <?php

            if($dbJednostki->getSelectedId()>0):
                $usersList = $dbUsers->getUsersList($dbJednostki->getSelectedId());

                foreach ($usersList as $strazak) :
            ?>
                <div><?php echo $strazak->printUserHtml() ?></div>


            <? endforeach;?>

            <?endif;?>
        </div>
    </div>
	<?php endif; ?>
    <div id="settings">
        <form action="" id="changeUserData" method="post" class="w3-quarter w3-margin w3-padding  w3-border">
            <h3>Zmiana danych podstawowych</h3>

            <div>
                <label>Imię:</label>
                <input class="w3-input" type="text" name="name" value="<?php echo $dbUsers->getImie() ?>"  />
            </div>

            <div>
                <label>Nazwisko:</label>
                <input class="w3-input" type="text" name="surname"  value="<?php echo $dbUsers->getNazwisko() ?>" />
            </div>

            <input class="w3-input w3-margin-top" type="submit" name="changeuserData" value="Zapisz" />
        </form>
        <form action="" id="changePass" method="post" class="w3-quarter w3-margin w3-padding  w3-border">
            <h3>Zmiana hasła</h3>
            <div>
                <label>Stare hasło:</label>
                <input class="w3-input w3-margin-top" type="password" name="oldpass" required />
            </div>

            <div>
                <label>Nowe hasło:</label>
                <input class="w3-input" type="password" id="newpass" name="newpass" required />
            </div>
            <div>
                <label>Powtórz hasło:</label>
                <input class="w3-input" type="password" name="newpass2" required />
            </div>

            <input class="w3-input  w3-margin-top" type="submit" name="changePass" value="Zmień" />

        </form>
    </div>
</main>

</body>

<script>
    /*
	jquery validator dla formularza #changePass
	 */
    $("#changePass").validate({
        rules: {
            oldpass: {
                required : true,
                minlength : 8
            },
            newpass : {
                required : true,
                minlength : 8
            },
            newpass2 : {
                required : true,
                minlength : 8,
                equalTo : '#newpass'
            }
        }
    });

    /*
    menu btns
     */
    $("#btnSett").click(function () {
       $("#settings").toggle();
    });
    $("#btnManage").click(function () {
       $("#manage").toggle();
    });
</script>
</html>