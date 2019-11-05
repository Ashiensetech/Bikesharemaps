function play()
{
    var video = document.getElementById('video');
    video.play();
    video.addEventListener('ended',function(){
        $.ajax({
            url: "command.php?action=getsystemurl"
            }).done(function(jsonresponse) {
               jsonobject=$.parseJSON(jsonresponse);
               if (jsonobject)
                  {
                  window.location = jsonobject["url"];
                  }
            });
    });
}