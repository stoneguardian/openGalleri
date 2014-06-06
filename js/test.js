function testFormdata(){
    var formData = new FormData();

    //Legg til
    formData.append('name', 'Hallvard');

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax.php", true);
    xhr.send(formData);
}