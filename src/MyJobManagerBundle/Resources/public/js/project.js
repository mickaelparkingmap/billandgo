$(document).ready(function() {
    $("#project_modal_name").hide();
    $("#project_modal_dates").hide();
    $("#project_modal_description").hide();
    $("#project_modal_todos").hide();
    $("#project_modal_submit").hide();
    $("#project_modal_client_ok").click(function (e) {
        //check if client selected
        $("#project_modal_client").hide();
        $("#project_modal_name").show();
    });
    $("#project_modal_client_back").click(function (e) {
        $("#project_modal_client").show();
        $("#project_modal_name").hide();
    });
    $("#project_modal_name_ok").click(function (e) {
        //check if name
        $("#project_modal_name").hide();
        $("#project_modal_dates").show();
    });
    $("#project_modal_name_back").click(function (e) {
        $("#project_modal_dates").hide();
        $("#project_modal_name").show();
    });
    $("#project_modal_dates_ok").click(function (e) {
        //check if name
        $("#project_modal_dates").hide();
        $("#project_modal_description").show();
    });
    $("#project_modal_dates_back").click(function (e) {
        $("#project_modal_description").hide();
        $("#project_modal_dates").show();
    });
    $("#project_modal_description_ok").click(function (e) {
        //check if name
        $("#project_modal_description").hide();
        $("#project_modal_todos").show();
        $("#project_modal_submit").show();
    });
    $("#project_modal_description_back").click(function (e) {
        $("#project_modal_description").show();
        $("#project_modal_todos").hide();
        $("#project_modal_submit").hide();
    });

    var $project_container = $('div#myjobmanagerbundle_project_todo');
    var project_index = $project_container.find(':input').length;
    $('#add_line').click(function(e) {
        project_addtodo($project_container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });

    if (project_index == 0) {
        project_addtodo($project_container);
    } else {
        $project_container.children('div').each(function() {
            project_addDeleteLink($(this));
        });
    }

    function project_addtodo($project_container) {
        var project_template = $project_container.attr('data-prototype')
            .replace(/__name__label__/g, 'Ligne n°' + (project_index+1))
            .replace(/__name__/g,        project_index)
        ;
        var $project_prototype = $(project_template);
        project_addDeleteLink($project_prototype);
        $project_container.append($project_prototype);
        project_index++;
        $('.ddl').datepicker();
    }

    function project_addDeleteLink($project_prototype) {
        var $project_deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $project_prototype.append($project_deleteLink);
        $project_deleteLink.click(function(e) {
            $project_prototype.remove();
            e.preventDefault();
            return false;
        });
    }
});
