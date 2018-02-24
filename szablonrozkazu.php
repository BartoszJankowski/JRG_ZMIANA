<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 15:16
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
$user = new User();
$dbJednostki = new DBJednostki();
$dbRozkazy = new DBRozkazy();
$ltd = new LocalDateTime();
$info = '';

if(!$dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/login.php');
	exit;
}
if(!$user->isAdmin()){
   echo 'Nie masz odpowiednich uprawnień do edycji tej strony.';
   exit;
}

$_SETTINGS->load($user->getJrgId());


if(isset($_POST)) {
	$_POST = test_input($_POST);
}
if(isset($_GET)){
	$_GET = test_input($_GET);
}

Szablon::$isEditing = true;

if(isset($_GET['start'])){
	$szablon = new Szablon($user->getJrgId());
	if($dbRozkazy->utworzSzablon($user, $szablon) ){
		header('Location: '.$base_url.$_SERVER['PHP_SELF'].'?edit='.$szablon->getId());
		exit;
	} else {
		$info = '<h2>Nie udało sie utworzyć szablonu</h2><p>'.$dbRozkazy->error.'</p>';
	}
} elseif(isset($_GET['edit'])){
	$szablon = new Szablon($user->getAdminJrgId());
	$dbJednostki->selectJrg($user->getAdminJrgId());
	if(!$dbRozkazy->getSzablon($user->getAdminJrgId(), $_GET['edit'], $szablon) )
	{
		$szablon = false;
	}
} else {
    $szablony = $dbRozkazy->getSzablony($user->getAdminJrgId());
}

