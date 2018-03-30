$(document).ready(function() {

    $("#document_modal_number").hide();
    $("#document_modal_description").hide();
    $("#document_modal_dates").hide();
    $("#document_modal_submit").hide();
    $("#document_modal_client_ok").click(function (e) {
        //check if client selected
        $opt = $('#myjobmanagerbundle_document_refClient').val();
        if ($opt)
        {
            $("#document_modal_client").hide();
            $("#document_modal_number").show();
            $("#document_modal_description").show();
            $("#document_modal_dates").show();
            $("#document_modal_submit").show();
        }
        else alert("Client non sélectionné !");
    });
    $("#document_modal_client_back").click(function (e) {
        $("#document_modal_client").show();
        $("#document_modal_number").hide();
        $("#document_modal_description").hide();
        $("#document_modal_dates").hide();
        $("#document_modal_submit").hide();
    });
    /**
     * DEVIS
     **/

    var $estimate_container = $('div#myjobmanagerbundle_devis_lines');
    var estimate_index = $estimate_container.find(':input').length;
    $('#add_line').click(function(e) {
        estimate_addLine($estimate_container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (estimate_index == 0) {
        estimate_addLine($estimate_container);
    } else {
        $estimate_container.children('div').each(function() {
            estimate_addDeleteLink($(this));
        });
    }

    function estimate_addLine($estimate_container) {
        var estimate_template = $estimate_container.attr('data-prototype')
            .replace(/__name__label__/g, 'Ligne n°' + (estimate_index+1))
            .replace(/__name__/g,        estimate_index)
        ;
        var $estimate_prototype = $(estimate_template);
        estimate_addDeleteLink($estimate_prototype);
        $estimate_container.append($estimate_prototype);
        estimate_index++;
    }

    function estimate_addDeleteLink($estimate_prototype) {
        var $estimate_deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $estimate_prototype.append($estimate_deleteLink);
        $estimate_deleteLink.click(function(e) {
            $estimate_prototype.remove();
            e.preventDefault();
            return false;
        });
    }
});
