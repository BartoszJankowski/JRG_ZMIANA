<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 31.01.2018
 * Time: 22:13
 */

class DyzuryDomowe {

	const LP_POL = 5;


	private $id, $zmiana, $rok, $msc ;

	private $strazacyIn = array();

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

	public function createEmpty(){
		$kalendarz = new Kalendarz($this->rok, $this->msc, 1);
		$dniTab = $kalendarz->getDayForMonth($this->msc);
		foreach ($dniTab as $k=>$v){
			if($v['z']+1 == $this->zmiana || $v['z']-2 == $this->zmiana){
				$this->dyzury[$k] = new PozycjaDD(($k+1),$this->msc,$this->rok);
			}
		}
	}

	public function printDyzury() {
		foreach ($this->dyzury as $nr=>$pozycja){
			if($pozycja instanceof PozycjaDD)
				$pozycja->printPozycja();
		}
	}

	public function printNaglowek(){
		echo '<h1>'.get_moth_name($this->msc).' '.$this->rok.'</h1><h3>Tabela dyżurów domowych</h3>';
	}

	public function setStrazacy(array $strazacy){
		foreach ($strazacy as $strazak){
			/* @var $strazak Strazak */
			$this->strazacyIn[] = new StrazakDD($strazak);
		}
	}

	public function printStrazacyInRows(){
		$res = '<tr><th>Strażak</th><th>Liczba dyżurów</th></tr>';

		foreach($this->strazacyIn as $strazakDD){
			if($strazakDD instanceof StrazakDD){
				$res .= '<tr><td>'.$strazakDD->toString().'</td><td>0</td></tr>';
			}
		}
		echo $res;
	}
	//dane strazaka: id, stopien, imie, nazwisko
}

class PozycjaDD {


	private $data;
	private $strazacy = array();

	public function __construct(int $nr, int $msc, int $rok) {
		$this->data = ($nr<10 ? '0'.$nr : $nr ).'-'.($msc<10 ? '0'.$msc : $msc ).'-'.$rok;
	}

	public function printPozycja(){

		echo '<tr><td class="w3-center" style="vertical-align: middle;">'.$this->data.'</td><td>'.$this->printDzienRows().'</td></tr>';
	}

	private function printDzienRows(){
		$res ='';
		for($i = 0; $i<DyzuryDomowe::LP_POL; $i++){
			if(array_key_exists($i, $this->strazacy)){
				$res .= '<li>'.$this->strazacy->toString().'</li>';
			} else {
				$res .= '<li><input type="text" class=""></li>';
			}
		}
		return '<ol>'.$res.'</ol>';
	}

	public static function printNaglowek(){

		echo '<tr><th  class="w3-center">Data</th><th  class="w3-center">Strażacy</th></tr>';
	}
}

class StrazakDD {

	private $id;
	private $stopien;
	private $imie;
	private $nazwisko;

	public function __construct(Strazak $strazak = null) {
		if($strazak!=null){
			$this->id = $strazak->getStrazakId();
			$this->stopien = $strazak->getStopien();
			$this->imie = $strazak->getImie();
			$this->nazwisko = $strazak->getNazwisko();
		}
	}

	public function toString(){
		return get_stopien_short($this->stopien).' '.$this->nazwisko.' '.$this->imie;
	}

}