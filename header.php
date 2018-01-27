<?php

/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 26.01.2018
 * Time: 19:28
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <title><?php echo $title ; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="img/jrg.ico"/>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
        .error  {
            color:red;
        }
        #settings {
            display: none;
        }
        th.Nd, th.Sob {
            background-color: rgb(200,200,200);
        }
        td.Nd, td.Sob {
            background-color: rgba(200,200,200,0.3);
        }

        td.tdHarmCell {
            padding: 0px !important;
        }
        td.tdHarmCell > label {
            cursor: pointer;
        }
        input.harmoCheck:checked ~div.harmoCell {
           background-color: rgba(255,0,0,0.5);
        }
        div.harmoCell {
            padding: 8px !important;
        }
        main {
            min-height: 600px;
        }
        .bottom {
            position: absolute;
            bottom: 0;
            left:0;
        }


    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
</head>

<body >
<header>

	<div class="w3-bar w3-border w3-light-grey">
		<a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button"><i class="fa fa-fw fa-home w3-xlarge"></i><div class="w3-small">Strona główna</div></a>
		<?php if($user->isAdmin()): ?>
			<a  href="jrgmanage.php" class="w3-bar-item w3-button"><i class="fa fa-fw fa-users w3-xlarge"></i><div class="w3-small">Zarządzaj JRG</div></a>
		<?php
		endif;

		if($user->isChef() ):
			?>
			<a  href="shiftmanage.php" class="w3-bar-item w3-button"><i class="fa fa-fw fa-users w3-xlarge"></i><div class="w3-small">Zarządzaj zmianą</div></a>
			<a href="" class="w3-bar-item w3-button"><i class="fa fa-fw fa-calendar w3-xlarge"></i><div class="w3-small">Grafik</div></a>
            <a href="harmonogram.php" class="w3-bar-item w3-button"><i class="fa fa-fw fa-history w3-xlarge"></i><div class="w3-small">Harmonogram</div></a>
            <a href="" class="w3-bar-item w3-button"><i class="fa fa-fw fa-list-alt w3-xlarge"></i><div class="w3-small">Rozkaz</div></a>
		<?php
		endif;
		?>
		<a  href="main.php?account_settings" class="w3-bar-item w3-button"><i class="fa fa-fw fa-cog w3-xlarge"></i><div class="w3-small">Ustawienia</div></a>
		<a href="main.php?logout=1" class="w3-bar-item w3-button"><i class="fa fa-fw fa-sign-out w3-xlarge"></i><div class="w3-small">Wyloguj</div></a>
	</div>
</header>
