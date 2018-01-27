<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 20:31
 */

class DbGrafiki extends DbConn {

	public function __construct() {
		parent::__construct();
		$this->createTable();
	}

	public function createTable() {
		try {
			$sql = "CREATE TABLE ".$this->tbl_grafiki." (
				jrg_id INT(6) NOT NULL,
	            rok INT(4) NOT NULL,
	            msc INT(2) NOT NULL,
	            CONSTRAINT GRAF UNIQUE(rok,msc)
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

}