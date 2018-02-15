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
	    if($dbJrgSettings->addUpr($_POST)){
		    header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
		    exit;
	    }
    }
    if(isset($_POST['addharmoVal'])){
	    if($dbJrgSettings->addHarmoValue($_POST)){
		    header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
		    exit;
        }
    }
	if(isset($_POST['addgrafVal'])){
		if($dbJrgSettings->addGrafValue($_POST)){
			header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
			exit;
		}
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
			<?php	if($dbJednostki->getSelectedId()>0) : ?>
                <div class="w3-bar w3-black">
                    <a href="#listaStrazakow"  class="w3-bar-item w3-hover-green settings_bars">Lista strażaków</a>
                    <a href="#zmienneJrg" class="w3-bar-item w3-hover-green settings_bars">Ustawienia zmiennych</a>
                </div>
            <div  class="jrg_settings w3-row listaStrazakow">
                <div class="w3-quarter w3-border">
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
					            foreach ( DBJrgSettings::getUprawnienia() as $uprawnienie ) {
						            echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'" /><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></i> '.$uprawnienie->getName().'</label><br>';
					            }

					            ?>
                            </div>
                            <input type="submit" class="w3-input w3-margin-top" name="addStrazak" value="Dodaj" />
                        </form>
                </div>
                <div class="w3-threequarter w3-border">
		            <?php
			            $zmiany = $dbStrazacy->getJRGListStrazacy($dbJednostki->getSelectedId());
			            $uzytkownicy = $dbUsers->getUsersList($user,$dbJednostki->getSelectedId() );
			            foreach ($zmiany  as $nr => $strazacy ) {
				            echo '<div class="w3-third w3-container"><h4>zmiana '.$nr.' ('.count($strazacy).')</h4><ul class="w3-ul">';
				            foreach ($strazacy as $str){
					            $str->printHtml($uzytkownicy);
				            }
				            echo '</ul></div>';
			            }
		            ?>
                </div>
                <div class="w3-threequarter w3-border" style="">
                    <div class="w3-container w3-large">Lista nieprzypisanych strażaków (<?php echo count($usersList) ?>)</div>
		            <?php
			            foreach ($usersList as $userObject) { $userObject->printUserHtml(); }
		            ?>
                </div>
            </div>
             <div  class="jrg_settings zmienneJrg">
                 <div class="w3-row w3-border-bottom">
                    <div class="w3-container w3-half">
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
                        <div class="w3-container w3-half">
                            <form action="" method="post">
                                <ul>
                                    <?php
                                    foreach (DBJrgSettings::getUprawnienia() as $uprawnienie){
                                        $uprawnienie->printLiElement();
                                    }
                                    ?>
                                </ul>
                                <input type="submit" name="uprDelete" value="Usuń">
                            </form>
                        </div>
                    </div>
                     <div class="w3-conteiner w3-half">
                         <h3><u>Uprawnienia strażaka: </u></h3>
                         <p>
                            Zdefiniowanie uprawnień strażaka pozwala przypisać je pracownikowi aby następnie były one wyświetlane szefowi zmiany przy tworzeniu grafiku, rozkazu lub dyzurów domowych.
                         </p>
                     </div>
                 </div>
                 <div  class="w3-row w3-border-bottom">
                    <div class="w3-conteiner w3-half">
                         <div class="w3-container w3-half">
                             <h4>Dodaj wartość:</h4>
                             <form action="" method="post">
                                 <label>Nazwa skrócona (max. 3 znaki)
                                     <input class="w3-input" type="text" name="id" maxlength="3"></label><br>
                                 <label>Nazwa pełna
                                     <input class="w3-input" type="text" name="name"></label><br>
                                 <label>Opis
                                     <input class="w3-input" type="text" name="desc"></label><br>
                                 <label>Kolor
                                     <input  type="color" name="color"></label><br>
                                 <button class="w3-input w3-margin-top" type="submit" name="addharmoVal"> Dodaj</button><br>
                             </form>
                         </div>
                        <div class="w3-half">
                            <ul>
                                <?php
                                foreach (DBJrgSettings::getHarmoValues() as $harmo_value){
                                    $harmo_value->printLiElement();
                                }
                                ?>
                            </ul>
                        </div>
                     </div>
                     <div class="w3-conteiner w3-half">
                         <h3><u>Zmienne/wartości pól dla Harmonogramu</u> </h3>
                         <p>
                            Wartości harmonogramu ustawiane są przez szefa zmiany tylko w oknie edycji harmonogramu, gdzie dodane pole widoczne jest jako kolor. Ustawione wartości harmonogramu sa również widoczne (skrócona nazwa) w podglądzie grafiku.
                         </p>
                         <p>
                             Wartości harmonogramy służą do automatyzacji rozkazu dziennego. Najczęściej są wykorzystywane jako listy osób nieobecnych.
                         </p>
                         <p>
                             Wartości (skrócone nazwy) sa widoczne dla strażaka w jego kalendarzu.
                         </p>
                     </div>
                 </div>
                 <div class="w3-row w3-border-bottom">

                    <div class="w3-conteiner w3-half">
                         <div class="w3-container w3-half">
                             <h4>Dodaj wartość:</h4>
                             <form action="" method="post">
                                 <label>Nazwa skrócona (max. 3 znaki)
                                     <input class="w3-input" type="text" name="id" maxlength="3"></label><br>
                                 <label>Nazwa pełna
                                     <input class="w3-input" type="text" name="name"></label><br>
                                 <label>Opis
                                     <input class="w3-input" type="text" name="desc"></label><br>
                                 <button class="w3-input w3-margin-top" type="submit" name="addgrafVal"> Dodaj</button><br>
                             </form>
                         </div>
                        <div class="w3-half">

                            <ul>
                                <?php
                                foreach (DBJrgSettings::getGrafValues() as $grafik_value){
                                    $grafik_value->printLiElement();
                                }
                                ?>
                            </ul>
                        </div>
                     </div>
                     <div class="w3-conteiner w3-half">
                         <h3><u>Zmienne/wartości pól dla Grafiku</u></h3>
                        <p>
                            Wartości pól grafiku ustawiane są przez szefa zmiany tylko w oknie edycji grafiku. Pokazywane są jako skrócona nazwa, nie są widoczne w oknie harmonogramu.
                        </p>
                         <p>
                             Wartości grafiku moga służyć przy tworzeniu rozkazu dziennego. Pojawiają sie tam jako zmienne do wykorzystania.
                         </p>
                         <p>
                             Wartości (skrócone nazwy) sa widoczne dla strażaka w jego kalendarzu.
                         </p>
                     </div>
                 </div>
             </div>

            <? endif;   ?>
        </div>
		<!-- KONIEC PANELU ADMINA -->
	<?php endif; ?>
</main>

<script>
    $(function () {
        if(window.location.hash!==""){
            $('.settings_bars').each(function () {
                if(this.hash === window.location.hash){
                    $(this).addClass("w3-green");
                }
            });
            var id = window.location.hash.slice(1,window.location.hash.length);
            $('.jrg_settings').hide();
            $('.'+id).show();
        }
    });

    $('.settings_bars').on("click", function (event) {
        event.preventDefault();
        var id = this.hash.slice(1,this.hash.length);
        $('.jrg_settings').hide();
        $('.'+id).show();
        location.hash = this.hash;
        $('.settings_bars').toggleClass("w3-green");
    });


</script>

<?php

require 'footer.php';

