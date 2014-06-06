/*function testDBConnection(){
    var host = 'localhost',
        username = 'root',
        password = '',
        database = 'opengalleri';

    $.ajax({
        type: 'POST',
        url: 'dbConCheck.php',
        data: { 'host' : host, 'un' : username, 'pw' : password, 'db' : database },
        datatype: 'json',
        success: function(data){
            if(data.dbConCode = 200){
                alert(data.dbConMsg);
            }else{
                alert(data.dbConCode + " " + data.dbConMsg);
            }

        }
    });
}*/

function testAnimation(){
    $('#database').addClass('collapsed');
    setTimeout(function(){ document.getElementById('dbSubTitle').innerHTML = ' - Sjekker tilkobling'}, 200);
    setTimeout(function(){ $('#database').addClass('pastStep')}, 2000);
    setTimeout(function(){ $('#mail').removeClass('futureStep')}, 2000);
}