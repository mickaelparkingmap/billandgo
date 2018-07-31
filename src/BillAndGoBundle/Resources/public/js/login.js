$(document).ready(function(){
    $('#goRight').on('click', function(){
        $('#slideBox').animate({
            'marginLeft' : '0'
        });
        $('.topLayer').animate({
            'marginLeft' : '100%'
        });
    });
    $('#goLeft').on('click', function(){
        $('#slideBox').animate({
            'marginLeft' : '50%'
        });
        $('.topLayer').animate({
            'marginLeft': '0'
        });
    });
    $("#connectnow").click(function () {
       $(".wrapper").fadeOut();
       $("#back").fadeIn();
        $("#slideBox").fadeIn();

    });
});

function notifyBar() {
    if(! $('.alert-box').length) {
        $('<div class="alert-box success" >Parfait ! Si vous avez un compte sur Bill&Go, vous recevrez un e-mail afin que vous puissiez réinitialiser votre mot de passe</div>').prependTo('body').delay(5000).fadeOut(1000, function() {
            $('.alert-box').remove();
        });
    };
};