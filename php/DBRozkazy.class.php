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
	            szablon_id INT(6) NOT NULL,
	            rozkaz TEXT NOT NULL,
	            edit_bool CHAR(4),
	            edit_user INT(6),
	            edit_time TIMESTAMP,
	            CONSTRAINT ROZKAZ_ID UNIQUE(jrg_id,data)
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

	public function selectCurrentOrderTemplate($jrg_id){

			try {
				$stmt = $this->conn->prepare("SELECT id, szablon, dataSzablonu FROM ".$this->tbl_szablony_rozkazu." WHERE jrg_id = :jrg_id AND finished = 1");
				$stmt->bindParam(':jrg_id',$jrg_id);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
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

	public function getSzablony($jrg_id) : array {
		$res = array();
		try {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->tbl_szablony_rozkazu." WHERE jrg_id = :jrg_id");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				$stmt = $this->conn->prepare("SELECT szablon_id FROM ".$this->tbl_rozkazy." WHERE jrg_id = :jrg_id");
				$stmt->bindParam(':jrg_id',$jrg_id);
				$stmt->execute();
				$ids = $stmt->fetchAll(PDO::FETCH_ASSOC);

				foreach ($result as $szbl) {
					$num = 0;
					if($ids){
						foreach ($ids as $id){
							if($id['szalobn_id'] == $szbl['id']){
								$num++;
							}
						}
					}
					$szablon = new Szablon($jrg_id);
					$szablon->setId($szbl['id']);
					$szablon->setDataSzablonu($szbl['dataSzablonu']);
					$szablon->setObiektyHtml($szbl['szablon']);
					$szablon->setFinished($szbl['finished']);
					$szablon->setNumOfCreatedOrders($num);
					$res[] = $szablon;
				}
			} else {
				$this->error = "Brak wybranego szablonu w bazie danych.";
			}
		}catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return $res;
	}

	public function saveSzablon($jrg_id, Szablon $szablon) : bool{
		try {
			$stmt = $this->conn->prepare("UPDATE ".$this->tbl_szablony_rozkazu." SET szablon = :szablon, finished = :finished WHERE jrg_id = :jrg_id AND id = :id");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->bindParam(':id',$szablon->getId());
			$stmt->bindParam(':finished',$szablon->getFinished());
			$stmt->bindParam(':szablon',serialize($szablon->getObiektyHtml()));
			$stmt->execute();
			return true;
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return false;
	}

	public function selectRozkaz( $jrg_id, LocalDateTime $date_time ) {
		$data = $date_time->getMySqlDate();
		try {
			$stmt = $this->conn->prepare("SELECT id,rozkaz FROM ".$this->tbl_rozkazy." WHERE jrg_id=:jrg_id AND data=:data");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->bindParam(':data',$data);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result) {
				$rozkaz = new Rozkaz();
				$rozkaz->setDane($result['id'],$jrg_id, $date_time, $result['rozkaz']);
				//print_r((unserialize($result['rozkaz']))['id'] );
				return $rozkaz;
			}
		} catch (PDOException $e){
			$this->error = $e;
		}
		return false;
	}

	public function saveRozkaz($jrg_id, Rozkaz $rozkaz) : bool{
		//TODO: zamknąc edycję po poprawnym zapisie rozkazu
		try{
			if($rozkaz->getRozkazId()<0){
				//dodać nowy rozkaz
				$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_rozkazy." (jrg_id,data,szablon_id, rozkaz)
						VALUES (:jrg_id, :data,:szablon_id, :rozkaz)");
				$stmt->bindParam(':data',$rozkaz->getDate() );
			} else {
				$stmt = $this->conn->prepare("UPDATE ".$this->tbl_rozkazy." SET rozkaz = :rozkaz,szablon_id = :szablon_id WHERE jrg_id=:jrg_id AND id=:id");
				$stmt->bindParam(':id',$rozkaz->getRozkazId() );
			}
			$stmt->bindParam(':jrg_id',$jrg_id );
			$stmt->bindParam(':szablon_id',$rozkaz->getSzablonId() );
			$stmt->bindParam(':rozkaz',serialize($rozkaz->getDaneRozkazu()) );
			$stmt->execute();
			return true;
		} catch (PDOException $e){
			$this->error = $e;
		}
		return false;
	}

	public function hasActiveTemplate($jrg_id) : bool {
		try {
			$stmt = $this->conn->prepare("SELECT id FROM ".$this->tbl_szablony_rozkazu." WHERE jrg_id = :jrg_id AND finished = 1");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return true;
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return false;
	}


}