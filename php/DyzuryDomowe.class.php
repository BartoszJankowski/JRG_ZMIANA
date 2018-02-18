<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 31.01.2018
 * Time: 22:13
 */

class DyzuryDomowe {

	const LP_POL = 5;
	const MAX_DD = 3;


	private $id, $zmiana, $rok, $msc ;

	/**
	 * @var array[StrazakDD,Strazak]
	 */
	private $strazacyIn = array();

	/**
	 * @var Harmonogram[]
	 */
	private $harmonogramy;

	/**
	 * @var PozycjaDD[]
	 */
	private $dyzury = array();

	/**
	 * DyzuryDomowe constructor.
	 *
	 * @param $id
	 * @param $zmiana
	 * @param $rok
	 * @param $msc
	 * @param array $dyzury
	 */
	public function __construct( $id = null,int $zmiana = null,int $rok = null,int $msc = null, string $dyzury = '' ) {
		if(strlen($dyzury) > 0){
			$this->id     = $id;
			$this->zmiana = $zmiana;
			$this->rok    = $rok;
			$this->msc    = $msc;
			$this->dyzury = unserialize($dyzury);
		} else if($zmiana!=null && $rok!=null && $msc !=null ){
			$this->zmiana = $zmiana;
			$this->rok    = $rok;
			$this->msc    = $msc;
		}
	}


	public function setHarmo(array $harmonogramy){$this->harmonogramy = $harmonogramy;}

	/**
	 * @return PozycjaDD[]
	 */
	public function getDyzury(): array {
		return $this->dyzury;
	}

	/**
	 * @return int
	 */
	public function getZmiana(): int {
		return $this->zmiana;
	}

	/**
	 * @return int
	 */
	public function getRok(): int {
		return $this->rok;
	}

