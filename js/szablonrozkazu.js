/*
* Szablon rozkazu
* za pomocą żądania AJAX dodaje, usuwa i edytuje elementy szablonu rozkazu.
*
* */

var klasyDoPominiencia = ['highlight-element','newEmptyElement'];

$(function () {
    setPopoverFunctions($("#szablon_container"));
    $("#szablon_container *").not('tr').each(function () {
        setPopoverFunctions($(this));
    });
    buildSzablonTree();
});

function setPopoverFunctions(jQrElement){
    jQrElement.on("click",function(event){
        event.stopPropagation();
        htmlObj.closePopover();
        $(this).addClass("highlight-element").popover('show');
    }).popover({content:function(){return htmlObj.popoverAddObj(this)},trigger:'manual'}).on('shown.bs.popover',function (event) {
        event.stopPropagation();
       $('label[data-toggle="tooltip"]').tooltip({trigger:'hover',placement:'top'});
    });

}

var htmlObj = {
    tempId : 0,
    element : null,
    elementyHtml : {div:'Sekcja',h2:'Nagłówek',span:'Tekst',input:'Pole tekstowe',select:'Pole wyboru',ul:'Lista',table:'Tabela'},
    zmienne : zmienne,
    listy : listyVar,

    popoverAddObj : function(obj){

        this.element = $(obj);
        var name = obj.nodeName;
        var inner = '';
        switch (name){
            case 'DIV':
                inner = '<p>Dodaj nowy element</p>'+this.getSelectElementy()+'<button onclick="htmlObj.addNewElement($(this).prev())"><i class="fas fa-plus-square"></i></button>';
                break;
            case 'H2':
                inner = '<p>Wprowadź zawartość: </p><textarea  class="w3-input w3-border" oninput="htmlObj.updateContent(this)">'+this.element.text()+'</textarea>'+this.getButtonsVars();
                break;
            case 'SPAN':
                inner = '<p>Wprowadź zawartość: </p><textarea class="w3-input w3-border" oninput="htmlObj.updateContent(this)">'+this.element.text()+'</textarea>'+this.getButtonsVars();
                break;
            case 'INPUT':
                inner = '';
                break;
            case 'SELECT':
                inner = this.getCheckBoxLists();
                break;
            case 'UL':
                inner = this.getCheckBoxLists();
                break;
            case 'TABLE':
                inner = '<p>Dodaj kolumnę</p><div><input type="text" value=""><button onclick="htmlObj.dodajKolumne($(this).prev().val())"><i class="fas fa-columns"></i></button></div>';
                break;
            case 'TH':
                inner = '<p>Nagłówek: </p><input class="w3-input w3-border" oninput="htmlObj.updateContent(this)" value="'+this.element.text()+'" />'+this.getCheckBoxLists()+'<div><button onclick="htmlObj.dodajWiersz()"><i class="fas fa-plus"></i> Dodaj wiersz</button><button onclick="htmlObj.usunKolumne()" >Usuń kolumnę</button></div>';
                break;
            case 'TD':
                inner = '<div><p>Dodaj nowy element</p>'+this.getSelectElementy()+'<button onclick="htmlObj.addNewElement($(this).prev())"><i class="fas fa-plus-square"></i></button></div><div><button onclick="htmlObj.dodajWiersz()"><i class="fas fa-plus"></i> Dodaj wiersz</button><button onclick="htmlObj.usunWiersz()" > <i class="fas fa-minus"></i> Usuń wiersz</button></div>';
                break;
        }

        if(obj.id !== 'szablon_container' && name!=='TD' && name!=='TH')
            inner += '<div><button title="Usuń element" onclick="htmlObj.delete()"><i class="far fa-trash-alt"></i>Usuń element</button></div>';


        return '<span class="close-span w3-btn" onclick="htmlObj.closePopover()"><i class="fas fa-times"></i></span><div class="container">' +
                    '<div class="row">' +
                        '<div class="col">' +
                            '<h5>Stylizacja</h5>' + htmlObj.getStyles(name) +
                        '</div>' +
                        '<div class="col"><h5>Zawartość</h5><div  class="">'+inner+'</div></div>' +
                    '</div>'+
                '</div>';
    },

    /**
     * zmiena klasę dla elementu jedna z wielu input:radio
     * @param input
     */
    toggleRadioInput : function(input){
        var val = $(input).val();
        var elementTemp = this.element;
        $('[name='+input.name+']').each(function () {
            elementTemp.removeClass(this.value);
        });
        this.element.addClass(val);
    },

    /**
     * zmienia klasę obiektu
     * @param klasa
     */
    toggleClassValue : function(klasa){
        this.element.toggleClass(klasa);
    },

    /**
     * Tworzy i dodaje nowy element w hierarchii
     * @param select
     */
    addNewElement : function(select){
        if(select.val() === 'table' && this.element.parents('table').length>0){
            alert('Nie mozna zagnieżdżać tabeli.');
            return;
        }
        var nowyElement = $('<'+select.val()+'>');
        nowyElement.attr({'data-toggle':'popover','data-html':'true','data-placement':'top','id':'temp-'+this.tempId++});
        nowyElement.addClass('newEmptyElement');
        if(select.val() === 'div'){
            nowyElement.addClass('w3-row');
        } else if(select.val() === 'table'){
            nowyElement.addClass('szablonTable');
            nowyElement.addClass('w3-border');
        }
        setPopoverFunctions(nowyElement);
        this.element.append(nowyElement);
        this.closePopover();
        nowyElement.toggleClass("highlight-element").popover('toggle');
        buildSzablonTree();
    },
    /**
     * Tworzy i dodaje wiersz ->kolumnę tabeli
     */
    dodajKolumne : function(nazwaKol){
        var firstRow = this.element.find('tr:first-of-type');
        var cols = this.element.find('th');
        var nowyElement = $('<th>');
        nowyElement.text(nazwaKol);
        nowyElement.attr({'data-col':cols.length,'data-toggle':'popover','data-html':'true','data-placement':'top','id':'temp-'+this.tempId++});
        nowyElement.addClass('newEmptyElement');
        setPopoverFunctions(nowyElement);

        if(firstRow.length>0){
            firstRow.append(nowyElement);
        } else {
            var row = $('<tr>');
            row.append(nowyElement);
            this.element.append(row);
        }
        this.element.find('tr:gt(0)').each(function () {
            var td = $('<td>');
            td.attr({'data-col':cols.length,'data-toggle':'popover','data-html':'true','data-placement':'top','id':'temp-'+htmlObj.tempId++});
            td.addClass('newEmptyElement');
            setPopoverFunctions(td);
            $(this).append(td);
        });
        buildSzablonTree();
    },

    usunKolumne : function(){
        this.closePopover();
        var nrCol = this.element.attr('data-col');
        var table = this.element.parent().parent();
        table.find('[data-col='+nrCol+']').each(function () {
            logD(this);
            $(this).remove();
        });
        var cols = table.find('th');

        //iteracja po weirszach i zmiana nr kolumn
        table.find('tr').each(function () {
            var i = 0;
            $(this).find('[data-col]').each(function () {
                $(this).attr('data-col',i++);
            });
        });
        buildSzablonTree();

    },

    dodajWiersz : function(){
        var table = this.element.parent().parent();
        var cols = table.find('th');
        var tr = $('<tr>');
        for(i =0; i<cols.length; i++){
            var td = $('<td>');
            td.attr({'data-col':i,'data-toggle':'popover','data-html':'true','data-placement':'top','id':'temp-'+this.tempId++});
            td.addClass('newEmptyElement');
            setPopoverFunctions(td);
            tr.append(td);
        }
        table.append(tr);
        buildSzablonTree();
    },

    usunWiersz : function(){
        var tr = $(this.element).parent();
        this.closePopover();
        tr.remove();
        buildSzablonTree();
    },

    /**
     * Zamyka popover i usuwa podswietlenie
     */
    closePopover : function(){
        if(this.element !== null){
            this.element.removeClass("highlight-element");
            this.element.popover('hide');
        }
    },

    /**
     * Wprowadza na biezaco wartosc do pola text i naglowka
     * @param textArea
     */
    updateContent : function (textArea) {
        //TODO: check for input,span etc. type of [this.element]
        this.element.text($(textArea).val());
    },

    /**
     * usuwa element w hierarchii
     */
    delete : function(){
        this.closePopover();
        this.element.remove();
        buildSzablonTree();
    },

    /**
     * zwraca BUTTON zmienne
     * @returns {string}
     */
    getButtonsVars : function () {
        var inner = '<div>Zmienne: ';
        for(x in this.zmienne){
            inner += '<button onclick="htmlObj.setInTextareaContent(this)" class="zmienne-btn" >'+this.zmienne[x]['id']+'</button>';
        }
        return inner+'</div>';
    },

    /**
     * Zwraca CheckBox LISTY
     * @returns {string}
     */
    getCheckBoxLists : function(){
        var checkboxes = '<div>Listy: ';
        for(x in this.listy){
            var check = '';
            if(this.checkListAttr(this.listy[x]['id'])){
                check = 'checked';
            }
            checkboxes += '<label class="listy-btn" ><input '+check+' onchange="htmlObj.changeElementListAttr(this)" class="w3-check" type="checkbox" name="listy" value="'+this.listy[x]['id']+'" /><span class="no-wrap">'+this.listy[x]['name']+' </span></label>';
        }
        return checkboxes+'</div>';
    },

    /**
     * ustawia zmienna w textarea
     * @param button
     */
    setInTextareaContent : function(button){
        var textAr = $(button).parent().parent().find('textarea');
        textAr.val(textAr.val()+($(button).text())).trigger('oninput');
    },

    /**
     * zwraca html dostepnych stylizacji dla elementu
     * @param nodeName
     * @returns {string}
     */
    getStyles : function(nodeName){
        if(this.element.attr("id") === 'szablon_container'){
            return '';
        }

        var style =
            '<label data-toggle="tooltip"  title="Wyrównaj do lewej" ><input name="align" type="radio" class="no-display radio-btn" value="align-left" '+this.getCheckedString("align-left")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-align-left "></i></span></label>' +
            '<label data-toggle="tooltip"  title="Wyśrodkuj" ><input name="align" type="radio" class="no-display radio-btn" value="w3-center" '+this.getCheckedString("w3-center")+' onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-center"></i></span></label>' +
            '<label data-toggle="tooltip"  title="Wyrównaj do prawej" ><input name="align" type="radio" class="no-display radio-btn" value="align-right" '+this.getCheckedString("align-right")+' onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-right"></i></span></label>' +
            '<label  data-toggle="tooltip"  title="Dodaj obramowanie" ><input name="border" type="checkbox" class="no-display radio-btn " value="w3-border" '+this.getCheckedString("w3-border")+' onchange="htmlObj.toggleClassValue(\'w3-border\')" /><span class="w3-btn w3-border"><i class="far fa-square"></i></label>' +
            '<label  data-toggle="tooltip"  title="Dodaj margines" ><input name="padding" type="checkbox" class="no-display radio-btn " value="padding" '+this.getCheckedString("padding")+' onchange="htmlObj.toggleClassValue(\'padding\')" /><span class="w3-btn w3-border"><i class="fas fa-expand"></i></label>';


        if(nodeName === 'DIV'){
            //kolumna lub wiersz
            style += '<label  data-toggle="tooltip"  title="Wiersz" ><input name="layout" type="radio" class="no-display radio-btn" value="w3-row" onchange="htmlObj.toggleRadioInput(this);htmlObj.toggleInputs(this,true,\'colSize\');" '+this.getCheckedString("w3-row")+' /><span class="w3-btn w3-border"><i class="fas fa-arrows-alt-h"></i></span></label>' +
                '<label  data-toggle="tooltip"  title="Kolumna" ><input name="layout" type="radio" class="no-display radio-btn" value="w3-col" onchange="htmlObj.toggleRadioInput(this);htmlObj.toggleInputs(this,false,\'colSize\')" '+this.getCheckedString("w3-col")+' /><span class="w3-btn w3-border"><i class="fas fa-arrows-alt-v"></i></span></label>';
            //szerokosc kolumny
            var disable = this.getCheckedString('w3-col') ? '' : 'disabled';
            style += '<div><label  data-toggle="tooltip"  title="Szerokość kolumny" ><input '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="s2" '+this.getCheckedString("s2")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/6</label>' +
                '<label  data-toggle="tooltip"  title="Szerokość kolumny"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="s3" '+this.getCheckedString("s3")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/4</label>' +
            '<label  data-toggle="tooltip"  title="Szerokość kolumny"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="s4" '+this.getCheckedString("s4")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/3</label>'+
            '<label  data-toggle="tooltip"  title="Szerokość kolumny"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="s6" '+this.getCheckedString("s6")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/2</label></div> '+
            '<div><label  data-toggle="tooltip"  title="Szerokość kolumny"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="s8" '+this.getCheckedString("s8")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 2/3</label>'+
                '<label  data-toggle="tooltip"  title="Szerokość kolumny"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="s10" '+this.getCheckedString("s10")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 5/6</label>' +
            '<label  data-toggle="tooltip"  title="Szerokość kolumny"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="s12" '+this.getCheckedString("s12")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 100%</label></div>';


        } else {

            style += '<div>' +
                '<label class="tiny-size"  data-toggle="tooltip"  title="Rozmiar czcionki: 50%"><input name="fontSize" type="radio" class="no-display radio-btn " value="tiny-size" '+this.getCheckedString("tiny-size")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-font"></i></span></label>' +
                '<label class="smaller-size" data-toggle="tooltip"  title="Rozmiar czcionki: 75%"><input name="fontSize" type="radio" class="no-display radio-btn " value="smaller-size" '+this.getCheckedString("smaller-size")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas  fa-font "></i></span></label>' +
                '<label class="normal-size" data-toggle="tooltip"  title="Rozmiar czcionki: domyślny"><input name="fontSize" type="radio" class="no-display radio-btn " value="normal-size" '+this.getCheckedString("normal-size")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas  fa-font "></i></span></label>' +
                '<label class="bigger-size" data-toggle="tooltip"  title="Rozmiar czcionki: 150%"><input name="fontSize" type="radio" class="no-display radio-btn" value="bigger-size" '+this.getCheckedString("bigger-size")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas  fa-font "></i></span></label>' +
                '<label class="jumbo-size"  data-toggle="tooltip"  title="Rozmiar czcionki: 200%"><input name="fontSize" type="radio" class="no-display radio-btn " value="jumbo-size" '+this.getCheckedString("jumbo-size")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas  fa-font "></i></span></label>' +
                '</div>';
            if(nodeName === 'TABLE'){
                style += '<div><label  data-toggle="tooltip"  title="Automatyczna szerokość"><input '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="t0" '+this.getCheckedString("t0")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> auto</label>' +
                '<div><label data-toggle="tooltip"  title="Szerokość tabeli: 18%"><input '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="t2" '+this.getCheckedString("t2")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/6</label>' +
                    '<label  data-toggle="tooltip"  title="Szerokość tabeli: 25%"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="t3" '+this.getCheckedString("t3")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/4</label>' +
                    '<label data-toggle="tooltip"  title="Szerokość tabeli: 33%"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="t4" '+this.getCheckedString("t4")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/3</label>'+
                    '<label data-toggle="tooltip"  title="Szerokość tabeli: 50%"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="t6" '+this.getCheckedString("t6")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 1/2</label></div> '+
                    '<div><label data-toggle="tooltip"  title="Szerokość tabeli: 66%"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="t8" '+this.getCheckedString("t8")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 2/3</label>'+
                    '<label  data-toggle="tooltip"  title="Szerokość tabeli: 82%"><input '+disable+'  name="colSize" type="radio" class="no-display radio-btn " value="t10" '+this.getCheckedString("t10")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 5/6</label>' +
                    '<label  data-toggle="tooltip"  title="maksymalna szerokość"><input  '+disable+' name="colSize" type="radio" class="no-display radio-btn " value="t12" '+this.getCheckedString("t12")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-th"></i> 100%</label></div>';

            } else {
                style += '<label  data-toggle="tooltip"  title="Rozszerz obiekt"><input name="width" type="radio" class="no-display radio-btn " value="max-width" '+this.getCheckedString("max-width")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-angle-left"></i> <i class="fas fa-angle-right"></i></span></label>' +
                    '<label  data-toggle="tooltip"  title="Automatyczna szerokość"><input name="width" type="radio" class="no-display radio-btn " value="auto-width" '+this.getCheckedString("auto-width")+' onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-angle-right"></i> <i class="fas fa-angle-left"></i></span></label>';
            }


        }



        return '<div class="no-wrap">'+style+'</div>';
    },

    toggleInputs : function(input,bool, nameAttr){
        if(input.checked){
            $('input[name='+nameAttr+']').each(function () {
                this.disabled = bool;
            })
        }
    },

    /**
     * Sprawdza czy podana klasa istnieje w obiekcie
     * @param className
     * @returns {string}
     */
    getCheckedString : function(className){
        if(this.element.hasClass(className)){
            return 'checked';
        }
        return false;
    },

    /**
     * Zwraca select wyboru elementów html
     * @returns {string}
     */
    getSelectElementy : function(){
        var select = '<select>';
        for(x in this.elementyHtml){
            select += '<option value="'+x+'">'+this.elementyHtml[x]+'</option>';
        }
        select += '<select>';
        return select;
    },

    /**
     * Zmienia atrybut listy dla elementu
     * @param box
     */
    changeElementListAttr : function(box){
        var value = box.value;
        var attrL = this.element.attr("data-jrg", function(id, val){
            if(val === undefined || val.length<=0){
                return value;
            } else {
                var tab = val.split(" ");
                var newAttr = [];
                if(tab.indexOf(value)>=0){
                    for(x in tab){
                        if(tab[x]!==value){
                            newAttr.push(tab[x]);
                        }
                    }
                    tab = newAttr;
                }  else {
                    tab.push(value);
                }
                return tab.join(" ");

            }

        });
    },

    /**
     * Sprawdza czy element posiada dany atrybut
     * @param listName
     * @returns {boolean}
     */
    checkListAttr : function(listName){
        var attrL = this.element.attr("data-jrg");
        if(attrL === undefined || attrL.length<=0){
            return false;
        } else {
            var tab = attrL.split(" ");
            if(tab.indexOf(listName)>-1){
                return true;
            }
        }
        return false;
    }


};

