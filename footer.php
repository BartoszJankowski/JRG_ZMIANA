<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 15:35
 */

$proceedTime = round(microtime(true) - $timeStart,3);

?>
	<footer class="footer">
		<div class="container">
				<?php
				echo 'Wygenerowano w '.$proceedTime.'s';
				?>
	        <button class="w3-button w3-gray w3-right" id="print" title="Drukuj"><i class="fa fa-print w3-large" aria-hidden="true"></i></button>
		</div>
	</footer>
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
	<script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>
	<script src="/js/jquery.validate.min.js"></script>
	
		

</body>
</html>
