<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 16:50
 */

class DBRozkazy extends DbConn {

	private $tbl_szablony_rozkazu;

	public function __construct() {
		parent::__construct();
		$this->tbl_szablony_rozkazu = $this->tbl_prefix.'ordertemplates';
		$this->createTableOrders();
		$this->createTableTemplates();
	}

	public function createTableOrders(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_rozkazy." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	            jrg_id INT(6) NOT NULL,
	            data DATE NOT NULL,
	            rozkaz TEXT,
	            nr_rozkazu INT (3) NOT NULL
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->conn->exec($sql);
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();

			if($e->getCode()==="42S01")
				return;
			else
				echo $sql . "<br>" . $e->getMessage();
		}
	}

	public function createTableTemplates(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_szablony_rozkazu." (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	            jrg_id INT(6) NOT NULL,
	            szablon TEXT,
	            finished INT(1) NOT NULL,
	            dataSzablonu DATE
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->conn->exec($sql);
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();

			if($e->getCode()==="42S01")
				return;
			else
				echo $sql . "<br>" . $e->getMessage();
		}
	}

	public function FselectFinSzablony($jrg_id){

			try {
				$stmt = $this->conn->prepare("SELECT id, szablon, dataSzablonu FROM ".$this->tbl_szablony_rozkazu." WHERE jrg_id = :jrg_id AND finished = 1");
				$stmt->bindParam(':jrg_id',$jrg_id);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if($result){
					return $result;
				}
			} catch (PDOException $e){
				$this->error = "DB error:".$e->getMessage();
			}
			return false;
	}

	public function utworzSzablon(User $user, Szablon $szablon) : bool {

		try {
			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_szablony_rozkazu." (jrg_id,dataSzablonu, szablon, finished)
			VALUES (:jrg_id, :dataSzablonu, :szablon, 0)");
			$stmt->bindParam(':jrg_id',$user->getJrgId() );
			$stmt->bindParam(':dataSzablonu',$szablon->getDataSzablonu() );
			$stmt->bindParam(':szablon',serialize($szablon->getObiektyHtml()) );
			$stmt->execute();
			$szablon->setId( $this->conn->lastInsertId() );
			return true;
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return false;
	}

	public function getSzablon($jrg_id, $idSzablonu, Szablon $szablon){
		try {
			$stmt = $this->conn->prepare("SELECT szablon, dataSzablonu, finished FROM ".$this->tbl_szablony_rozkazu." WHERE jrg_id = :jrg_id AND id = :id");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->bindParam(':id',$idSzablonu);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				$szablon->setId($idSzablonu);
				$szablon->setDataSzablonu($result['dataSzablonu']);
				$szablon->setObiektyHtml($result['szablon']);
				$szablon->setFinished($result['finished']);
				return true;
			} else {
				$this->error = "Brak wybranego szablonu w bazie danych.";
			}
		}catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return false;
	}
}