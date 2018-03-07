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

if(isset($_POST)){
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}


if(   $user->isAdmin() ) {
	$list = $dbJednostki->getJrgListForAdmin($user);
	if(count($list)==1){
		$dbUsers->setTempJrgId($user, $list[0]['id']);
		$list = array();
    } else if(isset($_GET['manage_jrg'])) {
		$dbUsers->setTempJrgId($user,$_GET['manage_jrg']);
	}

	if($user->getAdminJrgId()>0){
		$dbJednostki->selectJrg( $user->getAdminJrgId() );

		$_SETTINGS->load($dbJednostki->getSelectedId());
		if(isset($_POST['addUpr'])){
			if($_SETTINGS->addUpr($_POST)){
				header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
				exit;
			}
		}
		if(isset($_POST['addharmoVal'])){
			if($_SETTINGS->addHarmoValue($_POST)){
				header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
				exit;
			}
		}
		if(isset($_POST['addgrafVal'])){
			if($_SETTINGS->addGrafValue($_POST)){
				header('Location: '.$base_url.$_SERVER['REQUEST_URI']);
				exit;
			}
		}
		if(isset($_POST['uprDelete'])){
			$_SETTINGS->deleteUpr($_POST['deleteUpr']);
		}
	}

} else {
    echo 'Nie posiadasz odpowiednich uprawnień do przebywania na tej stronie.';
    exit;
}



if( isset($_POST['addStrazak']) && ($user->isAdmin() || $user->isChef()) ){
	$dbStrazacy->dodajStrazaka((new Strazak())->create($_POST));
}

if(isset($_GET['deleteFireman'] )){
	if($dbStrazacy->deleteFireman($user, $_GET['strazakId'])){
		echo "Strazak usunięty";
	} else {
		echo $dbStrazacy->error;
	}
}
$title = "Zarządzaj JRG";
require 'header.php';
?>

