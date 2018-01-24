<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 24.01.2018
 * Time: 11:30
 */

class User {

	public $logged = false;
	public $sessionSet = false;
	public $login;
	public $sesId;


	private $id, $email, $name, $surname, $jrg_id, $session, $dateTime, $previlages;

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
			$this->session    = $tab['session'];
			$this->dateTime   = $tab['datatime'];
			$this->previlages = $tab['previlages'];
		} elseif(isset($_SESSION['login'],$_SESSION['id']) && strlen($_SESSION['id'])>0 && strlen($_SESSION['login'])>0){
			$this->login = $_SESSION['login'];
			$this->sesId = $_SESSION['id'];
			$this->sessionSet = true;
		}
	}

	public function setuserData($tab ) {
			$this->id         = $tab['id'];
			$this->email      = $tab['email'];
			$this->name       = $tab['name'];
			$this->surname    = $tab['surname'];
			$this->jrg_id     = $tab['jrg_id'];
			$this->session    = $tab['session'];
			$this->dateTime   = $tab['datatime'];
			$this->previlages = $tab['previlages'];
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	 * @return string
	 */
	public function getSession() {
		return $this->session;
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
		return  $this->previlages === 'CHEF';
	}

	public function printUserHtml(){

	}


}