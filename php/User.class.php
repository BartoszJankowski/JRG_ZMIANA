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
			return true;
		} catch (PDOException $e){

			echo $this->error = "Error: " . $e->getMessage();
			return false;
		}
	}

	private function genPassword(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
			$randstring .= $characters[rand(0, strlen($characters))];
		}
		return $randstring;
	}
}