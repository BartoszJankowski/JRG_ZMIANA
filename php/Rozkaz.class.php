<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 11.02.2018
 * Time: 20:10
 */

class Rozkaz {

	private $dane = array(
		'szablon'=>array('id'=>null, 'hash'=>null),
		'constant'=>array('miasto'=>null,'nr_jrg'=>null,'data'=>null),
		'variable'=>array('data'=>null,'nr_rozkazu'=>null,'nr_zmiany'=>null),
		'list'=>array(),
		'id'=>array()
	);

	private $date;
	private $rozkazId = -1;
	private $JRG_ID;
	private $obiekty = array();

	public function __construct( ) {

	}

	public function createDane(int $jrg_id,LocalDateTime $ltd, DBJednostki $jednostka, DbDyzuDomowy $dbDyzury = null){
		$kalendar = new Kalendarz($ltd->getYear(), $ltd->getMonth(), $ltd->getDayOfMsc());
		$this->dane['constant']['miasto'] = $jednostka->getSelectedCity();
		$this->dane['constant']['nr_jrg'] = $jednostka->getSelectedJrgNr().'';
		$this->dane['constant']['data'] = (new LocalDateTime())->getDate();
		$this->dane['variable']['data'] = $ltd->getDate();
		$this->dane['variable']['rok'] = $ltd->getYear().'';
		$this->dane['variable']['msc'] = $ltd->getMonth().'';
		$this->dane['variable']['nr_rozkazu'] = $ltd->getDayOfYearNum().'';
		$this->dane['variable']['nr_zmiany'] = $kalendar->getCurrentZmiana().'';

		if($dbDyzury!=null){
			try{

				$ddomowe = $dbDyzury->loadDyzuryNaMsc($jrg_id,$ltd->getYear(), $ltd->getMonth());

			} catch (Exception $e){
				echo ' Błąd tworzenie kalendarza w klasie Rozkaz '.$e->getTraceAsString();
			}


			foreach ($ddomowe as $dyzuryDomowe){
				foreach ($dyzuryDomowe->listaStrZDyzuru($ltd->getDayOfMsc()-1) as $name){
					$this->dane['list']['harmo_fireman_Dd'][] = array('value'=>'','key'=>$name);
				}
			}
		}


		$this->JRG_ID = $jrg_id;
		$this->date = $ltd;
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
				$this->dane['list']['firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
				//print_r($strazak->getHarmonogram());
				if($strazak->getHarmonogram() instanceof Harmonogram){
					$dayVal = $strazak->getHarmonogram()->getDayVal($this->date->getMonth(), $this->date->getDayOfMsc()-1);

					if(!empty(get_harmo_val($dayVal))) {
						$this->dane['list']['harmo_fireman_'.$dayVal][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					} else if(!empty(get_graf_val($dayVal))) {
						$this->dane['list']['graf_fireman_'.$dayVal][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					} else
						$this->dane['list']['available_firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
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
		return $this->dane['variable']['nr_zmiany'];
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
		$var_val = $html_obj->getDataVarType();
		if($var_val){
			$var = explode('-',$var_val);
			$type = $var[0];
			$value = $var[1];

			switch($type){
				case 'constant':
					if(array_key_exists($value, $this->dane['constant'])  && !empty($this->dane['constant'][$value]))
						$html_obj->putContent($this->dane['constant'][$value]);
					break;
				case 'variable':
					if(array_key_exists($value, $this->dane['variable']) && !empty($this->dane['variable'][$value]) )
						$html_obj->putContent($this->dane['variable'][$value]);
					break;
				case 'list':
					if($html_obj instanceof ListAdapter){
						$types = explode(' ',$value);
						foreach ($types as $typData){
							if(array_key_exists($typData, $this->dane['list'])){
								$html_obj->setListContent($this->dane['list'][$typData]);
							}
						}

					}

					break;
				default:
					break;
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