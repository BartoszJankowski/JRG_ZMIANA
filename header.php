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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
        hr {
            margin:30px 0px !important;
        }
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
        .w3-xsmall {
            font-size: 10px !important;
        }
        table.table-grafik {
            width:auto !important;
            display: inline-block;
        }
        table.table-grafik th {
            text-align: center;
        }
        table.table-grafik td,table.table-grafik th {
            border-left: 1px solid rgba(220,220,220,0.8);
            padding:4px !important;
        }
        table.table-grafik td.scale {
            width:60px;
            text-align: center;
        }
        .my-own-select {
            padding:2px 8px;
            border:none;
            background: transparent;
            background-image: none;
            -webkit-appearance: none;
            text-align: center;
        }
        .my-own-select option {
            text-align: center;
        }
        table.table-calendar {
            width: 100%;
        }
        table.table-calendar tr td {
            border:1px solid rgba(220,220,220,0.6);
            width:14.2%;
            height: 40px;
        }
        table.table-calendar tr:first-child td {
            border:none !important;
        }
        .zmiana-1 {
          background-color: rgba(255, 255, 153,0.6);
        }
        .zmiana-2 {
            background-color: rgba(153, 255, 102,0.6);

        }
        .zmiana-3 {
            background-color: rgba(255, 153, 153,0.6);
        }

        /*
        Tymczasowe klasy do szablonu rozkazu
         */
        .szablon_element {
            display: inline-block;
            border-radius:5px;
            padding:4px;
            margin:4px;
            white-space: nowrap ;
        }
        .jrg_const {
            background-color: #999999;
        }
        .jrg_var {
            background-color: #c69500;
        }

        .jrg_list {
            background-color: #9fcdff;
        }
        .jrg_obj {
            background-color: #1e7e34;
        }
        .align-right {
            text-align: right;
        }
        .align-left {
            text-align: left;
        }
        .no-margin {
            margin:2px;
        }

        .podglad-rozkazu select {
           border:none;
            -webkit-appearance: none;
            background-color: transparent;
            min-width:150px;
        }
        /*
        * Styl definiowany odrębnie w zmiennej $style na podstronach
        * (zmienna powinna znajdowac się zaraz przed wczytaniem header.php)
         */
        <?php echo $style; ?>

    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
</head>

<body   >
<header class="w3-top">

	<div class="w3-bar w3-border w3-light-grey w3-hide-small w3-hide-medium">
		<a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button"><i class="fa fa-fw fa-home w3-xlarge"></i><div class="w3-small">Strona główna</div></a>
		<?php if($user->isAdmin()): ?>
			<a  href="jrgmanage.php" class="w3-bar-item w3-button <?php echo $jrgmanage;?> "><i class="fa fa-fw fa-users w3-xlarge"></i><div class="w3-small">Zarządzaj JRG</div></a>
		<?php
		endif;

		if($user->isChef() ):
			?>
			<a  href="shiftmanage.php" class="w3-bar-item w3-button <?php echo $shiftmanage;?> "><i class="fa fa-fw fa-users w3-xlarge"></i><div class="w3-small">Zarządzaj zmianą</div></a>
			<a href="grafiksluzb.php" class="w3-bar-item w3-button  <?php echo $grafiksluzb;?> "><i class="fa fa-fw fa-calendar w3-xlarge"></i><div class="w3-small">Grafik</div></a>
            <a href="harmonogramsluzb.php" class="w3-bar-item w3-button  <?php echo $harmonogramsluzb;?> "><i class="fa fa-fw fa-history w3-xlarge"></i><div class="w3-small">Harmonogram</div></a>
            <a href="rozkazpodglad.php" class="w3-bar-item w3-button  <?php echo $edycjarozkazu;?> "><i class="fa fa-fw fa-list-alt w3-xlarge"></i><div class="w3-small">Rozkaz</div></a>
            <a href="homeduties.php" class="w3-bar-item w3-button  <?php echo $homeduties;?> "><i class="fa fa-fw fa-bed w3-xlarge"></i><div class="w3-small">Dyzury domowe</div></a>
		<?php
		endif;
		?>
        <a  href="mojkalendarz.php" class="w3-bar-item w3-button  <?php echo $mojkalendarz;?> "><i class="fa fa-fw fa-address-book w3-xlarge" aria-hidden="true"></i><div class="w3-small">Kalendarz</div></a>
        <a  href="main.php?account_settings" class="w3-bar-item w3-button  <?php echo $main;?> "><i class="fa fa-fw fa-cog w3-xlarge"></i><div class="w3-small">Ustawienia</div></a>
		<a href="logout.php?logout=1" class="w3-bar-item w3-button  <?php echo $logout;?> "><i class="fa fa-fw fa-sign-out-alt w3-xlarge"></i><div class="w3-small">Wyloguj</div></a>
	</div>

    <div class="w3-bar  w3-border w3-light-grey  w3-hide-large">
        <button class="w3-bar-item w3-button w3-xlarge w3-hover-theme" onclick="openSidebar()">&#9776;</button>
        <span class="w3-bar-item w3-xlarge"><?php echo $title ?></span>
    </div>

    <nav id="sidebar" class="w3-sidebar w3-bar-block w3-card" style="width:60%;display: none;">
        <a href="<?php echo $base_url; ?>" class="w3-bar-item w3-button"><i class="fa fa-fw fa-home w3-xlarge"></i><span class="w3-small">Strona główna</span></a>
		<?php if($user->isAdmin()): ?>
            <a  href="jrgmanage.php" class="w3-bar-item w3-button <?php echo $jrgmanage;?> "><i class="fa fa-fw fa-users w3-xlarge"></i><span class="w3-small">Zarządzaj JRG</span></a>
		<?php
		endif;
		if($user->isChef() ):
			?>
            <a  href="shiftmanage.php" class="w3-bar-item w3-button <?php echo $shiftmanage;?> "><i class="fa fa-fw fa-users w3-xlarge"></i><span class="w3-small">Zarządzaj zmianą</span></a>
            <a href="grafiksluzb.php" class="w3-bar-item w3-button  <?php echo $grafiksluzb;?> "><i class="fa fa-fw fa-calendar w3-xlarge"></i><span class="w3-small">Grafik</span></a>
            <a href="harmonogramsluzb.php" class="w3-bar-item w3-button  <?php echo $harmonogramsluzb;?> "><i class="fa fa-fw fa-history w3-xlarge"></i><span class="w3-small">Harmonogram</span></a>
            <a href="" class="w3-bar-item w3-button  <?php echo "";?> "><i class="fa fa-fw fa-list-alt w3-xlarge"></i><span class="w3-small">Rozkaz</span></a>
		<?php
		endif;
		?>
        <a  href="mojkalendarz.php" class="w3-bar-item w3-button  <?php echo $mojkalendarz;?> "><i class="fa fa-fw fa-address-book-o w3-xlarge" aria-hidden="true"></i><span class="w3-small">Kalendarz</span></a>
        <a  href="main.php?account_settings" class="w3-bar-item w3-button  <?php echo $main;?> "><i class="fa fa-fw fa-cog w3-xlarge"></i><span class="w3-small">Ustawienia</span></a>
        <a href="logout.php?logout=1" class="w3-bar-item w3-button  <?php echo $logout;?> "><i class="fa fa-fw fa-sign-out w3-xlarge"></i><span class="w3-small">Wyloguj</span></a>

    </nav>
    <script>
        function openSidebar(){
            $("#sidebar").toggle();
        }
    </script>
</header>

<hr>
