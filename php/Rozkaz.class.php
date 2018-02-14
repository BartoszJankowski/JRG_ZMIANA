<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 11.02.2018
 * Time: 20:10
 */

class Rozkaz {

	private $dane = array(
		'constant'=>array('miasto'=>null,'nr_jrg'=>null,'data'=>null),
		'variable'=>array('data'=>null));

	private $date;
	private $JRG_ID;
	private $obiekty = array();
	private $strazacy = array();
	/**
	 * @var DyzuryDomowe
	 */
	private $dyzuDomowy;

	public function __construct( int $jrg_id, array $obiektyHtml, LocalDateTime $ltd, DBJednostki $jednostka) {
		$kalendar = new Kalendarz($ltd->getYear(), $ltd->getMonth(), $ltd->getDayOfMsc());
		$this->dane['constant']['miasto'] = $jednostka->getSelectedCity();
		$this->dane['constant']['nr_jrg'] = $jednostka->getSelectedJrgNr().'';
		$this->dane['constant']['data'] = (new LocalDateTime())->getDate();
		$this->dane['variable']['data'] = $ltd->getDate();
		$this->dane['variable']['rok'] = $ltd->getYear().'';
		$this->dane['variable']['msc'] = $ltd->getMonth().'';
		$this->dane['variable']['nr_rozkazu'] = $ltd->getDayOfYearNum().'';
		$dbDyzury = new DbDyzuDomowy();

		try{
			$this->dane['variable']['nr_zmiany'] = $kalendar->getCurrentZmiana().'';
			$ddomowe = $dbDyzury->loadDyzuryNaMsc($jrg_id,$ltd->getYear(), $ltd->getMonth());

		} catch (Exception $e){
			echo ' Błąd tworzenie kalendarza w klasie Rozkaz '.$e->getTraceAsString();
		}

		foreach ($ddomowe as $dyzuryDomowe){
			foreach ($dyzuryDomowe->listaStrZDyzuru($ltd->getDayOfMsc()-1) as $name){
				$this->dane['list']['harmo_fireman_Dd'][] = array('value'=>'','key'=>$name);
			}
		}



		$this->date = $ltd;
		$this->JRG_ID = $jrg_id;
		$this->obiekty = $obiektyHtml;
	}

	public function setFiremans(array $strazacy){
		$this->strazacy = $strazacy;
		foreach ($strazacy as $strazak){
			if($strazak instanceof Strazak){
				$this->dane['list']['firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
				//print_r($strazak->getHarmonogram());
				if($strazak->getHarmonogram() instanceof Harmonogram){
					$dayVal = $strazak->getHarmonogram()->getDayVal($this->date->getMonth(), $this->date->getDayOfMsc()-1);

					if(!empty(get_harmo_val($dayVal)))
						$this->dane['list']['harmo_fireman_'.$dayVal][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
					else
						$this->dane['list']['available_firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());
				}
				//$this->dane['list']['available_firemans'][] = array('value'=>$strazak->getStrazakId(),'key'=>$strazak->toString());

			}
		}
	}




	public function displaySzablon(){
		$this->loopThroughObjects($this->obiekty);
		foreach ($this->obiekty as $html){
			if($html instanceof HtmlObj) {
				$html->print();
			}
		}
	}

	private function loopThroughObjects($objects){
		if($objects instanceof HtmlObj){
			$this->recognizeTypeData($objects);
			if($objects->hasChilds()){
				$this->loopThroughObjects($objects->getChilds() );
			}
		} elseif(is_array($objects)){
			foreach ($objects as $html_obj){
				$this->loopThroughObjects($html_obj);
			}
		}
	}

	private function recognizeTypeData(HtmlObj $html_obj){
		$var_val = $html_obj->getDataVarType();
		if($var_val){
			$var = explode('-',$var_val);
			$type = $var[0];
			$value = $var[1];

			switch($type){
				case 'constant':
					if(array_key_exists($value, $this->dane['constant']))
						$html_obj->putContent($this->dane['constant'][$value]);
					break;
				case 'variable':
					if(array_key_exists($value, $this->dane['variable']))
						$html_obj->putContent($this->dane['variable'][$value]);
					break;
				case 'list':
					if($html_obj instanceof ListAdapter){
						if(array_key_exists($value, $this->dane['list'])){
							$html_obj->setListContent($this->dane['list'][$value]);
						}
					}

					break;
				default:
					break;
			}
		}
	}


}