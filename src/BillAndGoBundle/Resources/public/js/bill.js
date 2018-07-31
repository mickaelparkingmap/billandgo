/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */



$(document).ready(function() {
    $("#bill_modal_number").hide();
    $("#bill_modal_dates").hide();
    $("#bill_modal_description").hide();
    $("#bill_modal_lines").hide();
    $("#bill_modal_submit").hide();
    $("#bill_modal_client_ok").click(function (e) {
        //check if client selected
        $("#bill_modal_client").hide();
        $("#bill_modal_number").show();
    });
    $("#bill_modal_client_back").click(function (e) {
        $("#bill_modal_client").show();
        $("#bill_modal_number").hide();
    });
    $("#bill_modal_number_ok").click(function (e) {
        //check if number
        $("#bill_modal_number").hide();
        $("#bill_modal_dates").show();
    });
    $("#bill_modal_number_back").click(function (e) {
        $("#bill_modal_dates").hide();
        $("#bill_modal_number").show();
    });
    $("#bill_modal_dates_ok").click(function (e) {
        //check if number
        $("#bill_modal_dates").hide();
        $("#bill_modal_description").show();
    });
    $("#bill_modal_dates_back").click(function (e) {
        $("#bill_modal_description").hide();
        $("#bill_modal_dates").show();
    });
    $("#bill_modal_description_ok").click(function (e) {
        //check if number
        $("#bill_modal_description").hide();
        $("#bill_modal_lines").show();
        $("#bill_modal_submit").show();
    });
    $("#bill_modal_description_back").click(function (e) {
        $("#bill_modal_description").show();
        $("#bill_modal_lines").hide();
        $("#bill_modal_submit").hide();
    });

    var $bill_container = $('div#billandgobundle_bill_lines');
    var bill_index = $bill_container.find(':input').length;
    $('#add_line').click(function(e) {
        bill_addLine($bill_container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (bill_index == 0) {
        bill_addLine($bill_container);
    } else {
        $bill_container.children('div').each(function() {
            bill_addDeleteLink($(this));
        });
    }

    function bill_addLine($bill_container) {
        var bill_template = $bill_container.attr('data-prototype')
            .replace(/__name__label__/g, 'Ligne n°' + (bill_index+1))
            .replace(/__name__/g,        bill_index)
        ;
        var $bill_prototype = $(bill_template);
        bill_addDeleteLink($bill_prototype);
        $bill_container.append($bill_prototype);
        bill_index++;
    }

    function bill_addDeleteLink($bill_prototype) {
        var $bill_deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $bill_prototype.append($bill_deleteLink);
        $bill_deleteLink.click(function(e) {
            $bill_prototype.remove();
            e.preventDefault();
            return false;
        });
    }
});