/**
 * Funkcja do chowania i pokazywanie legendy / menu po lewej/prawej stronie edycji szablonu
 */
function animateBar(btn) {
    var div = $(btn).parent();
    if(div.css('width') === '50px'){
        div.animate({
            width:'25%'
        },500, function () {
            $(btn).next().fadeToggle();
        });
    }  else {
        $(btn).next().fadeToggle(function () {
            div.animate({
                width:'50px'
            },500);
        });
    }
}

function buildSzablonTree(){

    var llista = $('<ul class="no-wrap">');
    $("#szablon_container").children().each(function(){
        llista.append($('<li>').html(getChildrenUlList($(this))) );
    });

    $("#szablon_tree").html(llista);
    $('<SPAN>').text('Szablon: ').insertBefore(llista);
}

function getChildrenUlList(parent) {
    if(parent.get(0).nodeName === 'TABLE'){
        return getChildrenUlForTable(parent);
    }
    var div = $('<DIV>');
    var btnUp = $('<BUTTON class="w3-btn w3-padding-small">').html('<i class="fas fa-chevron-up"></i>').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var prev = parent.prev();
        if(prev.length>0){
            parent.detach().insertBefore(prev);
        }
        parent.toggleClass('backlight-element');
        buildSzablonTree();
    });
    var btnDown = $('<BUTTON class="w3-btn w3-padding-small">').html('<i class="fas fa-chevron-down"></i>').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var next = parent.next();
        if(next.length>0){
            parent.detach().insertAfter(next);
        }
        parent.toggleClass('backlight-element');
        buildSzablonTree();
    });
    var link = $('<SPAN>')
        .addClass('w3-btn w3-padding-small')
        .text(convertNodeName(parent.get(0).nodeName))
        .hover(function () {
            parent.toggleClass('backlight-element');
        })
        .click(function () {
            htmlObj.closePopover();
            parent.addClass("highlight-element").popover('show');
        });
    if(parent.siblings().length>0)
        link.append(btnUp).append(btnDown);

    var ul = $('<UL>');

    parent.children().each(function(){
        ul.append($('<LI>').html(getChildrenUlList($(this)) ) );//'<li>'+getChildrenUlList($(this))+'</li>');
    });
    div.append(link).append(ul);
    return div;
}