/*

try {
    if(class_exists("HtmlObj", false)){
	    $miejscowoscIdata = new Sekcja();
	    $miejscowoscIdata->addClass("align-right");
	    $miejscowoscIdata->putContent((new Variable('miasto'))->setConstant('miasto'));
	    $miejscowoscIdata->putContent(new Text(', dnia '));
	    $miejscowoscIdata->putContent((new Variable('data'))->setConstant('data'));
	    $miejscowoscIdata->putContent(new Text('r.'));

	    $naglowek = new Sekcja();
	    $naglowek->addClass('w3-center');
	    $h1 = new Naglowek(2);
	    $h1->addClass("no-margin");
	    $h1->putContent(new Text("ROZKAZ DZIENNY NR "));
	    $h1->putContent((new Variable('nr rozkazu'))->setVariable('nr_rozkazu'));
	    $h1->putContent(new Text("/"));
	    $h1->putContent((new Variable('rok'))->setVariable('rok'));
	    $h4 = new Naglowek(4);
	    $h4->addClass("no-margin");
	    $h4->putContent(new Text('Dowódcy JRG nr '));
	    $h4->putContent((new Variable('nr jrg'))->setConstant('nr_jrg'));
	    $h4->putContent(new Text(' na dzień '));
	    $h4->putContent((new Variable('data'))->setVariable('data'));
	    $h4->putContent(new Text('r.'));
	    $naglowek->putContent($h1);
	    $naglowek->putContent($h4);

	    $pkt1 = new Sekcja();
	    $pkt1->putContent((new Naglowek(2))->putContent("Pkt 1. Służba: "));
	    $pkt1Inn = new Sekcja();
	    $pkt1Inn->addClass("align-right");

	    $pkt1Inn->putContent(
		    (new Sekcja())
			    ->putContent(new Text("Zmiana służbowa: zmiana "))
			    ->putContent((new Variable('nr zmiany'))->setVariable('nr_zmiany'))
	    );
	    $pkt1Inn->putContent(
		    (new Sekcja())
			    ->putContent(new Text("D-ca zmiany służbowej: - "))
			    ->putContent( (new Select('szefzmiany'))->setList('available_firemans') )
	    );
	    $pkt1Inn->putContent(
		    (new Sekcja())
			    ->putContent(new Text("Dyspozytor PA: - "))
			    ->putContent((new Select('dyspozytor'))->setList('available_firemans'))
	    );
	    $pkt1Inn->putContent(
		    (new Sekcja())
			    ->putContent(new Text("Podoficer dyżurny: - "))
			    ->putContent((new Select('podoficer'))->setList('available_firemans'))
	    );
	    $pkt1Inn->putContent(
		    (new Sekcja())
			    ->putContent(new Text("Pomocnik podoficera: - "))
			    ->putContent((new Select('pomocnik'))->setList('available_firemans'))
	    );
	    $pkt1->putContent($pkt1Inn);

	    $pkt2 = new Sekcja('w3-row');
	    $pkt2->putContent((new Naglowek(2))->putContent("Pkt 2. Obsada: "));
	    $tabGba  = new Table(2, 4);
	    $tabGba->addClass('w3-table-all','table-grafik');
	    $tabGba->addStyle('vertical-align','top');

	    $tabGba->addCell('D-ca',0);
	    $tabGba->addCell('Kierowca',0);
	    $tabGba->addCell('Ratownik',0);
	    $tabGba->addCell('Ratownik',0);
	    $tabGba->addCol(new Col('GBA 2,5/20',4),2);
	    $tabGba->addCell((new Select('gba'))->setList('available_firemans'), 1);
	    $tabGba->addCell((new Select('gba'))->setList('available_firemans'), 1);
	    $tabGba->addCell((new Select('gba'))->setList('available_firemans'), 1);
	    $tabGba->addCell((new Select('gba'))->setList('available_firemans'), 1);

	    $gcba = new Table(2, 3);
	    $gcba->addStyle('vertical-align','top');
	    $gcba->addClass('w3-table-all','table-grafik');
	    $gcba->addCol(new Col('GCBA 5/40',3), 2);
	    $gcba->addCell('D-ca',0);
	    $gcba->addCell('Kierowca',0);
	    $gcba->addCell('Ratownik',0);
	    $gcba->addCell((new Select('gcba'))->setList('available_firemans'), 1);
	    $gcba->addCell((new Select('gcba'))->setList('available_firemans'), 1);
	    $gcba->addCell((new Select('gcba'))->setList('available_firemans'), 1);

	    $scd = new Table(2, 3);
	    $scd->addStyle('vertical-align','top');
	    $scd->addClass('w3-table-all','table-grafik');
	    $scd->addCol(new Col('SCD 40',3),2);
	    $scd->addCell('D-ca',0);
	    $scd->addCell('Kierowca',0);
	    $scd->addCell('Ratownik',0);
	    $scd->addCell((new Select('scd'))->setList('available_firemans'), 1);
	    $scd->addCell((new Select('scd'))->setList('available_firemans'), 1);
	    $scd->addCell((new Select('scd'))->setList('available_firemans'), 1);

	    $pkt2->putContent( (new Sekcja('w3-third'))->putContent($tabGba));
	    $pkt2->putContent((new Sekcja('w3-third'))->putContent($gcba));
	    $pkt2->putContent((new Sekcja('w3-third'))->putContent($scd));

	    $pkt3 = new Sekcja();
	    $pkt3->putContent((new Naglowek(2))->putContent("Pkt 3. Dyżur domowy: "));
	    $pkt3->putContent(
	            (new Sekcja())
                    ->putContent((new Lista())->addClass('w3-padding')->setList('harmo_fireman_Dd'))
        );

	    $pkt4 = new Sekcja();
	    $pkt4->putContent((new Naglowek(2))->putContent("Pkt 4. Nieobecni: "));
	        $tabNieobecni = (new Table())->addStyle('vertical-align','top')->addClass('w3-table-all');;
	        $tabNieobecni->addCol((new Col('Urlop',5))->setList('harmo_fireman_Ud','harmo_fireman_Uw','harmo_fireman_O'), 1);
	    $tabNieobecni->addCol((new Col('Wolne',5))->setList('graf_fireman_Ws'), 2);
	    $tabNieobecni->addCol((new Col('Delegacja',5))->setList('harmo_fireman_D'), 3);
	    $tabNieobecni->addCol((new Col('Chorzy',5))->setList('harmo_fireman_Ch'), 4);
	    $pkt4->putContent($tabNieobecni);

	    $pkt5 = new Sekcja('w3-margin','align-right');
	    $pkt5->putContent((new Sekcja('w3-container','w3-border','w3-padding','w3-margin-top'))->addStyle('display','inline-block')->putContent(new Text('Podpis d-cy Jrg'))->putContent((new Sekcja())->addStyle('width','100px')->addStyle('height','50px')) );


	    $szablon->addObjects($miejscowoscIdata, $naglowek, $pkt1, $pkt2, $pkt3, $pkt4, $pkt5);
        $szablon->setFinished(true);

	    if($dbRozkazy->saveSzablon($user->getStrazak()->getJrgId(),$szablon)) {

	        echo 'Poprawnie zapisano szablon.';

        }

    }


} catch (UserErrors $e){
    echo $e->getMessage();
}


*/



