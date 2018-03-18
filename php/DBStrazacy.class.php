<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 24.01.2018
 * Time: 13:12
 */

class DBStrazacy extends DbConn {

	private $strazacyZaladowani = array();

	/**
	 * Przy utworzeniu obiektu sprawdza czy obecna tabela istnieje
	 */
	public function __construct() {
		parent::__construct();
		$this->createTable();
	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_strazacy." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	            zmiana TINYINT(1) NOT NULL,
	            jrg_id INT(6) NOT NULL,
	            nazwa_funkcji ENUM('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39') NOT NULL,
	            previlages ENUM('CHEF','USER') NOT NULL,
	            user_id INT(6) UNIQUE,
	            nr_porz INT(6),
	            stopien ENUM('STR','ST_STR','SEKC','ST_SEKC','ML_OGN','OGN','ST_OGN','ML_ASP','ASP','ST_ASP','ASP_SZTAB','ML_KPT','KPT','ST_KPT','ML_BRYG','BRYG','ST_BRYG'),
	            imie CHAR(255),
	            nazwisko CHAR(255),
	            kolor CHAR(10),
	            badania DATE,
	            uprawnienia SET('kierowca','nurek','d-ca zastepu','operator hiab')
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->conn->exec($sql);
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();

			if($e->getCode()==="42S01")
				return;
			else
				$this->logError($e);
		}
	}

	public function dodajStrazaka(Strazak $strazak){


		try {
			$user = null;
			if($strazak->getUserId() != null){
				$dbUsr = new DBUsers();
				$user = $dbUsr->setUserJrgId($strazak->getJrgId(), $strazak->getUserId() );
				if($user==null){
					$this->error = "Zły id zarejestrowanego użytkownika";
					return false;
				}
			}

			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_strazacy."
			 (zmiana, jrg_id, user_id, nazwa_funkcji, previlages, nr_porz, stopien, imie, nazwisko, kolor,badania, uprawnienia)
    		 VALUES (:zmiana, :jrg_id, :user_id, :nazwa_funkcji, :previlages, :nr_porz, :stopien, :imie, :nazwisko, :kolor,:badania, :uprawnienia )");

			$stmt->bindParam(':zmiana', $strazak->getZmiana());
			$stmt->bindParam(':jrg_id', $strazak->getJrgId());
			$stmt->bindParam(':nazwa_funkcji', $strazak->getNazwafunkcji());
			$stmt->bindParam(':previlages', $strazak->getPrevilages());

			//POLA NIEWYMAGANE
			$stmt->bindParam(':user_id', $strazak->getUserId());
			$stmt->bindParam(':nr_porz', $strazak->getNrPorz());
			$stmt->bindParam(':stopien', $strazak->getStopien());

			//pobranie danych z konta uzytkownika jesli administrator nie wprowadził innych danych imienia msc nazwiska
			if($user!=null && empty($strazak->getImie())){
				$stmt->bindParam(':imie', $user->getName());
			} else {
				$stmt->bindParam(':imie', $strazak->getImie());
			}
			if($user!=null && empty($strazak->getImie())){
				$stmt->bindParam(':nazwisko', $user->getSurname());
			} else {
				$stmt->bindParam(':nazwisko', $strazak->getNazwisko());
			}
			$stmt->bindParam(':badania', $strazak->getbadaniaData());
			$stmt->bindParam(':kolor', $strazak->getKolor());
			$stmt->bindParam(':uprawnienia', serialize($strazak->getUprawnienia()));
			$stmt->execute();

			//TODO: send inf email
			return true;
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
			$this->logError($e);
			return false;
		}
	}

	public function edytujStrazaka( Strazak $strazak){

		$oldStrazak = $this->getStrazak($strazak->getStrazakId());
		if($oldStrazak){
			$user = null;
			$dbUsr = new DBUsers();
			$user = $dbUsr->setUserJrgId($oldStrazak->getJrgId(), $strazak->getUserId() );

			$stmt = $this->conn->prepare("UPDATE ".$this->tbl_strazacy." SET
			 user_id = :user_id, 
			 nazwa_funkcji = :nazwa_funkcji, 
			 previlages = :previlages, 
			 nr_porz = :nr_porz, 
			 stopien = :stopien, 
			 imie = :imie, 
			 nazwisko = :nazwisko, 
			 kolor = :kolor, 
			 badania = :badania,
			 uprawnienia = :uprawnienia
    		 WHERE id = :id");
			$userId = empty($user) ? $user: $user->getId();
			$stmt->bindParam(':user_id',$userId );
			$stmt->bindParam(':nazwa_funkcji', $strazak->getNazwafunkcji());
			$stmt->bindParam(':previlages', $strazak->getPrevilages());
			$stmt->bindParam(':nr_porz', $strazak->getNrPorz());
			$stmt->bindParam(':stopien', $strazak->getStopien());
			$stmt->bindParam(':badania', $strazak->getbadaniaData());

			//pobranie danych z konta uzytkownika jesli administrator nie wprowadził innych danych imienia msc nazwiska
			if($user!=null && empty($strazak->getImie())){
				$stmt->bindParam(':imie', $user->getName());
			} else {
				$stmt->bindParam(':imie', $strazak->getImie());
			}
			if($user!=null && empty($strazak->getImie())){
				$stmt->bindParam(':nazwisko', $user->getSurname());
			} else {
				$stmt->bindParam(':nazwisko', $strazak->getNazwisko());
			}
			$stmt->bindParam(':kolor', $strazak->getKolor());
			$stmt->bindParam(':uprawnienia', serialize($strazak->getUprawnienia()));
			$stmt->bindParam(':id', $strazak->getStrazakId() );
			$stmt->execute();


			echo "Poprawnie edytowano strażaka o id:".$strazak->getStrazakId();
		} else {

			echo "Strazak nie odnaleziony w bazie danych.";
			return false;
		}
	}

	public function removeStrazaksUser($userId) : bool{
		try {
			$stmt = $this->conn->prepare("UPDATE  ".$this->tbl_strazacy." SET user_id = NULL WHERE user_id = :user_id");
			$stmt->bindParam(':user_id',$userId);
			$stmt->execute();
			return true;
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return false;
	}

	public function getJRGListStrazacy($jrg_id){

		$tab=array(1=>array(),2=>array(),3=>array());
		try {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->tbl_strazacy." WHERE jrg_id = :jrg_id");
			$stmt->bindParam(':jrg_id',$jrg_id);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				foreach ($result as $dane){
					$tab[$dane['zmiana']][] = (new Strazak())->create($dane);
				}

			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return $tab;
	}

	public function getStrIds() : array {
		$res = array();
		try {
			$stmt = $this->conn->prepare("SELECT id FROM ".$this->tbl_strazacy);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}
		}catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return $res;
	}

	public function getZmianaListStrazacy($nrJrg, $zmiana){
		$tab = array();
		try {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->tbl_strazacy." WHERE jrg_id = :nrjrg AND zmiana = :zmiana");
			$stmt->bindParam(':nrjrg',$nrJrg);
			$stmt->bindParam(':zmiana',$zmiana);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				uasort($result, function ($a, $b){
					return $a['nr_porz'] <=> $b['nr_porz'];
				});
				foreach ($result as $dane){
					$tab[] = (new Strazak())->create($dane);
				}
				$this->strazacyZaladowani = $tab;
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}

		return $tab;
	}

	/**
	 * Zwraca obiekt Strazaka lub False jesli nic nie znajdzie
	 * @param $idStrazaka
	 *
	 * @return bool|Strazak
	 */
	public function getStrazak($idStrazaka){
		if(!empty($this->strazacyZaladowani)){
			foreach ($this->strazacyZaladowani as $strazak){
				if($idStrazaka == $strazak->getStrazakId()){
					return $strazak;
				}
			}
		}
		try{
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->tbl_strazacy." WHERE id = :id");
			$stmt->bindParam(':id',$idStrazaka);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return (new Strazak())->create($result);
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return false;
	}

	public function getStrazakByUserId($user_id){
		try{
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->tbl_strazacy." WHERE user_id = :user_id");
			$stmt->bindParam(':user_id',$user_id);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return (new Strazak())->create($result);
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return false;
	}

	public function deleteFireman(User $user, $firemanId){


		$this->deletedStrazak = $strazak = $this->getStrazak($firemanId);
		if($strazak){
			$allowedToDelete = false;
			if($user->isAdmin()){
				$dbJednostki = new DBJednostki();
				$jednostki = $dbJednostki->getJrgListForAdmin($user);
				foreach($jednostki as $jednostka){
					if($strazak->getJrgId() === $jednostka['id']){
						$allowedToDelete = true;
					}
				}
			} else if($user->isChef()){
				if($user->getJrgId() === $strazak->getJrgId()){
					$allowedToDelete = true;
				}
			}
			if($allowedToDelete){
				try {

					$stmt = $this->conn->prepare("DELETE FROM ".$this->tbl_strazacy." WHERE id = :id");
					$stmt->bindParam(':id',$strazak->getStrazakId());
					$stmt->execute();
					//if(!empty($strazak->getUserId())){
					 //zmienna strazaka USERA jego id jest tylko w tabeli strazaka wiec przy usunieciu user jest wolny
					//}
					$dbHarm = new DBHarmonogramy();
					$dbHarm->deleteHarmonogramyStr($strazak->getJrgId(), $strazak->getStrazakId());
					return true;
				}catch (PDOException $e){
					$this->error = "DB error:".$e->getMessage();$this->logError($e);
					return false;
				}
			} else {
				$this->error = "Not allowed to delete.";
				return false;
			}
		} else{
			$this->error = 'Nie odnaleziono strazaka o podanym ID';
			return false;
		}
	}

	/**
	 * Czysci uprawnienia pracowników- zostawic na przyszłośc na wszelki wypadek
	 * @param $jrgId
	 */

	public function clearFiremansUpr($jrgId){
		try{
			$stmt = $this->conn->prepare("UPDATE ".$this->tbl_strazacy." SET uprawnienia = :uprawnienia WHERE jrg_id = :jrg_id");
			$stmt->bindParam(':jrg_id',$jrgId );
			$stmt->bindParam(':uprawnienia',serialize(array()) );
			$stmt->execute();
		} catch (PDOException $e){
			$this->logError($e);
		}
	}

	public function afterPrevilageDelete($jrgId, $upr_id){
		$tab = array();
		try{
			$stmt = $this->conn->prepare("SELECT id,uprawnienia FROM ".$this->tbl_strazacy." WHERE jrg_id = :jrg_id");
			$stmt->bindParam(':jrg_id',$jrgId );
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $wynik){
				$tab[$wynik['id']] = removeFromArray($upr_id,unserialize($wynik['uprawnienia']));
			}
			$this->conn->beginTransaction();
			foreach ($tab as $idStr=>$uprs){
				$this->conn->exec( "UPDATE ".$this->tbl_strazacy." SET uprawnienia='".serialize($uprs)."' WHERE id=".$idStr.';');
			}
			$this->conn->commit();


		} catch (PDOException $e){
			$this->conn->rollback();
			 $this->logError($e);
		}
	}


	public function changeStrazakNrPorz($strId, int $nrPlusMinus){


		try {
			$stmt = $this->conn->prepare("SELECT nr_porz FROM ".$this->tbl_strazacy." WHERE id = :strId");
			$stmt->bindParam(':strId',$strId );
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				if($result['nr_porz'] === null){
					$Nr_porz = max(0,$nrPlusMinus);
				} else {
					$Nr_porz = max(0, $result['nr_porz']+$nrPlusMinus);
				}
				$stmt = $this->conn->prepare("UPDATE  ".$this->tbl_strazacy." SET nr_porz = ".$Nr_porz." WHERE id = :strId");
				$stmt->bindParam(':strId',$strId );
				$stmt->execute();
				return true;
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();$this->logError($e);
		}
		return false;
	}



}