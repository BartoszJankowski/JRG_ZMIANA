<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 12:59
 */

class LocalDateTime extends DateTime {

	public function __construct( $time = 'now', DateTimeZone $timezone = null ) {
		parent::__construct( $time, new DateTimeZone("Europe/Warsaw") );
	}

	public function getDataTime(){
		return $this->format('d-m-Y H:i:s');
	}
	public function getDate(){
		return $this->format('d-m-Y');
	}
	public function unixTime(){
		return time();
	}

	public function getDayOfYearNum(){
		return ($this->format('z')+1);
	}

	/**
	 * @return int
	 */
	public function getYear(){
		return intval($this->format('Y'));
	}

	public function getMonth(){
		return intval($this->format('n'));
	}

	public function getDayOfMsc(){
		return $this->format('j');
	}

}