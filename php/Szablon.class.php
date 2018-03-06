<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 17:24
 */

class Szablon {
	private $createdOrders  =0;

	protected $id;
	protected $jrg_id;
	protected $dataSzablonu; //YYYY-MM-DD
	protected $finished = 0;

	protected $obiekty = array();

	public static $currentHtmlId = 0;
	public static $isEditing = false;

	public function __construct(int $jrg_id) {
		$this->jrg_id = $jrg_id;
		$this->dataSzablonu = (new DateTime())->format('Y-m-d');
	}

	/**
	 * data yyyy-mm-dd
	 * @return string
	 */
	public function getDataSzablonu(): string {
		return $this->dataSzablonu;
	}

	/**
	 * @param int $id
	 */
	public function setId( $id ): void {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function setObiektyHtml($obiektyString){
		if($obiektyString!=null && strlen($obiektyString)>0)
			$this->obiekty = unserialize($obiektyString);
	}

	/**
	 * @param string $dataSzablonu
	 */
	public function setDataSzablonu( string $dataSzablonu ): void {
		$this->dataSzablonu = $dataSzablonu;
	}

	/**
	 * @return array
	 */
	public function getObiektyHtml(): array {
		return $this->obiekty;
	}

	/**
	 * @return mixed
	 */
	public function getFinished() {
		return $this->finished;
	}

	/**
	 * @param mixed $finished
	 */
	public function setFinished( $finished ): void {
		$this->finished = $finished;
	}

	public function addObjects(HtmlObj ...$objs){
		foreach ($objs as $obj)
			$this->obiekty[] = $obj;

	}

	public function setNumOfCreatedOrders($num = null){
		if($num!=null && $num>0){
			$this->createdOrders = $num;
		}
	}


	public function getCreatedOrdersNum(): int {
		return $this->createdOrders;
	}

}

abstract class HtmlObj {

	/**
	 * name
	 */
	protected $n;
	/**
	 * id
	 */
	protected $i;
	/**
	 * klasy css
	 */
	protected $c = array();
	/**
	 * attrybuty
	 */
	protected $a = array();
	/**
	 * style
	 */
	protected $s = array();
	/**
	 * content
	 */
	protected $cnt;

	public function __construct() {
		if($this->isInputType())
			$this->i = 'ord-'.(++Szablon::$currentHtmlId);
	}

	private function isInputType(){
		return ($this instanceof Input || $this instanceof Select );
	}
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->i;
	}


	public function addClass( $class) : HtmlObj{
		if(is_array($class)){
			for ($i = 0; $i<count($class); $i++){
				if(!in_array($class[$i], $this->c)){
					$this->c[] = $class[$i];
				}
			}
		} else if(is_string($class)){
			if(!in_array($class, $this->c)){
				$this->c[] = $class;
			}
		}


		return $this;
	}

	public function removeClasses(string ...$class){

		$temp = array();
		foreach($this->c as $klasa){
			if(!in_array($klasa, $class)){
				$temp[] = $klasa;
			}
		}
		$this->c = $temp;

	}

	public function addStyle($key, $val) : HtmlObj{
		$this->s[$key] = $val;
		return $this;
	}

	public function addAttr(string $name, $val) : HtmlObj{
		if($name == 'style'){
			return $this->addStyle($name, $val);
		}
		$this->a[$name] = $val;
		return $this;
	}

	public function removeAttr(string $name){

		if(array_key_exists($this->a)){
			$tabtemp = array();
			foreach ($this->a as $n=>$v){
				if($n!==$name){
					$tabtemp[$n] = $v;
				}
			}
			$this->a = $tabtemp;
		}
	}

	protected  function getContent() {
		if(Szablon::$isEditing){
			$this->addClass('newEmptyElement');
		}
		$content = '';
		if( $this->cnt instanceof HtmlObj){
			$content = $this->cnt->getHtml();
		} else if(is_array($this->cnt)){
			foreach ($this->cnt as $obj){
				if($obj instanceof HtmlObj){
					$content .= $obj->getHtml();
				}
			}
		} else {
			$content = $this->cnt;
		}
		return $content;
	}

