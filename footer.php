<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 27.01.2018
 * Time: 15:35
 */

$proceedTime = round(microtime(true) - $timeStart,2);

?>
<footer class=" w3-light-grey w3-padding-small w3-small bottom" style="width: 100%;">
	<div class="w3-row">
		<div class="w3-col l3">
			<?php
			echo 'Wygenerowano w '.$proceedTime.'s';
			?>
		</div>
	</div>
</footer>
</body>

<script>
</script>
</html>
