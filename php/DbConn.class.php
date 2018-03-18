<?php
// Extend this class to re-use db connection

//TODO:  ZROBIC LOGOWANIE BLEDÓW SQL Z KAZDEGO CATCHA

class DbConn
{
    public $conn;
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
		} catch (Exception $e) {
	        $this->error = $e->getMessage();
			die('Database connection error');
		}
    }

    public function getError(){
    	return $this->error;
    }
}
