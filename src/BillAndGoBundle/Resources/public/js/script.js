
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
    /**
     * FACTURES
     **/
    /*var $container = $('div#billandgobundle_bill_lines');
    var index = $container.find(':input').length;
    $('#add_line').click(function(e) {
        addLine($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    if (index == 0) {
        addLine($container);
    } else {
        $container.children('div').each(function() {
            addDeleteLink($(this));
        });
    }

    function addLine($container) {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Ligne n°' + (index+1))
            .replace(/__name__/g,        index)
        ;
        var $prototype = $(template);
        addDeleteLink($prototype);
        $container.append($prototype);
        index++;
    }

    function addDeleteLink($prototype) {
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $prototype.append($deleteLink);
        $deleteLink.click(function(e) {
            $prototype.remove();
            e.preventDefault();
            return false;
        });
    }*/

    /**
     * PROJETS
     */
    /*var $container = $('div#billandgobundle_project_todo');
    var index = $container.find(':input').length;
    $('#add_line').click(function(e) {
        addLine($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    if (index == 0) {
        addLine($container);
    } else {
        $container.children('div').each(function() {
            addDeleteLink($(this));
        });
    }

    function addLine($container) {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Ligne n°' + (index+1))
            .replace(/__name__/g,        index)
        ;
        var $prototype = $(template);
        addDeleteLink($prototype);
        $container.append($prototype);
        index++;
    }

    function addDeleteLink($prototype) {
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $prototype.append($deleteLink);
        $deleteLink.click(function(e) {
            $prototype.remove();
            e.preventDefault();
            return false;
        });
    }*/
});