	/**
	 * @return int
	 */
	public function getMsc(): int {
		return $this->msc;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id ): void {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function createEmpty(){
		$kalendarz = new Kalendarz($this->rok, $this->msc, 1);
		$dniTab = $kalendarz->getDayForMonth($this->msc);
		foreach ($dniTab as $k=>$v){
			if($v['z']+1 == $this->zmiana || $v['z']-2 == $this->zmiana){
				$this->dyzury[$k] = new PozycjaDD($k,$this->msc,$this->rok);
			}
		}
	}
	public function fillWithPostData(array $post){

		foreach ($this as $property=>$value){
			if(isset($post[$property])){
				if($property === 'dyzury'){
					if(is_array($post[$property])){
						foreach ($post[$property] as $data => $listaStrId){
							$pozycja_DD = $this->getPozycjaDD($data);
							if($pozycja_DD){
								$pozycja_DD->clear();
								foreach ($listaStrId as $id_str=>$name){
									if(array_key_exists($id_str, $this->strazacyIn)){
										$pozycja_DD->addStr($this->strazacyIn[$id_str]['strDD']);
									} else {
										$pozycja_DD->addStr((new StrazakDD())->setIdName($id_str,$name));
									}
								}
							}
						}
					} else {
						$this->dyzury = array();
					}
				} else {
					$this->$property = $post[$property];
				}
			}
		}
	}

	public function printDyzury() {
		echo '<input type="hidden" name="id" value="'.$this->id.'">';
		foreach ($this->dyzury as $nr=>$pozycja){
			if($pozycja instanceof PozycjaDD)
				$pozycja->printPozycja($this);
		}
	}

	public function printDyzuryDiff(){
		$inn= '';
		foreach ($this->dyzury as $pozycja_DD){
			$inn .= '<td>'.($pozycja_DD->dd+1).'</td>';
		}
		$firstRow = '<tr><td>Upr.</td><td>Strażak</td>'.$inn.'</tr>';

		foreach ($this->strazacyIn as $id=>$tab){
			$uprI = '';
			foreach ($tab['str']->getUprawnienia() as $nr){
				$uprawnienie = DBJrgSettings::getUprawnienie($nr);
				$uprI .= '<msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></msc>';
			}
			$row = '<tr><td>'.$uprI.'</td><td>'.$tab['strDD']->getname().'</td>';
			foreach ($this->dyzury as $pozycja_DD){
				if($pozycja_DD->checkIfExists($id)){
					$row.= '<td width="50">D</td>';
				} else {
					$row.= '<td width="50"></td>';
				}
			}
			$row .= '</tr>';
			$firstRow .= $row;
		}
		echo '<table class="w3-table-all table-grafik" >'.$firstRow.'</table>';
	}

	public function setStrazacy(array $strazacy){
		foreach ($strazacy as $strazak){
			/* @var $strazak Strazak */
			$this->strazacyIn[$strazak->getStrazakId()] = array('strDD'=>new StrazakDD($strazak),'str'=>$strazak);
		}
	}

	public function printStrazacyInRows(){
		$res = '<tr><th>Upr.</th><th>Strażak</th><th>Liczba dyżurów</th></tr>';
		$uprawnieniaJrg = DBJrgSettings::getUprawnienia();

		foreach($this->strazacyIn as $str_id=> $tab){
			if($tab['str'] instanceof Strazak){
				$uprI = '';
				foreach ($tab['str']->getUprawnienia() as $id){
					$uprawnienie = DBJrgSettings::getUprawnienie($id);
					$uprI .= '<msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></msc>';
				}
				$res .= '<tr><td nowrap >'.$uprI.'</td><td class="highlightFireman" id="str_id_'.$str_id.'">'.$tab['strDD']->getName().' </td><td>'.$this->countStrOccurences($str_id).'</td></tr>';
			}
		}
		echo $res;
	}

	//zwraza strazaka z listyy $strazacyIn
	public function getStrazakIn($str_id) : Strazak{
		if(array_key_exists($str_id,$this->strazacyIn)){
			return $this->strazacyIn[$str_id]['str'];
		} else {
			return null;
		}
	}

	public function listaStrZDyzuru(int $dzien) : array {
		$res = array();
		foreach ($this->dyzury as $pozycja_DD){
			if($pozycja_DD->dd == $dzien){
				foreach ($pozycja_DD->getStrList() as $strazak_DD){
					$res[] = $strazak_DD->getName();
				}
			}
		}
		return $res;
	}

	public function autoUzupelnienie($dane){
		//$min = min($dane['range']);
		//$max = max($dane['range']);
		$uprWymagane = is_array($dane['wymagane']) ? $dane['wymagane'] : array() ;

		$tabCount = array(); //id => lp
		foreach ($this->strazacyIn as $id=>$tab){ $tabCount[$id]  = 0; }

		//uzupelnienie ludzikami przed kazda wolna słuzbą jesli mozna
		if($dane['przedWS']==1)
			$this->uzupelnijPrzedWs( $tabCount);

		//Dalsze uzupełnienie wg kryteriów uprawnien
		$this->uzupelnijWgKryteriow($uprWymagane, $tabCount);
		//wypelnienie pozostałych dyzurow (wolnych miejsc) oraz
		if($dane['fillUp']==1)
		$this->uzupelnijPozostalymiStrazakami($tabCount);

		//poprawka
		//jesli są strazacy ktorzy maja mniej od maksa o więcej niz 1 to nalezy zastosowac poprawkę korygującą

	}

	private function uzupelnijPozostalymiStrazakami( array &$tabCount){

		//sprawdzenie wszystkich strazakow
		//stara się uzupelnij równomiernie
		do{
			$added = false;
			foreach ($this->strazacyIn as $strId=>$tab){
				//sprawdzenie wszystkich dni
				foreach ($this->dyzury as $pozycja_DD){
					if($tabCount[$strId]<DyzuryDomowe::MAX_DD){
						if($pozycja_DD->getLp()<DyzuryDomowe::LP_POL && !$pozycja_DD->checkIfExists($strId)){
							if(!$this->czyStrazakMaUrlop($this->harmonogramy[$strId]->getDayVal($pozycja_DD->mm,$pozycja_DD->dd))){
								$pozycja_DD->addStr($tab['strDD']);
								$tabCount[$strId]++;
								$added = true;
								continue 2;
							}
						}
					}
				}
			}
		} while($added);
	}

	private function uzupelnijWgKryteriow(array $uprWymagane, array &$tabCount){
		foreach ($this->dyzury as $pozycja_DD){

			$uprObecnie = $pozycja_DD->getUprList($this->strazacyIn);
			$uprRoznica  = array_diff($uprWymagane, $uprObecnie);
			//kontynuuje dopuki nie spelni warunku uzupelnienia lub nic juz nie znajdzie
			while (count($uprRoznica)>0){
				$added = false;
				//TODO: wybrac najlepszego strazaka z najlepszym dopasowaniem
				foreach ($this->strazacyIn as $id=>$tab){
					//jesli ten dzien nie jest juz pelny
					if($pozycja_DD->getLp()<DyzuryDomowe::LP_POL){
						//jesli w tym dniu niema jeszcze tego strazaka
						if(!$pozycja_DD->checkIfExists($id)){
							//czy strazak nie przkroczyl jeszcze 72h
							if($tabCount[$id]<DyzuryDomowe::MAX_DD){
								//jesli w tym dniu strazak moze miec dyzur domowy
								if(!$this->czyStrazakMaUrlop($this->harmonogramy[$id]->getDayVal($pozycja_DD->mm,$pozycja_DD->dd))){
									//czy strazak w ogole posiada te uprawnienia ktorych szukamy
									$uprPoDodaniu = array_diff($uprRoznica,$tab['str']->getUprawnienia());
									if(count($uprPoDodaniu)<count($uprRoznica)){
										$pozycja_DD->addStr($tab['strDD']);
										$tabCount[$id]++;
										$added = true;
										$uprRoznica = $uprPoDodaniu;
										break;
									}
								}
							}
						}

					}
				}
				if(!$added){
					break;
				}
			}
		}
	}

	private function uzupelnijPrzedWs(array &$tabCount){

		foreach ($this->strazacyIn as $str_id => $tab){

			/** @var $strazak Strazak  */
			//$strazak = $tab['str'];
			$harmonogram = $this->harmonogramy[$str_id];


			foreach ($this->dyzury as $pozDD){
				$currentDayVal = $harmonogram->getDayVal($pozDD->mm,$pozDD->dd);
				//sprawdzenie w harmonogramie czy strazak ma urlop, delegacje, chorobowe itp
				if(!$this->czyStrazakMaUrlop($currentDayVal)){
					$nextDayVal = $harmonogram->getDayVal($pozDD->mm,$pozDD->dd+1);
					//pobranie wartosci nastepnego dnia [Ws, Urlop itp]
					if($nextDayVal === false && $pozDD->mm<12){
						$nextDayVal = $harmonogram->getDayVal($pozDD->mm+1,0);
					}
					//sprawdzenie wartosci nastepnego dnia - czy to wolna służba
					if($nextDayVal !== false && $nextDayVal === 'Ws'){
						//kontynuowac tylko kiedy dzien nie jest pełny jeszcze
						if($pozDD->getLp()<DyzuryDomowe::LP_POL){
							//sprawdzic czy strazak nie jest juz przypadkiem dodany
							if(!$pozDD->checkIfExists($str_id)){
								//sprawdzic czy strazak nie przekroczył juz 72h dd
								if($tabCount[$str_id] < DyzuryDomowe::MAX_DD){
									//dodanie do pozycji oraz do sumy Dyzurów dla kazdego strazaka
									$pozDD->addStr($tab['strDD']);
									$tabCount[$str_id]++;
								}
							}
						}
					}
				}
			}
		}
	}

	//zlicza wystapienia strazak w dniach
	private function countStrOccurences($strId) : int{
		$o = 0;
		foreach ($this->dyzury as $pozycja_DD){
			if($pozycja_DD->checkIfExists($strId))
				$o++;
		}
		return $o;
	}

	//sprawdza czy strak ma w harmonogramie jakąs wartośc urlopową
	private function czyStrazakMaUrlop($currentDayVal) : bool {
		if(empty($currentDayVal)){
			return false;
		} elseif (empty(get_harmo_val($currentDayVal))){
			return false;
		} else {
			return true;
		}
	}

	private function getPozycjaDD(string  $data) {
		foreach ($this->dyzury as $pozycja_DD){
			if($pozycja_DD->data === $data){
				return $pozycja_DD;
			}
		}
		return false;
	}

	public function czyStrazakMaDyzur($strId, $day){

		if(array_key_exists($day, $this->dyzury )){
			return  $this->dyzury[$day]->checkIfExists($strId);
		}
		return false;
	}
}

class PozycjaDD {

