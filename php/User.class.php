<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 22:28
 */

class User extends DbConn {

	public function __construct() {
		parent::__construct();
		$this->createTable();
	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_users." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	            email CHAR(255) UNIQUE NOT NULL,
	            name CHAR(255),
	            surname CHAR(255),
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

	private function genPassword(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
			$randstring .= $characters[rand(0, strlen($characters))];
		}
		return $randstring;
	}

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

	public function checkSession(){
		if(isset($_SESSION['login'],$_SESSION['id']) && strlen($_SESSION['id'])>0 && strlen($_SESSION['login'])>0){
			try {
				$stmt =  $this->conn->prepare("SELECT session FROM ".$this->tbl_users." WHERE email = :email");
				$stmt->bindParam(':email', $_SESSION['login']);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result && isset($result['session']) && $result['session'] === $_SESSION['id']){
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

	public function destroySession(){
		if(isset($_SESSION['login'],$_SESSION['id']) && strlen($_SESSION['id'])>0 && strlen($_SESSION['login'])>0){
			try {
				$stmt =  $this->conn->prepare("SELECT session FROM ".$this->tbl_users." WHERE email = :email");
				$stmt->bindParam(':email', $_SESSION['login']);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result && isset($result['session']) && $result['session'] === $_SESSION['id']){
					$this->setSession($_SESSION['login'], null);
				}
			} catch (\PDOException $ignored){}
		}
		session_destroy();
	}

	public function changePass($oldPass, $newpass, $newpas2){

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
			$stmt->bindParam(':email', $_SESSION['login']);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result && password_verify($oldPass, $result['password'])){
				$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_users." SET password = :pass WHERE email = :email");
				$stmt->bindParam(':pass', password_hash ($newpass,PASSWORD_DEFAULT) );
				$stmt->bindParam(':email', $_SESSION['login'] );
				$stmt->execute();
				$emails = new Emails();
				$emails->sendPasswordChangeInformation($_SESSION['login']);
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




}