<main>
	<!--  PANEL ADMINA -->
	<?php if($user->isAdmin()): ?>
		<div id="manageJrg">
            <?php if(!empty($list)) : ?>
			<div id="list_jrg" class=" w3-row-padding">
				<h5>Zarządzaj jednostką: </h5>
				<?php
				foreach ($list as $jrg){
					$dbJednostki->printJrgBtn($jrg);
				}
				?>
			</div>
			<?php endif;
			    if($dbJednostki->getSelectedId()>0) :?>
                    <ul class="nav nav-tabs" id="jrg_tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="strazacy-tab" data-toggle="tab" href="#strazacy" role="tab" aria-controls="strazacy" aria-selected="true">Strażacy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Ustawienia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/szablonrozkazu.php" >Szablon rozkazu</a>
                        </li>
                    </ul>
            <div class="tab-content">
            <div  class="tab-pane w3-row show active" id="strazacy" role="tabpanel" aria-labelledby="strazacy-tab" >
                <div class="w3-quarter w3-border-right w3-border-bottom">
                    <form action="" method="post" id="addFireman" class="w3-margin">
                            <h4><i class="fas fa-user-plus"></i> Dodaj strażaka</h4>
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
                                <label class="w3-text-gray">Nazwisko</label>
                                <input class="w3-input" type="text" name="nazwisko"  />
                            </div>
                            <div>
                                <label class="w3-text-gray">Imię</label>
                                <input class="w3-input" type="text" name="imie"  />
                            </div>
                            <div>
                                <label class="w3-text-gray">Data badań</label>
                                <input class="w3-input" type="date" name="badania"  />
                            </div>
                            <div>
                                <label class="w3-text-gray">Kolor</label>
                                <input class="w3-input" type="color" name="kolor"  />
                            </div>
                            <div>
                                <label class="w3-text-gray">Zaznacz uprawnienia pracownika: </label><br>
					            <?php
					            foreach ( DBJrgSettings::getUprawnienia() as $uprawnienie ) {
						            echo '<label><input type="checkbox" name="uprawnienia[]" value="'.$uprawnienie->getId().'" /><msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'" ></msc> '.$uprawnienie->getName().'</label><br>';
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
			            Strazak::printJrgTableStrazacy($zmiany, $uzytkownicy);

		            ?>
                </div>
                <div class="w3-threequarter w3-border" style="">
                    <div class="w3-container w3-large">Lista nieprzypisanych strażaków (<?php echo count($usersList) ?>)</div>
		            <?php
			            foreach ($usersList as $userObject) { $userObject->printUserHtml(); }
		            ?>
                </div>
            </div>
            <div  class="tab-pane w3-row" id="settings" role="tabpanel" aria-labelledby="settings-tab"  >
                <div class="w3-col s4 w3-container">
                    <h4 class="w3-row w3-margin-top">Uprawnienia: <button class="w3-btn w3-right" data-toggle="popover"  title="Dodaj uprawnienie"><i class="far fa-plus-square"></i></button></h4>
                    <ul class="list-group">
		                <?php
		                foreach (DBJrgSettings::getUprawnienia() as $uprawnienie){
			                $uprawnienie->printLiElement();
		                }
		                ?>
                    </ul>
                    <p class="w3-margin-top">
                        <a class="btn btn-info" data-toggle="collapse" href="#uprawnieniaInfo" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fas fa-info-circle"></i> Dowiedz się więcej
                        </a>
                    </p>
                    <div class="collapse" id="uprawnieniaInfo">
                        <div class="card card-body">
                            <p class="w3-margin">
                                Zdefiniowanie uprawnień strażaka
                                pozwala przypisać je pracownikowi aby
                                następnie były one wyświetlane szefowi
                                zmiany przy tworzeniu grafiku,
                                rozkazu lub dyzurów domowych.
                            </p>
                        </div>
                    </div>

                </div>

                <div class="w3-col s4 w3-container">
                    <h4 class="w3-row w3-margin-top">Harmonogram: <button class="w3-btn w3-right" title="Zdefiniuj nowe pole"><i class="far fa-plus-square"></i></button></h4>

                        <ul class="list-group">
                            <?php
                            foreach (DBJrgSettings::getHarmoValues() as $harmo_value){
                                $harmo_value->printLiElement();
                            }
                            ?>
                        </ul>
                    <p class="w3-margin-top">
                        <a class="btn btn-info" data-toggle="collapse" href="#harmonogramInfo" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fas fa-info-circle"></i> Dowiedz się więcej
                        </a>
                    </p>
                    <div class="collapse" id="harmonogramInfo">
                        <div class="card card-body">
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

                </div>

                <div class="w3-col s4">
                    <h4 class="w3-row w3-margin-top">Grafik: <button class="w3-btn w3-right" data-toggle="popover"  title="Zdefiniuj nowe pole"><i class="far fa-plus-square"></i></button></h4>
                    <ul class="list-group">
		                <?php
		                foreach (DBJrgSettings::getGrafValues() as $grafik_value){
			                $grafik_value->printLiElement();
		                }
		                ?>
                    </ul>
                    <p class="w3-margin-top">
                        <a class="btn btn-info" data-toggle="collapse" href="#grafikInfo" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fas fa-info-circle"></i> Dowiedz się więcej
                        </a>
                    </p>
                    <div class="collapse" id="grafikInfo">
                        <div class="card card-body">
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
             </div>
            </div>
			    <?php endif;  ?>
        </div>
		<!-- KONIEC PANELU ADMINA -->
	<?php endif; ?>
</main>

<script>
    $(function () {
        if(window.location.hash!==""){
            var id = window.location.hash.slice(1,window.location.hash.length);
            $('#'+id+"-tab").tab('show');

        }
        $('button[data-toggle="popover"]').popover({content:function(){return createNewValue(this)},html:true,placement:'bottom'})
    });

    function createNewValue(btn){
        logD(btn);
        return 'Siema';
    }



</script>

<?php

require 'footer.php';

