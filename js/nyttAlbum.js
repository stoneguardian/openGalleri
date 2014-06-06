/**
 * Created by Hallvard on 29.03.14.
 */
//- init -------------------------------------------//

var uid = document.getElementById('uid').value;
var mail = document.getElementById('mail').value;

//- settings ---------------------------------------//

var ctrlAlbum = "../upload/controllAlbum.php";

//--------------------------------------------------//

var aid = 0;
var counter = 1;
var created = false;
var name,
    year;

function savedMsg(msg, id){
    document.getElementById(id).innerHTML = msg;
    setTimeout(function (){ document.getElementById(id).innerHTML = ''; }, 2500);
}

function test(){
    savedMsg('Album opprettet', 'omStatus');
}

function checkBasic(){
    //Hent fra form
    var inName = document.getElementById('albumNavn').value;
    var inYear = document.getElementById('albumYear').value;

    //Konverter år til dato
    var year = new Date('January 1, ' + inYear).getFullYear();

    //Variabler
    var thisYear = new Date().getFullYear();
    var baseYear = new Date('January 1, 1930').getFullYear();

    //Sjekkvariabler
    var chkName = 0,
        chkYear = 0;

    //Kontroll
    if (inName.length > 0){
        chkName = 1;
    }

    if (year >= baseYear && year <= thisYear){
        chkYear = 1;
    }

    if (chkName == 1 && chkYear == 1){
        return true
    } else {
        if (chkYear == 0){
            document.getElementById('yearError').innerHTML = 'Vennligs fyll inn et gyldig årstall';
        }
        if (chkName == 0){
            document.getElementById('nameError').innerHTML = 'Vennligs skriv inn et navn';
        }
        return false;
    }
}//checkBasic end//

function genAlbum(){
    //Prepare array
    var values = {'switch' : '0', 'uid' : uid };
    ajaxPostCall(values, ctrlAlbum, genAlbumReturn);
}//genAlbum end//

function genAlbumReturn(post){
    if(post == false){
        alert('Uventet feil oppstod (genAlbum), prøv igjen');
    }else if(post.albumID > 0){
        aid = post.albumID;
    }else{
        alert("Noe gikk galt, " + post.errorMsg);
    }
}

function changeNameYear(){
    name = document.getElementById('albumNavn').value;
    year = document.getElementById('albumYear').value;

    //Prepare array
    var values = { 'albumID' : aid, 'albumName' : name, 'albumYear' : year, 'uid' : uid, 'count' : counter, 'mail' : mail, 'switch' : '1' };

    if(checkBasic() == true){
        ajaxPostCall(values, ctrlAlbum, changeNameYearReturn);
    }
}//changeAlbum end//

function changeNameYearReturn(post){
    if(post == false){
        alert('Uventet feil oppstod (changeNameYear), prøv igjen');
    }else if(post.updateAlbum == true){
        if(created == false){
            created = true;
            savedMsg("Album " + name + " opprettet", "omStatus");
        }else{
            savedMsg("Navn og årstall oppdatert", "omStatus");
        }

        document.getElementById('nAlbTitle').innerHTML = name + "(" +year + ")";
    }
}

function removeAlbum(){
    $.ajax({
        type: 'POST',
        url: ctrlAlbum,
        data: { 'albumName' : name, 'albumYear' : year, 'albumID' : aid, 'switch' : '3' },
        datatype: 'json',
        success: function (data){
            if(data.rm == false){
                alert(data.errorMsg);
                return false;
            }else{
                return true;
            }
        }
    });
}

function selectCover(){
    var e = document.getElementById('coverBilde');
    var current = e.options[e.selectedIndex].value;
    var coverStatus = document.getElementById('currentCover');

    $.ajax({
       type: 'POST',
       url: ctrlAlbum,
       data: { 'switch' : '2', 'albumID': aid, 'albumCover': current },
       datatype: 'json',
       success: function (data){
           if (data.cover == true){
               coverStatus.innerHTML = "Cover oppdatert";
               setTimeout(function (){coverStatus.innerHTML = ''}, 5000);
           } else if (data.status == 'false'){
               coverStatus.innerHTML = "Det oppstod en feil";
               setTimeout(function (){coverStatus.innerHTML = ''}, 5000);
           }
       }
    });
 }

