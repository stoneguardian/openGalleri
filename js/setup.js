/**
 * Created by Hallvard on 22.03.14.
 */

var messages = new Array('dbConnect.php opprettet', 'dbname.php opprettet', 'Databasetabeller opprettet', 'Opprettet mail.php', 'Administrator lagt inn', 'Klart til bruk, videresender deg til hjemmesiden');
var IDs = new Array('dbConfOK', 'dbnameOK', 'tablesOK', 'mailOK', 'brukerOK', 'progressNumber');
var steps = 5;
var count = 0;

//- Variables ------------------------//
//-- database ------------------------//
var dbHost,
    dbName,
    dbUser,
    dbPwd,
    dbPref;

//-- mail ----------------------------//
var fromName,
    fromMail,
    mailHost,
    mailPort,
    mailUser,
    mailPwd;

//-- user ----------------------------//
var usrFna,
    usrLna,
    usrMai,
    usrPwd;

//- end ------------------------------//

//status: 0-2, error: 3-4
var dbMessages = new Array('Tester tilkobling', 'Skriver config-fil', 'Oppretter tabeller', 'Oppkobling mislyktes', 'Klarte ikke opprette databasetabeller');

//status: 0
var mailMessages = new Array('Skriver config-fil');
var userMessages = new Array('Oppretter administratorbruker');

var allowReduce = true;

function progress(){
    var percentage = (100/steps) * (count + 1);
    if(count < steps){
        $('#progressBar').css('width', percentage + '%');
    }
    if(count > steps - 1){
        document.getElementById('progressNumber').innerHTML = 'Videresender deg til forsiden';
    }else{
        document.getElementById('progressNumber').innerHTML = (count + 1) + ' av ' + steps;
    }


    $('#' + IDs[count]).addClass('progress');
    count += 1;
    allowReduce = true;
}

function tempProgress(){
    var currentwidth = $('#progressBar').width();
    var currentPercent = (100 * currentwidth)/700 ;
    var bump = currentPercent + 5;
    $('#progressBar').css('width', bump + '%');
    allowReduce = true;
}

function counterTempProgress(){
    if(allowReduce == true){
        var currentwidth = $('#progressBar').width();
        var currentPercent = (100 * currentwidth)/700 ;
        var bump = currentPercent - 5;
        $('#progressBar').css('width', bump + '%');
        allowReduce = false;
    }
}

function setMessage(msgNr, category){
    if(category == 'db'){
        $('#dbSubTitle').removeClass('subError');
        if(msgNr > 2){
            $('#dbSubTitle').addClass('subError');
        }
        document.getElementById('dbSubTitle').innerHTML = ' - ' + dbMessages[msgNr];
    }else if(category == 'mail'){
        $('#mailSubTitle').removeClass('subError');
        if(msgNr > 0){
            $('#mailSubTitle').addClass('subError');
        }
        document.getElementById('mailSubTitle').innerHTML = ' - ' + mailMessages[msgNr];
    }else if(category == 'user'){
        $('#userSubTitle').removeClass('subError');
        if(msgNr > 0){
            $('#userSubTitle').addClass('subError');
        }
        document.getElementById('userSubTitle').innerHTML = ' - ' + userMessages[msgNr];
    }
}

function getValue(id){
    return document.getElementById(id).value;
}

var dbNames = new Array('dbHost', 'dbName', 'dbUser', 'dbPwd', 'dbPref');
var mailNames = new Array('mailFromName', 'mailFromMail', 'mailHost', 'mailPort', 'mailUser', 'mailPwd');
var userNames = new Array('usrFna', 'usrLna', 'usrMai', 'usrPwd');

function setDBvariables(){
    dbHost = document.getElementById(dbNames[0]).value;
    dbName = document.getElementById(dbNames[1]).value;
    dbUser = document.getElementById(dbNames[2]).value;
    dbPwd = document.getElementById(dbNames[3]).value;
    dbPref = document.getElementById(dbNames[4]).value;
}

function setMailVariables(){
    fromName = document.getElementById(mailNames[0]).value;
    fromMail = document.getElementById(mailNames[1]).value;
    mailHost = document.getElementById(mailNames[2]).value;
    mailPort = document.getElementById(mailNames[3]).value;
    mailUser = document.getElementById(mailNames[4]).value;
    mailPwd = document.getElementById(mailNames[5]).value;
}

function dbReturnToForm(){
    setTimeout(function () { counterTempProgress(); }, 500);
    $("#database").removeClass('collapsed');
}

function testDBConnection(next){
    setMessage(0, 'db');
    setDBvariables();

    $.ajax({
        type: 'POST',
        url: 'dbConCheck.php',
        data: { 'host' : dbHost, 'un' : dbUser, 'pw' : dbPwd, 'db' : dbName },
        datatype: 'json',
        success: function(data){
            if(data.dbConCode == 200){
                createDbConfigs(next);
                console.log(data.dbConMsg);
            }else{
                //alert(data.dbConCode + " " + data.dbConMsg);
                setMessage(3, 'db');
                dbReturnToForm();
            }
        }
    });
}

