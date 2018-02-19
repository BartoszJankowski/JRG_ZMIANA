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
		echo '<form action="" method="post" class="w3-center"><table  class="w3-table-all w3-hoverable w3-xsmall table-grafik" >';
		echo $this->printHeader();
		foreach ($strazacy as $strazak){
			echo $this->strazakLine($strazak);
		}
		echo '</table><div class="w3-margin-top"><button class="w3-button w3-border w3-light-grey " type="submit" name="saveGraf"  ><msc class="fa fa-fw fa-save"></msc> Zapisz grafik</button></div></form>';
	}

	public function printMiesiacForUser(User $user, array $strazacy){
		$this->user = $user;
		echo '<form action="" method="post" class="w3-center"><table  class="w3-table-all w3-hoverable w3-xsmall table-grafik" >';
		echo $this->printHeader();
		foreach ($strazacy as $strazak){
			echo $this->strazakCroppedLine($strazak);
		}
		echo '</table><div class="w3-margin-top"><button class="w3-button w3-border w3-light-grey " type="submit" name="saveGraf"  ><msc class="fa fa-fw fa-save"></msc> Zapisz grafik</button></div></form>';

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
			$val = '';
			$notka = '';
			if($strazak->getHarmonogram()!=null ){
				$val = $strazak->getHarmonogram()->getDayVal($this->msc, $nr);
				$val2 = $strazak->getHarmonogram()->getDayVal2($this->msc, $nr);
				if($val2){
					$notka = '<span class="w3-display-topright w3-small" data-toggle="tooltip" data-placement="top" title="'.$val2.'"><i class="fas fa-comment-alt "  ></i></span>';
				}
			}
			$resultString .= '<td class="scale w3-display-container">'.$notka.'<select name="'.$strazak->getStrazakId().'['.($nr).']" class="w3-select my-own-select" >'.$this->getSelectOptions($val).'</select></td>';
		}
		return  '<tr>'.$resultString.'</tr>';
	}

	private function strazakCroppedLine(Strazak $strazak) : string {
		$resultString = '<td>'.$strazak->getNrPorz().'.</td><td>'.$strazak->toString().'</td>';
		foreach ($this->dni as $nr => $dzienTyg){
			$val = '';
			$notka = '';
			if($strazak->getHarmonogram()!=null ){
				$val = $strazak->getHarmonogram()->getDayVal($this->msc, $nr);
				if($strazak->getStrazakId() === $this->user->getStrazak()->getStrazakId()){
					$val2 = $strazak->getHarmonogram()->getDayVal2($this->msc, $nr);
					if($val2){
						$notka = '<span class="w3-display-topright w3-small" data-toggle="tooltip" data-placement="top" title="'.$val2.'"><i class="fas fa-comment-alt "  ></i></span>';
					}
				}

			}
			$resultString .= '<td class="scale w3-display-container">'.$notka.$val.'</td>';
		}
		return  '<tr>'.$resultString.'</tr>';
	}

	private function getSelectOptions(string $val){
		$harmVal = get_harmo_val($val);
		$res = '';
		if(!empty($harmVal)){
			$res .= '<option selected disabled >'.$val.'</option><option value="" ></option>';
		} else {
			$res .= '<option '.(empty($val) ? "selected" : "").' value=""></option>';
		}

		foreach (get_grafik_values() as $v=>$tab) {

			$res .= '<option '.($v==$val ? " selected" : "").' value="' . $v . '">' . $v . '</option>';

		}
		return $res;
	}


}