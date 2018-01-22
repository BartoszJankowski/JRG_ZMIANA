<?php
//ini_set('display_errors', 3);
error_reporting(E_ERROR | E_WARNING | E_PARSE );

//Pull '$base_url' and '$signin_url' from this file
spl_autoload_register(function ($class_name) {
	include 'php/'.$class_name . '.class.php';
});

include 'globalcon.php';
//Pull database configuration from this file
include 'php/dbconf.php';

//Set this for global site use
$site_name = 'energoland.bjit.pl';

//Maximum Login Attempts
$max_attempts = 5;
//Timeout (in seconds) after max attempts are reached
$login_timeout = 300;

//ONLY set this if you want a moderator to verify users and not the users themselves, otherwise leave blank or comment out
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

function sendMail($adresRecipient = 'badbart10@wp.pl',$name, $content, $tabFiles = array() ){
	$mail = new PHPMailer(true);
	$fromEmail = FROM_EMAIL;// 'admin@zmiana.bjit.pl';
	$fromName = FROM_NAME;// 'Admin';
	$smptServ = SMPT_SERV;//'mail.bjit.pl';
	$smptuser = SMPT_USER;//'admin@zmiana.bjit.pl';
	$smptPW = SMPT_PW;//'komputer';
	// Passing `true` enables exceptions
	try {
		//Server settings
		$mail->SMTPDebug = 2;                                 // Enable verbose debug output
		$mail->isMail();                                      // Set mailer to use SMTP
		$mail->Host = $smptServ;  // Specify main and backup SMTP servers // h1.hitme.pl
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $smptuser;                 // SMTP username
		$mail->Password = $smptPW;                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		//Recipients
		$mail->setFrom($fromEmail, $fromName);
		$mail->addReplyTo($fromEmail, $fromName);
		$mail->addAddress($adresRecipient, 'Bartosz Jankowski');

		//Attachments
		foreach ($tabFiles as $fil){
			$mail->addAttachment($fil);
		} // Optional name

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->CharSet = 'UTF-8';
		$mail->Subject  = 'Wiadomość od zmiana.bjit.pl';
		$mail->Body     = '<div><h3>Wiadomość od klienta:</h3><p>'.$name.'</p></div><div><h4>Treść wiadomości:</h4><p>'.$content.'</p></div>';
		$mail->AltBody = $content;

		$mail->send();
		echo 'Message has been sent to '.$adresRecipient;
	} catch (Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
}

