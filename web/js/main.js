function c1() {
    console.log('from_main');
}

window.addEventListener('load', function() {
    initDynamicContent();
});

function initDynamicContent(){
    $.ajax({
        url: '/request/getInitContent',
        dataType: 'json',
        success: function(jsonData){
            for(var i in jsonData){
                $(jsonData[i]['selector']).html(jsonData[i]['html']);
            }
        },
        error: function(jqXHR, status, msg){
            console.log(jqXHR); console.log(msg+' '+status);
        }
    });
}

