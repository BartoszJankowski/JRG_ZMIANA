<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 02.02.2018
 * Time: 14:50
 */

class DBJrgSettings extends DbConn {
//TODO: przy usuwaniu uprawnienia przejrzez wszystkich pracowników z tego jrg i tez im wyjebac uprawnienia, elo
	private $tbl_settings;
	private $jrg_id;

	/**
	 * @var Uprawnienie[]
	 */
	private $uprawnieniaList = array();

	public function __construct() {
		parent::__construct();
		$this->tbl_settings = $this->tbl_prefix.'settings';
		$this->createTable();

	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_settings." (
	            jrg_id INT(4) NOT NULL UNIQUE PRIMARY KEY,
	            uprawnieniaList TEXT NOT NULL
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

	public function load($jrg_id){
		$this->jrg_id = $jrg_id;
		//ładuje liste uprawnien dla strażaków JRG [nr]
		$this->selectUprawniania();
	}

	/**
	 * ładuje tablice uprawnien dla jrg
	 */
	private function selectUprawniania(){
		try {
			$stmt =  $this->conn->prepare("SELECT uprawnieniaList FROM ".$this->tbl_settings." WHERE jrg_id = :jrg_id") ;
			$stmt->bindParam(':jrg_id', $this->jrg_id);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				$this->uprawnieniaList = unserialize($result['uprawnieniaList']);
			} else {
				$stmt =  $this->conn->prepare("INSERT INTO ".$this->tbl_settings."
				 (jrg_id, uprawnieniaList ) 
				 VALUES(:jrg_id, :uprawnieniaList)") ;
				$stmt->bindParam(':jrg_id', $this->jrg_id);
				$stmt->bindParam(':uprawnieniaList', serialize($this->uprawnieniaList));
				$stmt->execute();
			}
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();

		}
	}

	/**
	 * @return Uprawnienie[]
	 */
	public function getUprawnienia(){
		return $this->uprawnieniaList;
	}

	/**
	 * @return Uprawnienie
	 */
	public function getUprawnienie(int $id){
		foreach ($this->uprawnieniaList as $upr){
			if($upr->getId() == $id){
				return $upr;
			}
		}
		return null;
	}

	public function addUpr($post) : bool{

		$upr = new Uprawnienie($post['name'],$post['icon'],$post['color']);
		$id = 0;
		//TODO: check if UPRAWNIENIE already not exists
		foreach ($this->uprawnieniaList as $uprawnienie){
			if($uprawnienie->getId() >= $id){
				$id = $uprawnienie->getId() + 1;
			}
			if($uprawnienie->getName() === $upr->getName()){
				$this->error = "Istnieje uprawnienie o takiej samej nazwie.";
				return false;
			}
		}
		$upr->setId($id);
		$this->uprawnieniaList[] = $upr;

		return $this->updateUprawnienia();

	}

	public function deleteUpr(array $listaId):bool{
		$tab = array();
		foreach ($this->uprawnieniaList as $upr){
			$found  = false;
			foreach ($listaId as $id){
				if($upr->getId() == $id){
					$found = true;
				}
			}
			if(!$found){
				$tab[] = $upr;
			}
		}
		$this->uprawnieniaList = $tab;
		return $this->updateUprawnienia();
	}

	private function updateUprawnienia() : bool{
		try {
			$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_settings." SET uprawnieniaList = :uprawnieniaList WHERE  jrg_id = :jrg_id") ;
			$stmt->bindParam(':jrg_id', $this->jrg_id);
			$stmt->bindParam(':uprawnieniaList', serialize($this->uprawnieniaList));
			$stmt->execute();
			return true;
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();
		}
		return false;
	}
}

class Uprawnienie {
	private $icon;
	private $name;
	private $id;
	private $color;

	/**
	 * Uprawnienie constructor.
	 *
	 * @param $icon
	 * @param $name
	 * @param $color
	 */
	public function __construct(  $name, $icon, $color ) {
		$this->icon  = $icon;
		$this->name  = $name;
		$this->color = $color;
	}


	public function printLiElement(){
		echo '<li> <input type="checkbox" class="w3-check" name="deleteUpr[]" value="'.$this->id.'" /> <i class="fa fa-fw '.$this->icon.'" style="color:'.$this->color.'"></i> '.$this->name.'</li>';
	}


	/**
	 * @return mixed
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @param mixed $icon
	 */
	public function setIcon( $icon ): void {
		$this->icon = $icon;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ): void {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId( $id ): void {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getColor() {
		return $this->color;
	}

	/**
	 * @param mixed $color
	 */
	public function setColor( $color ): void {
		$this->color = $color;
	}




}