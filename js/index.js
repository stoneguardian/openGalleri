/** Created 04.03.14. */

var errorcount = 0;

$( document ).ready(function(){
    document.getElementById('reqJS').innerHTML = '';
    document.getElementById('sub').innerHTML = '';
});

function checkLogin() {
    slideRouter('checkLogin');

    var una = document.getElementById('bnavn').value;
    var pas = document.getElementById('pwd').value;
    var rem = document.getElementById('husk').checked;
    setTimeout( function (){
        $.ajax({
            type: "POST",
            url: "login/login.php",
            data: { 'brukernavn': una, 'passord': pas, 'husk': rem },
            headers: { "X_Ajax": "TRUE" },
            datatype: "json",
            success: function (data) {
                //alert(data.login);
                if (data.login == 'true') {
                    window.location = "user/";
                } else if (data.login == 'false') {
                    //window.location = "test.php?error=" + data.login;
                    $('.login').addClass('error');
                    $('#center').addClass('buzz');
                    slideRouter('login');
                    setTimeout(function () { $('#center').removeClass('buzz') }, 700);
                } else {
                    alert('feil!');
                    slideRouter('login');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.status);
                slideRouter('login');
            }
        }, 5000);
    }, 300);
    setTimeout(function () { slideRouter('login') }, 10000);
}

function uImg(){
    var una = document.getElementById('bnavn').value;

    $.ajax({
        type: 'POST',
        url: 'user/gravatar.php',
        data: { 'mail': una },
        header: { "type": 'ajax' },
        success: function (data){
            //alert('får respons');
            errorcount = 0;
            $('#currentIcon').addClass('userOut');
            $('.userOut').removeAttr('id');
            $('#loginImg').append('<div id="currentIcon" class="userIcon userInn"></div>');
            $('#currentIcon').css('background-image', 'url('+data.userimg+')');
            setTimeout(function() { $('#currentIcon').removeClass('userInn') }, 50);
            setTimeout(function() { $('.userOut').remove() }, 1000);
        },
        error: function () {
            alert('auda');
            if(errorcount < 4){
                uImg();
                errorcount += 1;
            }else{
                alert('maks ganger nådd');
            }
        }
    });
}

function loader(state) {
    if (state == 'create') {
        document.getElementById('loading').innerHTML = '<div id="en" class="dott">' +
            '</div><div id="to" class="dott">' +
            '</div><div id="tr" class="dott">' +
            '</div><div id="fi" class="dott">' +
            '</div><div id="fe" class="dott"></div>';
    } else {
        document.getElementById('loading').innerHTML = '';
    }
}

function slideRouter(opp) {
    //Knapper
    $('#btnLogin').addClass('botRightHidden');
    $('#btnLukk').addClass('botRightHidden');
    $('#btnTilbake').addClass('botRightHidden');
    $('#btnKode').addClass('botLeftHidden');
    $('#loading').addClass('botBoxHidden');
    loader('destroy');

    //"Cards"
    $('#login').addClass('loginNed');
    $('#login').removeClass('loginCheck');
    $('#nyPwd').addClass('nyPwdNed');
    $('#kode').addClass('kodeNed');

    if (opp == 'login') {
        $('#btnLogin').removeClass('botRightHidden');
        $('#login').removeClass('loginNed');
        $('#btnKode').removeClass('botLeftHidden');
    } else if (opp == 'nyttPwd') {
        $('#btnLukk').removeClass('botRightHidden');
        $('#nyPwd').removeClass('nyPwdNed');
    } else if (opp == 'kode') {
        $('#btnTilbake').removeClass('botRightHidden');
        $('#kode').removeClass('kodeNed');
    } else if (opp == 'checkLogin') {
        $('#login').removeClass('loginNed');
        $('#login').addClass('loginCheck');
        loader('create');
        $('#loading').removeClass('botBoxHidden');
    }
}

function removeRed(id) {
    $(id).removeClass('error');
}