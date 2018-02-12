    $("#print").click(function () {
        window.print();
    });

$(".login__icon").mouseenter(function () {
      $(".register__box").fadeIn(400);
});
$(".login__icon").mouseleave(function () {
      $(".register__box").fadeOut(400);

});

    $("#formRegister").validate({
        rules: {
            login : {
                required : true,
	            email : true
            },
            password : {
                required : true,
                minlength : 8
            },
            confirm_password : {
                required : true,
                minlength : 8,
                equalTo : "#password"
            }
        }
    });
