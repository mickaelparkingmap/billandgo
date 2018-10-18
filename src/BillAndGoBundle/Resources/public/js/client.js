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
    $("#client_modal_country").hide();
    $("#client_modal_location").hide();
    $("#client_modal_contacts").hide();
    $("#client_modal_submit").hide();
    $("#client_modal_value").hide();
    $("#client_modal_name_ok").click(function (e) {
        //check if name
        if ("" == $.trim($("#billandgobundle_client_companyName").val())) {
            if (typeof $(".error") != "undefined")
                $(".errorbillandgobundle_client_companyName").remove();
            $("#billandgobundle_client_companyName").parent().append("<span class='text-red error errorbillandgobundle_client_companyName'>Veuillez renseigner le nom de l'entreprise</span>");
            return ;
        }
        $(".error").remove();
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
        if ("" == $.trim($("#billandgobundle_client_adress").val()) || "" == $.trim($("#billandgobundle_client_zipcode").val()) || "" == $.trim($("#billandgobundle_client_city").val())) {
            if (typeof $(".error") != "undefined")
                $(".error").remove();
            if ("" == $.trim($("#billandgobundle_client_adress").val()))
                $("#billandgobundle_client_adress").parent().append("<span class='text-red error errorbillandgobundle_client_adress'>Veuillez renseigner l'adresse</span>");
            if ("" == $.trim($("#billandgobundle_client_zipcode").val()) )
                $("#billandgobundle_client_zipcode").parent().append("<span class='text-red error errorbillandgobundle_client_zipcode'>Veuillez renseigner le code postal</span>");
            if ("" == $.trim($("#billandgobundle_client_city").val()) )
                $("#billandgobundle_client_city").parent().append("<span class='text-red error errorbillandgobundle_client_city'>Veuillez renseigner la ville</span>");


            return ;
        }

        $(".error").remove();
        $("#client_modal_location").hide();
        $("#client_modal_contacts").show();
    });
    $("#client_modal_contacts_ok").click(function (e) {
        //check if name
        if ("" == $.trim($("input[id^=billandgobundle_client_contacts_][id$=_lastname]").val()) || "" == $.trim($("input[id^=billandgobundle_client_contacts_][id$=_firstname]").val()) ||
            "" == $.trim($("input[id^=billandgobundle_client_contacts_][id$=_email]").val()) || "" == $.trim($("input[id^=billandgobundle_client_contacts_][id$=_mobile]").val())
        ) {

            if (typeof $(".error") != "undefined")
                $(".error").remove();
            $("input[id^=billandgobundle_client_contacts_][id$=_lastname]").each(function () {
                if ("" == $.trim($(this).val()))
                    $(this).parent().append("<span class='text-red error'>Veuillez renseigner le nom</span>");
            });

            $("input[id^=billandgobundle_client_contacts_][id$=_firstname]").each(function () {
                if ("" == $.trim($(this).val()))
                    $(this).parent().append("<span class='text-red error'>Veuillez renseigner le prénom</span>");
            });

            $("input[id^=billandgobundle_client_contacts_][id$=_email]").each(function () {
                if ("" == $.trim($(this).val()))
                    $(this).parent().append("<span class='text-red error'>Veuillez renseigner l'email</span>");
            });

            $("input[id^=billandgobundle_client_contacts_][id$=_mobile]").each(function () {
                if ("" == $.trim($(this).val()))
                    $(this).parent().append("<span class='text-red error'>Veuillez renseigner le numéro de mobile</span>");
            });
           return ;
        }

        $(".error").remove();


        $("#client_modal_location").hide();
        $("#client_modal_contacts").hide();
        $("#client_modal_submit").show();
    });
    $("#client_modal_location_back").click(function (e) {
        $("#client_modal_contacts").hide();
        $("#client_modal_location").show();
        $("#client_modal_submit").hide();
    });

    $("#client_modal_submit_back").click(function (e) {
        $("#client_modal_contacts").show();
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
                .replace(/__name__label__/g, 'Contact n°' + (client_index+1))
                .replace(/__name__/g,        client_index)
            ;
        var $client_prototype = $(client_template);
        client_addDeleteLink($client_prototype);
        $client_container.append($client_prototype);
        client_index++;
        $('.ddl').datepicker();
    }

    function client_addDeleteLink($client_prototype) {
        var $client_deleteLink = $('<a href="#" class="btn btn-danger btn-outline btn-delete">Supprimer</a>');
        $client_prototype.append($client_deleteLink);
        $client_deleteLink.click(function(e) {
            $client_prototype.remove();
            e.preventDefault();
            return false;
        });
    }
});
