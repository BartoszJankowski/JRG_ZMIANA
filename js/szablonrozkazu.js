/*
* Szablon rozkazu
* za pomocą żądania AJAX dodaje, usuwa i edytuje elementy szablonu rozkazu.
*
* */

$(function () {
    setPopoverFunctions($("#szablon_container"));
});

function setPopoverFunctions(jQrElement){
    jQrElement.on("click",function(){
        $(this).toggleClass("highlight-element").popover('toggle');
    }).popover({content:function(){return htmlObj.popoverAddObj(this)},trigger:'manual'});
}

var htmlObj = {

    element : null,

    popoverAddObj : function(obj){

        this.element = $(obj);
        var name = obj.name;
        logD(obj);

        switch (name){

        }

        return '<label><input name="align" type="radio" class="no-display radio-btn" value="align-left" onchange="htmlObj.toggleRadioInput(this)" /><span class="w3-btn w3-border"><i class="fas fa-align-left "></i></span></label>' +
            '<label><input name="align" type="radio" class="no-display radio-btn" value="w3-center" onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-center"></i></span></label>' +
            '<label><input name="align" type="radio" class="no-display radio-btn" value="align-right" onchange="htmlObj.toggleRadioInput(this)"  /><span class="w3-btn w3-border"><i class="fas fa-align-right"></i></span></label>' +
            '<div><p>Dodaj nowy element</p><select><option value="div">Sekcja</option><option value="h2">Nagłówek</option><option value="span">Text</option><option value="input">Pole tekstowe</option><option value="select">Pole wyboru</option></select><button onclick="htmlObj.addNewElement($(this).prev())"><i class="fas fa-plus-square"></i></button></div>';
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
        this.disposePopover();
        nowyElement.toggleClass("highlight-element").popover('toggle');
    },
    disposePopover : function(){
        this.element.toggleClass("highlight-element");
        this.element.popover('dispose');
    }



};

