<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 23.01.2018
 * Time: 13:29
 */

require 'php/config.php';

if(isset($_POST['register'])){
	$dbusers = new DBUsers();
	if($dbusers->registerNewUser(
		test_input($_POST['login']),
		test_input($_POST['password']),
		test_input($_POST['confirm_password']),
		test_input($_POST['jrg']),
		test_input($_POST['name']),
		test_input($_POST['surname'])
	) ) {
		header('Location: '.$base_url.'/register.php?succes');
		exit;
	} 
    else {
		echo $dbusers->error;
	}
}

if(isset($_GET['succes'])){
	$infoAdd = '<h3>Twoje konto zostało utworzone. Możesz się zalogować <a href="http://zmiana.bjit.pl/login.php">Tutaj</a>. Na Twoją pocztę zostało wysłane potwierdzenie rejestracji.</h3>';

}


$jednostki = new DBJednostki();
$res = $jednostki->getJrgList();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Zmiana-login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
                    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" /> 
                        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
                         <link rel="stylesheet" type="text/css" href="css/main.css" />
                            <link rel="stylesheet" type="text/css" href="css/login.css" />
</head>

<body>
<main>
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
                                    <i class="fas fa-user-plus  nav__ico"></i>
                                        <p class="font-weight-light">Rejestracja</p></a>
                        </div>
                           <?php endif; ?>
                       
                    </div>
                  </div>
        </nav>
       <h1>Harmonogram / Rozkaz Dzienny / Kalendarz zmianowy</h1>
        <p class="intro">Zarejestruj się i skorzystaj ze wszystkich możliwości aplikacji !</p>
    </header>
        <section class="col-12 intro__section">
                    
                            <div class="col-lg-5 offset-lg-1 col-sm-6 addJrg">
                            
                            
                            <?php if (!empty($dbusers->error)) { ?>
                                <div  class="alert alert-danger info__wrong" role="alert">
                                     <?php echo $dbusers->error; ?>
                                </div>
                            <?php } ?>
                           <!--  dodałem taka stylizacje komunikatu errora -->

                                        <?php
                                            echo $infoAdd;
                                        ?>
                                <h2>Rejestracja</h2>
                                    <form method="post" action="" class="form-group addJrg__form" id="formRegister">

                                        <label  class="text-secondary">Email*</label>
                                            <input type="email" name="login" value="<?php echo $_POST['email'] ?>" class="form-control" required />

                                                <label class="text-secondary">Hasło*</label>
                                                    <input type="password" id="password" name="password" value="" class="form-control" required />

                                                        <label class="text-secondary"> Powtórz hasło*</label>
                                                            <input type="password" name="confirm_password" value="" class="form-control" required />

                                                                <label  class="text-secondary"> Imię</label>
                                                                    <input type="text" name="name" value="" class="form-control"  />

                                                                        <label  class="text-secondary"> Nazwisko</label>
                                                                            <input type="text" name="surname" value="" class="form-control"  />

                                                                                <label class="text-secondary">Wybierz jednostkę</label>
                                                                                    <select name="jrg" class="form-control register__select__jrg">
                                                                                        <option disabled selected> Rozwiń listę JRG</option>
                                                                                            <?php
                                                                                                if(is_array($res)){
                                                                                                    foreach($res as $id=>$val){
                                                                                                        echo '<option value="'.$id.'"> jrg '.$val.'</option>';
                                                                                                    }
                                                                                                }
                                                                                            ?>
                                                                                    </select>

                                            <p class="info__required">* dane wymagane do rejestracji użytkownika</p>
                                        <button type="submit" name="register" class="btn btn-danger btn-lg btn-block btn_register_submit">Zarejestruj</button>
                                </form>
                            </div>

            <article class="col-lg-5 offset-lg-1 col-sm-6 description">
                <p class="description__item text-danger">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> 
                                Ut porta interdum neque, at vulputate arcu faucibus in. Fusce aliquam eleifend quam, eu aliquam mi pretium id.</br> 
                                - Donec dapibus facilisis neque a placerat. </br></br>
                                - Sed varius eu ipsum ac commodo. </br>
                                - Fusce rhoncus molestie condimentum. </br>
                                - Donec ultrices eleifend lacus id vulputate. </br>
                                - Aenean id gravida enim, ut luctus lacus.</br>
                                - Mauris lacus ex, lobortis vel sapien quis, porttitor finibus dolor.</br></br> 
                                    Nam egestas lorem vel ex tristique ultrices. Ut vel rhoncus magna.
                                </br></br> 
            </article>
        </section>
</main>

<?php

require 'footer.php';


