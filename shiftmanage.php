<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 26.01.2018
 * Time: 19:21
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$user = new User();

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
}

if(  isset($_GET['manage_jrg']) && $user->isAdmin() ) {
	$dbJednostki->selectJrg( $_GET['manage_jrg'] );
}

if( isset($_POST['addStrazak']) && ($user->isAdmin() || $user->isChef()) ){
	$dbStrazacy->dodajStrazaka((new Strazak())->create($_POST));
}

if(isset($_POST['editFireman'])){
  //  print_r($_POST);
	$dbStrazacy->edytujStrazaka($user, (new Strazak())->create($_POST));
}

if(isset($_POST['deleteFireman'] )){
	if($dbStrazacy->deleteFireman($user, $_POST['strazakId'])){
		echo "Strazak usunięty";
	} else {
		echo $dbStrazacy->error;
	}
}
$title = "Stan zmiany";
require 'header.php';
?>
<main>
	<div>
		Witaj, <?php echo $user->getName() != null ? $user->getName() . ' ' . $user->getSurname() : $user->login; echo ' [' . $user->getPrevilages() . ']'; ?>
	</div>
    <!-- PANEL SZEFA ZMIANY -->
	<?php if($user->isChef()) :
        $strazak = $user->getStrazak();
	    if(isset($_GET['editFireman'])) :
            $edytowanyStrazak = $dbStrazacy->getStrazak($_GET['editFireman']);
	?>
            <div class="w3-row">
                <h2>Edycja danych strazaka</h2>
                <div class="w3-container w3-quarter" >

                    <form action="?" method="post" id="editFireman" class="w3-margin">
                        <input type="hidden" name="id" value="<? echo $edytowanyStrazak->getStrazakId() ?>" />

                        <div>
                            <label class="w3-text-gray">Funkcja PSP:</label>
                            <select  class="w3-select" name="nazwa_funkcji">
							    <?php
                                echo $edytowanyStrazak->getNazwafunkcji();
							    foreach (get_tab_funkcje() as $id=>$fnkca){
							        if( $edytowanyStrazak->getNazwafunkcji() == $id ){
								        echo '<option value="'.$id.'" selected>'.$fnkca[0].'</option>';
                                    } else {
								        echo '<option value="'.$id.'" >'.$fnkca[0].'</option>';
                                    }

							    }
							    ?>
                            </select>
                        </div>
                        <div>
                            <label class="w3-text-gray">Stopień</label>
                            <select  class="w3-select" name="stopien">
							    <?php
							    foreach (get_tab_stopnie() as $st=>$val){
							        if($edytowanyStrazak->getStopien() === $st){
								        echo '<option value="'.$st.'" selected >'.$val[0].'</option>';
                                    } else {
								        echo '<option value="'.$st.'" >'.$val[0].'</option>';
                                    }

							    }
							    ?>
                            </select>
                        </div>
                        <div>
                            <label class="w3-text-gray">Uprawnienia apliacji:</label>
                            <select  class="w3-select" name="previlages">
                                <?php if($edytowanyStrazak->isChef()) {
                                    echo '<option value="USER">Strażak</option>
                                            <option value="CHEF" selected>Szef/z-ca zmiany</option>';
                                } else {
	                                echo '<option value="USER" selected>Strażak</option>
                                <option value="CHEF">Szef/z-ca zmiany</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="w3-text-gray">Przypisz użytkownika</label>
                            <select  class="w3-select" name="user_id">

							    <?php
							    $usersList =  $dbUsers->getFreeUserList($user,$strazak->getJrgId() ) ;
							    if(empty($edytowanyStrazak->getUserId())){
							        echo '<option value="0" selected >brak przypisanego użytkownika</option>';
                                } else {
							        $userEdytowanegoStrazaka = $dbUsers->getUserById($edytowanyStrazak->getUserId());
								    echo '<option value="0" >brak przypisanego użytkownika</option>';
								    echo '<option selected value="'.$userEdytowanegoStrazaka->getId().'">'.$userEdytowanegoStrazaka->getNameEmailIfNull().'</option>';
							    }
							    foreach ($usersList as $userObject){
								    echo '<option value="'.$userObject->getId().'">'.$userObject->getNameEmailIfNull().'</option>';
							    }
							    ?>
                            </select>
                        </div>
                        <div>
                            <label class="w3-text-gray">Numer porządkowy</label>
                            <input class="w3-input" type="number" value="<?php echo $edytowanyStrazak->getNrPorz(); ?>" name="nr_porz" min="0" max="99" />
                        </div>
                        <div>
                            <label class="w3-text-gray">Nazwisko</label>
                            <input class="w3-input" type="text" name="nazwisko" value="<?php echo $edytowanyStrazak->getNazwisko(); ?>" />
                        </div>
                        <div>
                            <label class="w3-text-gray">Imię</label>
                            <input class="w3-input" type="text" name="imie"  value="<?php echo $edytowanyStrazak->getImie(); ?>" />
                        </div>

                        <div>
                            <label class="w3-text-gray">Harmonogram</label>
                            <select class="w3-select" name="typHarmo">
                                <?php
                                foreach (get_harmo_types() as $typ=>$val){
                                    if($edytowanyStrazak->getHarmoType()==$typ){
	                                    echo '<option selected value="'.$typ.'">'.$val[0].'</option>';
                                    } else{
	                                    echo '<option value="'.$typ.'">'.$val[0].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="w3-text-gray">Kolor</label>
                            <input class="w3-input" type="color" name="kolor" value="<?php echo $edytowanyStrazak->getKolor(); ?>" />
                        </div>
                        <div>
                            <label class="w3-text-gray">Zaznacz uprawnienia pracownika: </label><br>
	                        <?php
	                        foreach ( DBJrgSettings::getUprawnienia() as $uprawnienie ) {

	                            if(array_search($uprawnienie->getId(),$edytowanyStrazak->getUprawnienia())!==false)
		                            echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'" checked  /><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></i> '.$uprawnienie->getName().'</label><br>';
	                            else
	                                echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'"/><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></i> '.$uprawnienie->getName().'</label><br>';

	                        }

	                        ?>
                        </div>
                        <input type="submit" class="w3-input w3-margin-top" name="editFireman" value="Zapisz zmiany" />
                    </form>
                </div>
                <div class="w3-container w3-half">
				    <?php
				    $uzytkownicy = $dbUsers->getUsersList($user,$strazak->getJrgId() );
				    $strazacy = $dbStrazacy->getZmianaListStrazacy($strazak->getJrgId(),$strazak->getZmiana());
				    echo '<div class="w3-container"><h4>zmiana '.$strazak->getZmiana().' <span class="w3-small">('.count($strazacy).' strażaków)</span></h4><ul class="w3-ul">';
				    foreach ($strazacy as $str){
					    $str->printHtml($uzytkownicy );
				    }
				    echo '</ul></div>';
				    ?>
                </div>
                <div class="w3-quarter w3-border" style="">
				    <?php
				    echo '<div class="w3-container w3-large">Lista nieprzypisanych strażaków ('.count($usersList).')</div>';
				    foreach ($usersList as $userObject) { $userObject->printUserHtml(); }
				    ?>
                </div>
            </div>
        <?php else: ?>
            <div class="w3-row">
                <h2>Dodaj nowego strażaka: </h2>
                <div class="w3-container w3-quarter" >
                    <form action="" method="post" id="addFireman" class="w3-margin">
                        <input type="hidden" name="jrg_id" value="<? echo $strazak->getJrgId() ?>" />
                        <input type="hidden" name="zmiana" value="<? echo $strazak->getZmiana() ?>" />

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
                                $usersList =  $dbUsers->getFreeUserList($user,$strazak->getJrgId() ) ;
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
                        <div>
                            <label class="w3-text-gray">Harmonogram</label>
                            <select class="w3-select" name="typHarmo">
			                    <?php
			                    foreach (get_harmo_types() as $typ=>$val){

				                    echo '<option value="'.$typ.'">'.$val[0].'</option>';
			                    }
			                    ?>
                            </select>
                        </div>

                        <div>
                            <label class="w3-text-gray">Kolor</label>
                            <input class="w3-input" type="color" name="kolor"  />
                        </div>
                        <div>
                            <label class="w3-text-gray">Zaznacz uprawnienia pracownika: </label><br>
                            <?php
                            foreach ( DBJrgSettings::getUprawnienia() as $uprawnienie ) {
                                echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'" /><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></i> '.$uprawnienie->getName().'</label><br>';
                            }

                            ?>
                        </div>
                        <input type="submit" class="w3-input w3-margin-top" name="addStrazak" value="Dodaj" />
                    </form>
                </div>
                <div class="w3-container w3-half">
                    <?php
                    $uzytkownicy = $dbUsers->getUsersList($user,$strazak->getJrgId() );
                    $strazacy = $dbStrazacy->getZmianaListStrazacy($strazak->getJrgId(),$strazak->getZmiana());
                    echo '<div class="w3-container"><h4>zmiana '.$strazak->getZmiana().' <span class="w3-small">('.count($strazacy).' strażaków)</span></h4><ul class="w3-ul">';
                    foreach ($strazacy as $str){
                        $str->printHtml($uzytkownicy);
                    }
                    echo '</ul></div>';
                    ?>
                </div>
                <div class="w3-quarter w3-border" style="">
                    <?php
                        echo '<div class="w3-container w3-large">Lista nieprzypisanych strażaków ('.count($usersList).')</div>';
                        foreach ($usersList as $userObject) { $userObject->printUserHtml(); }
                    ?>
                </div>
            </div>
	    <?php endif; ?>
	<?php endif; ?>
    <!-- KONIEC PANELU SZEFA -->
</main>

<datalist id="funkcje">

</datalist>


<script>
</script>

<?php

require 'footer.php';