	protected function getHtml() {
		$content = $this->getContent();
		$class = $this->getClasses();
		$attr = $this->getAttributes();
		$style = '';


		foreach ($this->s as $key=>$val){
			$style = $key.':'.$val.';';
		}
		if(strlen($style)>0){
			$attr .= ' style="'.$style.'"';
		}
		return $this->getHtmlTag($class, $attr, $content);
	}

	protected function getClasses(){
		return count($this->c) > 0 ? ' class="' . implode(' ',$this->c) . '" ' : '';
	}

	protected function  getAttributes(){
		$attr = '';
		if( count($this->a) > 0){
			$atab = array();
			foreach ($this->a as $name=>$val){
				$atab[] = $name.'="'.$val.'"';
			}
			$attr = ' '.implode(' ',$atab).' ';
		}
		if( $this->i != null){
			$attr .= ' id="'.$this->i . '"';
		}
		return $attr;
	}

	protected function getHtmlTag($class, $attr, $content){
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
		}
		return  '<'.$this->n . ' ' . $attr . ' ' . $class . ' '.$popover.' >' . $content . '</' . $this->n . '>';
	}

	public function print() {
		echo $this->getHtml();
	}

	public function setList($names) : HtmlObj{
		//$this->addClass('jrg_list');
		//$this->addClass('szablon_element');
		$wartosc = '';
		if(is_array($names)){
			foreach ($names as $name){
				$wartosc .= $name.' ';
			}
		} else if(is_string($names)) {
			$wartosc = $names;
		}

		if(strlen($wartosc)>1){
			$this->addAttr('data-jrg', $wartosc );
		}
		return $this;
	}

	public function getDataVarType() {
		if(count($this->a)>0){
			foreach ($this->a as $attr=>$val){
				if($attr === 'data-jrg'){
					return $val;
				}
			}
		}
		return false;
	}

	public function hasChilds() : bool {
		if(is_array($this->cnt) || $this->cnt instanceof HtmlObj){
			return true;
		}
		return false;
	}

	public function getChilds(){
		return $this->cnt;
	}

	abstract public function putContent($content) : HtmlObj;
}

class Sekcja extends HtmlObj {
	public function __construct( $klasy) {
		parent::__construct();
		$this->n   = 'div';
		$this->cnt = array();

		$this->addClass($klasy);

	}


	public function putContent( $content ) : HtmlObj {
		if ( $content instanceof HtmlObj) {
			$this->cnt[] = $content;
		} else {
			throw new UserErrors( "Do tego elementu można dodac tylko inny obiekt." );
		}
		return $this;
	}
}

class Naglowek extends HtmlObj  implements TextType  {

	public function __construct(int $nr) {
		parent::__construct();
		$this->n = 'h' . $nr;
	}


	public function putContent($content) : HtmlObj {
		if(is_string($content)){
			if(is_array($this->cnt)){
				throw new UserErrors("Nie można dodać obiektu innego niz String do tego elementu.");
			}
			$this->cnt = $content;
		} else if($content instanceof HtmlObj){
			if(is_string($this->cnt)){
				throw new UserErrors("Nie można dodać kolejnego obiektu html gdy obiekt zawiera już treśc typu tekst.");
			} else {
				$this->cnt[] = $content;
			}
		} else {
			throw new UserErrors("Nie można dodać tego typu elementu do nagłówka.");
		}
		return $this;
	}

	public function changeContent( array $zmienne, array $values ) {
		foreach ($zmienne as $var){
			$this->cnt = str_replace($var,$values[$var],$this->cnt );
		}
	}
}

class Input extends HtmlObj implements ValueAdapter {

	public function __construct(string $typ, string $name) {
		parent::__construct();
		$this->n = 'input';
		$this->addAttr('type',$typ);
		$this->addAttr('name',$name);

	}

	protected function getHtmlTag( $class, $attr, $content ) {
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
		}
		$val = 'value=""';
		if(isset($this->value)){
			$val = 'value="'.$this->value.'"';
		}
		return '<'.$this->n . ' ' . $attr . ' ' . $class . ' '.$popover.' '.$val.' />';
	}

	public function putContent( $content ) : HtmlObj {
		throw new UserErrors("Ten obiekt nie posiada zawartości");
		return $this;
	}

	public function setVal( $value ) {
		$this->value = $value;
	}
}

