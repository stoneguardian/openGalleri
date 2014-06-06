
$( document ).ready(function(){
    $('#reqJS').remove();
    document.getElementById('sub').innerHTML = '';
    slideRouter('login');
});

var errorcount = 0;

//- Glemt passord -------------------------------------------//

var npStep = 0;
var npFormStep = 1;
var npHeight = 27; //px
var npMargin = 0; //px
var npPwdLenght = 4;

// 0 = Success, 1+ = Error
var npStatusMail = ['E-post sendt', 'Beklager, fant ikke e-postadressen', 'Beklager, tjenerfeil'],
    npStatusCode = ['Koden er godkjent', 'Beklager, fant ikke koden', 'Beklager, koden er utløpt', 'Beklager, tjenerfeil'],
    npStatusPwd = ['Passord oppdatert', 'Passordene er ikke like', 'Beklager, tjenerfeil'];

var npStatusLabel = ['Brukerkontoens e-postadresse:', 'Koden du fikk på e-post:', 'Nytt passord:', 'Bekreft nytt passord:'];
var nyPwdMail,
    nyPwdKode,
    nyPwdPass,
    nyPwdPassR;

function updateStatus(status){
    if(npFormStep == 1){
        document.getElementById('npMailStatus').innerHTML = npStatusMail[status];
    }else if(npFormStep == 2){
        document.getElementById('npCodeStatus').innerHTML = npStatusCode[status];
    }else if(npFormStep == 4){
        document.getElementById('npPwdStatus').innerHTML = npStatusPwd[status];
    }
}

function updateStep(){
    document.getElementById('nyPwdStep').innerHTML = npFormStep;
}

function updateLabel(){
    document.getElementById('npLabel').innerHTML = npStatusLabel[npFormStep-1];
}

function npRePos(){
    var height = npHeight + npMargin;
    var newPos = height*npStep;
    $('#npFlex').css({'top': '-' + newPos + 'px'});
    updateLabel();
    updateStep();
}

function npRedBorder(){
    $('#nyPwdInput').css({'box-shadow': '0 0 0 2px #db5252'});
}

function npRmRedBorder(){
    $('#nyPwdInput').css({'box-shadow': '0 0 0 2px rgba(255,255,255,0.3)'});
}

function npNextStep(statusStep, destinationStep, formStep, status){
    npStep = statusStep;
    updateStatus(status);
    npRePos();

    npStep = destinationStep;
    npFormStep = formStep;

    setTimeout(function (){ npRePos(); }, 2500);
}

function checkPwdLenght(pwd){
    if(pwd.length >= npPwdLenght){ return true; }else{ return false; }
}

function npMail() {
    //npStep: 0 = mailForm, 1 = Wait, 2 = Status
    nyPwdMail = document.getElementById('npMail').value;
    npStep = 1;
    npRePos();

    $.ajax({
        method: 'POST',
        url: 'login/glemtPwd.php',
        data: { 'glemtMail' : nyPwdMail, 'ajax' : true },
        datatype: 'json',
        success: function(data){
            if(data.glemtStatusCode == 200 && data.mailStatusCode == 200){
                npNextStep(3, 3, 2, 0);
            }else if(data.glemtStatusCode == 502){
                npNextStep(2, 0, 1, 1);
                npRedBorder();
            }else{
                npNextStep(2, 2, 1, 2);
            }
        },
        error: function (jqXHR, textStatus, errorThrown){
            alert(jqXHR+", "+textStatus+", "+errorThrown);
        }
    });
}

function npCode() {
    //npStep: 3 = codeForm, 4 = Wait, 5 = Status
    nyPwdKode = document.getElementById('npCode').value;
    npStep = 4;
    npRePos();

    $.ajax({
        method: 'POST',
        url: 'login/recover.php',
        data: { 'code' : nyPwdKode, 'mail' : nyPwdMail },
        datatype: 'json',
        success: function(data){
            if(data.codeStatusCode == 200){//Success
                npNextStep(5, 6, 3, 0);
            }else if(data.codeStatusCode == 502){//Kode utløpt
                npNextStep(5, 3, 2, 2);
            }else if(data.codeStatusCode == 504){//Kode + Mail = feil
                npNextStep(5, 3, 2, 1);
            }else{
                npNextStep(5, 3, 2, 3);
            }
        }
    });
}

function npPwd(){
    //npStep: 6 = pwdForm
    nyPwdPass = document.getElementById('npPwd').value;
    if(checkPwdLenght(nyPwdPass) == true){
        npStep = 7;
        npFormStep = 4;
        npRePos();
        setTimeout(function (){document.getElementById('npPwd').value = '';}, 500);
    }else{
        alert('Må være lenger eller lik '+ npPwdLenght +' bokstaver');
        npRedBorder();
    }
}

