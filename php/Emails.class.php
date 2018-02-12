<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 19:21
 */

class Emails {

	//TODO: error logs

	private $host;
	private $smptPW;
	private $smptuser;
	private $port = 587;
	private $smptSecure = 'tls';
	private $charset = 'UTF-8';

	public $mail;

	public function __construct() {
		$this->host = SMPT_SERV;
		$this->smptPW = SMPT_PW ;
		$this->smptuser = SMPT_USER;


	}

	private function configureEmail($adresRecip){
		try {
			$this->mail = new PHPMailer(true);
			$this->mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$this->mail->isMail();                                      // Set mailer to use SMTP
			$this->mail->Host = $this->host;  // Specify main and backup SMTP servers // h1.hitme.pl
			$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
			$this->mail->Username = $this->smptuser;                 // SMTP username
			$this->mail->Password = $this->smptPW;                           // SMTP password
			$this->mail->SMTPSecure = $this->port;                            // Enable TLS encryption, `ssl` also accepted
			$this->mail->Port = $this->smptSecure;

			$this->mail->isHTML(true);                                  // Set email format to HTML
			$this->mail->CharSet = $this->charset;
		} catch (Exception $e) {
			// TODO:  logo into error logs
			// use adres recipt to log error
			throw new Exception("Błąd podczas konfiguracji poczty email." );
		}
	}


	public function sendMail($adresRecipient = 'badbart10@wp.pl',$name, $content, $tabFiles = array() ){

		$fromEmail = FROM_EMAIL;//
		$fromName = FROM_NAME;//
		// Passing `true` enables exceptions
		try{
			//Server settings
			$this->configureEmail($adresRecipient);
		}catch (Exception $e){
			echo 'Mailer Error: ' . $e->getMessage();
			return;
		}


		try {

			$this->mail->setFrom($fromEmail, $fromName);
			$this->mail->addReplyTo($fromEmail, $fromName);
			$this->mail->addAddress($adresRecipient, 'Bartosz Jankowski');

			//Attachments
			foreach ($tabFiles as $fil){
				$this->mail->addAttachment($fil);
			} // Optional name

			//Content

			$this->mail->Subject  = 'Wiadomość od zmiana.bjit.pl';
			$this->mail->Body     = '<div><h3>Wiadomość od klienta:</h3><p>'.$name.'</p></div><div><h4>Treść wiadomości:</h4><p>'.$content.'</p></div>';
			$this->mail->AltBody = $content;

			$this->mail->send();
			echo 'Message has been sent to '.$adresRecipient;
		} catch (Exception $e) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		}
	}

	public function sendConfirmationEmail($adres,$password) {
		$fromEmail = "admin@zmiana.bjit.pl";
		$fromName = "Zmiana JRG";

		try{
			$this->configureEmail($adres);
		}catch (Exception $e){
			echo 'Mailer Error: ' . $e->getMessage();
			return;
		}

		try {
			//Recipients
			$this->mail->setFrom($fromEmail, $fromName);
			$this->mail->addReplyTo($fromEmail, $fromName);
			$this->mail->addAddress($adres);

			//Content
			$this->mail->Subject  = 'Dane dostępowe';
			$this->mail->Body     = '<div><h3>Dziekujemy za rejestrację</h3><p>Poniżej znajdują się Twoje dane dostępowe:</p></div><div><h4>Login: '.$adres.'</h4><h4>Hasło: '.$password.'</h4><p>Aby się zalogowac wprowadź dane na stronie <a href="http://zmiana.bjit.pl/login.php">logowania</a></p></div>';
			$this->mail->AltBody = "Dziekujemy za rejestrację \r\n Poniżej znajdują się Twoje dane dostępowe: \r\n Login: ".$adres." \r\n\ Hasło: ".$password." \r\n Aby się zalogowac wprowadź dane na stronie \r\n >> http://zmiana.bjit.pl/login.php \r\n Pozdrawiamy zespół zmiana.bjit.pl ";

			if($this->mail->send()){
				echo 'Poprawnie wysłano wiadomość.';
			} else {
				echo  $this->mail->ErrorInfo;
			}
		} catch (Exception $e) {
			echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		}
	}

	public function sendPasswordChangeInformation($adres){
		$fromEmail = "admin@zmiana.bjit.pl";
		$fromName = "Zmiana JRG";

		try{
			$this->configureEmail($adres);
		}catch (Exception $e){
			echo 'Mailer Error: ' . $e->getMessage();
			return;
		}

		try {
			//Recipients
			$this->mail->setFrom($fromEmail, $fromName);
			$this->mail->addReplyTo($fromEmail, $fromName);
			$this->mail->addAddress($adres);

			//Content
			$this->mail->Subject  = 'Zmiana hasła';
			$this->mail->Body     = '<div><h3>Zmiana hasła do konto</h3><p>Twoje hasło do konta '.$adres.' zostało zmienione.</p></div><div><h4>Uwaga!</h4><p>Jeśli to nie Ty zmieniałeś hasło zawsze możesz je zresetowac na stronie  <a href="http://zmiana.bjit.pl/reset.php">tutaj</a></p></div>';
			$this->mail->AltBody = "Zmiana hasła do konto \r\n Twoje hasło do konta '.$adres.' zostało zmienione. \r\n UWAGA ! \r\n Jeśli to nie Ty zmieniałeś hasło zawsze możesz je zresetowac na stronie \r\n >> http://zmiana.bjit.pl/reset.php \r\n Pozdrawiamy zespół zmiana.bjit.pl ";

			$this->mail->send();
		} catch (Exception $e) {
			echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		}
	}

	public function sendResetPassword($adres,$password ){
		$fromEmail = "admin@zmiana.bjit.pl";
		$fromName = "Zmiana JRG";

		try{
			$this->configureEmail($adres);
		}catch (Exception $e){
			echo 'Mailer Error: ' . $e->getMessage();
			return;
		}

		try {
			//Recipients
			$this->mail->setFrom($fromEmail, $fromName);
			$this->mail->addReplyTo($fromEmail, $fromName);
			$this->mail->addAddress($adres);

			//Content
			$this->mail->Subject  = 'Reset hasła';
			$this->mail->Body     = '<div><h3>Reset hasła</h3><p>W odpowiedzi na Twoje zgłoszenie zresetowaliśmy hasło do twojego konta</p></div><div><h4>Nowe hasło: '.$password.'</h4><p>Aby się zalogowac wprowadź login i hasło na stronie <a href="http://zmiana.bjit.pl/login.php">logowania</a></p></div>';
			$this->mail->AltBody = "Reset hasła \r\n W odpowiedzi na Twoje zgłoszenie zresetowaliśmy hasło do twojego konta: \r\n Nowe hasło: ".$password." \r\n Aby się zalogowac wprowadź dane na stronie \r\n >> http://zmiana.bjit.pl/login.php \r\n Pozdrawiamy zespół zmiana.bjit.pl ";

			$this->mail->send();
		} catch (Exception $e) {
			echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		}
	}
}