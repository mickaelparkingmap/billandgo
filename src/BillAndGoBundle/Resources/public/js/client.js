$(document).ready(function() {
    $("#client_modal_country").hide();
    $("#client_modal_location").hide();
    $("#client_modal_contacts").hide();
    $("#client_modal_submit").hide();
    $("#client_modal_value").hide();
    $("#client_modal_name_ok").click(function (e) {
        //check if name
        $("#client_modal_name").hide();
        $("#client_modal_country").show();
    });
    $("#client_modal_name_back").click(function (e) {
        $("#client_modal_country").hide();
        $("#client_modal_name").show();
    });
    $("#client_modal_country_ok").click(function (e) {
        //check if value
        $("#client_modal_country").hide();
        $("#client_modal_location").show();
    });
    $("#client_modal_country_back").click(function (e) {
        $("#client_modal_location").hide();
        $("#client_modal_country").show();
    });
    $("#client_modal_location_ok").click(function (e) {
        //check if name
        $("#client_modal_location").hide();
        $("#client_modal_contacts").show();
        $("#client_modal_submit").show();
    });
    $("#client_modal_location_back").click(function (e) {
        $("#client_modal_contacts").hide();
        $("#client_modal_location").show();
        $("#client_modal_submit").hide();
    });

    var $client_container = $('div#billandgobundle_client_contacts');
    var client_index = $client_container.find(':input').length;
    $('#add_contact').click(function(e) {
        client_addcontact($client_container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (client_index == 0) {
        client_addcontact($client_container);
    } else {
        $client_container.children('div').each(function() {
            client_addDeleteLink($(this));
        });
    }

    function client_addcontact($client_container) {
        var client_template = $client_container.attr('data-prototype')
                .replace(/__name__label__/g, 'Ligne n°' + (client_index+1))
                .replace(/__name__/g,        client_index)
            ;
        var $client_prototype = $(client_template);
        client_addDeleteLink($client_prototype);
        $client_container.append($client_prototype);
        client_index++;
        $('.ddl').datepicker();
    }

    function client_addDeleteLink($client_prototype) {
        var $client_deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $client_prototype.append($client_deleteLink);
        $client_deleteLink.click(function(e) {
            $client_prototype.remove();
            e.preventDefault();
            return false;
        });
    }
});
