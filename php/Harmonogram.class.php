<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 01:06
 */

class Harmonogram {
	//TODO: Iwonka po dodaniu nie miała harmonogramu :O

	private $rok;

	private $miesiace = array();

	public function __construct($rok, $serializeMsc = false) {
		$this->rok = $rok;
		if($serializeMsc){
			$this->miesiace = unserialize($serializeMsc);
		}
	}

	/**
	 * Tworzy pusty harmonogram / mozliwe ze deprecated
	 */
	public function genHarmonogram(){
		$kalendarz = new Kalendarz($this->rok, 1,1);
		$tab = $kalendarz->getDayForYear($this->rok);
		if($tab){
			foreach ($tab as $num=>$day){
				$this->miesiace[$day['msc']][] = array('z'=>$day['zmiana'],'t'=>$day['dzien_tyg'],'h'=>0);
			}
		} else {
			echo "Podano zły rok";
		}
	}

	/**
	 * Tworzy puste pola z godzinami w harmonogramie dla strazaka
	 * @param Strazak $strazak
	 */
	public function genHarmoForStrazak(Strazak $strazak){
		$kalendarz = new Kalendarz($this->rok, 1,1);
		$tab = $kalendarz->getDayForYear($this->rok);
		if($tab){
			$sluzby = get_harmo_types()[$strazak->getHarmoType()][1];
			$obj = new ArrayObject( $sluzby );
			$i = $obj->getIterator();
			foreach ($tab as $num=>$day){
				$g = $this->calcHours($strazak->getZmiana(), $day['zmiana'], $i);

				$this->miesiace[$day['msc']][] = array('v'=>'','h'=>$g,'v2'=>'');

				if(!$i->valid()){
					$i->rewind();
				}
			}
		} else {
			echo "Podano zły rok";
		}
	}

	/**
	 * Oblicza godziny 16/8/0 względem typu harmonogramu
	 * @param $zmiana
	 * @param $obecnaZmiana
	 * @param ArrayIterator $i
	 *
	 * @return float|int
	 */
	private function calcHours($zmiana, $obecnaZmiana, ArrayIterator $i ){
		$res = 0;
		$mno = $i->current();
		if($zmiana == $obecnaZmiana){

			$res = 16;
		} elseif($zmiana == 1 && $obecnaZmiana == 2 || $zmiana == 2 && $obecnaZmiana == 3 || $zmiana == 3 && $obecnaZmiana == 1){
			$res = 8;
			$i->next();
		}
		return $res*$mno ;
	}

	/**
	 * naglowek tabeli <th> dla wierszy strazaka harmonogramu
	 * @param int $month
	 */
	public function printHarmoHeader(int $month){
		$header = '<th>Nr</th><th>Strażak</th>';
		if(array_key_exists($month,$this->miesiace)){
			foreach($this->miesiace[$month] as $nrDnia => $dzien){
				$header .= '<th class="'.$dzien["t"].'">'.($nrDnia+1).'<br>'.$dzien["t"].'</th>';
			}
		}
		echo '<tr>'.$header.'</tr>';
	}

	/**
	 * Wiersz harmonogramu strazaka
	 * @param Strazak $strazak
	 * @param int $month
	 */
	public function printharmoRow(Strazak $strazak, int $month){
		$inner = '<td>'.$strazak->getNrPorz().'.</td><td>'.$strazak->toString().'</td>';
		foreach ($this->miesiace[$month] as $nrDnia =>$dzien){
			$color = get_harmo_val($dzien['v'])['col'];
			$inner .= '<td class=" '.$color.' tdHarmCell"><label><input class="w3-hide harmoCheck" type="checkbox" name="'.$strazak->getStrazakId().'[]" value="'.$nrDnia.'" /><div class="harmoCell ">'.( $dzien['h']>0 ? '<b>'.$dzien['h'].'</b>': $dzien['h'] ).'</div></label></td>';

		}
		echo '<tr>'.$inner.'</tr>';
	}


	public function getDayVal($month, $day){
		if(array_key_exists($month, $this->miesiace)){
			if(array_key_exists($day,$this->miesiace[$month] )){
				return $this->miesiace[$month][$day]['v'];
			}
		}
		return false;
	}



	public function putChanges(int $month, array $changes, string $value){
		foreach($changes as $day){
			$this->miesiace[$month][$day]['v'] = $value ;
		}
	}

	public function putGrafChanges(int $month, array $changesDaysTab){
		foreach ($changesDaysTab as $day => $change){
			if(empty($change) && empty($this->miesiace[$month][$day]['v'])){
				continue;
			}
			$this->miesiace[$month][$day]['v'] = $change;
		}
	}
}