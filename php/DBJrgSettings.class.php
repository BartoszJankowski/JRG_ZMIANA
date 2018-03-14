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

	public static $NOTIFY_EMAIL = true;
	public static $NOTIFY_CHEF = true;
	public static $NOTIFY_ADMIN = true;

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
				self::createDefaultSettings();
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
	public static function getUprawnienie( $id){
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

	public function deleteUpr( $idUpr):bool {
		$tab = array();
		foreach (self::$uprawnieniaList as $upr){
			$found  = false;
				if($upr->getId() == $idUpr){
					$found = true;
				}
			if(!$found){
				$tab[] = $upr;
			}
		}
		self::$uprawnieniaList = $tab;
		return $this->updateDatabase();
	}
	public function deleteHarm($idUpr){
		$tab = array();
		foreach (self::$harmoValues as $upr){
			$found  = false;
			if($upr->getId() == $idUpr){
				$found = true;
			}
			if(!$found){
				$tab[] = $upr;
			}
		}
		self::$harmoValues = $tab;
		return $this->updateDatabase();
	}
	public function deleteGraf($idUpr){
		if($idUpr === 'Ws'){
			return false;
		}
		$tab = array();
		foreach (self::$grafikValues as $upr){
			$found  = false;
			if($upr->getId() == $idUpr){
				$found = true;
			}
			if(!$found){
				$tab[] = $upr;
			}
		}
		self::$grafikValues = $tab;
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

	public static function getZmienneRozkazu(){
		$wartosci = array(
			array('id'=>'[$nr_jrg]','opis'=>'Numer JRG'),
			array('id'=>'[$miasto]','opis'=>'Miasto'),
			array('id'=>'[$nr_rozkazu]','opis'=>'Nr rozkazu liczony od nr 1 dla 1 stycznia danego roku'),
			array('id'=>'[$nr_zmiany]','opis'=>'Nr zmiany wypadającej w datę rozkazu'),
			array('id'=>'[$data_rozkazu]','opis'=>'Data dla której pisany jest rozkaz dzienny ( wg tej daty liczony jest nr rozkazu oraz nr zmiany)'),
			array('id'=>'[$data_edycji]','opis'=>'Data utworzenia lub edycji rozkazu'),
			array('id'=>'[$rok]','opis'=>'Rok względem daty rozkazu'),
			array('id'=>'[$msc]','opis'=>'Miesiąc z roku względem daty rozkazu.'),
			array('id'=>'[$dzien]','opis'=>'Dzień w miesiącu względem daty rozkazu.')
			);
		return $wartosci;
	}

	public static function getListValues(){
		$wartosci = array(
			array('id'=>'@Dd','name'=>'Dyżur domowy'),
			array('id'=>'@zmiana_str','name'=>'Lista strażaków'),
			array('id'=>'@zmiana_free_str','name'=>'Wolni/dostępni strażacy')
		);
		foreach (self::$harmoValues as $harmo_value){
			$wartosci[] = array('id'=>$harmo_value->getValueName(),'name'=>$harmo_value->getName());
		}
		foreach (self::$grafikValues as $grafik_value){
			$wartosci[] = array('id'=>$grafik_value->getValueName(),'name'=>$grafik_value->getName());
		}
		return $wartosci;
	}

	public static function printJsListValues(){
		$wartosci = self::getListValues();

		echo '<script>var listyVar='.json_encode($wartosci).'; var zmienne='.json_encode(self::getZmienneRozkazu()).'</script>';
	}

	private static function createDefaultSettings(){
		self::$uprawnieniaList = array();
		self::$grafikValues = array();
		self::$harmoValues = array();

		$upr = new Uprawnienie('D-ca zastępu','fa-chess-king','green');
		$upr->setId(0);
		self::$uprawnieniaList[] = $upr;
		$upr =new Uprawnienie('Kierowca ','fa-car','orange');
		$upr->setId(0);
		self::$uprawnieniaList[] = $upr;
		$upr = new Uprawnienie('Operator','fa-truck','yellow');
		$upr->setId(0);
		self::$uprawnieniaList[] = $upr;

		self::$grafikValues[] = new GrafikValue('Ws','Wolna służba','Wolne');
		self::$grafikValues[] = new GrafikValue('Pd','Podoficer','Służba podoficera');

		self::$harmoValues[] = new HarmoValue('Uw','Urlop wypoczynkowy','Urlop wypoczynkowy', 'orange');
		self::$harmoValues[] = new HarmoValue('Ud','Urlop dodatkowy','Urlop dodatkowy', 'green');
		self::$harmoValues[] = new HarmoValue('O','Urlop okolicznościowy','Urlop okolicznościowy', 'blue');
		self::$harmoValues[] = new HarmoValue('D','Delegacja','Delegacja', '#80ffff');
		self::$harmoValues[] = new HarmoValue('Ch','Chorobowe','Chorobowe', '#996600');


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
		echo '<li class="list-group-item"><span><i class="fa fa-fw '.$this->i.'" style="color:'.$this->c.'"></i> '.$this->n.'</span> <button onclick="usunUpr(this)" class="btn btn-sm w3-right" value="'.$this->getId().'" data-toggle="tooltip" data-trigger="hover" title="Usuń pozycję" data-type="uprawnienie" ><i class="far fa-trash-alt"></i></button></li>';
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
		return '@upr_'.$this->id;
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

		echo '<li class="list-group-item"><button onclick="usunUpr(this)" class="btn btn-sm w3-right" value="'.$this->getId().'" data-type="grafik" data-toggle="tooltip" data-trigger="hover" title="Usuń pozycję"><i class="far fa-trash-alt"></i></button><span class="w3-border">'.$this->id.'</span> '.$this->getName().' <div class="w3-small w3-center"><i>'.$this->getDesc().'</i></div></li>';
	}
	public function getValueName(): string {
		return '@grafik_'.$this->id;
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

		echo '<li class="list-group-item"><button onclick="usunUpr(this)" class="btn btn-sm w3-right" data-type="harmonogram"  value="'.$this->getId().'" data-toggle="tooltip" data-trigger="hover" title="Usuń pozycję"><i class="far fa-trash-alt"></i></button><span class="my_badge" style="background-color: '.$this->getColor().'">'.$this->id.'</span> <div class="d-inline-block">'.$this->getName().'<div  class="w3-small w3-center"><i>'.$this->getDesc().'</i></div></div></li>';
	}

	public function getValueName(): string {
		return '@harmo_'.$this->id;
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

