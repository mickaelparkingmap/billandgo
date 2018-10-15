$(document).ready(function(){
    $('.selected-template-pdf').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url : $(this).attr("action"),
            type : 'POST',
            data: {
                "pdfchoice" : $(".pdfchoice option:selected").val(),
                "cpstyle" : $.trim($(".custom-template-style").val()),
                "cpheader" : $.trim($(".custom-template-header").val()),
                "cpbody" : $.trim($(".custom-template-body").val()),
                "cpfooter" : $.trim($(".custom-template-footer").val())
            },
            success : function(data, statut){ // success est toujours en place, bien sûr !
                if (500 === data["code"]) {
                    $(".alert-pdf").html(" <div class=\"alert alert-danger\">"+(data['msg'])+"</div>");
                }
                else {
                    $(".alert-pdf").html(" <div class=\"alert alert-success\">"+(data['msg'])+"</div>");
                }

                $(".alert-pdf").show();
                setTimeout(function () {
                    $(".alert-pdf").hide();
                }, 6000);
            },

            error : function(resultat, statut, erreur){
                $(".alert-pdf").html(" <div class=\"alert alert-danger\">"+(resultat['msg'])+"</div>");
                $(".alert-pdf").show();
                setTimeout(function () {
                    $(".alert-pdf").hide();
                }, 6000);
            }

        });
    });

    $(".pdfchoice").change(function () {
        var base = "/bundles/billandgo/img/pdf/";
        console.log("dede");
        var selected = $(".pdfchoice option:selected").val();
        if ("custom" !== selected) {
            $("#image").fadeIn('slow');;
            $(".render").fadeIn('slow');;
            $("#image").attr("src", base+selected+".png");
            $(".custom-template").fadeOut('slow');
        }
        else {
            $("#image").fadeOut('slow');
            $(".render").fadeOut('slow');
            $(".custom-template").fadeIn('slow');
        }
    });


    $('.selected-task-calendar').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url : $(this).attr("action"),
            type : 'POST',
            data: {"task" : $('input[name=syncktask]:checked', this).val()},
            success : function(data, statut){ // success est toujours en place, bien sûr !
                if (500 === data["code"]) {
                    $(".alert-task").html(" <div class=\"alert alert-danger\">"+(data['msg'])+"</div>");
                }
                else {
                    $(".alert-task").html(" <div class=\"alert alert-success\">"+(data['msg'])+"</div>");
                }

                $(".alert-task").show();
                setTimeout(function () {
                    $(".alert-task").hide();
                }, 6000);
            },

            error : function(resultat, statut, erreur){
                $(".alert-task").html(" <div class=\"alert alert-danger\">"+(resultat['msg'])+"</div>");
                $(".alert-task").show();
                setTimeout(function () {
                    $(".alert-task").hide();
                }, 6000);
            }

        });
    });

});
