<?php

/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 26.01.2018
 * Time: 19:28
 */


$t = str_replace(array('/','.php'),'',$_SERVER['PHP_SELF']);
$$t = ' w3-green ';
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
            <link rel="stylesheet" type="text/css" href="css/maine.css" />
                <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" /> 
                    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;subset=latin-ext" rel="stylesheet">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
</head>

<body >
<header class="w3-top"> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
        </button>
                    <div class="collapse navbar-collapse nav__mobile" id="navbarSupportedContent">
                        <div class="navbar-nav nav__bar mr-auto">
                            <div <?php if ($activePage =="main") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                    <a href="main.php" <?php if ($activePage =="main") {?> class="nav-link active" <?php } ?>class="nav-link" class="nav-link">
                                        <i class="fas fa-user nav__ico"></i>
                                            <p class="font-weight-light">Konto</p></a>
                            </div>
        		<?php 
                    if($user->isAdmin()): 
                ?>
                                <div <?php if ($activePage =="jrgmanage") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                        			<a  href="jrgmanage.php" <?php if ($activePage =="jrgmanage") {?> class="nav-link active" <?php } ?> class="nav-link">
                                        <i class="fas fa-sitemap nav__ico"></i>
                                            <p class="font-weight-light">Zarządzaj JRG</p></a>
                                </div>  
                                        
                                		<?php endif;
                                            if($user->isChef() ):
                                		?>

                                        <div <?php if ($activePage =="shiftmanage") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                			<a  href="shiftmanage.php" <?php if ($activePage =="shiftmanage") {?> class="nav-link active" <?php } ?> class="nav-link <?php echo $shiftmanage;?> ">
                                                <i class="fas fa-users nav__ico"></i>
                                                    <p class="font-weight-light">Zarządzaj zmianą</p></a>
                                        </div>
                                            <div <?php if ($activePage =="grafiksluzb") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                            	<a href="grafiksluzb.php" <?php if ($activePage =="grafiksluzb") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $grafiksluzb;?> ">
                                                    <i class="fas fa-newspaper nav__ico"></i>
                                                        <p class="font-weight-light">Grafik</p></a>
                                            </div>
                                                <div <?php if ($activePage =="harmonogramsluzb") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                                    <a href="harmonogramsluzb.php" <?php if ($activePage =="harmonogramsluzb") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $harmonogramsluzb;?> ">
                                                        <i class="fas fa-history nav__ico"></i>
                                                            <p class="font-weight-light">Harmonogram</p></a>
                                                </div>
                                                    <div <?php if ($activePage =="edycjarozkazu") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                                        <a href="edycjarozkazu.php" <?php if ($activePage =="edycjarozkazu") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $edycjarozkazu;?> ">
                                                            <i class="fas fa-list-alt nav__ico"></i>
                                                                <p class="font-weight-light">Rozkaz</p></a>
                                                    </div>
                                                        <div <?php if ($activePage =="homeduties") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                                            <a href="homeduties.php" <?php if ($activePage =="homeduties") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $homeduties;?> ">
                                                                <i class="fas fa-bed nav__ico"></i>
                                                                     <p class="font-weight-light">Dyzury domowe</p></a>
                                                        </div>
                                                        
                                                    		<?php endif; ?>

                                                            <div <?php if ($activePage =="mojkalendarz") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                                                <a  href="mojkalendarz.php" <?php if ($activePage =="mojkalendarz") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $mojkalendarz;?> ">
                                                                    <i class="fas fa-calendar-alt nav__ico" aria-hidden="true"></i>
                                                                        <p class="font-weight-light">Kalendarz</P></a>
                                                            </div>
                                                                <div <?php if ($activePage =="grafiksluzb") {?> class="nav-item p-2 active" <?php } ?> class="nav-item p-2">
                                                                    <a  href="main.php?account_settings" <?php if ($activePage =="grafiksluzb") {?> class="nav-link active" <?php } ?> class="nav-link  <?php echo $main;?> ">
                                                                        <i class="fas fa-cog nav__ico"></i>
                                                                            <p class="font-weight-light">Ustawienia</p></a>
                                                                </div>
                                                                     <div class="nav-item p-2 logout__icon">
                                                                        <a href="logout.php?logout=1" class="nav-link  <?php echo $logout;?> ">
                                                                            <i class="fas fa-sign-out-alt nav__ico"></i>
                                                                              <p class="font-weight-light">Wyloguj się</p></a>
                                                                    </div>
                    </div>
                </div>
    </nav>
</header>