function getChildrenUlForTable(table){

    var colsNum = table.find('tr:first-of-type th').length;
    var div = $("<DIV>");
    var link = $('<SPAN>')
        .addClass('w3-btn w3-padding-small')
        .text(convertNodeName(table.get(0).nodeName))
        .hover(function () {
            table.toggleClass('backlight-element');
        })
        .click(function () {
            htmlObj.closePopover();
            table.addClass("highlight-element").popover('show');
        });
    var fin = $("<UL>");
    for(var x =0; x < colsNum; x++){
        var th = table.find('th[data-col='+x+']');
        var tds = table.find('td[data-col='+x+']');
        fin.append(getCol(th, tds));
    }
    div.append(link).append(fin);
    return div;
}

function getCol(th, tds){
    var div = $('<LI>');
    var link = $('<SPAN>')
        .addClass('w3-btn w3-padding-small')
        .text('Kolumna ('+th.text()+')')
        .hover(function () {
            th.toggleClass('backlight-element');
        })
        .click(function () {
            htmlObj.closePopover();
            th.addClass("highlight-element").popover('show');
        });

    var ul = $('<UL>');

    tds.each(function(){
        ul.append($('<LI>').html(getChildrenUlList($(this)) ) );//'<li>'+getChildrenUlList($(this))+'</li>');
    });

    div.append(link).append(ul);
    return div;
}

