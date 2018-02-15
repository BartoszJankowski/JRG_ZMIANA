<?php
$timeStart = microtime(true);


//ini_set('display_errors', 3);
error_reporting(E_ERROR | E_WARNING | E_PARSE );

//Pull '$base_url' and '$signin_url' from this file
spl_autoload_register(function ($class_name) {
	include 'php/'.$class_name . '.class.php';
});

//Pull database configuration from this file
include 'php/dbconf.php';

include 'php/PHPMailer/PHPMailerAutoload.php';

include 'php/functions.php';

//SYSTEM SETTINGS
$base_url = 'http://' . $_SERVER['SERVER_NAME'];

//DO NOT CHANGE
$ip_address = $_SERVER['REMOTE_ADDR'];




//Set this for global site use
$site_name = 'energoland.bjit.pl';

//Maximum Login Attempts
$max_attempts = 5;
//Timeout (in seconds) after max attempts are reached
$login_timeout = 300;

//ONLY set this if you want a moderator to verify users and not the dbUsers themselves, otherwise leave blank or comment out
$admin_email = 'admin@zmiana.bjit.pl';//'jankowski.ba@gmail.com';

//EMAIL SETTINGS
//SEND TEST EMAILS THROUGH FORM TO https://www.mail-tester.com GENERATED ADDRESS FOR SPAM SCORE
define('FROM_EMAIL','admin@zmiana.bjit.pl'); //Webmaster email
define('FROM_NAME', 'Admin ZMIANA' ); //"From name" displayed on email

//Find specific server settings at https://www.arclab.com/en/kb/email/list-of-smtp-and-pop3-servers-mailserver-list.html
$mailServerType = 'postfix';
//IF $mailServerType = 'smtp'
define('SMPT_SERV' ,'mail.bjit.pl');
define('SMPT_USER' , 'admin@zmiana.bjit.pl');
define( 'SMPT_PW' , 'komputer');
define('smtp_port' , 465 ); //465 for ssl, 587 for tls, 25 for other
define( 'smtp_security' , 'ssl');//ssl, tls or ''


//DO NOT TOUCH BELOW THIS LINE
//Unsets $admin_email based on various conditions (left blank, not valid email, etc)
if (trim($admin_email, ' ') == '') {
    unset($admin_email);
} elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL) == true) {
    unset($admin_email);
    echo $invalid_mod;
};
$invalid_mod = '$adminemail is not a valid email address';

//Makes readable version of timeout (in minutes). Do not change.
$timeout_minutes = round(($login_timeout / 60), 1);

//ZMIENNA GLOBALNA
$_SETTINGS = new DBJrgSettings();


