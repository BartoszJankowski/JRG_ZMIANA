<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 22:22
 */

session_start();
require 'config.php';
$user = new User();

if(isset($_GET['logout'])){
	$user->destroySession();
	header('Location: '.$base_url.'/index.php');
	exit;
}


if(!$user->checkSession()){
	header('Location: '.$base_url.'/login.php');
	exit;
}

if(isset($_POST['changePass'])) {
    if($user->changePass(test_input($_POST['oldpass']),test_input($_POST['newpass']),test_input($_POST['newpass2'])) ){
        echo 'Poprawnie zmieniono hasło.';
    } else {
        echo $user->error;
    }
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
	</style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
</head>

<body>
<header>
    <div>
        Witaj, <?php echo $_SESSION['login'] ?>
    </div>
    <div class="w3-bar w3-border w3-light-grey">
        <a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button w3-green"><i class="fa fa-fw fa-home w3-xlarge"></i><div class="w3-small">Strona główna</div></a>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-fw fa-calendar w3-xlarge"></i><div class="w3-small">Grafik</div></a>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-fw fa-cog w3-xlarge"></i><div class="w3-small">Ustawienia</div></a>
        <a href="?logout=1" class="w3-bar-item w3-button"><i class="fa fa-fw fa-power-off w3-xlarge"></i><div class="w3-small">Wyloguj</div></a>
    </div>

</header>
<main>
    <div id="settings">
        <form action="" id="changePass" method="post" class="w3-quarter w3-margin w3-padding  w3-border">
            <h3>Zmiana hasła</h3>
            <div>
                <label>Stare hasło:</label>
                <input class="w3-input" type="password" name="oldpass" required />
            </div>

            <div>
                <label>Nowe hasło:</label>
                <input class="w3-input" type="password" id="newpass" name="newpass" required />
            </div>
            <div>
                <label>Powtórz hasło:</label>
                <input class="w3-input" type="password" name="newpass2" required />
            </div>

            <input class="w3-input" type="submit" name="changePass" value="Zmień" />
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
                equalTo : "#newpass"
            }
        }
    });
</script>
</html>