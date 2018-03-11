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


//$dbRozkazy->copyTemplate(20,21);

if(isset($_GET['start'])){
	$szablon = new Szablon($user->getAdminJrgId());
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
} elseif(isset($_GET['delete'])){
	//$dbJednostki->selectJrg($user->getAdminJrgId());
	$szablony = $dbRozkazy->getSzablony($user->getAdminJrgId());
	foreach ($szablony as $i=>$szbl){
	    if($szbl->getId()===$_GET['delete'] ){
	        if($szbl->getCreatedOrdersNum()>0){
	            $info = '<h2>Nie można usunąć szablonu dla którego zostały utworzone rozkazy.</h2>';
            } else {
		        if($dbRozkazy->deleteSzablon($user->getAdminJrgId(), $szbl)){
			        $info = '<h2>Poprawnie usunieto szablon.</h2>';
			        $szablony[$i] = null;
			        break;
                } else {
			        $info = '<h2>Nie udało sie usunąć szablonu </h2><p>'.$dbRozkazy->error.'</p>';
                }
            }
        }
    }
} else {
    $szablony = $dbRozkazy->getSzablony($user->getAdminJrgId());

}

if(isset($_POST['save'])){

    $result = array('result'=>false);
    $obiekty = json_decode(str_replace('&quot;','"',$_POST['save']),true );
	$szablon = new Szablon($user->getAdminJrgId());
	$szablon->setId($_POST['id']);
    foreach ($obiekty as $nr=>$obiekt){
        $szablon->addObjects(createHtmlObj($obiekt));
    }
	$szablon->setFinished($_POST['active']);

	if($dbRozkazy->saveSzablon($user->getAdminJrgId(),$szablon)) {
        $result['result'] = true;
	} else {
	    $result['error'] = $dbRozkazy->getError();
    }
    //print_r($obiekty);
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($result);
	die;
}

function createHtmlObj($obiektArray) : HtmlObj{
    $name = $obiektArray['name'];
    $htmlObj = null;
    switch ($name){
        case 'DIV':
	        $htmlObj = new Sekcja($obiektArray['class']);
	        foreach($obiektArray['content'] as $obiekt){
                $htmlObj->putContent(createHtmlObj($obiekt));
            }
            break;
        case 'H2':
	        $htmlObj = new Naglowek(2);
	        $htmlObj->addClass($obiektArray['class']);
	        $htmlObj->putContent($obiektArray['content']);
            break;
        case 'SPAN':
	        $htmlObj = new Text($obiektArray['content']);
	        $htmlObj->addClass($obiektArray['class']);
            break;
        case 'INPUT':
            $htmlObj = new Input('text');
	        $htmlObj->addClass($obiektArray['class']);
	        $htmlObj->addAttr('list',$obiektArray['attr']['list']);
	        $htmlObj->setVal($obiektArray['value']);
	        //TODO: attrybut
            break;
        case 'SELECT':
            $htmlObj = new Select('wybor');
	        $htmlObj->addClass($obiektArray['class']);
	        $htmlObj->setList($obiektArray['attr']);
            break;
        case 'UL':
	        $htmlObj = new Lista();
	        $htmlObj->addClass($obiektArray['class']);
	        $htmlObj->setList($obiektArray['attr']);
            break;
        case 'TD':
            $htmlObj = new TD();
	        $htmlObj->addClass($obiektArray['class']);
            foreach ($obiektArray['content'] as $cnt){
                $htmlObj->putContent(createHtmlObj($cnt));
            }
            break;
        case 'TABLE':
	        $htmlObj = new Table();
	        $htmlObj->addClass($obiektArray['class']);
	        foreach($obiektArray['content'] as $nrCol =>$col){
	            $kolumna = new Col($col['value']);
		        $kolumna->setList($col['attr']);
		        $htmlObj->addCol($kolumna);
		        foreach ($col['content'] as $td){
			        $htmlObj->addCell(createHtmlObj($td),$nrCol );
                }

            }
            break;
        default:
	        $htmlObj = new Text("");
    }
    return $htmlObj;
}



