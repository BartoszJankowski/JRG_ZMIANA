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
		return $this->format('d-m-Y H:msc:s');
	}
	public function getDate(){
		return $this->format('d-m-Y');
	}
	public function getMySqlDate(){
		return $this->format('Y-m-d');
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



	public function addDays($days) : LocalDateTime{
		if($days>0)
			$this->add(new DateInterval('P'.$days.'D'));
		else
			$this->sub(new DateInterval('P'.($days*-1).'D'));

		return $this;
	}

	public function getTimeTillNow(){
		$ltdTImestamp = (new LocalDateTime())->getTimestamp();
		return $ltdTImestamp-$this->getTimestamp();

	}

}