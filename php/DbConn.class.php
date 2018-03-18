<?php
// Extend this class to re-use db connection


class DbConn
{
    protected $conn;
    protected $tbl_error_log = 'errors';
    public $error = '';

	public $tbl_jednostki;
	public $tbl_users;
	public $tbl_strazacy;
	public $tbl_harmonogramy;
	public $tbl_grafiki;
	public $tbl_rozkazy;




    public function __construct()
    {
    	require 'dbconf.php';
        $this->host = $host; // Host name
        $this->username = $username; // Mysql username
        $this->password = $password; // Mysql password
        $this->db_name = $db_name; // Database name
        $this->tbl_prefix = $tbl_prefix; // Prefix for all database tables
	    $this->tbl_jednostki = $tbl_jednostki;
	    $this->tbl_users = $tbl_users;
	    $this->tbl_strazacy = $tbl_strazacy;
	    $this->tbl_harmonogramy = $tbl_harmonogramy;
	    $this->tbl_grafiki = $tbl_grafiki;
	    $this->tbl_rozkazy = $tbl_rozkazy;
        try {
			// Connect to server and select database.
			$this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $db_name . ';charset=utf8', $username, $password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	        $this->createErrorTable();
		} catch (Exception $e) {
	        $this->error = $e->getMessage();
			die('Database connection error');
		}
    }

	public function createErrorTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_error_log." (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	            czas TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	            msg TEXT NOT NULL,
	            trace TEXT NOT NULL,
	            code CHAR(255)
	            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->conn->exec($sql);
		} catch(PDOException $e) {}
	}

	protected function logError(PDOException $exception): void{
		try{
			$stmt = $this->conn->prepare("INSERT INTO ".$this->tbl_error_log." (msg, trace, code)
    		 VALUES (:msg, :trace, :code)");
			$stmt->bindParam(':msg', $exception->getMessage());
			$stmt->bindParam(':trace', $exception->getTraceAsString());
			$stmt->bindParam(':code',$exception->getCode());
			$stmt->execute();
		} catch (PDOException $ignored){}
	}


    public function getError(){
    	return $this->error;
    }
}
