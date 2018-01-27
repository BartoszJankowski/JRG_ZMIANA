<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 01:06
 */

class Harmonogram {

	private $rok;

	private $miesiace = array();

	public function __construct($rok, $serializeMsc = false) {
		$this->rok = $rok;
		if($serializeMsc){
			$this->miesiace = unserialize($serializeMsc);
		}
	}

	public function genHarmonogram(){
		$kalendarz = new Kalendarz($this->rok, 1,1);
		$tab = $kalendarz->getDayForYear($this->rok);
		if($tab){
			foreach ($tab as $num=>$day){
				$this->miesiace[$day['msc']][] = $day;
			}
		} else {
			echo "Podano zły rok";
		}

	}

	public function printHarmoHeader(int $month){
		$header = '<th>Nr</th><th>Strażak</th>';
		if(array_key_exists($month,$this->miesiace)){
			foreach($this->miesiace[$month] as $nrDnia => $dzien){
				$header .= '<th class="'.$dzien["dzien_tyg"].'">'.($nrDnia+1).'<br>'.$dzien["dzien_tyg"].'</th>';
			}
		}
		echo '<tr>'.$header.'</tr>';
	}

	public function printHarmoStrazakRow(Strazak $strazak, int $month) {
		$inner = '<td>'.$strazak->getNrPorz().'.</td><td>'.$strazak->toString().'</td>';
		if(array_key_exists($month,$this->miesiace)){
			$sluzby = get_harmo_types()[$strazak->getHarmoType()][1];
			$obj = new ArrayObject( $sluzby );
			$it = $obj->getIterator();
			$lp = 0;
			foreach($this->miesiace[$month] as $nrDnia => $dzien){

				$godziny = $this->getHours($dzien['zmiana'],$strazak->getZmiana());
				$dayVal = $strazak->getHarmonogram()!=null ? $strazak->getHarmonogram()->getDayVal($month, $nrDnia) : false;
				$color = "";
				if($dayVal){
					$color = get_harmo_val($dayVal)['col'];
				}
				$inner .= '<td class="'.$dzien["dzien_tyg"].' '.$color.' tdHarmCell"><label><input class="w3-hide harmoCheck" type="checkbox" name="'.$strazak->getStrazakId().'[]" value="'.$nrDnia.'" /><div class="harmoCell ">'.($godziny)*$it->current().'</div></label></td>';
				$lp += $godziny;
				if($dzien['zmiana'] == $strazak->getZmiana()){
					$lp = $godziny;
				} else if($lp==24){
					$lp = 0;
					$it->next();
				}
				if(!$it->valid()){
					$it->rewind();
				}
			}
		}

		echo '<tr>'.$inner.'</tr>';
	}

	public function getDayVal($month, $day){
		if(array_key_exists($month, $this->miesiace)){
			if(array_key_exists($day,$this->miesiace[$month] )){
				return $this->miesiace[$month][$day];
			}
		}
		return false;
	}

	/**
	 * Zwraca ilosc godzin do harmonogramu
	 * @param int $currentZmiana
	 * @param int $zmiana
	 *
	 * @return int
	 */
	private function getHours(int $currentZmiana, int $zmiana){
		if($currentZmiana == $zmiana){
			return 16;
		} else if( ($currentZmiana+1) == $zmiana || ($currentZmiana-2) == $zmiana){
			return 0;
		} else if( ($currentZmiana+2) == $zmiana || ($currentZmiana-1) == $zmiana ){
			return 8;
		}
		return 0;
	}

	public function putChanges(int $month, array $changes, string $value){
		foreach($changes as $day){
			$this->miesiace[$month][$day] = $value;
		}

	}
}