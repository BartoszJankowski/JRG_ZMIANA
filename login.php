<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 22.01.2018
 * Time: 20:52
 */

session_start();
require 'php/config.php';
$dbUsers = new DBUsers();
$user = new User();

if($dbUsers->checkSession($user)){
	header('Location: '.$base_url.'/main.php');
	exit;
}

if(isset($_POST['log_in'])){
     if(!$dbUsers->login($_POST['login'],$_POST['password']))
     {
         echo $dbUsers->error;
     } else {
         header('Location: '.$base_url.'/main.php');
         exit;
     }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
              <link rel="stylesheet" type="text/css" href="css/main.css" />
                <link rel="stylesheet" type="text/css" href="css/login.css" />
                    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" /> 
                        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
</head>

<body>
<header>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="navbar-nav mr-auto">
                          <div class="nav-item p-2">
                            <a class="nav-link " href="<?php echo $base_url; ?>">
                                <i class="fas fa-home nav__ico"></i>
                                    <p class="font-weight-light">Strona główna</p></a>
                          </div>
                          <?php if($user->logged):?>

                        <div class="nav-item p-2 logout__icon">
                            <a class="nav-link" href="<?php echo $base_url; ?>/main.php?logout=1">
                                <i class="fas fa-sign-out-alt nav__ico"></i>
                                    <p class="font-weight-light">Wyloguj się</p></a>
                          </div>
                           <?php else : ?>
                        <div class="nav-item p-2 login__icon active">
                                <a class="nav-link active" href="#">
                                    <i class="fas fa-sign-in-alt  nav__ico"></i>
                                        <p class="font-weight-light">Logowanie</p></a>
<!--                             <div class="register__box">
                                <p>Nie masz jeszcze konta ?</p>
                                    <a class="register__item" href="<?php echo $base_url; ?>/register.php">
                                        <button type="submit" name="register" class="btn btn-danger btn-lg btn__register">  Zarejestruj się <i class="fas fa-user-plus nav__ico"></i></button></a> -->
                            <!-- </div> -->
                        </div>
                        <div class="nav-item p-2 register__icon__mobile">
                            <a class="nav-link" href="<?php echo $base_url; ?>/register.php">
                                <i class="fas fa-user-plus nav__ico"></i>
                                    <p class="font-weight-light">Zarejestruj się</p></a>
                          </div>
                           <?php endif; ?>
                       
                    </div>
                  </div>
        </nav>
       <h1>Harmonogram / Rozkaz Dzienny / Kalendarz zmianowy</h1>
</header>

<main>

	<div class="col-lg-3 col-sm-6 col-xs-6 login">
        <?php
         if(isset($_POST['log_in'])){
         if(!$dbUsers->login($_POST['login'],$_POST['password']))
         {
             echo $dbUsers->error;
                       // popraw !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
         } else {
             header('Location: '.$base_url.'/main.php');
             exit;
         }
      }
     ?>
	<form method="post" action="">
      <h2>Zaloguj się</h2>
          <?php
          echo $infoAdd;
          ?>
      <div class="form-group">
        <label for="exampleInputEmail1">Login / email</label>
          <input type="email" name="login" value="<?php echo $_POST['email'] ?>" class="form-control" required />
      </div>
        <div class="form-group">
          <label for="exampleInputPassword1">Hasło</label>
            <input type="password" name="password" value="" class="form-control" required />
        </div>
        <button type="submit" name="log_in" class="btn btn-danger btn-lg btn-block btn__login__submit">Zaloguj</button>
<!--   </form> -->
			<div class="w3-container">
				<a class="w3-left" href="register.php" target="_self">Rejestracja</a>
				<a class="w3-right" href="reset.php" target="_self">Zapomniałeś hasła?</a>
			</div>
		</form>
	</div>
</main>
<?php

require 'footer.php';

