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
     if(!$dbUsers->login($_POST['login'],$_POST['pass']))
     {
         echo $dbUsers->error;
     } else {
         header('Location: '.$base_url.'/main.php');
         exit;
     }
}

if(isset($_GET['success'])){
  // $infoAdd = "<h3>Twoje hasło zostało zresetowane. Sprawdź skrzynkę email.</h3>";
}

if(isset($_POST['reset'])){
  $dbUsers = new DBUsers();
  if( $dbUsers->resetPass(
    test_input($_POST['email'])

    ) ){
    header('Location: '.$base_url);
    // header('Location: '.$base_url.'/jrg_zmiana/reset.php?succes=1');
    exit;
  } else {
    $infoAdd = "<h3>" . $dbUsers->error . "</h3>";
  }
}

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
    header('Location: '.$base_url);
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
$dbUsers->checkSession($user);

if(isset($_POST['addJrg'])){
  $db = new DBJednostki();
  $db->createTable();
  if($db->createJrg($_POST['jrg'], $_POST['city'],$_POST['street'],$_POST['nr'], $_POST['email'])){
      if($dbUsers->createJrgAdmin($_POST['email'])){
      $infoAdd .= '<p class="info info__sendMail text-success">Na podany adres email zostały wysłane dane dostępowe do konta.</p>';
        };
    } else {
      $infoAdd = $db->error;
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
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    
    <script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>
      <script type="text/javascript" src="js/ajax.js?ver=<?php echo time() ?>"></script>
        <script type="text/javascript" src="js/ajaxreset2.js?ver=<?php echo time() ?>"></script>
          <script type="text/javascript" src="js/ajaxjrg.js?ver=<?php echo time() ?>"></script>
            <script type="text/javascript" src="js/ajaxregister.js?ver=<?php echo time() ?>"></script>
        
</head>

<body>
<header>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="navbar-nav mr-auto">
                        <div class="nav-item p-2 active">
                                <a class="nav-link active" href="#">
                                    <i class="fas fa-sign-in-alt  nav__ico"></i>
                                        <p class="font-weight-light">Logowanie</p></a>
                        </div>
                          <?php if($user->logged):?>

                        <div class="nav-item p-2 logout__icon">
                            <a class="nav-link" href="<?php echo $base_url; ?>/main.php?logout=1">
                                <i class="fas fa-sign-out-alt nav__ico"></i>
                                    <p class="font-weight-light">Wyloguj się</p></a>
                          </div>
                           <?php else : ?>
                        
                        <div class="nav-item p-2 login__icon">
                            <a class="nav-link" data-toggle="modal" data-target="#myModal2">
                                <i class="fas fa-home nav__ico"></i>+
                                    <p class="font-weight-light">Dodaj jednostkę</p></a>
                                        <div class="register__box">
                                            <p>Nie masz jeszcze konta ?</p>
                                                <a class="register__item" data-toggle="modal" data-target="#myModal1">
                                                    <button type="submit" name="register" class="btn btn-danger btn-lg btn__register">Zarejestruj się
                                                        <i class="fas fa-user-plus nav__ico"></i></button></a>
                                        </div>
                          </div>
                        <div class="nav-item p-2 register__icon__mobile">
                            <a class="nav-link" data-toggle="modal" data-target="#myModal1">
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
         if(!$dbUsers->login(
          $_POST['login'],
          $_POST['password']
          ))
         {
             echo $dbUsers->error;

         } else {
             header('Location: '.$base_url.'/main.php');
             exit;
         }
      }
     ?> 
    <form id="login_form" name="" method="post" action="" class="form">
      <input type="hidden" name="action" value="log_in" />
        <div id="errorlog">
            
        </div>

      <h2>Zaloguj się</h2>

          <?php
          echo $infoAdd;
          ?>
        

      <div class="form-group">
        <label for="exampleInputEmail1">Login / email</label>
          <input id="login" type="login" name="login" value="" class="form-control" required />
      </div>
        <div class="form-group">
          <label for="exampleInputPassword1">Hasło</label>
            <input id="password" type="password" name="password" value="" class="form-control" required />
        </div>
        <button id="log_in" type="submit" name="log_in" action="" class="btn btn-danger btn-lg btn-block btn__login__submit">Zaloguj</button>
  </form>
            <div class="w3-container">
                <a class="w3-left" data-toggle="modal" data-target="#myModal1" target="_self">Rejestracja</a>
                <a class="w3-right" data-toggle="modal" data-target="#myModal3" target="_self">Zapomniałeś hasła?</a>
            </div>

    </div>
</main>

 <div id="myModal1" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Rejestracja użytkownika</h3>
            <button type="button" class="close" data-dismiss="modal">×</button>  
        </div>
              <div class="modal-body col-lg-12">
                        <section class="col-12 intro__section">
                    
                            <div class="col-lg-5 offset-lg-1 col-sm-6 addJrg">
                                                               
                                <h2>Rejestracja</h2>

                                    <form id="formRegister" method="post" action="" class="form-group addJrg__form">
                                        <input type="hidden" name="action" value="register" />
                                            <div id="errorreg"></div>
                                                <div id="success" name="info" value="info"></div>

                                        <label class="text-secondary">Email*</label>
                                            <input type="email" name="login" value="<?php echo $_POST['email'] ?>" class="form-control" required />

                                        <label class="text-secondary">Hasło*</label>
                                            <input type="password" id="pass" name="password" value="" class="form-control" required />

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

                                        <button id="register" type="submit" name="register" class="btn btn-danger btn-lg btn-block btn_register_submit">Zarejestruj</button>
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
              </div>
                        <div class="modal-footer col-lg-12">
                          <button class="btn btn-success" data-dismiss="modal">Zamknij</button>
                        </div>
      </div>
    </div>
  </div>

  <div id="myModal2" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Rejestracja użytkownika</h3>
            <button type="button" class="close" data-dismiss="modal">×</button>  
        </div>
              <div class="modal-body col-lg-12">
                  <section class="col-12 intro__section">
                    <article class="col-lg-5 col-sm-6 description">
                        <p class="description__item text-danger">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> 
                                        Ut porta interdum neque, at vulputate arcu faucibus in. Fusce aliquam eleifend quam, eu aliquam mi pretium id.</br> 
                                        Donec dapibus facilisis neque a placerat. Sed varius eu ipsum ac commodo. Fusce rhoncus molestie condimentum. 
                                        Donec ultrices eleifend lacus id vulputate. Aenean id gravida enim, ut luctus lacus.</br>
                                        Mauris lacus ex, lobortis vel sapien quis, porttitor finibus dolor. 
                                        Nam egestas lorem vel ex tristique ultrices. Ut vel rhoncus magna.
                                        </br></br> 
                    </article>
                    
                            <div class="col-lg-6 offset-lg-1 col-sm-6 addJrg">

<!--                                     Formularz aby dodac jednostkę: nr jrg, miasto i email wymagane.
                                     Tworzy jrg i uzytkownika. Inforumuje o błedach:
                                    # istnieje juz nr jednostki dla tego miasta
                                    # wewnętrzne bł\edy bazy danych
                                    # problem z wysłaniem email -->
                               

                                <form id="Jrg" method="post" action="" class="form-group addJrg__form">
                                  <input type="hidden" name="action" value="addJrg" />
                                      <div id="erroradd"></div>
                                      <div id="infoadd" name="info" value="info"></div>
                                    <h2>Dodaj jednostkę do bazy</h2>

                                    <!-- <?php
                                        echo $infoAdd;
                                    ?> -->

                                    <label  class="text-secondary"> Miasto</label>
                                    <select class="form-control" name="city" required>
                                        <option value="" disabled >Wybierz miasto</option>
                                        <option value="Wrocław" selected>Wrocław</option>
                                        <option value="Poznań">Poznań</option>
                                        <option value="Warszawa">Warszawa</option>
                                    </select>

                                    <label class="text-secondary"> Nr jrg</label>
                                    <input type="text" name="jrg" value="<?php echo $_POST['jrg'] ?>" class="form-control" required />

                                    <label class="text-secondary"> Ulica</label>
                                    <input type="text" name="street" value="<?php echo $_POST['street'] ?>" class="form-control"  />

                                    <label class="text-secondary"> Nr budynku</label>
                                    <input type="text" name="nr" value="<?php echo $_POST['nr'] ?>" class="form-control"  />

                                    <label class="text-secondary"> Administrator (email)</label>
                                    <input type="email" name="email" value="<?php echo $_POST['email'] ?>" class="form-control" placeholder="jan_kowalski@wp.pl" required />

                                    <button id="addJrg" type="submit" name="addJrg" class="btn btn-danger btn-lg btn__addJrg">Dodaj</button>
                                </form>
                            </div>
                    </section>
              </div>
                        <div class="modal-footer col-lg-12">
                          <button class="btn btn-success" data-dismiss="modal">Zamknij</button>
                        </div>
      </div>
    </div>
  </div>


  <div id="myModal3" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Rejestracja użytkownika</h3>
            <button type="button" class="close" data-dismiss="modal">×</button>  
        </div>
              <div class="modal-body col-lg-12">
                  <section class="col-12 intro__section">
                    <div class="reset_box col-lg-5">

                          <div id="errorreset"></div>
                                    <div id="inforeset" name="info" value="info"></div>

                          <form id="reset" method="post" action="">
                            <input type="hidden" name="action" value="reset" />
                                    
                            <h2>Zresetuj hasło</h2>

                            <label  class="w3-text-gray"> Login / email</label>
                            <input type="email" name="email" value="<?php test_input($_POST['email']) ?>" class="w3-input" required />

                            <button id="resetbt" type="submit" name="reset" class="btn btn-danger btn-lg btn__addJrg">Resetuj</button>
                          </form>
                    </div>
                  </section>
              </div>
                        <div class="modal-footer col-lg-12">
                          <button class="btn btn-success" data-dismiss="modal">Zamknij</button>
                        </div>
      </div>
    </div>
  </div>

<?php

require 'footer.php';
