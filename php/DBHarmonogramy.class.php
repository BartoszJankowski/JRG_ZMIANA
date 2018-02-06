<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 10:29
 */

class DBHarmonogramy extends DbConn {


	public function __construct() {
		parent::__construct();
		$this->createTable();
	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_harmonogramy." (
				jrg_id INT(6) NOT NULL,
	            strazak_id CHAR(10) NOT NULL,
	            rok INT(4) NOT NULL,
	            harmo TEXT NOT NULL,
	            CONSTRAINT H UNIQUE(strazak_id,rok)
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

	public function getJrgharmos($jrg_id, $rok){
		$tab = array();
		try {
			$stmt = $this->conn->prepare("SELECT strazak_id, harmo FROM ".$this->tbl_harmonogramy." WHERE jrg_id = :jrg_id AND rok = :rok");
			$stmt->bindParam(':jrg_id',$jrg_id );
			$stmt->bindParam(':rok',$rok);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				foreach ($result as $res){
					$tab[$res['strazak_id']] = unserialize($res['harmo']);// new Harmonogram($rok, );
				}
				//return new Harmonogram($rok, $result);
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		return $tab;
	}

	/**
	 * @param Strazak $strazak
	 * @param $rok
	 *
	 * @return Harmonogram
	 */
	public function getHarmo(Strazak $strazak, $rok) {
		try {
			$stmt = $this->conn->prepare("SELECT harmo FROM ".$this->tbl_harmonogramy." WHERE strazak_id = :str_id AND rok = :rok");
			$stmt->bindParam(':str_id',$strazak->getStrazakId());
			$stmt->bindParam(':rok',$rok);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return unserialize($result['harmo']);
			}
		} catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
		}
		$harmo =  new Harmonogram($rok);
		$harmo->genHarmoForStrazak($strazak);
		return $harmo;
	}

	public function saveHarmos($jrgId, $rok, array $harmos  ){

		try{
			foreach ($harmos as $strId=>$tab){
				if(!($tab['harmonogram'] instanceof Harmonogram)){
					continue;
				}
				if($tab['exists']){
					$stmt = $this->conn->prepare("UPDATE ".$this->tbl_harmonogramy." SET harmo = :harmo WHERE strazak_id = :str_id AND rok = :rok");
					$stmt->bindParam(':harmo',serialize($tab['harmonogram']) );
					$stmt->bindParam(':str_id',$strId);
					$stmt->bindParam(':rok',$rok);

				} else {
					$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_harmonogramy." (jrg_id, strazak_id, rok, harmo ) 
					VALUES(:jrg_id, :str_id, :rok, :harmo)");
					$stmt->bindParam(':jrg_id',$jrgId);
					$stmt->bindParam(':str_id',$strId);
					$stmt->bindParam(':rok',$rok);
					$stmt->bindParam(':harmo',serialize($tab['harmonogram']) );

				}
				$stmt->execute();
			}
		}
		catch (PDOException $e){
			$this->error = "DB error:".$e->getMessage();
			echo $this->error;
		}
	}

}