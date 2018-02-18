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
	private $id,$jrg_id, $zmiana, $nazwa_funkcji, $previlages, $user_id, $nr_porz, $imie, $nazwisko, $stopien, $kolor;
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

}