/**
 * Funkcja pobiera nazwę nodeName htmlDom i zwraca wartośc dla uzytkownika
 * @param nodeName
 * @returns {*}
 */
function convertNodeName(nodeName){
    switch (nodeName){
        case 'DIV':
            return 'Sekcja';
        case 'H2':
            return 'Nagłówek';
        case 'SPAN':
            return 'Tekst';
        case 'INPUT':
            return 'Pole tekstowe';
        case 'SELECT':
            return 'Pole wyboru';
        case 'UL':
            return 'Lista';
        case 'TABLE':
            return 'Tabela';
        case 'TD':
            return 'Komórka';
        default:
            return nodeName;
    }
}

/**
 * Tworzy obiekt szablonu do odczytania przez php
 */
var szablon = {
    obiekty : [],
    create : function(){
        this.obiekty = [];
        var root = $("#szablon_container");
        root.children().each(function (index, element) {
          szablon.obiekty.push(szablon.traverseRootTree(element));
        });
    },
    traverseRootTree : function(root){
        var obiekt = {
            'name':$(root).get(0).nodeName,
            'value':null,
            'class':this.getClasses($(root)),
            'attr':this.getAttrs($(root)),
           /* 'id':$(root).attr('id'), */
            'content':[]
        };

        if(obiekt.name === 'SPAN' || obiekt.name === 'H2'){
            obiekt.content = $(root).text();
        } else if(obiekt.name==='INPUT'){
            obiekt.value = $(root).val();
        } else if(obiekt.name === 'TABLE'){
            obiekt.content = szablon.traverseTableTree($(root));
        } else {
            obiekt.content = [];
            $(root).children().each(function(index,element){
                obiekt.content.push(szablon.traverseRootTree(element));
            });
        }
        return obiekt;
    },
    traverseTableTree : function (jQTable){
        var kolumny = [];
        var colsTh = jQTable.find('th');
        for(var i=0; i<colsTh.length; i++){
            var naglowekTh = colsTh.get(i);
            var obiekt = {'name':'col',
                'value':$(naglowekTh).text(),
                'class':szablon.getClasses($(naglowekTh)),
                'attr':szablon.getAttrs($(naglowekTh)),
                'id':$(naglowekTh).attr('id'),
                'content':[]
            };
            jQTable.find('td[data-col='+i+']').each(function () {
                obiekt.content.push(szablon.traverseRootTree(this));
            });
            kolumny.push(obiekt);
        }
        return kolumny;
    },
    getClasses : function (jQNode) {
        var temp = [];
        var klasy = jQNode.get(0).className.split(" ");
            for(x in klasy){
                if(klasyDoPominiencia.indexOf(klasy[x])<0){
                    temp.push(klasy[x]);
                }
            }
        return temp;
    },
    getAttrs : function(jQNode){
        var node =  jQNode.attr('data-jrg');
        if(node === undefined ||  node.length<=0) {
            node = [];
        } else {
            node = node.trim().split(" ");
        }
        return node;
    },
    save : function (btn){
        this.create();
        var idR = $('#szablon_container').attr('rozkaz-id');
        var act = $('#szablon_active').get(0).checked ? 1:0;
        $.ajax({
            type:'POST',
            data:{save:JSON.stringify(szablon.obiekty),id:idR,active:act},
            url:'szablonrozkazu.php',
            beforeSend : function(xhr){
               btn.disabled = true;
                $(btn).html('<i class="w3-spin fas fa-spinner"></i> Wysyłanie...');
            },
            success : function (response) {
                if(response.result){
                    $("<div class='w3-panel w3-green w3-card-4'>Poprawnie zapisano szablon.</div>").insertBefore(btn).fadeOut(2500);
                } else {
                    $("<div class='w3-panel w3-red w3-card-4'>Podczas zapisu wystapił bład: "+response.error+"</div>").insertBefore(btn).fadeOut(5000);
                }
                logD(response);
            },
            error : function () {

            },
            complete : function (xhr, status) {
                $(btn).html('<i class="far fa-save"></i> Zapisz');
                btn.disabled = false;
            }
        });
    }
};

