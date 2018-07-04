<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 11.02.2018
 * Time: 20:10
 */

//zmiany

class Rozkaz {

	private $dane = array(
		'szablon'=>array('id'=>null, 'hash'=>null),
		'variable'=>array('$data_rozkazu'=>null,'$nr_rozkazu'=>null,'$nr_zmiany'=>null),
		'list'=>array(),
		'id'=>array()
	);

	private $date;
	private $rozkazId = -1;
	private $JRG_ID;
	private $obiekty = array();

	public function __construct( ) {}

	public function createDane(int $jrg_id,LocalDateTime $ltd, DBJednostki $jednostka, DbDyzuDomowy $dbDyzury = null){
		$kalendar = new Kalendarz($ltd->getYear(), $ltd->getMonth(), $ltd->getDayOfMsc());
		$this->dane['variable']['[$miasto]'] = $jednostka->getSelectedCity();
		$this->dane['variable']['[$nr_jrg]'] = $jednostka->getSelectedJrgNr().'';
		$this->dane['variable']['[$data_edycji]'] = (new LocalDateTime())->getDate();
		$this->dane['variable']['[$data_rozkazu]'] = $ltd->getDate();
		$this->dane['variable']['[$rok]'] = $ltd->getYear().'';
		$this->dane['variable']['[$msc]'] = $ltd->getMonth().'';
		$this->dane['variable']['[$nr_rozkazu]'] = $ltd->getDayOfYearNum().'';
		$this->dane['variable']['[$nr_zmiany]'] = $kalendar->getCurrentZmiana().'';

		$this->JRG_ID = $jrg_id;
		$this->date = $ltd;

		if($dbDyzury!=null){
			try{

				$ddomowe = $dbDyzury->loadDyzuryNaMsc($jrg_id,$ltd->getYear(), $ltd->getMonth());

			} catch (Exception $e){
				echo ' Błąd tworzenie kalendarza w klasie Rozkaz '.$e->getTraceAsString();
			}


			foreach ($ddomowe as $dyzuryDomowe){
				foreach ($dyzuryDomowe->listaStrZDyzuru($ltd->getDayOfMsc()-1) as $name){
					$this->dane['list']['@Dd'][] = array('value'=>'','key'=>$name);
				}
				$ltd->addDays(1);
				foreach ($dyzuryDomowe->listaStrZDyzuru($ltd->getDayOfMsc()-1) as $name){
					$this->dane['list']['@Dd+1'][] = array('value'=>'','key'=>$name);
				}
				$ltd->addDays(1);
				foreach ($dyzuryDomowe->listaStrZDyzuru($ltd->getDayOfMsc()-1) as $name){
					$this->dane['list']['@Dd+'][] = array('value'=>'','key'=>$name);
				}
				$ltd->addDays(-2);
			}
		}



	}

	public function setDane(int $rozkazID, int $jrg_id,LocalDateTime $ltd,string $dane){
		$this->rozkazId = $rozkazID;
		$this->dane = unserialize($dane);
		$this->JRG_ID = $jrg_id;
		$this->date = $ltd;
	}

	public function setSzablon( int $szablonId, array $obiektyHtml){

		$this->obiekty = $obiektyHtml;
		if(empty($this->dane['szablon']['hash'])){
			$this->dane['szablon']['id'] = $szablonId;
			$this->dane['szablon']['hash'] = sha1(serialize($obiektyHtml));
		}
	}

	public function setFiremans(array $strazacy){
		foreach ($strazacy as $strazak){
			if($strazak instanceof Strazak){
				$this->dane['list']['@zmiana_str'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
				//print_r($strazak->getHarmonogram());
				if($strazak->getHarmonogram() instanceof Harmonogram){
					$dayVal = $strazak->getHarmonogram()->getDayVal($this->date->getMonth(), $this->date->getDayOfMsc()-1);

					if(!empty(get_harmo_val($dayVal))) {
						$this->dane['list']['@harmo_'.$dayVal][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					} else if(!empty(get_graf_val($dayVal))) {
						$this->dane['list']['@grafik_'.$dayVal][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
						if($dayVal !== 'Ws')
						$this->dane['list']['@zmiana_free_str'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					} else{
						$this->dane['list']['@zmiana_free_str'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					}
				}
				//$this->dane['list']['available_firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());

			}
		}
	}

	public function getDate() : string{
		return $this->date->getMySqlDate();
	}
	public function getRozkazId() : int{
		return $this->rozkazId;
	}
	public function getDaneRozkazu() : array {
		return $this->dane;
	}

	public function getZmiana() : int{
		return $this->dane['variable']['[$nr_zmiany]'];
	}
	public function getSzablonId(){
		return $this->dane['szablon']['id'];
	}


	public function setObjectData(){
		$this->loopThroughObjects($this->obiekty,'recognizeTypeData');
		$this->loopThroughObjects($this->obiekty,'setDaneId');
	}

	public function displaySzablon(){
		foreach ($this->obiekty as $html){
			if($html instanceof HtmlObj) {
				$html->print();
			}
		}
	}

	public function displayRozkaz(){
		$this->loopThroughObjects($this->obiekty, 'removeClasses');
		$this->displaySzablon();
	}

	public function printLists(){
		//print_r($this->dane['list']);
		foreach ($this->dane['list'] as $id=>$lista){
			$datalist = '<datalist id="'.$id.'">';
			foreach ($lista as $pozycja){
				$datalist .= '<option value="'.$pozycja['key'].'">'.$pozycja['value'].'</option>';
			}
			$datalist .= '</datalist>';
			echo $datalist;
		}

	}


	private function loopThroughObjects($objects, string $functionName){
		if($objects instanceof HtmlObj){
			$this->{$functionName}($objects);
			if($objects->hasChilds()){
				$this->loopThroughObjects($objects->getChilds(), $functionName);
			}
		} elseif(is_array($objects)){
			foreach ($objects as $html_obj){
				$this->loopThroughObjects($html_obj, $functionName);
			}
		}
	}




	public function save($dane){
		foreach ($dane as $id=>$value){
			$this->dane['id'][$id] = $value;
		}
		$this->loopThroughObjects($this->obiekty, 'setDaneId');

	}

	private function recognizeTypeData(HtmlObj $html_obj){

		if($html_obj instanceof TextType){
			$zmienne = array();
			foreach (DBJrgSettings::getZmienneRozkazu() as $var){
				$zmienne[] = $var['id'];
			}
			$html_obj->changeContent($zmienne, $this->dane['variable']);
		} else if($html_obj instanceof ListAdapter) {
			$var_val = $html_obj->getDataVarType();
			if($var_val){
				$var = explode(' ',$var_val);
				foreach ($var as $lista){
					if(array_key_exists($lista,$this->dane['list'])){
						$html_obj->setListContent($this->dane['list'][$lista]);
					}
				}
			}
		}


	}

	private function setDaneId(HtmlObj $html_obj){
		$idObiektu = $html_obj->getId();
		if(array_key_exists($idObiektu,$this->dane['id'] )){
			if($html_obj instanceof ValueAdapter){
				$html_obj->setVal($this->dane['id'][$idObiektu]);
			}
		}
	}

	private function removeClasses(HtmlObj $html_obj){
		$html_obj->removeClasses('jrg_const','jrg_var','jrg_list','szablon_element');
		$html_obj->addAttr('disabled','disabled');
	}
}