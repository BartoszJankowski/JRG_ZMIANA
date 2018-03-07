<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 06.03.2018
 * Time: 23:34
 */

class UserSettings {
	/**
	 * @var int dni do powiadomienia
	 */
	static $ALERT_LOW = 14;
	static  $ALERT_MID = 7;
	static $ALERT_HI = 3;

	public static function getAlertType(int $dni):string {
		if($dni<=self::$ALERT_HI){
			return 'red';
		} elseif ($dni<=self::$ALERT_MID){
			return 'orange';
		} elseif ($dni<=self::$ALERT_LOW){
			return 'yellow';
		}else {
			return '';
		}
	}

	/**
	 * POWIADOMIENIA
	 */
	static $NOTIFY_EMAIL = true;

}