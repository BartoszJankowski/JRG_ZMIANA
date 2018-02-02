<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 29.01.2018
 * Time: 17:24
 */

class Szablon {


	private $id;
	private $jrg_id;
	private $dataSzablonu; //YYYY-MM-DD
	private $finished = 0;

	private $obiekty = array();

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



	public function addObject(HtmlObj $obj){
		$this->obiekty[] = $obj;
	}



}

abstract class HtmlObj {

	protected $name;
	protected $id;
	protected $classes = array();
	protected $attributes = array();

	protected $content;

	public function __construct() {}

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

	public function addClass(string $class){
		if(!in_array($class, $this->classes)){
			$this->classes[] = $class;
		}
	}

	public function removeClass(string $class){
		if(in_array($class, $this->classes)){
			$temp = array();
			foreach($this->classes as $klasa){
				if($klasa !== $class){
					$temp[] = $klasa;
				}
			}
			$this->classes = $temp;
		}
	}

	public function addAttr(string $name, $val){
		$this->attributes[$name] = $val;
	}

	public function removeAttr(string $name){

		if(array_key_exists($this->attributes)){
			$tabtemp = array();
			foreach ($this->attributes as $n=>$v){
				if($n!==$name){
					$tabtemp[$n] = $v;
				}
			}
			$this->attributes = $tabtemp;
		}
	}

	protected function getHtml(){
		if($this->content instanceof HtmlObj){
			$content = $this->content->getHtml();
		}	else {
			$content = $this->content;
		}
		$class = count($this->classes)>0 ? ' class="'.implode(' ',$this->classes).'" ' : '';
		$attr = '';
		if(count($this->attributes)>0){
			$atab = array();
			foreach ($this->attributes as $name=>$val){
				$atab[] = $name.'="'.$val.'"';
			}
			$attr = ' '.implode(' ',$atab).' ';
		}
		if($this->id!=null){
			$attr .= ' id="'.$this->id.'"';
		}

		return $this->getHtmlTag($class, $attr, $content);

	}

	abstract public function putContent($content);

	protected function getHtmlTag($class, $attr, $content){
		return  '<'.$this->name.' '.$attr.' '.$class.' >'.$content.'</'.$this->name.'>';
	}

	public function print(){
		echo $this->getHtml();
	}

}

class Naglowek extends HtmlObj {

	public function __construct(int $nr) {
		parent::__construct();
		$this->name = 'h'.$nr;
	}


	public function putContent($content) {
		if(is_string($content)){
			$this->content = $content;
		} else {
			throw new Exception("Nie można dodać obiektu innego niz String do tego elementu.");
		}
	}
}

class Input extends HtmlObj {


	public function __construct(string $typ, string $name) {
		parent::__construct();
		$this->name = 'input';
		$this->addAttr('type',$typ);
		$this->addAttr('name',$name);

	}

	protected function getHtmlTag( $class, $attr, $content ) {
		return '<'.$this->name.' '.$attr.' '.$class.' />';
	}

	public function putContent( $content ) {
		throw new Exception("Ten obiekt nie posiada zawartości");
	}
}

class Text extends HtmlObj {


	public function __construct(string $content = '') {
		parent::__construct();
		$this->name = 'span';
		if($content!=''){
			$this->content = $content;
		}
	}

	public function putContent( $content ) {
		if(is_string($content)){
			$this->content = $content;
		} else {
			throw new Exception("Nie można dodać obiektu innego niz String do tego elementu.");
		}
	}
}

class Paragraf extends HtmlObj {


	public function __construct(string $content = null) {
		parent::__construct();
		$this->name = 'p';
		if($content!=null){
			$this->content = $content;
		}
	}

	public function putContent( $content ) {
		if(is_string($content)){
			$this->content = $content;
		} else {
			throw new Exception("Nie można dodać obiektu innego niz String do tego elementu.");
		}
	}
}