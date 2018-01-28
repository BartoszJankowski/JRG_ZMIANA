<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 22:28
 */

class DBUsers extends DbConn {

	private $dane = array();

	/**
	 * Przy utworzeniu obiektu sprawdza czy obecna tabela istnieje
	 */
	public function __construct() {
		parent::__construct();
		$this->createTable();
	}

	/**
	 * Tworzy tabelę USERS w bazie danych
	 */
	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_users." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	            email CHAR(255) UNIQUE NOT NULL,
	            name CHAR(255),
	            surname CHAR(255),
	            jrg_id INT(6),
	            password CHAR(255) NOT NULL,
	            session CHAR(255),
	            datatime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	            previlages ENUM('SUPERADMIN','ADMIN','CHEF','USER') NOT NULL
	            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->conn->exec($sql);
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();
			if($e->getCode()==="42S01")
				return;
			else
				echo $sql . "<br>" . $e->getCode();
		}
	}

	/**
	 * Tworzy nowa jednotkę JRG
	 * - uprzednio sprawdza czy taka juz nie została utworzona (para kluczy nr jrg + miasto)
	 * tworzy administratora JRG jesli jeszcze taki nie istnieje
	 */
	public function createJrgAdmin($email){
		try{
			$this->pass = $this->genPassword();
			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_users." (email, password, previlages)
    		 VALUES (:email, :password, 'ADMIN')");

			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':password', password_hash ($this->pass,PASSWORD_DEFAULT));
			$stmt->execute();
			$Emails = new Emails();
			$Emails->sendConfirmationEmail($email, $this->pass);
			return true;
		} catch (PDOException $e){

			echo $this->error = "Error: " . $e->getMessage();
			return false;
		}
	}

	public function login($email, $password){

		$mail = filter_var($email, FILTER_VALIDATE_EMAIL);

		if($mail == false){
			$this->error = "Wprowadzono błedny adres email (".$email.").";
			return false;
		}

		if(strlen($password) < 8){
			$this->error = "Wprowadzone hasło jest za krótkie.";
			return false;
		}

		try {
			$stmt =  $this->conn->prepare("SELECT password FROM ".$this->tbl_users." WHERE email = :email");
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if($result && isset($result['password'])){
				if(password_verify($password, $result['password'])){
					$this->setSession($email, sha1($this->genPassword().microtime()) );
					return true;
				} else {
					$this->error = "Podano błedny login lub hasło.";
				}
			} else {
				$this->error = "Podano błedny login lub hasło.";
			}
		} catch (PDOException $e){
			echo $this->error = "Error: " . $e->getMessage();
		}
		return false;
	}

	/**
	 * Generuje randomowy ciąg 10 znaków
	 * @return string
	 */
	private function genPassword(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
			$randstring .= $characters[rand(0, strlen($characters))];
		}
		return $randstring;
	}

	/**
	 * Tworzy zmienne sesyjne
	 * oraz ID sesji które zapisuje w bazie danych uzytkownika
	 * @void
	 */
	private function setSession($email, $hash){
		try{
			//$random = sha1($this->genPassword().microtime());
			$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET session = :sesId WHERE email = :email");
			$stmt->bindParam(':sesId', $hash);
			$stmt->bindParam(':email', $email);
			$stmt->execute();

			$_SESSION['login'] = $email;
			$_SESSION['id'] = $hash;
		} catch (PDOException $e){
			$this->error = "Error: " . $e->getMessage();
		}
	}

	/**
	 * Sprawdza zmienne sesyjne w bazie danych
	 * oraz sprawdza czy nie są puste
	 * @return bool
	 */
	public function checkSession(User $user){
		if($user->sessionSet){
			try {
				$stmt =  $this->conn->prepare("SELECT id,email,session,name,surname,jrg_id,previlages FROM ".$this->tbl_users." WHERE email = :email");
				$stmt->bindParam(':email', $user->login);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result && isset($result['session']) && $result['session'] === $user->sesId){
					$user->setuserData($result);
					$user->logged = true;
					return true;
				}
				$this->error = "Błedna sesja lub brak sesji.";
			} catch (PDOException $e){
				$this->error = "Error: " . $e->getMessage();
			}
		} else {
			$this->error = "Błedna sesja.";
		}
		return false;
	}

	/**
	 * Niszczy sesję
	 * oraz próbuje usunąc zmienną sesyjną w bazie danych uzytkownika
	 */
	public function destroySession(User $user){
		if($user->sessionSet){
			try {
				$stmt =  $this->conn->prepare("SELECT session FROM ".$this->tbl_users." WHERE email = :email");
				$stmt->bindParam(':email', $user->login);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result && isset($result['session']) && $result['session'] === $user->sesId){
					$this->setSession($user->login, null);
				}
			} catch (\PDOException $ignored){}
		}
		session_destroy();
	}

	/**
	 * Zmienia stare hasło uzytkownika na nowe
	 * Przy zmianie hasła uzytkownika sprawdza stare hasło, porównuje dwa nowe czy są zgodne i nadpisuje stare hasło
	 * powiadamia uzytkownika emialem o zmianie hasła
	 * @return bool
	 */
	public function changePass(User $user, $oldPass, $newpass, $newpas2){

		if(strlen($oldPass) <8 || strlen($newpass)<8){
			$this->error = "Wprowadzone hasło jest zbyt krótkie. Min. 8 znaków.";
			return false;
		}
		if($newpass!== $newpas2){
			$this->error = "Błędnie potwierdzone hasło. Wprowadx dane ponownie.";
			return false;
		}

		try {
			$stmt =  $this->conn->prepare("SELECT password FROM ".$this->tbl_users." WHERE email = :email");
			$stmt->bindParam(':email', $user->login);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result && password_verify($oldPass, $result['password'])){
				$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET password = :pass WHERE email = :email");
				$stmt->bindParam(':pass', password_hash ($newpass,PASSWORD_DEFAULT) );
				$stmt->bindParam(':email', $user->login );
				$stmt->execute();
				$emails = new Emails();
				$emails->sendPasswordChangeInformation($user->login);
				return true;
			} else {
				$this->error = "Podano błędne stare hasło.";
				return false;
			}
		} catch (PDOException $e){
			$this->error = "Błąd bazy danych: " . $e->getMessage();
			return false;
		}
	}

	/**
	 * Resetuje hasło uzytkownika
	 * tworzy nowe randomowe hasło
	 * wysyła nowe hasło uzytkownikowi emailem
	 * @return bool
	 */
	public function resetPass($email){
		$mail = filter_var($email, FILTER_VALIDATE_EMAIL);

		if($mail == false){
			$this->error = "Wprowadzono błedny adres email (".$email.").";
			return false;
		}
		$newPassword = $this->genPassword();

		try {
			$stmt =  $this->conn->prepare("SELECT id FROM ".$this->tbl_users." WHERE email = :email");
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result && isset($result['id'])){
				$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET password = :pass WHERE email = :email");
				$stmt->bindParam(':pass', password_hash($newPassword,PASSWORD_DEFAULT ));
				$stmt->bindParam(':email', $email);
				$stmt->execute();
				$Emails = new Emails();
				$Emails->sendResetPassword($email, $newPassword);
				return true;
			} else {
				$this->error = "Brak użytkownika w bazie danych." ;
				return false;
			}
		} catch (PDOException $e){
			echo $this->error = "Error: " . $e->getMessage();
		}
		return false;
	}

	/**
	 *  Rejestracja nowego użytkownika
	 * @param $email
	 * @param $pass
	 * @param $pass_conf
	 * @param null $nr_jrg
	 * @param null $imie
	 * @param null $nazwisko
	 *
	 * @return bool
	 */
	public function registerNewUser($email, $pass, $pass_conf, $nr_jrg = null, $imie = null, $nazwisko = null ){
		$mail = filter_var($email, FILTER_VALIDATE_EMAIL);

		if($mail == false){
			$this->error = "Wprowadzono błedny adres email (".$email.").";
			return false;
		}

		if(strlen($pass)<8){
			$this->error = "Wprowadzone hasło jest zbyt krótkie.";
			return false;
		}

		if($pass !== $pass_conf){
			$this->error = "Wprowadzone hasła są różne.";
			return false;
		}

		//check jrg
		$dbJednostki = new DBJednostki();
		if(strlen($nr_jrg)>0 && !array_key_exists($nr_jrg,$dbJednostki->getJrgList())){
			$this->error = "Wybrana jednostka nie istnieje.";
			return false;
		}

		try {
			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_users." (email, password, previlages, jrg_id, name, surname)
    		 VALUES (:email, :password, 'USER', :jrg_id, :name, :surname )");

			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':password', password_hash ($pass,PASSWORD_DEFAULT));
			$stmt->bindParam(':jrg_id', $nr_jrg);
			$stmt->bindParam(':name', $imie);
			$stmt->bindParam(':surname', $nazwisko);
			$stmt->execute();

			$Emails = new Emails();
			$Emails->sendConfirmationEmail($email, $pass);
			return true;
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
			return false;
		}

	}

	public function changeUserData(User $user,$imie, $nazwisko){
		try {
				$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET name = :name, surname = :surname WHERE email = :email");
				$stmt->bindParam(':name', $imie );
				$stmt->bindParam(':surname', $nazwisko );
				$stmt->bindParam(':email', $user->login );
				$stmt->execute();
				$this->dane['name'] = $imie;
				$this->dane['surname'] = $nazwisko;
				return true;
		} catch (PDOException $e){
			$this->error = "Błąd bazy danych: " . $e->getMessage();
			return false;
		}
	}


	/**
	 * Pobiera liste uzytkowników dla JRG
	 * @param $idJrg
	 *
	 * @return array(User)
	 */
	public function getUsersList(User $userAdmin, $idJrg){
		$arr = array();
		try{
			$stmt =  $this->conn->prepare("SELECT * FROM ".$this->tbl_users." WHERE jrg_id = :id OR jrg_id IS NULL");
			$stmt->bindParam(':id', $idJrg);
			//$stmt->bindParam(':admin_id', $userAdmin->getId());
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($stmt->rowCount()>0){
				for($i =0 ; $i<$stmt->rowCount(); $i++){
					$arr[] = new User($result[$i]);
				}
			}
		} catch (PDOException $e){
			//TODO: log error
			$this->error = $e->getMessage();
		}
		return $arr;
	}

	public function getFreeUserList(User $userAdmin,$idJrg){
		$users = $this->getUsersList($userAdmin, $idJrg);
		$result =  array();
		if(count($users)>0) {
			$dbStrazacy   = new DBStrazacy();
			$strazacyList = $dbStrazacy->getJRGListStrazacy( $idJrg );
			foreach ( $users as $user ) {
				$delete = false;
				foreach ( $strazacyList as $zmiana ) {
					foreach ( $zmiana as $Strazak ) {
						if($Strazak->getUserid() === $user->getId()){
							$delete = true;
							break 2;
						}
					}
				}
				if(!$delete){
					$result[] = $user;
				}
			}
		}
		return $result;
	}

	public function getUserById($id_user){
		try{
			$stmt =  $this->conn->prepare("SELECT * FROM ".$this->tbl_users." WHERE id = :id");
			$stmt->bindParam(':id', $id_user);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return new User($result);
			}
		} catch (PDOException $e){
			//TODO: log error
			$this->error = $e->getMessage();
		}
		return null;
	}

	/**
	 * @param $idJrg
	 * @param $userId
	 *
	 * @return bool
	 */
	public function setUserJrgId($idJrg, $userId){
		$user = $this->getUserById($userId);
		if(!empty($user)){
			if(empty($user->getJrgId())){
				try{
					$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET jrg_id = :idjrg WHERE id = :id");
					$stmt->bindParam(':idjrg', $idJrg);
					$stmt->bindParam(':id', $userId);
					$stmt->execute();
					return $user;
				} catch (PDOException $e){
					//TODO: log error
					$this->error = $e->getMessage();
				}
			} elseif($user->getJrgId() === $idJrg) {
				return $user;
			}
		}
		return null;
	}


	public function deleteAccount(User $user, string $pasword) : bool{
		$dbStr = new DBStrazacy();

		if($dbStr->removeStrazaksUser($user->getId())){
			try{
				$stmt =  $this->conn->prepare("SELECT password FROM ".$this->tbl_users." WHERE id = :id");
				$stmt->bindParam(':id', $user->getId());
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result){
					if(!password_verify($pasword, $result['password'])){
						$this->error = 'Błędne hasło';
						return false;
					}
				} else {
					$this->error = 'Błąd pozyskiwania danych';
					return false;
				}
				$stmt =  $this->conn->prepare("DELETE FROM ".$this->tbl_users." WHERE id = :id");
				$stmt->bindParam(':id', $user->getId() );
				$stmt->execute();
				$this->destroySession($user);
				return true;
			} catch (PDOException $e){
				$this->error = 'Error#1: '.$e->getMessage();
				return false;
			}
		} else {
			$this->error = 'Error#2: '.$dbStr->error;
			return false;
		}
	}




}