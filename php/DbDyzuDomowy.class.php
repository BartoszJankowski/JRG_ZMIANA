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
			$sql = "CREATE TABLE ".$this->tbl_dyzurydomowe." (
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

	/**
	 * @param $jrg_id
	 * @param $zmiana
	 * @param $rok
	 * @param $msc
	 *
	 * @return DyzuryDomowe|null
	 */
	public function loadDyzuryNaMsc($jrg_id, $zmiana, $rok, $msc) {

		try {
			$stmt =  $this->conn->prepare("SELECT id, dyzury FROM ".$this->tbl_dyzurydomowe." 
				WHERE jrg_id = :jrg_id AND zmiana = :zmiana AND rok = :rok AND msc = :msc");
			$stmt->bindParam(':jrg_id', $jrg_id);
			$stmt->bindParam(':zmiana', $zmiana);
			$stmt->bindParam(':rok', $rok);
			$stmt->bindParam(':msc', $msc);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if($result){
				return new DyzuryDomowe($result['id'],$zmiana,$rok,$msc,$result['dyzury']);
			} else {
				$this->error = "Podano błedny login lub hasło.";
			}
		} catch (PDOException $e){
			echo $this->error = "Error: " . $e->getMessage();
		}
		return null;
	}

	public function addNewDD(DyzuryDomowe $dyzury_domowe, $jrg_id) : bool {
		try {
			$stmt =  $this->conn->prepare("INSERT INTO ".$this->tbl_dyzurydomowe." (jrg_id, zmiana, rok, msc, dyzury) 
			VALUES(:jrg_id, :zmiana, :rok, :msc, :dyzury)");
			$stmt->bindParam(':jrg_id', $jrg_id);
			$stmt->bindParam(':zmiana', $dyzury_domowe->getZmiana());
			$stmt->bindParam(':rok', $dyzury_domowe->getRok());
			$stmt->bindParam(':msc', $dyzury_domowe->getMsc());
			$stmt->bindParam(':dyzury', serialize($dyzury_domowe->getDyzury()));
			$stmt->execute();
			$dyzury_domowe->setId( $this->conn->lastInsertId() );
			return true;
		} catch (PDOException $e){
			echo $this->error = "Error: " . $e->getMessage();
		}
		return false;
	}


	public function updateDD($jrg_id,$zmiana, DyzuryDomowe $dyzury_domowe) : bool {

		if($dyzury_domowe->getId()!=null){
			try{
				$stmt = $this->conn->prepare("UPDATE ".$this->tbl_dyzurydomowe." SET dyzury = :dyzury WHERE  id = :id AND jrg_id = :jrg_id AND zmiana = :zmiana");
				$stmt->bindParam(':id',$dyzury_domowe->getId() );
				$stmt->bindParam(':jrg_id',$jrg_id );
				$stmt->bindParam(':zmiana',$zmiana );
				$stmt->bindParam(':dyzury',serialize($dyzury_domowe->getDyzury() ) );
				$stmt->execute();
				return true;
			} catch (PDOException $e){
				$this->error = "Error: " . $e->getMessage();
			}
			return false;
		} else {
			$this->error = "Obiekt nie posiada poprawnego ID.";
			return false;
		}
	}
}