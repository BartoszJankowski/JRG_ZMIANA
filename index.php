<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 20.01.2018
 * Time: 11:22
 */
session_start();
require 'config.php';


$infoAdd = null;

if(isset($_POST['addJrg'])){
	$db = new DBJednostki();
	$users = new User();
	$db->createTable();
	if($db->createJrg($_POST['jrg'], $_POST['city'],$_POST['street'],$_POST['nr'], $_POST['email'])){
		$infoAdd = '<h3>Poprawnie dodano jednostkę</h3>';
		if($users->createJrgAdmin($_POST['email'])){
			$infoAdd .= '<p>Na podany adres email zostały wysłane dane dostępowe do konta.</p>';
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">

    </style>
</head>

<body>
    <div class="w3-container w3-third w3-border w3-margin w3-padding-16">
        <!--
            Formularz aby dodac jednostkę: nr jrg, miasto i email wymagane.
             Tworzy jrg i uzytkownika. Inforumuje o błedach:
            # istnieje juz nr jednostki dla tego miasta
            # wewnętrzne bł\edy bazy danych
            # problem z wysłaniem email
        -->
        <form method="post" action="">
            <h2>Dodaj jednostkę do bazy</h2>
            <?php
                echo $infoAdd;
            ?>

            <label  class="w3-text-gray"> Miasto</label>
            <select class="w3-select" name="city" required>
                <option value="" disabled >Wybierz miasto</option>
                <option value="Wrocław" selected>Wrocław</option>
                <option value="Poznań">Poznań</option>
                <option value="Warszawa">Warszawa</option>
            </select>

            <label class="w3-text-gray"> Nr jrg</label>
            <input type="text" name="jrg" value="<?php echo $_POST['jrg'] ?>" class="w3-input" required />

            <label class="w3-text-gray"> Ulica</label>
            <input type="text" name="street" value="<?php echo $_POST['street'] ?>" class="w3-input"  />

            <label class="w3-text-gray"> Nr budynku</label>
            <input type="text" name="nr" value="<?php echo $_POST['nr'] ?>" class="w3-input"  />

            <label class="w3-text-gray"> Administrator (email)</label>
            <input type="email" name="email" value="<?php echo $_POST['email'] ?>" class="w3-input" placeholder="jan_kowalski@wp.pl" required />

            <input class="w3-input w3-margin-top" type="submit" name="addJrg" value="Dodaj"/>
        </form>
    </div>

</body>
</html>