class Select extends HtmlObj implements ListAdapter,ValueAdapter {

	public function __construct( string $name) {
		parent::__construct();
		$this->n   = 'select';
		$this->cnt = array();
		$this->addAttr('name',$this->i);
	}

	public function putContent( $content ) : HtmlObj {
		throw new UserErrors("Ten obiekt nie posiada zawartości");
		return $this;
	}



	protected function getContent() {
		if(Szablon::$isEditing){
			$this->addClass('newEmptyElement');
			return '';
		}
		$res = '<option disabled '.(isset($this->value) ? "":"selected").' >&nbsp;</option>';
		foreach ($this->cnt as $cont){
			if(isset($this->value) && $this->value === $cont['value'] ){
				$res .= '<option value="'.$cont['value'].'" selected >'.$cont['key'].'</option>';
			} else {
				$res .= '<option value="'.$cont['value'].'"  >'.$cont['key'].'</option>';
			}

		}
		return $res;
	}

	protected function getHtmlTag( $class, $attr, $content ) {
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
		}
		return  '<'.$this->n . ' ' . $attr . ' ' . $class . ' value="'.( isset($this->value) ? $this->value : '' ).'" '. $popover.' >' . $content . '</' . $this->n . '>';
	}


	public function setListContent( array $name ) {
		foreach ($name as $value){
			$this->cnt[] = $value;
		}

	}

	public function setVal( $value ) {
		$this->value = $value;
	}
}

class Text extends HtmlObj  implements TextType  {


	public function __construct(string $content = '') {
		parent::__construct();
		$this->n = 'span';
		if($content!=''){
			$this->cnt = $content;
		}
	}

	public function putContent( $content ) : HtmlObj{
		if(is_string($content)){
			$this->cnt = $content;
		} else {
			throw new UserErrors("Nie można dodać obiektu innego niz String do tego elementu.");
		}
		return $this;
	}

	public function changeContent( array $zmienne, array $values ) {


		foreach ($zmienne as $var){
			$this->cnt = str_replace($var,$values[$var],$this->cnt );
		}
	}
}

class Paragraf extends HtmlObj implements TextType {


	public function __construct(string $content = null) {
		parent::__construct();
		$this->n = 'p';
		if($content!=null){
			$this->cnt = $content;
		}
	}

	public function putContent( $content ) : HtmlObj {
		if(is_string($content)){
			$this->cnt = $content;
		} else {
			throw new UserErrors("Nie można dodać obiektu innego niz String do tego elementu.");
		}
		return $this;
	}

	public function changeContent( array $zmienne, array $values ) {
		foreach ($zmienne as $var){
			$this->cnt = str_replace($var,$values[$var],$this->cnt );
		}
	}
}

class Lista extends HtmlObj implements ListAdapter {

	public function __construct() {
		parent::__construct();
		$this->n   = 'ul';
		$this->cnt = array();
	}

	public function putContent( $content ) : HtmlObj {
		$this->cnt[] = $content;
		return $this;
	}

	protected function getContent() {
		if(Szablon::$isEditing){
			$this->addClass('newEmptyElement');
		}
		$res = '';
		foreach ($this->cnt as $cont){
			if($cont instanceof HtmlObj)
				$res .= '<li>'.$cont->getContent().'</li>';
			else
				$res .= '<li>'.$cont.'</li>';
		}
		return $res;
	}

	public function setListContent( array $tab ) {
		foreach ($tab as $v){
			$this->cnt[] = $v['key'];
		}
	}
}

class Col extends HtmlObj implements ListAdapter {

	/**
	 * @var int Rowsa
	 */
	private $r = 0;

	public function __construct($header,int $min_rows = 0) {
		parent::__construct();
		$this->cnt  = array();
		$this->n = $header;
		$this->r = $min_rows;
	}

	public function setName(string $naem){
		$this->n = $naem;
	}

