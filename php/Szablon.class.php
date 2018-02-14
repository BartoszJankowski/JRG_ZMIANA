<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 17:24
 */

class Szablon {


	protected $id;
	protected $jrg_id;
	protected $dataSzablonu; //YYYY-MM-DD
	protected $finished = 0;

	protected $obiekty = array();

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

	public function __construct() {}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->i;
	}

	/**
	 * @param mixed $i
	 */
	public function setId( $i ): void {
		$this->i = $i;
	}

	public function addClass(string $class) : HtmlObj{
		if(!in_array($class, $this->c)){
			$this->c[] = $class;
		}
		return $this;
	}

	public function removeClass(string $class){
		if(in_array($class, $this->c)){
			$temp = array();
			foreach($this->c as $klasa){
				if($klasa !== $class){
					$temp[] = $klasa;
				}
			}
			$this->c = $temp;
		}
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
		$class = count($this->c) > 0 ? ' class="' . implode(' ',$this->c) . '" ' : '';
		$attr = '';
		$style = '';
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

		foreach ($this->s as $key=>$val){
			$style = $key.':'.$val.';';
		}
		if(strlen($style)>0){
			$attr .= ' style="'.$style.'"';
		}



		return $this->getHtmlTag($class, $attr, $content);

	}

	protected function getHtmlTag($class, $attr, $content){
		return  '<'.$this->n . ' ' . $attr . ' ' . $class . ' >' . $content . '</' . $this->n . '>';
	}

	public function print() {
		echo $this->getHtml();
	}

	public function setConstant(string $name) : HtmlObj{
		$this->addClass('jrg_const');
		$this->addClass('szablon_element');
		if(strlen($name)>1){
			$this->addAttr('data-jrg', 'constant-'.$name);
		} else {
			throw new UserErrors("Nazwa stałej musi zawierać więcej niz 1 znak");
		}
		return $this;
	}

	public function setVariable(string $name) : HtmlObj{
		$this->addClass('jrg_var');
		$this->addClass('szablon_element');
		if(strlen($name)>1){
			$this->addAttr('data-jrg', 'variable-'.$name );
		} else {
			throw new UserErrors("Nazwa stałej musi zawierać więcej niz 1 znak");
		}
		return $this;
	}

	public function setList(string $name){
		$this->addClass('jrg_list');
		$this->addClass('szablon_element');
		if(strlen($name)>1){
			$this->addAttr('data-jrg', 'list-'.$name );
		} else {
			throw new UserErrors("Nazwa stałej musi zawierać więcej niz 1 znak");
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
	public function __construct() {
		parent::__construct();
		$this->n   = 'div';
		$this->cnt = array();
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

class Naglowek extends HtmlObj {

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
}

class Input extends HtmlObj {

	public function __construct(string $typ, string $name) {
		parent::__construct();
		$this->n = 'input';
		$this->addAttr('type',$typ);
		$this->addAttr('name',$name);

	}

	protected function getHtmlTag( $class, $attr, $content ) {
		return '<'.$this->n . ' ' . $attr . ' ' . $class . ' />';
	}

	public function putContent( $content ) : HtmlObj {
		throw new UserErrors("Ten obiekt nie posiada zawartości");
		return $this;
	}
}

class Select extends HtmlObj implements ListAdapter{

	public function __construct( string $name) {
		parent::__construct();
		$this->n   = 'select';
		$this->cnt = array();
		$this->addAttr('name',$name);
	}

	public function putContent( $content ) : HtmlObj {
		throw new UserErrors("Ten obiekt nie posiada zawartości");
		return $this;
	}



	protected function getContent() {
		$res = '<option >wybór z listy</option>';
		foreach ($this->cnt as $cont){
			$res .= '<option value="'.$cont['value'].'" >'.$cont['key'].'</option>';
		}
		return $res;
	}


	public function setListContent( array $name ) {
			$this->cnt = $name;
	}
}

class Text extends HtmlObj {


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
}

class Paragraf extends HtmlObj {


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

class Table extends HtmlObj{

	public function __construct($cols = 0, $rows = 0) {
		parent::__construct();
		$this->n = 'table';

		$this->cnt = array();

		for($i = 0; $i<$rows; $i++){
			for ($k = 0; $k<$cols; $k++){
				$this->cnt[$i][$k] = null;
			}
		}
	}

	public function putContent( $content) : HtmlObj {
		throw new UserErrors("Dla elementu typu table użyj funkcji dodaj wiersz/kolumnę.");
	}

	public function addCell($value, $row, $kolumn){

		if( $this->cnt[$row][$kolumn] instanceof HtmlObj){
			$this->cnt[$row][$kolumn]->putContent($value);
		} else {
			$this->cnt[$row][$kolumn] = $value;
		}
	}

	protected function getContent() : string {
		$content = '';
		if(is_array($this->cnt)){
			foreach ($this->cnt as $row){
				$content .= '<tr>';
				if(is_array($row)){
					foreach ($row as $col){
						if($col instanceof HtmlObj){
							$content .= '<td>'.$col->getHtml().'</td>';
						} else {
							$content .= '<td>'.$col.'</td>';
						}
					}
				}
				$content .= '</tr>';
			}
		}
		return $content;
	}
}

interface ListAdapter {
	public function setListContent( array $name );
}