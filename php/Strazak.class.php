<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 24.01.2018
 * Time: 12:44
 */

class Strazak {



	/**
	 * Dane bazy danych
	 * @var
	 */
	private $nr_porz = 0;
	private $id,$jrg_id, $zmiana, $nazwa_funkcji, $previlages, $user_id, $imie, $nazwisko, $stopien, $kolor, $badania;
	private $uprawnienia = array();

	/**
	 * harmonogram Strażaka
	 * @var Harmonogram
	 */
	public $harmonogram;


	/**
	 * Tworzy nowego strazaka
	 * @param $dane
	 * @return Strazak
	 */
	public function create($dane){

		foreach ($this as $key => $value) {
			if(isset($dane[$key])){

				if($key==='uprawnienia'){
					if(empty($dane[$key])){
						$this->$key = array();
					}else if(is_string($dane[$key])){
						$this->$key = unserialize($dane[$key]);
					} else if(is_array($dane[$key])){
						$this->$key = $dane[$key];
					}
				} else {
					$this->$key = $dane[$key];
				}
			}
		}
		return $this;
	}

	public function getStrazakId(){
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getJrgId() {
		return $this->jrg_id;
	}

	/**
	 * @return mixed
	 */
	public function getZmiana() {
		return $this->zmiana;
	}

	/**
	 * @return mixed
	 */
	public function getNazwafunkcji() {
		return $this->nazwa_funkcji;
	}

	/**
	 * @return mixed
	 */
	public function getPrevilages() {
		return $this->previlages;
	}

	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * @return mixed
	 */
	public function getNrPorz() {
		return $this->nr_porz;
	}

	/**
	 * @return mixed
	 */
	public function getImie() {
		return $this->imie;
	}

	/**
	 * @return mixed
	 */
	public function getNazwisko() {
		return $this->nazwisko;
	}

	/**
	 * @return mixed
	 */
	public function getStopien() {
		return $this->stopien;
	}

	/**
	 * @return mixed
	 */
	public function getKolor() {
		return $this->kolor;
	}

	public function getbadaniaData(){
		if((new LocalDateTime($this->badania))->getYear()<0){
			return null;
		}
		return $this->badania;
	}

	public function getBadaniaDayTillNow():int{
		$days = 365;
		if($this->getbadaniaData()!=null){
			$sec = (new LocalDateTime($this->badania))->getTimeTillNow()*-1;
			$days = round($sec / (24*3600),1);
		}
		return $days;
	}

	/**
	 * @return int[]
	 */
	public function getUprawnienia() {

		return $this->uprawnienia;
	}


	/**
	 * @return Harmonogram
	 */
	public function getHarmonogram() {
		return $this->harmonogram;
	}



	/**
	 * @param Harmonogram $harmonogram
	 */
	public function setHarmonogram( Harmonogram $harmonogram ): void {
		$this->harmonogram = $harmonogram;
	}





	public function isChef(){
		return $this->previlages === 'CHEF';
	}

	public function toString(){

		return get_stopien_short($this->getStopien()).' '.$this->getNazwisko().' '.$this->getImie();
	}



	public function printHtml(array $listUsers){
		$star = $this->isChef() ? '<msc class="fa fa-star fa-fw w3-text-amber"></msc>': '';
		if(!empty($this->getUserId())){
			foreach($listUsers as $us){
				if($us->getId()===$this->getUserId()){
					$user =  '<msc class="fa fa-user-circle-o fa-fw" title="Użytkownik: '.$us->getNameEmailIfNull().' (id.'.$us->getId().')"></msc>';
					break;
				}
			}

		} else {

			$user ='<msc class="fa fa-user-times fa-fw" title="Brak przypisanego użytkownika aplikacji."></msc>';
		}
		$funkcja = get_nazwa_funkcji($this->nazwa_funkcji);
		$uprI = '';
			$uprawnienia = $this->uprawnienia;
			foreach ($uprawnienia as $id){
				$uprawnienie =  DBJrgSettings::getUprawnienie($id);
				$uprI .= '<msc class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></msc>';
			}
		echo '<form class="" action="" method="post" ><li class="w3-display-container"><msc class="w3-large">#'.$this->getNrPorz().' </msc> '.$user.$star.' <a href="?editFireman='.$this->id.'">'.get_stopien_short($this->stopien).' '.$this->nazwisko.' '.$this->imie.$uprI.'</a><input type="hidden" value="'.$this->getStrazakId().'" name="strazakId"><button type="submit" class="w3-button w3-border w3-display-right" name="deleteFireman" ><msc class="fa fa-trash"></msc></button></li>
				<span class="w3-text-gray">'.$funkcja.'</span>
				</form>';
	}

	public static function printTableHtml(array $strazacy, array $listUsers){
		$naglowek = '<tr class="w3-small w3-light-gray">
							<th>L.p.</th>
							<th onclick="sortTable($(this).parent().parent(),1)">Stopień</th>
							<th onclick="sortTable($(this).parent().parent(),2)">Nazwisko i imię</th>
							<th onclick="sortTable($(this).parent().parent(),3)">Funkcja</th>
							<th onclick="sortTable($(this).parent().parent(),4)">Uprawnienia</th>
							<th onclick="sortTable($(this).parent().parent(),5)">Badania</th>
							<th>Akcja</th>
						</tr>';
		$wiersze = '';
		$i = 1;

		foreach ($strazacy as $strazak){
			if($strazak instanceof  Strazak){
				$wiersze .= '<tr>';
				$wiersze .= '<td>'.$i++.'</td>';
				$wiersze .= '<td>'.get_stopien_short($strazak->getStopien()).'</td>';
				$wiersze .= '<td>'.$strazak->getUserHtmlInfo($listUsers).'<a href="?editFireman='.$strazak->getStrazakId().'">'.$strazak->getNazwisko().' '.$strazak->getImie().'</a></td>';
				$wiersze .= '<td>'.get_nazwa_funkcji($strazak->getNazwafunkcji()).'</td>';
				$wiersze .= '<td>'.$strazak->getUprawnieniaHtml().'</td>';
				$wiersze .= '<td style="background-color: '.UserSettings::getAlertType($strazak->getBadaniaDayTillNow()).'"><span data-toggle="tooltip" data-placement="top" title="Pozostało: '.$strazak->getBadaniaDayTillNow().' dni">'.$strazak->getBadaniaData().'</span></td>';
				$wiersze .= '<td>
									<div class="w3-row">
										<div class="w3-col s3"><form class="form_str_actions" action="" method="get" ><input type="hidden" value="'.$strazak->getStrazakId().'" name="strazakId"><button class="btn btn-outline-dark btn-sm" type="submit" name="deleteFireman" value="1" data-toggle="tooltip" data-placement="top" title="Usuń strażaka" ><i class="fa fa-trash"></i></button></form></div>
										<div class="w3-col s6"><button type="button" class="btn btn-outline-dark btn-sm" onclick="strazakGoUpDown(this, '.$strazak->getStrazakId().',-1)" data-toggle="tooltip" data-placement="top" title="przesuń wyżej"><i class="fas fa-angle-up"></i></button><button class="btn btn-outline-dark btn-sm" type="button" onclick="strazakGoUpDown(this, '.$strazak->getStrazakId().',1)" data-toggle="tooltip" data-placement="top" title="przesuń niżej"><i class="fas fa-angle-down"></i></button></div>	
									</div></td>';
				$wiersze .= '</tr>';
			}
		}

		echo '<table class="w3-table w3-border">'.$naglowek.$wiersze.'</table>';
	}

	public static function printJrgTableStrazacy(array $zmiany, array $listUsers){
		$naglowek = '<tr class="w3-small w3-light-gray">
							<th >L.p.</th>
							<th onclick="sortTable($(this).parent().parent(),1)" style="cursor:pointer;">Zmiana</th>
							<th onclick="sortTable($(this).parent().parent(),2)" style="cursor:pointer;">Stopień, nazwisko i imię</th>
							<th onclick="sortTable($(this).parent().parent(),3)" style="cursor:pointer;">Funkcja</th>
							<th onclick="sortTable($(this).parent().parent(),4)" style="cursor:pointer;">Uprawnienia</th>
							<th>Badania</th>
							<th>Usuń</th>
						</tr>';
		$wiersze = '';
		$i = 1;
		foreach ($zmiany as $nrZmiany =>$strazacy){
			foreach ($strazacy as $strazak){
				if($strazak instanceof  Strazak){
					$wiersze .= '<tr>';
					$wiersze .= '<td>'.$i++.'</td>';
					$wiersze .= '<td>'.$nrZmiany.'</td>';
					$wiersze .= '<td>'.get_stopien_short($strazak->getStopien()).' '.$strazak->getNazwisko().' '.$strazak->getImie().'</td>';
					$wiersze .= '<td>'.get_nazwa_funkcji($strazak->getNazwafunkcji()).'</td>';
					$wiersze .= '<td>'.$strazak->getUprawnieniaHtml().'</td>';
					$wiersze .= '<td style="background-color: '.UserSettings::getAlertType($strazak->getBadaniaDayTillNow()).'"><span data-toggle="tooltip" data-placement="top" title="Pozostało: '.$strazak->getBadaniaDayTillNow().' dni">'.$strazak->getBadaniaData().'</span></td>';
					$wiersze .= '<td><form class="form_str_actions no-margin" action="" method="get" ><input type="hidden" value="'.$strazak->getStrazakId().'" name="strazakId"><button class="w3-small" type="submit" name="deleteFireman" value="1" data-toggle="tooltip" data-placement="top" title="Usuń strażaka" ><i class="fa fa-trash"></i></button></form></td>';
					$wiersze .= '</tr>';
				}
			}
		}

		echo '<table id="printable" class="w3-table w3-border">'.$naglowek.$wiersze.'</table>';
	}

	private function getUprawnieniaHtml(){
		$uprI = '';
		$uprawnienia = $this->uprawnienia;
		foreach ($uprawnienia as $id){
			$uprawnienie =  DBJrgSettings::getUprawnienie($id);
			if($uprawnienie!=null)
			$uprI .= '<span data-toggle="tooltip" title="'.$uprawnienie->getName().'" data-placement="top" ><i class="fa fa-fw '.$uprawnienie->getIcon().'" style="color: '.$uprawnienie->getColor().'"></i></span>';
		}
		return $uprI;
	}

	public function getUserHtmlInfo(array $userList){
		$user = '';
		if(!empty($this->getUserId())){
			foreach($userList as $us){
				if($us->getId()===$this->getUserId()){
					$user =  '<span data-toggle="tooltip" data-placement="top" title="Użytkownik: '.$us->getNameEmailIfNull().'"><i class="fas fa-user-circle"  ></i></span>';
					break;
				}
			}

		} else {

			$user ='<span data-toggle="tooltip" data-placement="top" title="Brak przypisanego użytkownika aplikacji."><i class="fa fa-user-times" ></i></span>';
		}
		return $user;
	}




}