	public function getNameTh($nr){
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
			$this->addClass('newEmptyElement');
		}
		return '<th data-col="'.$nr.'" '.$this->getClasses().' '.$this->getAttributes().' '.$popover.' >'.$this->n.'</th>';
	}

	public function setListContent( array $lista ) {

		foreach ($lista as $el){
			$this->cnt[] =  (new TD())->putContent(new Text($el['key']));
		}
	}

	public function getRowVal(int $row,$col){
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
			$this->addClass('newEmptyElement');
		}
		if(array_key_exists($row, $this->cnt)){
			return $this->cnt[$row]->getHtml();
		} else if($row < $this->r){
			return '<td data-col="'.$col.'"  '.$this->getClasses().' '.$popover.'></td>';
		} else {
			return false;
		}
	}

	public function putContent( $content ): HtmlObj {
		$this->cnt[] = $content;
		return $this;
	}

	public function getHtml() {
		throw new UserErrors('Ten obiekt nie zwraca zwartości. Może byc wywołany tylko z klasy Table.');
	}

}

class TD extends HtmlObj{

	public function __construct() {
		parent::__construct();
		$this->cnt  = array();
		$this->n = 'td';

	}

	public function setColumn($nrCol) : HtmlObj{
		$this->col = $nrCol;
		return $this;
	}

	public function putContent( $content ): HtmlObj {
		if($content instanceof HtmlObj){
			$this->cnt[] = $content;
		}
		return $this;
	}

	public function getHtmlTag( $class, $attr, $content ) {
		if(Szablon::$isEditing){
			$popover = 'data-toggle="popover" data-html="true" data-placement="top" ';
			$this->addClass('newEmptyElement');
		}
		return '<td data-col="'.$this->col.'"  '.$this->getClasses().' '.$popover.'>'.$content.'</td>';
	}
}

class Table extends HtmlObj{

	public function __construct($cols = 0, $minRows = 0) {
		parent::__construct();
		$this->n = 'table';

		$this->cnt = array();

		for($k = 0; $k<$cols; $k++){
			$this->cnt[$k] = new Col('', $minRows);
		}
	}

	public function putContent( $content) : HtmlObj {
		if($content instanceof Col){
			$this->cnt[] = $content;
		} else {
			throw new UserErrors("Dla elementu typu table użyj funkcji addCell lub dodaj obiekt 'Kolumna'.");
		}
		return $this;
	}

	protected function getContent() : string {
		if(Szablon::$isEditing){
			$this->addClass('newEmptyElement');
		}
		/**
		 * NAGLOWEK
		 */
		$content = '<tr>';
		foreach ($this->cnt as $nr=> $col){
			if($col instanceof Col){
				$content .= $col->getNameTh($nr);
			}
		}
		$content .= '</tr>';

		/**
		 * ZAWARTOSC / WIERSZE
		 */
		$a = 0;
		do{

			$temp = '<tr>';
			$kontynuuj = false;
			foreach ($this->cnt as $nr=> $col){
				if($col instanceof Col){
					$val = $col->getRowVal($a, $nr);
					if($val){
						$kontynuuj = true;
						$temp.= $val;
					} else {
						$temp .= '<td></td>';
					}
				}
			}
			$temp .= '</tr>';
			if($kontynuuj){
				$content .= $temp;
			}
			$a++;
		} while($kontynuuj);


		return $content;
	}

	public function addCol(Col $col,int $colId = -1){
		if($colId>0)
			$this->cnt[$colId-1] = $col;
		else
			$this->cnt[] = $col;
	}

	public function addCell(TD $value,int $kolumn) : HtmlObj{
		$value->setColumn($kolumn);
		if(array_key_exists($kolumn, $this->cnt)){
			$this->cnt[$kolumn]->putContent($value);
		} else {
			$col = new Col('');
			$col->putContent($value);
			$this->cnt[$kolumn] = $col;
		}
		return $this;
	}

}

interface ListAdapter {
	public function setListContent( array $name );
}

interface ValueAdapter {
	public function setVal($value);
}

interface TextType {
	public function changeContent(array $zmienne, array $values);
};