$title = "Szablon rozkazu";
require 'header.php';
?>
<main class="w3-container" style="margin-bottom:100px;background-color:rgba(0,0,0,0.2)">
	<?php
	DBJrgSettings::printJsListValues();
	?>
    <script type="text/javascript" src="js/szablonrozkazu.js?ver=<?php echo time() ?>"></script>
    <?php
        echo $info;
        ?>
    <div class="w3-row" style="background-color: rgba(0,0,0,0.1)">

        <?php
            if(!isset($_GET['edit'])) :
                echo '<h3>Szablony rozkazu: </h3>';
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
                                        <button class="w3-right" type="submit" name="delete" value="'.$szbl->getId().'">Usuń</button>
                                    </form>
                                    </div>
                               </div>';
		            }

	            }
         ?>
        <div class="w3-col l1 m1 s1 w3-border w3-padding w3-margin">
        <form action="" method="get">
            <button type="submit" name="start" value="1">Utwórz nowy szablon</button>
        </form>
        </div>
            <?php endif; ?>
    </div>

    <?php if($szablon) :
		echo '<h2>Edycja szablonu '.$szablon->getDataSzablonu().'/'.$szablon->getId().'</h2>';

		?>
        <div class="w3-row" >
            <p>Rozpocznij edycję szablonu klikając w element ponizej </p>
            <label><input class="w3-check" type="checkbox" id="szablon_active" <?php echo $szablon->getFinished()?'checked':''; ?> /> Ustaw jako aktywny</label>
            <div class="w3-col" style="width: 50px;">
                <button class="w3-xlarge" onclick="animateBar(this)">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="w3-border w3-container w3-white" style="display: none">
                    <div>
                        <h4>Obiekty: </h4>
                        <b>Sekcja</b>
                        <p>
                            Pojemnik dla innych obiektów, domyślnie grupuje się jako wiersz ale może tez posłużyć jako kolumna w innej sekcji.
                        </p>
                        <b>Nagłówek</b>
                        <p>
                            Wykorzystywany jako nagłówek rozkazu lub punkt rozkazu. Jego wielkośc można zmienic w edycji. Domyślnie zajmuje całą dostepną szerokość (podobnie jak sekcja).
                        </p>
                        <b>Text</b>
                        <p>
                          Zwykły tekst, domyslnie nie jest stylizowany. Domyslnie zajmuje taką szerokośc jak jego zawartość.
                        </p>
                        <b>Pole tekstowe</b>
                        <p>
                            Podstawowe pole zawierające wprowadzoną wartość w szablonie - zawartość pola może być edytowana w trakcie tworzenia rozkazu.
                        </p>
                        <b>Pole wyboru</b>
                        <p>
                           Lista rozwijana z elementami listy zdefinowanymi wg. wybranej dostępnej listy. (np. lista strażaków na jrg, na zmianie itd.)
                        </p>
                    </div>
                    <div>
                        <h4>Zmienne: </h4>
                        <p>
                            Zmienne dostępne dla obiektów typu Nagłówek i Tekst. Ich wartość jest zmienna w wyświetlanym rozkazie w zależności od dnia, zmiany i jrg.
                        </p>
                        <?php
                         foreach (DBJrgSettings::getZmienneRozkazu() as $zmienna){
                             echo '  <span class="jrg_var szablon_element" data-toggle="tooltip" title="'.$zmienna['opis'].'">'.$zmienna['id'].'</span>';
                         }
                        ?>
                    </div>
                    <div>
                        <h4>Listy:  </h4>
                        <p>
                           Listy wystepują jako zmienne względem grafiku, harmonogramu, dnia, zmiany i jrg. Moga byc ustawione w polu wyboru jako domyślne wartości oraz w kolumnie tabeli.
                        </p>
                        <?php
                            foreach (DBJrgSettings::getListValues() as $val){
                                echo '<span class="jrg_list szablon_element" data-toggle="tooltip" title="'.$val['name'].'" >'.$val['id'].'</span>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="w3-col w3-right" style="width: 50px;">
                <button class="w3-xlarge w3-right" onclick="animateBar(this)" >
                    <i class="fas fa-sitemap"></i>
                </button>
                <div id="szablon_tree" class="w3-border w3-container"></div>

            </div>
            <div class="w3-rest">
                <div id="szablon_container" class="w3-border w3-margin-bottom" rozkaz-id="<?php echo $_GET['edit']; ?>" data-toggle="popover" data-html="true" data-placement="top"  >
		            <?php
		            foreach ($szablon->getObiektyHtml() as $obiekt){
			            if($obiekt instanceof HtmlObj){
				            $obiekt->print();
			            }
		            }
		            ?>
                </div>
                <div>
                    <button class="w3-btn w3-light-gray w3-border" onclick="szablon.save(this)"><i class="far fa-save"></i> Zapisz</button>
                </div>
            </div>


        </div>

	<?php endif; ?>
</main>
<?php

require 'footer.php';