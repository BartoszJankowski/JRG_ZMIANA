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


	private $suma = array();

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

		//TODO: szef zmiany ustala co ma byc dla niego widoczne w grafiku
		$tabGrafVals = get_grafik_values();
		foreach ($tabGrafVals as $val){
			foreach ($this->dni as $nr=>$v){
				$this->suma['Stan'][$nr] = 0;
				$this->suma[$val['n']][$nr] = 0;
			}
		}
		$uprList = DBJrgSettings::getUprawnienia();
		foreach ($uprList as $uprawnienie){
			foreach ($this->dni as $nr=>$v) {
				$this->suma[ $uprawnienie->getName() ][$nr] = 0;
			}
		}
	}

	public function printMiesiac( array $strazacy ){

		echo '<form action="" method="post" class="w3-center"><table  class="w3-table-all w3-hoverable w3-xsmall table-grafik" >';
		echo $this->printHeader();
		foreach ($this->dni as $nr=>$v){
			$this->suma['Stan'][$nr] = count($strazacy);
		}
		foreach ($strazacy as $strazak){
			echo $this->strazakLine($strazak);
		}

		$this->printFooter();

		echo '</table><div class="w3-margin-top"><button class="w3-button w3-border w3-light-grey " type="submit" name="saveGraf"  ><msc class="fa fa-fw fa-save"></msc> Zapisz grafik</button></div></form>';
	}

	public function printMiesiacForUser(User $user, array $strazacy){
		echo '<form action="" method="post" class="w3-center"><table  class="w3-table-all w3-hoverable w3-xsmall table-grafik" >';
		echo $this->printHeader();
		foreach ($strazacy as $strazak){
			echo $this->strazakCroppedLine( $user,$strazak);
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

	private function printFooter(){
		echo '<tr><td>Suma:</td></tr>';
		foreach($this->suma as $name=>$tab){
			$inner = '';
			foreach ($tab as $val){
				$inner .= '<td>'.$val.'</td>';
			}

			echo '<tr><td></td><td>'.$name.'</td>'.$inner.'</tr>';
		}
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
				$this->setSumaVal($strazak, $val,$nr);
			}
			$resultString .= '<td class="scale w3-display-container">'.$notka.'<select name="'.$strazak->getStrazakId().'['.($nr).']" class="w3-select my-own-select" >'.$this->getSelectOptions($val).'</select></td>';
		}
		return  '<tr>'.$resultString.'</tr>';
	}

	private function setSumaVal(Strazak $strazak, $val, $nrDnia){

		if(!empty(get_graf_val($val))){
			$this->suma[get_harmo_val($val)['n']][$nrDnia]++;
		} else if(!empty(get_harmo_val($val))){
			$this->suma['Stan'][$nrDnia]--;
		}
		foreach ($strazak->getUprawnienia() as $idUrp){
			$upr = DBJrgSettings::getUprawnienie($idUrp);
			if(array_key_exists($upr->getName(),$this->suma)){
				$this->suma[$upr->getName()][$nrDnia]++;
			}
		}
	}

	private function strazakCroppedLine(User $user,Strazak $strazak) : string {
		$resultString = '<td>'.$strazak->getNrPorz().'.</td><td>'.$strazak->toString().'</td>';
		foreach ($this->dni as $nr => $dzienTyg){
			$val = '';
			$innForPopover = '';
			$notka = '';
			if($strazak->getHarmonogram()!=null ){
				$val = $strazak->getHarmonogram()->getDayVal($this->msc, $nr);
				if($strazak->getStrazakId() === $user->getStrazak()->getStrazakId()){
					$val2 = $strazak->getHarmonogram()->getDayVal2($this->msc, $nr);
					if($val2){
						$notka = '<span class="w3-display-topright w3-small" data-toggle="tooltip" data-placement="top" title="'.$val2.'"><i class="fas fa-comment-alt "  ></i></span>';
					}
					$innForPopover = 'data-toggle="popover" 
						data-html="true" 
						data-val2="'.$val2.'" 
						data-jrg="'.$this->rok.'-'.$this->msc.'-'.($nr+1).'" 
						title="Dodaj notatkę dla szefa zmiany" data-placement="bottom"';
				}

			}
			$resultString .= '<td '.$innForPopover.' class="scale w3-display-container" >'.$notka.$val.'</td>';
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