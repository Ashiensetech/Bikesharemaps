var base_url = window.location.origin;
$(function(){
    $('.user-logout').on('click',function(){
        console.log("logout");
        $.ajax({
            global: false,
            url: "command.php?action=logout"
        }).done(function (jsonresponse) {
            console.log(jsonresponse);
            jsonobject = $.parseJSON(jsonresponse);
            if(jsonobject.error == 1){
                alert("something went wrong");
            }else{
                goToHomePage();
            }

        });
    });
});

function goToHomePage(){
    if(window.location.hostname != 'bikesharemaps' && window.location.hostname != 'bikesharemapstst' && window.location.hostname != 'bikeleoti'){
        window.location = base_url+"/"+window.location.pathname.split('/')[1];
    }else{
        window.location = base_url;
    }
}