	public $dd;
	public $mm;
	public $yy;

	public $data;
	/**
	 * @var StrazakDD[]
	 */
	private $strazacy = array();

	public function __construct(int $nr, int $msc, int $rok) {
		$this->dd = $nr;
		$this->mm = $msc;
		$this->yy = $rok;
		$this->data = (($nr+1)<10 ? '0'.($nr+1) : ($nr+1) ).'-'.($msc<10 ? '0'.$msc : $msc ).'-'.$rok;
	}

	public function printPozycja(DyzuryDomowe $dyzury_domowe){

		echo '<tr><td class="w3-center" style="vertical-align: middle;">'.$this->data.'</td><td>'.$this->printDzienRows($dyzury_domowe).'</td></tr>';
	}

	private function printDzienRows(DyzuryDomowe $dyzury_domowe){
		$res ='';
		for($i = 0; $i<DyzuryDomowe::LP_POL; $i++){
			if(array_key_exists($i, $this->strazacy)){
				$uprI = '';
				$idStr = $this->strazacy[$i]->getId();
				$uprawnienia = $dyzury_domowe->getStrazakIn($idStr)->getUprawnienia();
				foreach ($uprawnienia as $id){
					$uprawnienie =  DBJrgSettings::getUprawnienie($id);
					$uprI .= '<msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></msc>';
				}

				$res .= '<li class="str_id_'.$this->strazacy[$i]->getId().'"><input type="hidden" name="dyzury['.$this->data.']['.$this->strazacy[$i]->getId().']" value="'.$this->strazacy[$i]->getName().'" />'.$this->strazacy[$i]->getName().' '.$uprI.'</li>';
			} else {
				$res .= '<li><input type="hidden" name="'.$this->data.'[dyzury][]" value="-1" /><input type="text" class=""></li>';
			}
		}
		return '<ol>'.$res.'</ol>';
	}

