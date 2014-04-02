/**
 * Created by Hallvard on 22.03.14.
 */

var messages = new Array('dbConnect.php opprettet', 'dbname.php opprettet', 'Databasetabeller opprettet', 'Opprettet mail.php', 'Administrator lagt inn', 'Klart til bruk, videresender deg til hjemmesiden');
var IDs = new Array('dbConfOK', 'dbnameOK', 'tablesOK', 'mailOK', 'brukerOK', 'progressNumber');
var steps = 5;
var count = 0;

var allowReduce = true;

function progress(){
    var percentage = (100/steps) * (count + 1);
    if(count < steps){
        $('#progressBar').css('width', percentage + '%');
    }

    document.getElementById('progressNumber').innerHTML = (count + 1) + ' av ' + steps;

    /*if(IDs[count] == 'done'){
        document.getElementById('progressNumber').innerHTML = messages['']
    }else{*/
        document.getElementById(IDs[count]).innerHTML = messages[count];
    //}

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

function getValue(id){
    return document.getElementById(id).value;
}

var dbNames = new Array('dbHost', 'dbName', 'dbUser', 'dbPwd', 'dbPref');
var mailNames = new Array('mailFromName', 'mailFromMail', 'mailHost', 'mailPort', 'mailUser', 'mailPwd');
var userNames = new Array('usrFna', 'usrLna', 'usrMai', 'usrPwd');

function createDbConfigs(next){
    var dbHost = document.getElementById(dbNames[0]).value,
        dbName = document.getElementById(dbNames[1]).value,
        dbUser = document.getElementById(dbNames[2]).value,
        dbPwd = document.getElementById(dbNames[3]).value,
        dbPref = document.getElementById(dbNames[4]).value;

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
    $.ajax({
        type: 'POST',
        url: 'setupDB.php',
        data: { 'updateDB' : 'true' },
        datatype: 'json',
        success: function (data){
            if(data.db == true){
                progress();
                $("#"+next).removeClass('collapsed');
            }else{
                alert(data.error);
            }
        }
    });
}

function createMailConfig(next){
    var fromName = document.getElementById(mailNames[0]).value,
        fromMail = document.getElementById(mailNames[1]).value,
        mailHost = document.getElementById(mailNames[2]).value,
        mailPort = document.getElementById(mailNames[3]).value,
        mailUser = document.getElementById(mailNames[4]).value,
        mailPwd = document.getElementById(mailNames[5]).value;

    $.ajax({
        type: 'POST',
        url: 'setupConfig.php',
        data: { 'mailFromName' : fromName, 'mailFromMail' : fromMail, 'mailHost' : mailHost, 'mailPort' : mailPort, 'mailUser' : mailUser, 'mailPwd' : mailPwd },
        datatype: 'json',
        success: function (data){
            if(data.mail == true){
                progress();
                $("#"+next).removeClass('collapsed');
            }else{
                alert(data.error);
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
}

function triggerNext(next){
    $("#database").addClass('collapsed');
    $("#mail").addClass('collapsed');
    $("#user").addClass('collapsed');

    if(count < steps){
        tempProgress();
    }

    if(next == 'mail'){
        if(checkInput('database') == true){
            createDbConfigs(next);
        }else{
            setTimeout(function () { counterTempProgress(); }, 500);
            $("#database").removeClass('collapsed');
            $("#dbTitle").css('background', '#FF3C27');
        }
    }else if(next == 'user'){
        if(checkInput('mail') == true){
            createMailConfig(next);
        }else{
            setTimeout(function () { counterTempProgress();  }, 500);
            $("#mail").removeClass('collapsed');
            $("#mailTitle").css('background', '#FF3C27');
        }
    }else if (next == 'progress'){
        if(checkInput('user') == true){
            addUser();
        }else{
            setTimeout(function () { counterTempProgress();  }, 500);
            $("#user").removeClass('collapsed');
            $("#userTitle").css('background', '#FF3C27');
        }
    }
}