function npPwdRep(){
    //npStep: 7 = pwdrepForm, 8 = Status
    nyPwdPassR = document.getElementById('npPwdRep').value;

    if(checkPwdLenght(nyPwdPassR) == true){
        npStep =8;
        if(nyPwdPassR == nyPwdPass){
            $.ajax({
                method: 'POST',
                url: 'login/recover.php',
                data: { 'nPwd' : nyPwdPassR, 'mail' : nyPwdMail },
                datatype: 'json',
                success: function(data){
                    if(data.pwdStatusCode == 200){//Success
                        npNextStep(8, 0, 1, 0);
                        setTimeout(function (){
                            slideRouter('login');
                        }, 1900);
                    }else{
                        npNextStep(8, 8, 4, 2);
                    }
                }
            });
        }else{
            npNextStep(8, 6, 3, 1);
        }
        setTimeout(function (){document.getElementById('npPwdRep').value = '';}, 500);
    }else{
        alert('Må være lenger eller lik '+ npPwdLenght +' bokstaver');
    }
}

function npRouter(){
    if(npFormStep == 1){
        npMail();
        return true;
    }else if(npFormStep == 2){
        npCode();
        return true;
    }else if(npFormStep == 3){
        npPwd();
        return true;
    }else if(npFormStep == 4){
        npPwdRep();
        return true;
    }else{
        return false;
    }
}

//- Glemt passord end ---------------------------------------//

//- Login ---------------------------------------------------//

function ajaxPostCall(values, url, done){
    $.ajax({
        type: 'POST',
        url: url,
        data: values,
        success: function (data){
            //return data;
            done(data);
        },
        error: function (){
            done(false);
        }
    });
}

function checkLogin() {
    slideRouter('checkLogin');

    //Get values
    var una = document.getElementById('bnavn').value;
    var pas = document.getElementById('pwd').value;
    var rem = document.getElementById('husk').checked;

    //Create array
    var sendData = { 'brukernavn': una, 'passord': pas, 'husk': rem };

    //Gend request
    ajaxPostCall(sendData, 'login/login.php', checkLoginResponse);

    //Just in case
    setTimeout(function () { slideRouter('login') }, 15000);
}

function checkLoginResponse(post){
    if(post == false){ //if ajax unsuccessful
        alert('Prøv igjen');

    }else if(post.login == 'true'){ //if successful login
        setTimeout(function (){ window.location = "user/"; }, 3000);

    }else if(post.login == 'false'){ //if unsuccessful login
        $('.login').addClass('error');
        $('#center').addClass('buzz');
        slideRouter('login');
        setTimeout(function () { $('#center').removeClass('buzz') }, 700);

    }else{ //in case of something unexpected
        alert('ERROR!');
        slideRouter('login');
    }
}

function uImg(){
    //Get value
    var una = document.getElementById('bnavn').value;

    //Make array
    var sendData = { 'mail' : una };

    //Send request
    ajaxPostCall(sendData, 'user/gravatar.php', setUserImage)
}

function setUserImage(post){
    if(post == false){
        if(errorCount < 4){
            uImg();
            errorcount = errorcount + 1;
        }else{
            alert('Klarer ikke laste bilde');
        }
    }else{
        errorcount = 0;
        $('#currentIcon').addClass('userOut');
        $('.userOut').removeAttr('id');
        $('#loginImg').append('<div id="currentIcon" class="userIcon userInn"></div>');
        $('#currentIcon').css('background-image', 'url('+post.userimg+')');
        setTimeout(function() { $('#currentIcon').removeClass('userInn') }, 50);
        setTimeout(function() { $('.userOut').remove() }, 1000);
    }
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
    //$('#btnKode').addClass('botLeftHidden');
    $('#loading').addClass('botBoxHidden');
    loader('destroy');

    //"Cards"
    $('#login').addClass('loginNed');
    $('#login').removeClass('loginCheck');
    $('#nyPwd').addClass('nyPwdNed');
    //$('#kode').addClass('kodeNed');

    $('.textInput').removeClass('textInputDown');

    if (opp == 'login') {
        $('#btnLogin').removeClass('botRightHidden');
        $('#login').removeClass('loginNed');
        //$('#btnKode').removeClass('botLeftHidden');
    } else if (opp == 'nyttPwd') {
        $('#btnLukk').removeClass('botRightHidden');
        $('#nyPwd').removeClass('nyPwdNed');
    } else if (opp == 'kode') {
        $('#btnTilbake').removeClass('botRightHidden');
        $('#kode').removeClass('kodeNed');
    } else if (opp == 'checkLogin') {
        $('.textInput').addClass('textInputDown');
        $('#login').removeClass('loginNed');
        $('#login').addClass('loginCheck');
        loader('create');
        $('#loading').removeClass('botBoxHidden');
    }
}

function removeRed(id) {
    $(id).removeClass('error');
}

//- Login end -----------------------------------------------//