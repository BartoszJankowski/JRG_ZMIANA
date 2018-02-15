<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 24.01.2018
 * Time: 11:30
 */

class User {
	//TODO: zmniejszyć wartośc przy deploymencie
	private const SESSION_TIME = 18000 ; // 1800 sekund czyli 30 minut

	public $logged = false;
	public $sessionSet = false;
	public $login;
	private $session;
	/**
	 * Strazak class
	 */
	public $strazak;



	private $id, $email, $name, $surname, $jrg_id, $dateTime, $previlages;

	/**
	 * User constructor.
	 *
	 * @param $id
	 * @param $email
	 * @param $name
	 * @param $surname
	 * @param $jrg_id
	 * @param $session
	 * @param $dateTime
	 * @param $previlages
	 */
	public function __construct($tab = null) {
		if($tab!=null){
			$this->id         = $tab['id'];
			$this->email      = $tab['email'];
			$this->name       = $tab['name'];
			$this->surname    = $tab['surname'];
			$this->jrg_id     = $tab['jrg_id'];
			$this->dateTime   = $tab['datatime'];
			$this->previlages = $tab['previlages'];
		} elseif(isset($_SESSION['login'],$_SESSION['session']) && strlen($_SESSION['session'])>0 && strlen($_SESSION['login'])>0){
			$this->login = $_SESSION['login'];
			$this->session = $_SESSION['session'];
			$this->sessionSet = true;
		}
	}

	public function setuserData($tab ) {
			$this->id         = $tab['id'];
			$this->email      = $tab['email'];
			$this->name       = $tab['name'];
			$this->surname    = $tab['surname'];
			$this->jrg_id     = $tab['jrg_id'];
			$this->dateTime   = $tab['datatime'];
			$this->previlages = $tab['previlages'];
			$dbStr = new DBStrazacy();
			$strazak = $dbStr->getStrazakByUserId($this->id);
			if($strazak){
				$this->strazak = $strazak;
				if(!$this->isAdmin() && $strazak->isChef()){
					$this->previlages = "CHEF";
				}
			}

	}

	public function checkSessionTime() : bool {
		$ltd =new LocalDateTime($this->dateTime);
		return (!($this->isChef() && $ltd->getTimeTillNow() > self::SESSION_TIME ));
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * @param mixed $session
	 */
	public function setSession( $session ): void {
		$this->session = $session;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSurname() {
		return $this->surname;
	}

	/**
	 * @return int
	 */
	public function getJrgId() {
		return $this->jrg_id;
	}



	/**
	 * @return time
	 */
	public function getDateTime() {
		return $this->dateTime;
	}

	/**
	 * @return string|ENUM
	 */
	public function getPrevilages() {
		return $this->previlages;
	}

	/**
	 * @return bool
	 */
	public function isAdmin(){
		return  $this->previlages === 'ADMIN';
	}

	/**
	 * @return bool
	 */
	public function isChef(){
		return(!empty($this->strazak) && $this->strazak->isChef());
	}

	public function getNameEmailIfNull(){
		return strlen($this->name) >0 ? $this->name.' '.$this->getSurname() : $this->email;
	}

	public function printUserHtml(){
		$name = strlen($this->getName())>0 ? $this->getName().' '.$this->getSurname() : $this->getEmail();

		echo '<div class="w3-third w3-border w3-margin"><span class="w3-text-gray">Strażak: </span>'.$name.'<br><span class="w3-text-gray">status: </span>'.$this->getPrevilages().'</div>';
	}

	/**
	 * @return Strazak
	 */
	public function getStrazak() {
		return $this->strazak;
	}


}