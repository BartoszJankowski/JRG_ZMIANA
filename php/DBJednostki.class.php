<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 20:11
 */



class DBJednostki extends DbConn {

	public $tbl_jednostki;

	/**
	 * DBJednostki constructor.
	 *
	 * @param $tbl_jednostki
	 */

	public function __construct() {
		parent::__construct();
	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_jednostki." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	            id_jrg CHAR(10) NOT NULL,
	            city VARCHAR(255) NOT NULL,
	            street CHAR(255) NOT NULL,
	            number CHAR(10) NOT NULL,
	            admin CHAR(255) NOT NULL,
	            CONSTRAINT JRG UNIQUE(id_jrg,city)
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

	public function createJrg($idJrg, $miasto, $ulica, $nr, $adminEmail){


		$adminEmail = filter_var($adminEmail, FILTER_VALIDATE_EMAIL);

		if($adminEmail == false){
			$this->error = "Wprowadzono błedny adres email.";
			return false;
		}

		try{
			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_jednostki." (id_jrg, city, street, number, admin)
    		 VALUES (:id_jrg, :city, :street, :number, :admin)");
			$stmt->bindParam(':id_jrg', $idJrg);
			$stmt->bindParam(':city', $miasto);
			$stmt->bindParam(':street', $ulica);
			$stmt->bindParam(':number', $nr);
			$stmt->bindParam(':admin', $adminEmail);
			$stmt->execute();
			return true;
		} catch (PDOException $e){

			 $this->error = "Error: " . $e->getMessage();
			return false;
		}
	}


}