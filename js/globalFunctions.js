function ajaxPostCall(values, url, done){
    $.ajax({
        type: 'POST',
        url: url,
        data: values,
        success: function (data){
            done(data);
        },
        error: function (){
            done(false);
        }
    });
}