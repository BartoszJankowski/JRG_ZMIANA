
var startSelect = false;
var lastSelected = {tr:null,input:null};

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



    $("[name='deleteFireman']").on('click',function (event) {
        if(!confirm('Potwierdź usunięcie strażaka - wszystkie dane zostaną nieodwracalnie utracone.')){
            event.stopPropagation();
            event.preventDefault();
        }
    });

    //TEN KOD DOTYCZY HARMONOGRAMU i ZAZNACZANIA KOMÓREK
   $('td.tdHarmCell').mousedown(function(event){
       if(event.button == 2) {return}
       startSelect = true;
       var inp = $(this).find('input.harmoCheck').get(0);
       inp.checked = !inp.checked;
   }).mouseenter(function (event) {
        if(startSelect){
            var inp = $(this).find('input.harmoCheck').get(0);
            inp.checked = !inp.checked;
        }
   });
   $('.harmoCell input').mousedown(function (event) {
       logD('siema');
       event.stopPropagation();
   });
   window.addEventListener("mouseup",function (ev) {
       startSelect = false;
   })

});


function sortTable(jQtable, n) {

    var  rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = jQtable.get(0);
    switching = true;
    logD(table);
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
    no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /* Loop through all table rows (except the
        first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
            one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
            based on the direction, asc or desc: */
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
            and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount ++;
        } else {
            /* If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
    countLp(table);
}

function countLp(table){
    var i = 1;
    $(table).find('tr td:first-child').each(function () {
        $(this).text(i++);
    })
}

function usunUpr(btn){
    var type = $(btn).attr('data-type');
    var idUpr = $(btn).val();
    $.ajax({
        type:'POST',
        data:{
            action:'delete',
            type:type,
            id:idUpr
        },
        url:'jrgmanage.php',
        beforeSend : function(xhr){
            btn.disabled = true;
            $(btn).html('<i class="w3-spin fas fa-spinner"></i>');
        },
        success : function (response) {
            logD(response);
            if(response.result){
                $(btn).tooltip('dispose').parents('li').remove();
            } else if(response.error) {
                logD(response.errorMsg);
            }
        },
        error : function () {

        },
        complete : function (xhr, status) {
            $(btn).html('<i class="far fa-trash-alt"></i>');
            btn.disabled = false;
        }
    });
}

var values = {
    typ : '',
    btn : null,
    icons : ['fas fa-ambulance','fas fa-asterisk','fas fa-bolt',
        'fas fa-bus','fas fa-car','fas fa-chess-king',
        'fas fa-chess-rook','fas fa-child','fas fa-fighter-jet','fas fa-truck','fas fa-life-ring'],

    /**
     * Funkcja zwraca html do popover
     * @param btn
     * @returns {string}
     */
    createNewValue : function (button) {
        this.typ = $(button).attr("data-type");
        this.btn = button;
        return '<form onsubmit="return values.checkForm(this)" action="#settings" method="post"><input type="hidden" name="action" value="addVal" /> ' +
            this.getTag() +
            '<div class="input-group mb-3">\n' +
            '  <div class="input-group-prepend">\n' +
            '    <span class="input-group-text" id="basic-addon3">Nazwa</span>\n' +
            '  </div>\n' +
            '  <input type="text" required class="form-control" id="basic-url" name="name" aria-describedby="basic-addon3">\n' +
            '</div> ' +
            '                                 ' + values.getIcons() + values.getDesc() + values.getColor() +
            '</div>' + this.getSubmitBtn() +
            '</form>';
    },
    getTag : function(){
        if(this.typ !== 'uprawnienie') {
            return '<div class="input-group mb-3">\n' +
                '  <div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="basic-addon3">Tag</span>\n' +
                '  </div>\n' +
                '  <input type="text" required class="form-control" id="basic-url" name="id" minlength="0" maxlength="3" aria-describedby="basic-addon3">\n' +
                '</div> '
        } else {
            return '';
        }
    },
    getIcons :function(){
        if(this.typ === 'uprawnienie'){
            var inn = '';
            for(x in this.icons){
                inn += '<a class="p-2 w3-text-black w3-large" href="#" onclick="values.setIcon(this)" data-icon="'+this.icons[x]+'" ><i class="'+this.icons[x]+'"></i></a>';
            }
            return '<div class="input-group mb-3">' +
                '  <div class="input-group-prepend">' +
                '    <input required type="hidden" name="icon"><button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ikona</button>\n' +
                '    <div class="dropdown-menu">' +
                '      <div class="d-flex">' + inn +'</div>' +
                '    </div>' +
                '  </div>' +
                '  <span type="text" class="form-control" ></span>' +
                '</div>';
        } else {
            return '';
        }

    },
    getDesc : function(){
        if(this.typ !== 'uprawnienie') {
            return '<div class="input-group mb-3">\n' +
                '  <div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="basic-addon3">Opis</span>\n' +
                '  </div>\n' +
                '  <input type="text" required class="form-control" id="basic-url" name="desc" minlength="0" maxlength="80" aria-describedby="basic-addon3">\n' +
                '</div> '
        } else {
            return '';
        }
    },
    getColor : function(){
        if(this.typ !== 'grafik'){
            return '<div class="input-group mb-3">\n' +
                '  <div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="inputGroup-sizing-default">Kolor</span>\n' +
                '  </div>\n' +
                '  <label class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default"><input class="w3-input"  required type="color" name="color"></label>' ;

        } else {
            return '';
        }
    },
    setIcon : function(a){
        var ico = $(a).attr('data-icon');
       var inp = $(a).parents('div.input-group-prepend').find('input[name="icon"]');
      inp.val(ico).parent().next().html('<i class="'+ico+'"></i>');
    },
    getSubmitBtn : function(){
        switch (this.typ){
            case 'uprawnienie':
                return '<button type="submit" name="addUpr" value="1" class="btn btn-secondary btn-lg btn-block">Dodaj</button>';
            case 'grafik':
                return '<button type="submit" name="addgrafVal" value="1" class="btn btn-secondary btn-lg btn-block">Dodaj</button>';
            case 'harmonogram':
                return '<button type="submit" name="addharmoVal" value="1" class="btn btn-secondary btn-lg btn-block">Dodaj</button>';
            default:
                break;
        }
    },
    checkForm : function(form){
        var res = true;
        $(form).find('input').each(function(){
            if(this.required){
                if($(this).val().length<=0){
                    $(this).addClass('error_inp');
                    res = false;
                } else {
                    $(this).removeClass('error_inp');
                }

            }
        });
        return res;
    }



};

function showHideGrafikRow(checkbox){
    var nazwaRow = checkbox.value;
    if(checkbox.checked){
        $('tr[row-name="'+nazwaRow+'"]').show();
    } else {
        $('tr[row-name="'+nazwaRow+'"]').hide();
    }
}

function zliczKolumneGrafiku(select) {
    var suma = {'stan':0};

   var col = $(select).attr('col');
   var selects = $('select[col="'+col+'"]');
   var opcje = selects.get(0).options;

    for(x in opcje){
        suma[opcje[x].value] = 0;
    }

    selects.each(function () {
       var wartosc = this.value;
       if(wartosc.length>0){
           if(wartosc !== 'Ws'){
               suma.stan++;
               //ZLICZ STRAZAKA UPR
           }
           suma[wartosc] ++;
       } else {
           suma.stan++;
           //ZLICZ STRAZAKA UPR
       }
   });
    for(x in suma){
        $('[row-id="'+x+'"][col="'+col+'"]').text(suma[x]);
    }



}