	public static function printNaglowek(){

		echo '<tr><th  class="w3-center">Data</th><th  class="w3-center">Strażacy</th></tr>';
	}

	/**
	 * @return int
	 */
	public function getLp(){
		return count($this->strazacy);
	}

	/**
	 * @return int[]
	 */
	public function getUprList(array $strazacyIn){
		$tab = array();

		foreach ($this->strazacy as $strazakDD){
			/** @var $strazak Strazak */
			$strazak = $strazacyIn[$strazakDD->getId()]['str'];
			foreach ($strazak->getUprawnienia() as $nr){
				if(array_search($nr,$tab ) === false){
					$tab[] = $nr;
				}
			}
		}

		return $tab;
	}

	public function checkIfExists(int $strId) : bool {
		foreach ($this->strazacy as $str){
			if($str->getId() == $strId){
				return true;
			}
		}
		return false;
	}

	public function addStr(StrazakDD $str){
		$this->strazacy[] = $str;
	}

	public function clear() :void {
		$this->strazacy = array();
	}

	public function getStrList(){
		return $this->strazacy;
	}

}

class StrazakDD {

	private $id, $name;


	public function __construct(Strazak $strazak = null) {
		if($strazak!=null){
			$this->id   = $strazak->getStrazakId();
			$this->name = get_stopien_short($strazak->getStopien()) . ' ' . $strazak->getImie() . ' ' . $strazak->getNazwisko();
		}
	}

	public function setIdName($id,$name) : StrazakDD{
		$this->id = $id;
		$this->name = $name;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function getId(){
		return $this->id;
	}


}