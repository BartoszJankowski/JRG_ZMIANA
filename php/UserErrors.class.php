<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 08.02.2018
 * Time: 15:36
 */

class UserErrors extends Exception {

	private static $errorSet = false;

	//TODO: zrobic blędy dla uzytkowników

	public function __construct( string $message = "", int $code = 0, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
		self::$errorSet = true;
	}

	public static function hasError(){
		return self::$errorSet;
	}


}