<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 31.01.2018
 * Time: 22:10
 */

class DbDyzuDomowy extends DbConn {

	private $tbl_dyzurydomowe;

	public function __construct() {
		parent::__construct();
		$this->tbl_dyzurydomowe = $this->tbl_prefix.'homeduties';
		$this->createTable();
	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_rozkazy." (
	            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	            jrg_id INT(6) NOT NULL,
	            zmiana INT(2) NOT NULL,
	            rok INT(4) NOT NULL,
	            msc INT(2) NOT NULL,
	            dyzury TEXT,
	            CONSTRAINT DD UNIQUE(jrg_id,zmiana,rok,msc)
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


	public function selectDyzuryNaMsc($jrg_id, $zmiana, $rok, $msc){

		try {
			$stmt =  $this->conn->prepare("SELECT id, dyzury FROM ".$this->tbl_users." 
				WHERE jrg_id = :jrg_id AND zmiana = :zmiana AND rok = :rok AND msc = :msc");
			$stmt->bindParam(':jrg_id', $jrg_id);
			$stmt->bindParam(':zmiana', $zmiana);
			$stmt->bindParam(':rok', $rok);
			$stmt->bindParam(':msc', $msc);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if($result){

			} else {
				$this->error = "Podano błedny login lub hasło.";
			}
		} catch (PDOException $e){
			echo $this->error = "Error: " . $e->getMessage();
		}

	}
}