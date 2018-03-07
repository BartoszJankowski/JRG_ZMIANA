
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
    logD(type+" "+idUpr);
}


