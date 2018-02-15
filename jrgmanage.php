
<?php
$activePage = "jrgmanage";
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 26.01.2018
 * Time: 19:21
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
$dbJrgSettings = new DBJrgSettings();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$user = new User();

if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}

if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}

if(  isset($_GET['manage_jrg']) && $user->isAdmin() ) {
	$dbJednostki->selectJrg( $_GET['manage_jrg'] );
	$dbJrgSettings->load($dbJednostki->getSelectedId());
	if(isset($_POST['addUpr'])){
	    $dbJrgSettings->addUpr($_POST);
    }
    if(isset($_POST['uprDelete'])){
	    $dbJrgSettings->deleteUpr($_POST['deleteUpr']);
    }
}



if( isset($_POST['addStrazak']) && ($user->isAdmin() || $user->isChef()) ){
	$dbStrazacy->dodajStrazaka((new Strazak())->create(test_input($_POST)));
}

if(isset($_POST['deleteFireman'] )){
	if($dbStrazacy->deleteFireman($user, test_input($_POST['strazakId']))){
		echo "Strazak usunięty";
	} else {
		echo $dbStrazacy->error;
	}
}
$title = "Zarządzaj JRG";
require 'header.php';
?>

<main>
	<div>
		Witaj, <?php echo $user->getName() != null ? $user->getName() . ' ' . $user->getSurname() : $user->login; echo ' [' . $user->getPrevilages() . ']'; ?>
	</div>
	<!--  PANEL ADMINA -->
	<?php if($user->isAdmin()): ?>
		<div id="manageJrg">
			<div id="list_jrg" class="w3-border-bottom w3-row-padding">
				<h5>Zarządzaj jednostką: </h5>
				<?php
				$list = $dbJednostki->getJrgListForAdmin($user);
				foreach ($list as $jrg){
					$dbJednostki->printJrgBtn($jrg);
				}
				?>
			</div>
			<div class="w3-quarter w3-border">
				<?php if($dbJednostki->getSelectedId()>0): ?>
					<form action="" method="post" id="addFireman" class="w3-margin">
						<input type="hidden" name="jrg_id" value="<? echo $dbJednostki->getSelectedId() ?>" />
						<div>
							<label class="w3-text-gray">Numer zmiany:</label>
							<select class="w3-select" name="zmiana">
								<option value="1">I</option>
								<option value="2">II</option>
								<option value="3">III</option>
							</select>
						</div>
						<div>
							<label class="w3-text-gray">Funkcja PSP:</label>
							<select  class="w3-select" name="nazwa_funkcji">
								<?php
								foreach (get_tab_funkcje() as $id=>$fnkca){
									echo '<option value="'.$id.'" >'.$fnkca[0].'</option>';
								}
								?>
							</select>
						</div>
						<div>
							<label class="w3-text-gray">Stopień</label>
							<select  class="w3-select" name="stopien">
								<?php
								foreach (get_tab_stopnie() as $st=>$val){
									echo '<option value="'.$st.'" >'.$val[0].'</option>';
								}
								?>
							</select>
						</div>
						<div>
							<label class="w3-text-gray">Uprawnienia apliacji:</label>
							<select  class="w3-select" name="previlages">
								<option value="USER">Strażak</option>
								<option value="CHEF">Szef/z-ca zmiany</option>

							</select>
						</div>
						<div>
							<label class="w3-text-gray">Przypisz użytkownika</label>
							<select  class="w3-select" name="user_id">
								<option selected disabled >wybierz z listy</option>
								<?php
								$usersList = $dbUsers->getFreeUserList($user,$dbJednostki->getSelectedId());
								foreach ($usersList as $userObject){
									echo '<option value="'.$userObject->getId().'">'.$userObject->getNameEmailIfNull().'</option>';
								}
								?>
							</select>
						</div>
						<div>
							<label class="w3-text-gray">Numer porządkowy</label>
							<input class="w3-input" type="number" name="nr_porz" min="0" max="99" />
						</div>
                        <div>
                            <label class="w3-text-gray">Nazwisko</label>
                            <input class="w3-input" type="text" name="nazwisko"  />
                        </div>
						<div>
							<label class="w3-text-gray">Imię</label>
							<input class="w3-input" type="text" name="imie"  />
						</div>
                        <select class="w3-select" name="typHarmo">
							<?php
							foreach (get_harmo_types() as $typ=>$val){

								echo '<option value="'.$typ.'">'.$val[0].'</option>';
							}
							?>
                        </select>
						<div>
							<label class="w3-text-gray">Kolor</label>
							<input class="w3-input" type="color" name="kolor"  />
						</div>
						<div>
							<label class="w3-text-gray">Zaznacz uprawnienia pracownika: </label><br>
							<?php
							foreach ( $dbJrgSettings->getUprawnienia() as $uprawnienie ) {
								echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'" /><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></i> '.$uprawnienie->getName().'</label><br>';
							}

							?></div>
						<input type="submit" class="w3-input w3-margin-top" name="addStrazak" value="Dodaj" />
					</form>
				<?php endif;?>   <!-- /*poprawka php -->
			</div>
			<div class="w3-threequarter w3-border">
				<?php
				if($dbJednostki->getSelectedId()>0){
					$zmiany = $dbStrazacy->getJRGListStrazacy($dbJednostki->getSelectedId());
					$uzytkownicy = $dbUsers->getUsersList($user,$dbJednostki->getSelectedId() );
					foreach ($zmiany  as $nr => $strazacy ) {
						echo '<div class="w3-third w3-container"><h4>zmiana '.$nr.' ('.count($strazacy).')</h4><ul class="w3-ul">';
						foreach ($strazacy as $str){
							$str->printHtml($uzytkownicy);
						}
						echo '</ul></div>';
					}

				}
				?>
			</div>
			<div class="w3-threequarter w3-border" style="">
				<?php
				if($dbJednostki->getSelectedId()>0) {
					echo '<div class="w3-container w3-large">Lista nieprzypisanych strażaków ('.count($usersList).')</div>';
					foreach ($usersList as $userObject) { $userObject->printUserHtml(); }
				}
				?>
			</div>
            <?php
             if($dbJednostki->getSelectedId()>0) : ?>
             <div class="w3-container w3-half">
                 <div class="w3-container w3-half">
                     <h4>Lista uprawnień </h4>
                     <form action="" method="post">
                     <ul>
                     <?php
                        foreach ($dbJrgSettings->getUprawnienia() as $uprawnienie){
                            $uprawnienie->printLiElement();
                        }
                     ?>
                     </ul>
                         <input type="submit" name="uprDelete" value="Usuń">
                     </form>
                 </div>
                 <div class="w3-container w3-half">
                     <h4>Dodaj nowe uprawnienie:</h4>
                     <form action="" method="post">
                         <label>Nazwa
                             <input class="w3-input" type="text" name="name"></label><br>
                         <label>Ikona
                             <input class="w3-input" type="text" name="icon"></label><br>
                         <label>Kolor
                             <input  type="color" name="color"></label><br>
                         <button class="w3-input w3-margin-top" type="submit" name="addUpr"> Dodaj</button><br>
                     </form>
                 </div>
             </div>



            <?php endif;  //poprawka php
                 ?>
        </div>
		<!-- KONIEC PANELU ADMINA -->
	<?php endif; ?>
</main>

<datalist id="funkcje">

</datalist>

<?php

require 'footer.php';

