<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 12:37
 */

class Grafik {

	private $rok, $msc, $zmiana, $kalendarz;
	private $dni = array();



	public function __construct(int $rok, int $msc, int $zmiana) {
		$this->rok = $rok;
		$this->msc = $msc;
		$this->zmiana = $zmiana;
		$this->kalendarz = new Kalendarz($rok, $msc);
		$this->extractDaysForMonth();
	}

	private function extractDaysForMonth(){
		$this->dni = array();
		$dni = $this->kalendarz->getDayForYear($this->rok);
		$nr = 0;
		foreach ($dni as $day){
			if($day['msc'] == $this->msc){

				if($day['zmiana'] == $this->zmiana){
					$this->dni[$nr] = $day['dzien_tyg'];
				}
				$nr++;
			}

		}
	}

	public function printMiesiac( array $strazacy ){
		echo '<form action="" method="post"><table  class="w3-table-all w3-hoverable w3-small table-grafik" >';
		echo $this->printHeader();
		foreach ($strazacy as $strazak){
			echo $this->strazakLine($strazak);
		}
		echo '</table><input class="w3-input w3-margin-top" type="submit" name="saveGraf" value="Zapisz grafik" /></form>';
	}

	private function printHeader():string {
		$resultString = '<th>Nr</th><th>Strażak</th>';
		foreach ($this->dni as $nr => $dzienTyg){
			$resultString .= '<th>'.($nr+1).'<br>'.$dzienTyg.'</th>';
		}
		return '<tr>'.$resultString.'</tr>';
	}

	private function strazakLine(Strazak $strazak) : string {
		$resultString = '<td>'.$strazak->getNrPorz().'.</td><td>'.$strazak->toString().'</td>';
		foreach ($this->dni as $nr => $dzienTyg){
			$val = $strazak->getHarmonogram() !=null ? $strazak->getHarmonogram()->getDayVal($this->msc, $nr) : "";
			$resultString .= '<td class="scale"><select name="'.$strazak->getStrazakId().'['.($nr).']" class="w3-select my-own-select" >'.$this->getSelectOptions($val).'</select></td>';
		}
		return  '<tr>'.$resultString.'</tr>';
	}

	private function getSelectOptions(string $val){
		$res = '';
		$res .= '<option '.(""==$val ? " selected" : "").' value=""></option>';
		foreach (get_grafik_values() as $v=>$tab) {

			$res .= '<option '.($v==$val ? " selected" : "").' value="' . $v . '">' . $v . '</option>';

		}
		return $res;
	}


}