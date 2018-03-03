$(function () {

    $('a.dropdown-item').click(function () {
        var that = this;
        var strId = $(this).attr('data-strid');
        var input = $(this).parent().prev().find('input');
        var prevValue = input.val();
        input.val(strId).next().text($(this).text());
        $(this).parent().children().each(function () {
            if(this === that){
                $(this).toggleClass('w3-pale-blue');
            } else {
                $(this).removeClass('w3-pale-blue');
            }
        });
        checkCurrentOl($(that).parents('ol'));
        countStr(prevValue, strId);
    });

    $(".highlightFireman").on({
        mouseenter: function(){
            $('input.input_onchange[value='+$(this).attr("id").split('_')[2]+']').parent().addClass("w3-gray")
        },
        mouseleave: function(){
            $('input.input_onchange[value='+$(this).attr("id").split('_')[2]+']').parent().removeClass("w3-gray");
        },
        click: function(){
            $('input.input_onchange').parent().removeClass("w3-text-blue");
            $('input.input_onchange[value='+$(this).attr("id").split('_')[2]+']').parent().toggleClass("w3-text-blue");

        }
    });

    $('#slide_type').change(function () {

        if(this.checked){
            $("#dyzury_list").hide();
            $("#dyzury_grafik").show();
        } else {
            $("#dyzury_grafik").hide();
            $("#dyzury_list").show();
        }
    });
    $("#dyzury_grafik input").change(function () {
        sumujGodziny();
    });
    sumujGodziny();
});


function countStr(prevMinus, currentPlus){
    if(prevMinus == currentPlus){
        return;
    }

    if(prevMinus>0){
       var tdContent =  $('#str_id_'+prevMinus).next();
        var newNum = Number( tdContent.text())-1;
        tdContent.text(newNum);
        if(newNum>3){
            tdContent.addClass('w3-red');
        } else {
            tdContent.removeClass('w3-red');
        }
    }

    if(currentPlus>0){
        var tdContent =  $('#str_id_'+currentPlus).next();
        var newNum = Number( tdContent.text())+1;
        tdContent.text(newNum);
        if(newNum>3){
            tdContent.addClass('w3-red');
        } else {
            tdContent.removeClass('w3-red');
        }
    }
}

function checkCurrentOl(olObj){
    var ids = [];
    var check = [];

    $(olObj).find('input.input_onchange').each(function () {
        check.push({td:$(this).parent(),id:this.value});
        ids.push(this.value);
    });

    for(x in check){
        if( check[x].id > -1 ){
            var occurances = 0;
            for(z in ids){
                if(ids[z] === check[x].id){
                    occurances++;
                }
            }
            if(occurances>1){
                check[x].td.addClass('w3-red');
            } else {
                check[x].td.removeClass('w3-red');
            }
        }
    }
}

function sumujGodziny(){
    $("#dyzury_grafik tr").each(function () {
        //var lastTd = $(this).find('td.sumaH');
        //logD($(this).find('input:checked').length);
        var godziny = 24*$(this).find('input:checked').length;
        if(godziny>72)
            $(this).find('td.sumaH').text(godziny+"h").addClass('w3-text-red');
        else
            $(this).find('td.sumaH').text(godziny+"h").removeClass('w3-text-red');
    })
}