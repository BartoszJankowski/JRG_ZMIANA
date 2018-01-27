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
	public function unixTime(){
		return time();
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

}