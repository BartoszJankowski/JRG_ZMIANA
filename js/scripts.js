
$(function () {
    $("#print").click(function () {
        window.print();
    });

    $(".login__icon").mouseenter(function () {
        $(".register__box").fadeIn(400);
    }).mouseleave(function () {
        $(".register__box").fadeOut(400);

    });

    $(".nextMsc").click(function () {
        var tabs = $('.kalendar-div').toArray();
        var parentDiv = $(this).parent().parent().get(0);

        for(var i=0; i<tabs.length ;i++){
            if(parentDiv.id === tabs[i].id){
                if((i+1) === tabs.length){
                    //koniec - zaladowac nowy msc
                    $("#nextYear").submit();
                } else {
                    $(parentDiv).toggleClass('w3-hide-small');
                    $(tabs[i+1]).toggleClass('w3-hide-small');
                    return;
                }
            }
        }
    });

    $(".prevMsc").click(function () {
        var tabs = $('.kalendar-div').toArray();
        var parentDiv = $(this).parent().parent().get(0);

        for(var i=0; i<tabs.length ;i++){
            if(parentDiv.id === tabs[i].id){
                if((i-1) === -1){
                    //koniec - zaladowac nowy msc
                    $("#prevYear").submit();
                } else {
                    $(parentDiv).toggleClass('w3-hide-small');
                    $(tabs[i-1]).toggleClass('w3-hide-small');
                    return;
                }
            }
        }
    });
});