function createDbConfigs(next){
    setMessage(1, 'db');

    $.ajax({
        type: 'POST',
        url: 'setupConfig.php',
        data: { 'dbHost' : dbHost, 'dbName' : dbName, 'dbUser': dbUser, 'dbPwd' : dbPwd, 'dbPref' : dbPref },
        datatype: 'json',
        success: function (data){
            if(data.db == true){
                progress();
                setTimeout(function () {progress();},500);
                createTables(next);
                //setTimeout(function () {createTables(next);}, 1000);
            }else{
                alert(data.error);
            }
        }
    });
}

function createTables(next){
    setTimeout(setMessage(2, 'db'), 1000);

    $.ajax({
        type: 'POST',
        url: 'setupDB.php',
        data: { 'updateDB' : 'true' },
        datatype: 'json',
        success: function (data){
            if(data.db == true){
                progress();
                $('#database').addClass('pastStep');
                $("#"+next).removeClass('futureStep');
            }else{
                alert(data.error);
            }
        }
    });
}

function mailReturnToForm(){
    setTimeout(function () { counterTempProgress();  }, 500);
    $("#mail").removeClass('collapsed');
}

function createMailConfig(next){
    setMailVariables();

    $.ajax({
        type: 'POST',
        url: 'setupConfig.php',
        data: { 'mailFromName' : fromName, 'mailFromMail' : fromMail, 'mailHost' : mailHost, 'mailPort' : mailPort, 'mailUser' : mailUser, 'mailPwd' : mailPwd },
        datatype: 'json',
        success: function (data){
            if(data.mail == true){
                progress();
                $('#mail').addClass('pastStep');
                $("#"+next).removeClass('futureStep');
            }else{
                alert(data.error);
                mailReturnToForm();
            }
        }
    });
}

function addUser(){
    var usrFna = document.getElementById(userNames[0]).value,
        usrLna = document.getElementById(userNames[1]).value,
        usrMai = document.getElementById(userNames[2]).value,
        usrPwd = document.getElementById(userNames[3]).value;

    $.ajax({
        type: 'POST',
        url: 'setupDB.php',
        data: { 'usrFna' : usrFna, 'usrLna' : usrLna, 'usrMai': usrMai, 'usrPwd' : usrPwd },
        datatype: 'json',
        success: function (data){
            if(data.user == true){
                progress();
                $('#user').addClass('pastStep');
                setTimeout(function () {progress();},500);
                setTimeout(function () {window.location.replace('../')}, 1500);
            }else{
                alert(data.error);
            }
        }
    });
}

function checkInput(form){
    if(form == 'database'){
        if(document.getElementById(dbNames[0]).value.length == 0){
            return false;
        }
        if(document.getElementById(dbNames[1]).value.length == 0){
            return false;
        }
        if(document.getElementById(dbNames[2]).value.length == 0){
            return false;
        }
        if(document.getElementById(dbNames[4]).value.length == 0){
            return false;
        }
        $("#dbTitle").css('background', '#FFF');
        return true;
    }else if(form == 'mail'){
        if(document.getElementById(mailNames[0]).value.length == 0){
            return false;
        }
        if(document.getElementById(mailNames[1]).value.length == 0){
            return false;
        }
        if(document.getElementById(mailNames[2]).value.length == 0){
            return false;
        }
        if(document.getElementById(mailNames[3]).value.length == 0){
            return false;
        }
        if(document.getElementById(mailNames[4]).value.length == 0){
            return false;
        }
        if(document.getElementById(mailNames[5]).value.length == 0){
            return false;
        }
        $("#mailTitle").css('background', '#FFF');
        return true;
    }else if(form == 'user'){
        if(document.getElementById(userNames[0]).value.length == 0){
            return false;
        }
        if(document.getElementById(userNames[1]).value.length == 0){
            return false;
        }
        if(document.getElementById(userNames[2]).value.length == 0){
            return false;
        }
        if(document.getElementById(userNames[3]).value.length == 0){
            return false;
        }
        $("#userTitle").css('background', '#FFF');
        return true;
    }
    return false;
}

function triggerNext(next){

    if(count < steps){
        tempProgress();
    }

    if(next == 'mail'){
        $("#database").addClass('collapsed');

        if(checkInput('database') == true){
            testDBConnection(next);
        }else{
            dbReturnToForm();
            $("#dbTitle").css('background', '#FF3C27');
        }
    }else if(next == 'user'){
        $("#mail").addClass('collapsed');

        if(checkInput('mail') == true){
            createMailConfig(next);
        }else{
            mailReturnToForm();
            $("#mailTitle").css('background', '#FF3C27');
        }
    }else if (next == 'progress'){
        $("#user").addClass('collapsed');

        if(checkInput('user') == true){
            addUser();
        }else{
            setTimeout(function () { counterTempProgress();  }, 500);
            $("#user").removeClass('collapsed');
            $("#userTitle").css('background', '#FF3C27');
        }
    }
}