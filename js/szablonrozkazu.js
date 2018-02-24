/*
* Szablon rozkazu
* za pomocą żądania AJAX dodaje, usuwa i edytuje elementy szablonu rozkazu.
*
* */

$(function () {
    setPopoverFunctions($("#szablon_container"));
});

function setPopoverFunctions(jQrElement){
    jQrElement.on("click",function(event){
        event.stopPropagation();
        htmlObj.closePopover();
        $(this).addClass("highlight-element").popover('show');
    }).popover({content:function(){return htmlObj.popoverAddObj(this)},trigger:'manual'});
}

var htmlObj = {

    element : null,
    zmienne : ['[$miasto]','[$data-rozkazu]','[$nr_jrg]','[$data-edycji]','[$nr-rozkazu]','[$rok]','[$miesiac]','[$dzien]'],
    listy : ['[@list-Ud]','[@list-Uw]'],

    popoverAddObj : function(obj){

        this.element = $(obj);
        var name = obj.nodeName;
        var inner = '';
        switch (name){
            case 'DIV':
                inner = '<p>Dodaj nowy element</p><select><option value="div">Sekcja</option><option value="h2">Nagłówek</option><option value="span">Text</option><option value="input">Pole tekstowe</option><option value="select">Pole wyboru</option></select><button onclick="htmlObj.addNewElement($(this).prev())"><i class="fas fa-plus-square"></i></button>';
                break;
            case 'H2':
                inner = '<p>Wprowadź zawartość: </p><textarea  class="w3-input w3-border" oninput="htmlObj.updateContent(this)">'+this.element.text()+'</textarea>'+this.getButtonsVars('zmienne');
                break;
            case 'SPAN':
                inner = '<p>Wprowadź zawartość: </p><textarea class="w3-input w3-border" oninput="htmlObj.updateContent(this)">'+this.element.text()+'</textarea>'+this.getButtonsVars('zmienne');
                break;
            case 'INPUT':
                inner = '';
                break;
            case 'SELECT':

                break;
        }

        if(obj.id !== 'szablon_container')
            inner += '<div><button title="Usuń element" onclick="htmlObj.delete()"><i class="far fa-trash-alt"></i></button></div>';


        return '<span class="close-span w3-btn" onclick="htmlObj.closePopover()"><i class="fas fa-times"></i></span><div class="container">' +
                    '<div class="row">' +
                        '<div class="col">' +
                            '<h5>Stylizacja</h5>' +
                            '<div class="no-wrap">' +
                                '<label><input name="align" type="radio" class="no-display radio-btn" value="align-left" onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-align-left "></i></span></label>' +
                                '<label><input name="align" type="radio" class="no-display radio-btn" value="w3-center" onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-center"></i></span></label>' +
                                '<label><input name="align" type="radio" class="no-display radio-btn" value="align-right" onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-right"></i></span></label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col"><h5>Zawartość</h5><div  class="no-wrap">'+inner+'</div></div>' +
                    '</div>'+
                '</div>';
    },
    toggleRadioInput : function(input){
        var val = $(input).val();
        var elementTemp = this.element;
        $('[name='+input.name+']').each(function () {
            elementTemp.removeClass(this.value);
        });
        this.element.addClass(val);
    },
    addNewElement : function(select){
        var nowyElement = $('<'+select.val()+'>');
        nowyElement.attr({'data-toggle':'popover','data-html':'true','data-placement':'top'});
        nowyElement.addClass('newEmptyElement');
        setPopoverFunctions(nowyElement);
        this.element.append(nowyElement);
        this.closePopover();
        nowyElement.toggleClass("highlight-element").popover('toggle');
    },
    closePopover : function(){
        if(this.element !== null){
            this.element.removeClass("highlight-element");
            this.element.popover('hide');
        }
    },
    updateContent : function (textArea) {
        //TODO: check for input,span etc. type of [this.element]
        this.element.text($(textArea).val());
    },
    delete : function(){
        this.closePopover();
        this.element.remove();
    },
    getButtonsVars : function (variable) {
        var inner = '<div>'+variable+': ';
        for(x in this[variable]){
            inner += '<button onclick="htmlObj.setInTextareaContent(this)" class="'+variable+'-btn" >'+this[variable][x]+'</button>';
        }
        return inner+'</div>';
    },
    setInTextareaContent : function(button){
        var textAr = $(button).parent().parent().find('textarea');
        textAr.val(textAr.val()+($(button).text())).trigger('oninput');
    },
    stylizacja : function(){

    }

};

/**
 * Funkcja do chowania i pokazywanie legendy / menu po lewej stronie edycji szablonu
 */
function animateLeftBar(btn) {
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

