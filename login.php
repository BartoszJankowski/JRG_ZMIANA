<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 11:22
 */

session_start();
require 'php/config.php';

$dbUsers = new DBUsers();
$user = new User();
$dbUsers->checkSession($user);

$infoAdd = null;

if(isset($_POST['addJrg'])){
  $db = new DBJednostki();
  $db->createTable();
  if($db->createJrg($_POST['jrg'], $_POST['city'],$_POST['street'],$_POST['nr'], $_POST['email'])){
    $infoAdd = '<p class="alert alert-success info__sendMail" role="alert"><strong>Świetnie ! - </strong> Poprawnie dodano jednostkę  ✔</p>';
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
    <title>Zmiana-main</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
                    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                            <link rel="stylesheet" type="text/css" href="css/main.css">
                                <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> 
                                    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <script type="text/javascript" src="ajaxjrg.js?ver=<?php echo time() ?>"></script>
    <script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>

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
                      <div class="nav-item p-2 active ">
                          <a class="nav-link active" href="<?php echo $base_url; ?>/login.php">
                              <i class="fas fa-home nav__ico"></i>+
                                  <p class="font-weight-light">Dodawanie jednostki</p></a>
                          </div>
                          <?php if($user->logged):?>

                        <div class="nav-item p-2">
                            <a class="nav-link" href="<?php echo $base_url; ?>/main.php">
                                <i class="fas fa-user nav__ico"></i>
                                    <p class="font-weight-light">Konto</p></a>
                          </div>
                          <div class="nav-item p-2 logout__icon">
                              <a class="nav-link" href="<?php echo $base_url; ?>/main.php?logout=1">
                                  <i class="fas fa-sign-out-alt nav__ico"></i>
                                      <p class="font-weight-light">Wyloguj się</p></a>
                            </div>
                           <?php else : ?>
                            <div class="nav-item p-2 login__icon">
                                <a class="nav-link" href="<?php echo $base_url; ?>">
                                  <i class="fas fa-sign-in-alt  nav__ico"></i>
                                    <p class="font-weight-light">Zaloguj się</p></a>
                            <div class="register__box">
                                <p>Nie masz jeszcze konta ?</p>
                                    <a class="register__item" href="<?php echo $base_url; ?>/register.php">
                                        <button type="submit" name="register" class="btn btn-danger btn-lg btn__register">  Zarejestruj się <i class="fas fa-user-plus nav__ico"></i></button></a>
                            </div>
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
                    <p class="intro">Witaj ! Dołącz do naszej społeczności i bądź na bieżąco z informacjami</p>
        </header>
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
                                      <div id="error"></div>
                                      <div id="success" name="info" value="info"></div>
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
    </main>
<?php

require 'footer.php';
 // -->