<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 23:40
 */

session_start();
require 'php/config.php';
$dbUsers     = new DBUsers();
$dbJednostki = new DBJednostki();
$dbStrazacy = new DBStrazacy();
$user = new User();

if(isset($_GET['logout'])){
	$dbUsers->destroySession($user);
	header('Location: '.$base_url.'/index.php');
	exit;
}