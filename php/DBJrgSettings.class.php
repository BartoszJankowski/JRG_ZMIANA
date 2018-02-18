<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 02.02.2018
 * Time: 14:50
 */

class DBJrgSettings extends DbConn {
//TODO: przy usuwaniu uprawnienia przejrzez wszystkich pracowników z tego jrg msc tez im wyjebac uprawnienia, elo
	private $tbl_settings;
	private static $jrg_id;

	private static $loaded = false;

	/**
	 * @var Uprawnienie[]
	 */
	private static $uprawnieniaList = array();

	/**
	 * @var GrafikValue[]
	 */
	private static $grafikValues = array();

	/**
	 * @var HarmoValue[]
	 */
	private static $harmoValues = array();

	public function __construct() {
		parent::__construct();
		$this->tbl_settings = $this->tbl_prefix.'settings';
		$this->createTable();

	}

	public function createTable(){
		try {
			$sql = "CREATE TABLE ".$this->tbl_settings." (
	            jrg_id INT(4) NOT NULL UNIQUE PRIMARY KEY,
	            uprawnieniaList TEXT NOT NULL,
	            grafikValues TEXT,
	            harmoValues TEXT
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
		self::$jrg_id = $jrg_id;
		//ładuje liste uprawnien dla strażaków JRG [nr]
		try {
			$stmt =  $this->conn->prepare("SELECT uprawnieniaList,grafikValues,harmoValues FROM ".$this->tbl_settings." WHERE jrg_id = :jrg_id") ;
			$stmt->bindParam(':jrg_id', self::$jrg_id);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				self::$uprawnieniaList = unserialize($result['uprawnieniaList']);
				self::$grafikValues = unserialize($result['grafikValues']);
				self::$harmoValues = unserialize($result['harmoValues']);
			} else {
				$stmt =  $this->conn->prepare("INSERT INTO ".$this->tbl_settings."
				 (jrg_id, uprawnieniaList,grafikValues,harmoValues ) 
				 VALUES(:jrg_id, :uprawnieniaList, :grafikValues,:harmoValues)") ;
				$stmt->bindParam(':jrg_id', self::$jrg_id);
				$stmt->bindParam(':uprawnieniaList', serialize(self::$uprawnieniaList));
				$stmt->bindParam(':grafikValues', serialize(self::$grafikValues));
				$stmt->bindParam(':harmoValues', serialize(self::$harmoValues));
				$stmt->execute();
			}
			self::$loaded = true;
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();

		}
	}


	/**
	 * @return Uprawnienie[]
	 */
	public static function getUprawnienia(){
		if(!self::$loaded){
			throw new Error('Próba pobrania danych ustawień przy nie załadowanych danych.');
		}
		return self::$uprawnieniaList;
	}

	/**
	 * @return GrafikValue[]
	 */
	public static function getGrafValues(){
		if(!self::$loaded){
			throw new Error('Próba pobrania danych ustawień przy nie załadowanych danych.');
		}
		return self::$grafikValues;
	}
	/**
	 * @return HarmoValue[]
	 */
	public static function getHarmoValues(){
		if(!self::$loaded){
			throw new Error('Próba pobrania danych ustawień przy nie załadowanych danych.');
		}
		return self::$harmoValues;
	}
	/**
	 * @return Uprawnienie
	 */
	public static function getUprawnienie(int $id){
		if(!self::$loaded){
			throw new Error('Próba pobrania danych ustawień przy nie załadowanych danych.');
		}
		foreach (self::$uprawnieniaList as $upr){
			if($upr->getId() == $id){
				return $upr;
			}
		}
		return null;
	}
	public function addUpr($post) : bool{

		$upr = new Uprawnienie($post['name'],$post['icon'],$post['color']);
		$id = 0;
		foreach (self::$uprawnieniaList as $uprawnienie){
			if($uprawnienie->getId() >= $id){
				$id = $uprawnienie->getId() + 1;
			}
			if($uprawnienie->getName() === $upr->getName()){
				$this->error = "Istnieje uprawnienie o takiej samej nazwie.";
				return false;
			}
		}
		$upr->setId($id);
		self::$uprawnieniaList[] = $upr;

		return $this->updateDatabase();
	}

	public function deleteUpr(array $listaId):bool {
		$tab = array();
		foreach (self::$uprawnieniaList as $upr){
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
		self::$uprawnieniaList = $tab;
		return $this->updateDatabase();
	}

	private function updateDatabase() : bool {
		try {
			$stmt =  $this->conn->prepare("UPDATE ".$this->tbl_settings." SET uprawnieniaList = :uprawnieniaList, harmoValues = :harmoValues, grafikValues = :grafValues WHERE  jrg_id = :jrg_id") ;
			$stmt->bindParam(':jrg_id', self::$jrg_id);
			$stmt->bindParam(':uprawnieniaList', serialize(self::$uprawnieniaList));
			$stmt->bindParam(':harmoValues', serialize(self::$harmoValues));
			$stmt->bindParam(':grafValues', serialize(self::$grafikValues));
			$stmt->execute();
			return true;
		}
		catch(PDOException $e)
		{
			$this->error = "Error: " . $e->getMessage();
		}
		return false;
	}

	public function addHarmoValue($post) : bool {

		$harmoVal = new HarmoValue($post['id'],$post['name'],$post['desc'],$post['color']);
		if($harmoVal->isCorrect()){
			foreach (self::$harmoValues as $harmo_value){
				if($harmo_value->getId() === $harmoVal->getId()){
					throw new UserErrors('Błąd. Istnieje już wartośc: "'.$harmoVal->getId().'"');
				}
			}
			self::$harmoValues[] = $harmoVal;
			return $this->updateDatabase();
		} else {
			throw new UserErrors('Wprowawdzone dane dla nowej wartości harmonogramu są błędne.');
		}
	}

	public function addGrafValue($post) : bool {
		$grafVal = new GrafikValue($post['id'],$post['name'],$post['desc']);
		if($grafVal->isCorrect()){
			foreach (self::$grafikValues as $grafik_value){
				if($grafik_value->getId() === $grafVal->getId()){
					throw new UserErrors('Błąd. Istnieje już wartośc: "'.$grafVal->getId().'"');
				}
			}
			self::$grafikValues[] = $grafVal;
			return $this->updateDatabase();
		} else {
			throw new UserErrors('Wprowawdzone dane dla nowej wartości grafiku są błędne.');
		}
	}
}

class Uprawnienie extends Value{

	/**
	 * @var mixed IKONA
	 */
	private $i;
	/**
	 * @var mixed KOlor
	 */
	private $c;

	/**
	 * Uprawnienie constructor.
	 *
	 * @param $icon
	 * @param $name
	 * @param $color
	 */
	public function __construct(  $name, $icon, $color ) {
		$this->i  = $icon;
		$this->n  = $name;
		$this->c = $color;
	}

	public function printLiElement(){
		echo '<li> <input type="checkbox" class="w3-check" name="deleteUpr[]" value="'.$this->id.'" /> <msc class="fa fa-fw '.$this->i.'" style="color:'.$this->c.'"></msc> '.$this->n.'</li>';
	}

	/**
	 * @return mixed
	 */
	public function getIcon() {
		return $this->i;
	}

	/**
	 * @param mixed $icon
	 */
	public function setIcon( $icon ): void {
		$this->i = $icon;
	}

	/**
	 * @return mixed
	 */
	public function getColor() {
		return $this->c;
	}

	/**
	 * @param mixed $color
	 */
	public function setColor( $color ): void {
		$this->c = $color;
	}

	public function getValueName(): string {
		return 'upr_'.$this->id;
	}
}

class GrafikValue extends Value{

	/**
	 * @var mixed Opis
	 */
	private $d;

	/**
	 * GrafikValue constructor.
	 *
	 * @param $id
	 * @param $name
	 * @param $desc
	 */
	public function __construct( $id, $name, $desc ) {
		$this->id    = $id;
		$this->n  = $name;
		$this->d  = $desc;
	}

	/**
	 * @return mixed
	 */
	public function getDesc() {
		return $this->d;
	}

	/**
	 * @param mixed $d
	 */
	public function setDesc( $d ): void {
		$this->d = $d;
	}

	public function isCorrect() :bool {
		return (strlen($this->id)<=3 && strlen($this->n)>0);
	}

	public function printLiElement(){

		echo '<li class="w3-padding"><span class="w3-border">'.$this->id.'</span> '.$this->getName().' <div class="w3-small w3-center"><msc>'.$this->getDesc().'</msc></div></li>';
	}
	public function getValueName(): string {
		return 'grafik_'.$this->id;
	}
}

class HarmoValue extends Value {

	/**
	 * @var mixed Opis
	 */
	private $d;
	/**
	 * @var mixed Kolor
	 */
	private $c;

	public function __construct( $id, $name, $desc, $color ) {
		$this->id = $id;
		$this->n = $name;
		$this->d = $desc;
		$this->c = $color;
	}

	public function isCorrect():bool {
		return (strlen($this->id)<=3 && strlen($this->n)>0);
	}

	/**
	 * @return mixed
	 */
	public function getColor() {
		return $this->c;
	}

	/**
	 * @param mixed $color
	 */
	public function setColor( $color ): void {
		$this->c = $color;
	}

	/**
	 * @return mixed
	 */
	public function getDesc() {
		return $this->d;
	}

	/**
	 * @param mixed $d
	 */
	public function setDesc( $d ): void {
		$this->d = $d;
	}

	public function printLiElement(){

		echo '<li class="w3-padding"><span class="w3-padding" style="background-color: '.$this->getColor().'">'.$this->id.'</span> '.$this->getName().' <div class="w3-small w3-center"><msc>'.$this->getDesc().'</msc></div></li>';
	}

	public function getValueName(): string {
		return 'harmo_'.$this->id;
	}
}

abstract class Value {

	/**
	 * @var mixed Nazwa
	 */
	protected $n;
	/**
	 * $var mixed ID
	 */
	protected $id;


	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->n;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ): void {
		$this->n = $name;
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

	public abstract function getValueName(): string ;
}

