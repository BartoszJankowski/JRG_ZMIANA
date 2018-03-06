<?php

/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 26.01.2018
 * Time: 19:28
 */


$t = str_replace(array('/','.php'),'',$_SERVER['PHP_SELF']);
$$t = ' active ';
function isActivePage($nazwa){global $t, $$t; echo $$nazwa;}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <title><?php echo $title ; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="img/jrg.ico"/>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="css/main.css" />
            <link rel="stylesheet" type="text/css" href="css/style.css?ver=<?php echo time(); ?>" />
            <link rel="stylesheet" type="text/css" href="css/maine.css?ver=1.1" />
                <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" /> 
                    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        <?php  echo $style; ?>
    </style>
    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script defer type="text/javascript" src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <script type="text/javascript" src="js/ajax.js?ver=<?php echo time() ?>"></script>
    <script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>
</head>

<body >
<header class="w3-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
        </button>
                    <div class="collapse navbar-collapse nav__mobile" id="navbarSupportedContent">
                        <div class="navbar-nav nav__bar mr-auto">
                            <div class="nav-item p-2 <?php isActivePage('main') ?>">
                                    <a href="main.php"  class="nav-link <?php isActivePage('main') ?>">
                                        <i class="fas fa-user nav__ico"></i>
                                            <p class="font-weight-light">Konto</p></a>
                            </div>
        		<?php 
                    if($user->isAdmin()): 
                ?>
                                <div class="nav-item p-2 <?php isActivePage('jrgmanage') ?>">
                        			<a  href="jrgmanage.php" class="nav-link <?php isActivePage('jrgmanage') ?>">
                                        <i class="fas fa-sitemap nav__ico"></i>
                                            <p class="font-weight-light">Zarządzaj JRG</p></a>
                                </div>  
                                        
                                		<?php endif;
                                            if($user->isChef() ):
                                		?>

                                        <div class="nav-item p-2 <?php isActivePage('shiftmanage') ?>">
                                			<a  href="shiftmanage.php" class="nav-link <?php isActivePage('shiftmanage') ?> ">
                                                <i class="fas fa-users nav__ico"></i>
                                                    <p class="font-weight-light">Zarządzaj zmianą</p></a>
                                        </div>

                                                        <div  class="nav-item p-2 <?php isActivePage('homeduties') ?>">
                                                            <a href="homeduties.php"  class="nav-link <?php isActivePage('homeduties') ?> ">
                                                                <i class="fas fa-bed nav__ico"></i>
                                                                     <p class="font-weight-light">Dyzury domowe</p></a>
                                                        </div>
                                                        
                                            <?php endif; ?>
                            <div  class="nav-item p-2 <?php isActivePage('mojkalendarz') ?>">
                                <a  href="mojkalendarz.php"  class="nav-link  <?php isActivePage('mojkalendarz') ?> ">
                                    <i class="fas fa-calendar-alt nav__ico" aria-hidden="true"></i>
                                    <p class="font-weight-light">Kalendarz</P></a>
                            </div>
                            <div class="nav-item p-2 <?php isActivePage('grafiksluzb') ?>">
                                <a href="grafiksluzb.php" class="nav-link <?php isActivePage('grafiksluzb') ?> ">
                                    <i class="fas fa-newspaper nav__ico"></i>
                                    <p class="font-weight-light">Grafik</p></a>
                            </div>
                            <div  class="nav-item p-2 <?php isActivePage('rozkazpodglad') ?>">
                                <a href="rozkazpodglad.php"  class="nav-link  <?php isActivePage('rozkazpodglad') ?> ">
                                    <i class="fas fa-list-alt nav__ico"></i>
                                    <p class="font-weight-light">Rozkaz</p></a>
                            </div>

                            <div class="nav-item p-2 <?php isActivePage('harmonogramsluzb') ?>">
                                <a href="harmonogramsluzb.php" class="nav-link  <?php isActivePage('harmonogramsluzb') ?> ">
                                    <i class="fas fa-history nav__ico"></i>
                                    <p class="font-weight-light">Harmonogram</p></a>
                            </div>
                            <div  class="nav-item p-2 <?php isActivePage('ustawienia') ?>">
                                <a  href="ustawienia.php"  class="nav-link  <?php isActivePage('ustawienia') ?> ">
                                    <i class="fas fa-cog nav__ico"></i>
                                    <p class="font-weight-light">Ustawienia</p></a>
                            </div>

                            <div class="nav-item p-2 logout__icon">
                                <a href="logout.php?logout=1" class="nav-link ">
                                    <i class="fas fa-sign-out-alt nav__ico"></i>
                                    <p class="font-weight-light">Wyloguj się</p></a>
                            </div>


                    </div>
                </div>
    </nav>
</header>

