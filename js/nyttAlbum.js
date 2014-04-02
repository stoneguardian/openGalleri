/**
 * Created by Hallvard on 29.03.14.
 */
//init----------------------------------------------//

var uid = document.getElementById('uid').value;
var mail = document.getElementById('mail').value;

//--------------------------------------------------//


//settings------------------------------------------//

var ctrlAlbum = "../upload/controllAlbum.php";

//--------------------------------------------------//

var aid = 0;
var counter = 1;
var created = false;
//var picDir = origDir; // + year + '-' + name;
var name,
    year;

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

    $.ajax({
        type: 'POST',
        url: ctrlAlbum,
        data: { 'switch' : '0', 'uid' : uid },
        datatype: 'json',
        success: function (data){
            if(data.albumID != false){
                aid = data.albumID;
                //picDir = origDir + data.alYear + '-' + data.alName;
                name = data.alName;
                year = data.alYear;
                //alert(picDir);
            }else{
                alert('noe gikk galt');
            }
            if(data.error == true){
                alert(data.errorMsg + ", " + data.switch);
            }
        }
    });
}//genAlbum end//

function changeAlbum(){
    name = document.getElementById('albumNavn').value;
    year = document.getElementById('albumYear').value;

    if(checkBasic() == true){
        $.ajax({
            type: 'POST',
            url: ctrlAlbum,
            data: { 'albumID' : aid, 'albumName' : name, 'albumYear' : year, 'uid' : uid, 'count' : counter, 'mail' : mail, 'switch' : '1' },
            datatype: 'json',
            success: function (data){
                if(data.updateAlbum == false){
                    alert(data.errorMsg);
                }else{
                    if(data.error == true){
                        alert(data.errorMsg);
                    }else{
                        created = true;
                    }
                }
            }
        }); //ajax end//
    }
}//changeAlbum end//


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
    var current = e.options[e.selectedIndex].value
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
    select.appendChild(option);
}

function load(){
    document.getElementById('submit').innerHTML = '';
    Init();
    genAlbum();
}

function exit(){
    //alert(created);

    if(checkBasic() == false){
        //alert("Pass failed");
        return false;
    }

    if(created == false){
        removeAlbum();
    }

}

function removeError(id){
    document.getElementById(id).innerHTML = '';
}