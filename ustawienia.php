<?php
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

if(isset($_POST['deleteAccount'])){
	if(count($dbJednostki->getJrgListForAdmin($user))<=0){
		if($dbUsers->deleteAccount($user, $_POST['password'])){
			header('Location: '.$base_url);
			exit;
		} else {
			$resDelete = $dbUsers->error;
		}
	} else {
		$resDelete =  'Nie można usunąc konta administratora jednostki. Należy przekazac administrację innemu użytkownikowi.';
	}

}


$title = "Ustawienia";
require 'header.php';
?>
	<main>

		<div>
			<h4>Witaj, <?php echo $user->getName() != null ? $user->getName() . ' ' . $user->getSurname() : $user->login; echo ' [' . $user->getPrevilages() . ']'; ?>
			</h4>
			<div>
			</div>
		</div>
			<div>
				<!-- USTAWIENIA KONTA -->
				<form action="" id="changeUserData" method="post" class="w3-quarter w3-margin w3-padding  w3-border">
					<h3>Zmiana danych podstawowych</h3>

					<div>
						<label>Imię:</label>
						<input class="w3-input" type="text" name="name" value="<?php echo $user->getName() ?>"  />
					</div>

					<div>
						<label>Nazwisko:</label>
						<input class="w3-input" type="text" name="surname"  value="<?php echo $user->getSurname() ?>" />
					</div>

					<input class="w3-input w3-margin-top" type="submit" name="changeuserData" value="Zapisz" />
				</form>
				<form action="" id="deleteAccount" method="post" class="w3-quarter w3-margin w3-padding  w3-border">
					<h3>Usuń konto</h3>
					<p class="w3-text-red"><?php echo $resDelete; ?></p>
					<p>Uwaga! Usunięcie konta jest permanentne i nieodwracalne, czy oby napewno chcesz to zrobić? </p>
					<div>
						<label>Aby usunąc wprowadź hasło: </label>
						<input class="w3-input" type="password" name="password" value="" required  />
					</div>


					<input class="w3-input w3-margin-top" type="submit" name="deleteAccount" value="Usuń konto" />
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
        $("#btnManageJrg").click(function () {
            $("#manageJrg").toggle();
        });
        $("#btnShiftManage").click(function () {
            $("#shiftManage").toggle();
        });
	</script>

<?php

require 'footer.php';