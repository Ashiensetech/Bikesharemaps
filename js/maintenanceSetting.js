$(document).ready(function () {
    $("#number-of-rentals-submit").on('click', function () {
        $.ajax({
            url: "command.php?action=maintenance-settings",
            method: "POST",
            data: {
                totalRent: $('#number-of-rentals-input').val(),
            }
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            eventMessage(jsonobject.content, jsonobject.http_code);
        });

    });
});