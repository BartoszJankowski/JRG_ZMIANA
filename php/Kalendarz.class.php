<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 12:57
 */

define("STY_2016_ZM1",array(2016,1,1));

class Kalendarz {


	//properties
	private $currentDataTime;
	private $dayLabels = array("Nd","Pon","Wt","Śr","Czw","Pt","Sob");

	private $tabGenDays = array();
	/**
	 * Kalendarz constructor.
	 *
	 * @param int $currentYear
	 * @param int $currentMonth
	 * @param int $currentDay
	 */
	public function __construct(int $currentYear = 0, int $currentMonth = 0, int $currentDay = 0 ) {
		if($currentYear == 0){
			$currentYear = date("Y",time());
		}
		if($currentMonth == 0){
			$currentMonth = date("n",time());
		}
		if($currentDay == 0){
			$currentDay = date("j",time());
		}
		$this->currentDataTime = new DateTime($currentYear.'-'.$currentMonth.'-'.$currentDay);
		$this->generateDays();
	}

	public function addDays(int $days){
		if($days>0)
			$this->currentDataTime->add(new DateInterval('P'.$days.'D'));
		else
			$this->currentDataTime->sub(new DateInterval('P'.($days*-1).'D'));
	}
	public function addWeeks(int $weeks){
		if( $weeks > 0)
			$this->currentDataTime->add(new DateInterval( 'P' . $weeks . 'W'));
		else
			$this->currentDataTime->sub(new DateInterval( 'P' . $weeks . 'W'));
	}

	public function addMonths(int $months){
		if($months>0)
			$this->currentDataTime->add(new DateInterval('P'.$months.'M'));
		else
			$this->currentDataTime->sub(new DateInterval('P'.$months.'M'));
	}

	public function addYears(int $years){
		if( $years > 0)
			$this->currentDataTime->add(new DateInterval( 'P' . $years . 'Y'));
		else
			$this->currentDataTime->sub(new DateInterval( 'P' . $years . 'Y'));
	}

	public function toString(){
		return $this->currentDataTime->format('d-m-Y');
	}

	public function info(){
		return $this->getDayOfWeek().", zmiana  ".$this->getCurrentZmiana().", ".$this->toString();
	}

	public function getDayOfWeek(){
		return $this->dayLabels[$this->currentDataTime->format('w')];
	}
	public function getYear(){
		return $this->currentDataTime->format('Y');
	}

	/*
	 * return 0-365
	 */
	public function numDayOfYear(){
		return $this->currentDataTime->format('z');
	}

	private function generateDays(){
		$tempDate = new DateTime($this->toString());
		$zmiana = 1;
		$this->currentDataTime->setDate(STY_2016_ZM1[0],STY_2016_ZM1[1],STY_2016_ZM1[2]);
		$this->tabGenDays = array();
		$licznikLat = 0;

		while($licznikLat<10){
			if( !array_key_exists($this->getYear(), $this->tabGenDays) ){
				$licznikLat++;
				$year = $this->getYear();
				$dni = array();

				while ($year == $this->getYear()){
					$dni[$this->numDayOfYear()] = array("zmiana"=>$zmiana,"dzien_tyg"=>$this->getDayOfWeek());

					$zmiana+1 >= 4 ? $zmiana = 1: $zmiana++;
					$this->addDays(1);
				}

				$this->tabGenDays[$year] = $dni;
			}
		}

		$this->currentDataTime = $tempDate;
	}

	private function getCurrentZmiana() {
		if(array_key_exists($this->getYear(), $this->tabGenDays)){
			if(array_key_exists($this->numDayOfYear(), $this->tabGenDays[$this->getYear()] )){
				return $this->tabGenDays[$this->getYear()][$this->numDayOfYear()]["zmiana"];
			} else {
				throw new Exception("Bład - wybrany dzień poza zakresem");
			}
		} else {
			throw new Exception("Bład - wybrany rok poza zakresem");
		}
	}

}