function addCover(name){
    var option = document.createElement("option");
    option.text = name;
    option.value = counter;
    var select = document.getElementById("coverBilde");
    //select.appendChild(option);
}

function load(){
    //document.getElementById('submit').innerHTML = '';
    Init();
    //genAlbum();
}

function exit(){
    if(checkBasic() == false){
        //alert("Pass failed");
        return false;
    }

    if(created == false){
        removeAlbum();
    }
    return true;
}

function removeError(id){
    document.getElementById(id).innerHTML = '';
}

function naSub(id){
    $('#'+id).addClass('naStepMin');
}

function naExpand(id){
    $('#'+id).removeClass('naStepMin');
}

var naAboutSwitch = true,
    naNameSwitch = false,
    naYearSwitch = false;

function naAbout(){
    if(naAboutSwitch == true){ //Turn off
        naAboutSwitch = false;
        naSub('naAbout');
        document.getElementById('naAboutTitle').innerHTML = '<i class="fa fa-plus"></i> Om Album';
    }else if(naAboutSwitch == false){ //Turn on
        naAboutSwitch = true;
        naExpand('naAbout');
        document.getElementById('naAboutTitle').innerHTML = '<i class="fa fa-minus"></i> Om Album';
    }
}

function naAboutEnd(){
    if(naYearSwitch == true && naNameSwitch == true){
        var name = document.getElementById('albumNavn').value;
        var year = document.getElementById('albumYear').value;

        document.getElementById('nAlbTitle').innerHTML = name + '(' + year + ')';
        naAbout();
    }else if(naNameSwitch == false && naYearSwitch == false){
        naAboutNameError('on');
        naAboutYearError('on');
    }else if(naNameSwitch == false){
        naAboutNameError('on');
    }else if(naYearSwitch == false){
        naAboutYearError('on');
    }

}

function naCheckLenght(){
    var string = document.getElementById('albumNavn').value;
    var length = string.length;
    document.getElementById('naAlbNameCount').innerHTML = length + '/30 tegn';
    if(length > 0){
        naNameSwitch = true;
        naAboutNameError('remove');
    }else{
        naNameSwitch = false;
        naAboutNameError('on');
    }
}

function naCheckYear(){
    var inYear = document.getElementById('albumYear').value;
    var length = inYear.length;

    //clear Earlier
    naAboutYearError('remove');

    if(length == 4){//Run only when all 4 numbers are entered
        //Konverter år til dato
        var year = new Date('January 1, ' + inYear).getFullYear();

        //Variabler
        var thisYear = new Date().getFullYear();
        var baseYear = new Date('January 1, 1930').getFullYear();

        //Sjekkvariabler
        var chkYear = false;

        if(year >= baseYear && year <= thisYear){
            naYearSwitch = true;
            document.getElementById('naAlbYearStatus').innerHTML = '<i class="fa fa-check"></i>';
            $('#naAlbYearStatus').addClass('naCorrect');
        }else{
            naAboutYearError('on');
        }
    }
}

function naAboutNameError(toggle){
    if(toggle == 'remove'){
        //$('#albumNavn').css({'box-shadow': '0 0 0 2px rgba(255,255,255,0.3)'});
        $('#albumNavn').removeClass('naAlbumNameError');
    }else if(toggle == 'on'){
        //$('#albumNavn').css({'box-shadow': '0 0 0 2px #c5282'});
        $('#albumNavn').addClass('naAlbumNameError');
    }
    //alert(toggle);
}

function naAboutYearError(toggle){
    if(toggle == 'remove'){
        $('#naAlbYearStatus').removeClass('naCorrect').removeClass('naError');
        document.getElementById('naAlbYearStatus').innerHTML = '';
    }else if(toggle == 'on'){
        document.getElementById('naAlbYearStatus').innerHTML = 'Vennligs fyll inn et gyldig årstall';
        $('#naAlbYearStatus').addClass('naError');
    }
}