$title = "Szablon rozkazu";
require 'header.php';
?>
<main>
    <script type="text/javascript" src="js/szablonrozkazu.js?ver=<?php echo time() ?>"></script>
    <?php
        echo $info;
        ?>
    <div class="w3-row" style="background-color: rgba(0,0,0,0.1)">
        <h3>Szablony rozkazu: </h3>
        <?php
            if(!isset($_GET['edit'])) {

	            foreach ($szablony as  $szbl){

		            if($szbl instanceof  Szablon){
			            echo '<div class="w3-col l2 m2 s2 w3-border w3-padding w3-margin">
                                    <div>Data utworzenia: <b>'.$szbl->getDataSzablonu().'</b></div>
                                    <div>Szablon id: <b>'.$szbl->getId().'</b></div>
                                    <div>Aktywny: <b>'.(($szbl->getFinished())?'TAK':'NIE').'</b></div>
                                    <div>Rozkazy dzienne: <b>'.$szbl->getCreatedOrdersNum().'</b></div>
                                    <div>
                                     <form action="" method="get">
                                        <button type="submit" name="edit" value="'.$szbl->getId().'">Edytuj</button>
                                    </form>
                                    </div>
                               </div>';
		            }

	            }
            }
        ?>
        <div class="w3-col l1 m1 s1 w3-border w3-padding w3-margin">
        <form action="" method="get">
            <button type="submit" name="create" value="1">Utwórz nowy szablon</button>
        </form>
        </div>
    </div>
	<?php  if(isset($_GET['create'])) : ?>
    <div class="w3-container">
        <h3>Nowy szablon</h3>
        <p>Rozpocznij tworzenie szablonu rozkazu od podstawowych ustawień nagłówka i stopki rozkazu oraz ilości sekcji.</p>
        <form class="w3-container"  action="" method="post">
            <ul>
                <li>
                    <h4>Nagłówek</h4>
                    <div>
                        <label><input type="checkbox" /> Adres i data</label>
                    </div>
                </li>
            </ul>
        </form>
    </div>

    <?php elseif($szablon) :
		echo '<p>Edycja szablonu '.$szablon->getDataSzablonu().'/'.$szablon->getId().'</p>';

		?>
        <div class="w3-container">

            <div class="w3-col" style="width: 50px;">
                <button class="w3-xxlarge" onclick="animateLeftBar(this)">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="w3-border" style="display: none">
                    <div>
                        <h4>Stałe: </h4>
                        <span class="jrg_const szablon_element">nr_jrg</span>
                        <span class="jrg_const szablon_element">miasto</span>
                    </div>
                    <div>
                        <h4>Zmienne: </h4>
                        <span class="jrg_var szablon_element">data</span>
                        <span class="jrg_var szablon_element">dzien</span>
                        <span class="jrg_var szablon_element">msc</span>
                        <span class="jrg_var szablon_element">rok</span>
                        <span class="jrg_var szablon_element">nr_rozkazu</span>
                        <span class="jrg_var szablon_element">nr_zmiany</span>
                    </div>
                    <div>
                        <h4>Zmienne listy: </h4>
                        <span class="jrg_list szablon_element">strażacy na zmianie</span>
                        <span class="jrg_list szablon_element">dostępni strażacy</span>
                        <span class="jrg_list szablon_element">strażacy wg. zmiennej z harmonogramu/grafiku</span>
                        <span class="jrg_list szablon_element">strażacy na dyżurze (aktualny dzień)</span>
                        <span class="jrg_list szablon_element">strażacy na dyżurze (następny dzień)</span>
                    </div>
                    <div>
                        <h4>Obiekty: </h4>
                        <span class="jrg_obj szablon_element">Sekcja</span>
                        <span class="jrg_obj szablon_element">Nagłówek</span>
                        <span class="jrg_obj szablon_element">Pole tekstowe</span>
                        <span class="jrg_obj szablon_element">Lista rozwijana</span>
                    </div>
                </div>

            </div>
            <div id="szablon_container" class="w3-container w3-rest w3-border " data-toggle="popover" data-html="true" data-placement="top"  >
		        <?php
		        foreach ($szablon->getObiektyHtml() as $obiekt){
			        if($obiekt instanceof HtmlObj){
				        $obiekt->print();
			        }
		        }

		        ?>


            </div>
        </div>

	<?php endif; ?>
</main>
<?php

require 'footer.php';