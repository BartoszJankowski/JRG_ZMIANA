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
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
<!--     <script src="/js/jquery.validate.min.js"></script> -->
    <script type="text/javascript" src="ajax.js"></script>
	<script type="text/javascript" src="js/scripts.js?ver=<?php echo time() ?>"></script>

<script>

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('button[data-toggle="popover"]').popover({content:createSelect});
        $('.table-calendar td[data-toggle="popover"]').popover({content:addInfo});

    });

    function createSelect(){
        var typ = this.name;
        return '<form action="" method="post">' +
            '<select class="w3-select w3-border" name="typ">' +
            '<option value="110" '+(typ === "110" ? 'selected disabled':'')+'>Służba Służba Wolne</option>' +
            '<option value="101"  '+(typ === "101" ? 'selected disabled':'')+'>Służba Wolne Służba</option>' +
            '<option value="011" '+(typ === "011" ? 'selected disabled':'')+'>Wolne Służba Służba</option>' +
            '</select>' +
            '<button type="submit" name="editHarmoType" class="w3-input" value="'+this.value+'" >Zmień</button>' +
            '</form>';
    }

    function addInfo(){
        console.log();

        return '<form action="" method="post">' +
            '<input type="hidden" name="data" value="'+$(this).attr("data-jrg")+'">' +
            '<textarea class="w3-input w3-border" name="info" >'+$(this).attr("data-val2")+'</textarea>' +
            '<button type="submit" class="w3-input" name="addUserInfo">Zapisz</button> ' +
            '</form>';
    }


</script>
	
		

</body>
</html>
