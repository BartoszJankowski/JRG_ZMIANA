<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 01:06
 */

class Harmonogram {

	private $rok;
	private $typ; // 110,101,011
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
	public function genHarmoForStrazak(Strazak $strazak, string $har_T){
		$this->setharmoType($har_T);
		$kalendarz = new Kalendarz($this->rok, 1,1);
		$tab = $kalendarz->getDayForYear($this->rok);
		if($tab){
			$sluzby = get_harmo_types()[$this->typ][1];
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

	public function printHarmoHeaderForUser(){
		$header = '<th>Miesiąc</th>';
		for($i=1;$i<=31; $i++)
				$header .= '<th class="w3-center">'.$i.'</th>';
		echo '<tr>'.$header.'</tr>';
	}



	/**
	 * Wiersz harmonogramu strazaka
	 * @param Strazak $strazak
	 * @param int $month
	 */
	public function printharmoRow(Strazak $strazak, int $month){
		$inner = '<td >'.$strazak->getNrPorz().'.</td><td class="tdHarmCell"><button type="button" name="'.$strazak->getHarmonogram()->getHarmoType().'" value="'.$strazak->getStrazakId().'" data-toggle="popover"  data-html="true" title="Zmień typ harmonogramu"  class="w3-btn w3-small"><i class="fa fa-wrench"></i></button> '.$strazak->toString().'</td>';
		if(!empty($this->miesiace)){
			foreach ($this->miesiace[$month] as $nrDnia =>$dzien){
				$color = get_harmo_val($dzien['v'])['col'];
				$inner .= '<td title="Zmień godziny pracy" data-placement="top"  class="w3-center  tdHarmCell" style="background-color: '.$color.'">
							
							<input class="w3-hide harmoCheck" type="checkbox" name="'.$strazak->getStrazakId().'['.$nrDnia.'][v]" value="'.$nrDnia.'" />
							<div class="harmoCell "><input type="text" name="'.$strazak->getStrazakId().'['.$nrDnia.'][h]" min="0" max="24" value="'.$dzien['h'].'" /></div>
							
							</td>';
			}
		} else {
			$inner .= '<td draggable="true" class="w3-center " colspan="10"></form><b>Brak harmonogramu </b></td><td  class="tdHarmCell" colspan="21" ><form action="" method="get"><select class="" name="typ"><option value="110">Służba Służba Wolne</option><option value="101">Służba Wolne Służba</option><option value="011">Wolne Służba Służba</option></select><button type="submit" name="createHarmo" value="'.$strazak->getStrazakId().'" class="">Stwórz</button></form></td>';
		}

		echo '<tr strid="'.$strazak->getStrazakId().'">'.$inner.'</tr>';
	}


	public function printMonthharmoRow(int $month){

		$inner = '<td>'.get_moth_name($month).'</td>';
		foreach ($this->miesiace[$month] as $nrDnia =>$dzien){
			$color = get_harmo_val($dzien['v'])['col'];
			$inner .= '<td title=" '.($nrDnia+1).' '.get_moth_name($month).' '.$this->rok.' " data-placement="top"  class="w3-center  tdHarmCell" style="background-color: '.$color.'">
								<div class="harmoCell ">'.$dzien['h'].'</div>
						</td>';
		}
		echo '<tr >'.$inner.'</tr>';
	}


	public function getDayVal($month, $day){
		if(array_key_exists($month, $this->miesiace)){
			if(array_key_exists($day,$this->miesiace[$month] )){
				return $this->miesiace[$month][$day]['v'];
			}
		}
		return false;
	}
	public function getDayVal2($month, $day){
		if(array_key_exists($month, $this->miesiace)){
			if(array_key_exists($day,$this->miesiace[$month] )){
				return $this->miesiace[$month][$day]['v2'];
			}
		}
		return false;
	}




	public function putChanges(int $month, array $changes, string $value){
		foreach($changes as $day=>$val){
			if(isset($val['v']))
				$this->miesiace[$month][$day]['v'] = $value ;
			if(isset($val['h']))
				$this->miesiace[$month][$day]['h'] = $val['h'] ;
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

	public function setV2(LocalDateTime $ltd, string $info) : bool{
		if(array_key_exists($ltd->getMonth(),$this->miesiace)){
			if(array_key_exists($ltd->getDayOfMsc()-1,$this->miesiace[$ltd->getMonth()])){
				//stripslashesh itp -already done in POST input_check
				$this->miesiace[$ltd->getMonth()][$ltd->getDayOfMsc()-1]['v2'] = $info;
				return true;
			}
		}
		return false;
	}


	public function setHarmoType(string $harm_t){
		$typy = get_harmo_types();
		if(array_key_exists($harm_t, $typy)){
			$this->typ = $harm_t;
		} else {
			$this->typ = array_keys($typy)[0];
		}
	}

	public function changeHarmoType(Strazak $strazak, string $harmT){

		$this->setharmoType($harmT);
		$kalendarz = new Kalendarz($this->rok, 1,1);
		$tab = $kalendarz->getDayForYear($this->rok);
		$licznik  = array();
		if($tab){
			$sluzby = get_harmo_types()[$this->typ][1];
			$obj = new ArrayObject( $sluzby );
			$i = $obj->getIterator();

			foreach ($tab as $num=>$day){
				$g = $this->calcHours($strazak->getZmiana(), $day['zmiana'], $i);
				if(!isset($licznik[$day['msc']])){
					$licznik[$day['msc']] = 0;
				}
				$old = $this->miesiace[$day['msc']][$licznik[$day['msc']]];
				$this->miesiace[$day['msc']][$licznik[$day['msc']]] = array('v'=>$old['v'],'h'=>$g,'v2'=>$old['v2']);

				if(!$i->valid()){
					$i->rewind();
				}
				$licznik[$day['msc']] ++;
			}
		} else {
			echo "Podano zły rok";
		}
	}

	public function getHarmoType(){
		return $this->typ;
	}

	public function isHarmoSet() :bool {
		return !empty($this->miesiace);
	}
}