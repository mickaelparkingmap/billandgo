$('.button').click(function(){
    var $btn = $(this),
$step = $btn.parents('.modal-body'),
stepIndex = $step.index(),
$pag = $('.modal-header span').eq(stepIndex);
console.log($pag)
    //check_required_inputs();
if(stepIndex === 0 || stepIndex === 1 || stepIndex === 2) { step1($step, $pag, stepIndex); }
else { step3($step, $pag, stepIndex); }

});


$('.button-start').click(function(){
    var $btn = $(this),
        $step = $btn.parents('.modal-body'),
        stepIndex = $step.index()
        $pag = $('.modal-header span').eq(stepIndex);


    console.log(stepIndex);
// animate the step out
    $step.addClass('animate-out');

// animate the step in
    setTimeout(function(){
        $step.removeClass('animate-out is-showing')
            .next().addClass('animate-in');
        $pag.addClass('is-active');
    }, 600);

// after the animation, adjust the classes
    setTimeout(function(){
        $step.next().removeClass('animate-in')
            .addClass('is-showing');

    }, 1200);

});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email));
}

function isStrongPwd1(password) {

    var regExp = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*()]).{8,}/;

    var validPassword = regExp.test(password);

    return validPassword;

}

function valideZipCode(zc) {
    var zpcodeexp = /^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/;
    return (zpcodeexp.test(zc));
}

function isTelephonePortable(numero){
    console.log('numéro : '+numero);
    var reg_telephone_portable = '^(06|07)[0-9]{8}$';

    if( numero.match(reg_telephone_portable) ){
        return true;
    } else {
        return false;
    }
}

function isTelephoneFixe(numero){
    var reg_telephone_fixe = '^(01|02|03|04|05|08|09)[0-9]{8}$';
    if( numero.match(reg_telephone_fixe) ){
        return true;
    } else {
        return false;
    }
}

function indexes(i) {
    var val = 0;
    $(".error").remove();
    $(".form-control").removeClass("error-b");
    if (1 === i) {
        if ("" === $("#fos_user_registration_form_username").val()) {
            val++;
            $("#fos_user_registration_form_username").addClass("error-b");
            $("#fos_user_registration_form_username").before('<span class="error">Veuillez renseigner un nom d\'utilisateur</span');
        }
        if ("" === $("#fos_user_registration_form_firstname").val()) {
            val++;
            $("#fos_user_registration_form_firstname").addClass("error-b");
            $("#fos_user_registration_form_firstname").before('<span class="error">Veuillez renseigner un nom</span');
        }
        if ("" === $("#fos_user_registration_form_lastname").val()) {
            val++;
            $("#fos_user_registration_form_lastname").addClass("error-b");
            $("#fos_user_registration_form_lastname").before('<span class="error">Veuillez renseigner un prénom</span');
        }
        if ("" === $("#fos_user_registration_form_email").val() ||
            false === validateEmail($("#fos_user_registration_form_email").val())) {
            val++;
            $("#fos_user_registration_form_email").addClass("error-b");
            $("#fos_user_registration_form_email").before('<span class="error">Veuillez renseigner un e-mail valide</span');
        }
        if (("" === $.trim($("#fos_user_registration_form_plainPassword_first").val()) || "" === $.trim($("#fos_user_registration_form_plainPassword_second").val())) || (
            $.trim($("#fos_user_registration_form_plainPassword_first").val()) !== $.trim($("#fos_user_registration_form_plainPassword_second").val()))) {
            val++;
            $("#fos_user_registration_form_plainPassword_first").addClass("error-b");
            $("#fos_user_registration_form_plainPassword_second").addClass("error-b");
            $("#fos_user_registration_form_plainPassword_first").before('<span class="error">Les mots de passes ne correspondent pas</span');
        }
        else if (!isStrongPwd1($.trim($("#fos_user_registration_form_plainPassword_first").val()))) {
            val++;
            $("#fos_user_registration_form_plainPassword_first").addClass("error-b");
            $("#fos_user_registration_form_plainPassword_second").addClass("error-b");
            $("#fos_user_registration_form_plainPassword_first").before('<span class="error">Le mot de passe doit avoir au moins 8 caractères avec au moins une lettre majuscule, une lettre minuscule, un chiffre et un caratère spécial !@#$%&;*() "</span');
        }

    }
    else if (2 === i) {
        if ("" === $("#fos_user_registration_form_address").val()) {
            val++;
            $("#fos_user_registration_form_address").addClass("error-b");
            $("#fos_user_registration_form_address").before('<span class="error">Veuillez renseigner une adresse</span');
        }
        if ("" === $("#fos_user_registration_form_zip_code").val() ||!valideZipCode($("#fos_user_registration_form_zip_code").val())) {
            val++;
            $("#fos_user_registration_form_zip_code").addClass("error-b");
            $("#fos_user_registration_form_zip_code").before('<span class="error">Veuillez renseigner un code postal valide</span');
        }

        if ("" === $("#fos_user_registration_form_city").val()) {
            val++;
            $("#fos_user_registration_form_city").addClass("error-b");
            $("#fos_user_registration_form_city").before('<span class="error">Veuillez renseigner une ville</span');
        }
        if ("" != $("#fos_user_registration_form_phone").val() && !isTelephoneFixe($("#fos_user_registration_form_phone").val())) {
            val++;
            $("#fos_user_registration_form_phone").addClass("error-b");
            $("#fos_user_registration_form_phone").before('<span class="error">Veuillez renseigner un numéro de téléphone valide (Format 09XXXXXXXX/01XXXXXXXX)</span');
        }
        if ("" === $("#fos_user_registration_form_mobile").val() || !isTelephonePortable($("#fos_user_registration_form_mobile").val())) {
            val++;
            $("#fos_user_registration_form_mobile").addClass("error-b");
            $("#fos_user_registration_form_mobile").before('<span class="error">Veuillez renseigner un numéro de mobile valide (Format 06XXXXXXXX/07XXXXXXXX)</span');
        }
    }
    else if (3 === i) {
        if ("" === $("#fos_user_registration_form_companyname").val()) {
            val++;
            $("#fos_user_registration_form_companyname").addClass("error-b");
            $("#fos_user_registration_form_companyname").before('<span class="error">Veuillez renseigner le nom de votre société</span');
        }
    }

    return val;
}

function step1($step, $pag, index){
    console.log('step1');
    if (indexes(index) > 0) {
    return false;
    }

// animate the step out
$step.addClass('animate-out');

console.log(index);
// animate the step in
setTimeout(function(){
    $step.removeClass('animate-out is-showing')
    .next().addClass('animate-in');
    $pag.removeClass('is-active')
    .addClass('is-active');
}, 600);

// after the animation, adjust the classes
setTimeout(function(){
    $step.next().removeClass('animate-in')
    .addClass('is-showing');

}, 1200);
}




function step3($step, $pag, index){
    console.log('3')
    ;
    if (indexes(index) > 0) {
        return false;
    }

// animate the step out
    $step.parents('.modal-wrap').addClass('animate-up');

    $("#fos_user_registration_register").submit();
    /*setTimeout(function(){
        $('.rerun-button').css('display', 'inline-block');
    }, 300);*/
}

