$(document).ready(function(){
    $('.selected-template-pdf').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url : $(this).attr("action"),
            type : 'POST',
            data: {"pdfchoice" : $(".pdfchoice option:selected").val()},
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
            $("#image").attr("src", base+selected+".png");
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
