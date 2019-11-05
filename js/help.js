$(document).ready(function () {
    getHelpQuestions();

    $('#report').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            phonenumber: {
                validators: {
                    notEmpty: {
                        message: 'Enter your phone number'
                    }
                }
            },
            useremail: {
                validators: {
                    emailAddress: {
                        message: _email_incorrect
                    },
                    notEmpty: {
                        message: 'Enter email address'
                    }
                }
            },
            inquiry: {
                validators: {
                    notEmpty: {
                        message: 'Fill in what you are reporting'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        sendreport();
    });
});

function sendreport() {
    var userid = $('#userid').val();
    if (userid == undefined || userid == '') {
        userid = 0;
    }
    $('#sendinquiry').html('<p>Sending...</p>');
    $.ajax({
        url: "command.php?action=saveinquiry",
        method: "POST",
        data: {
            userid: userid,
            phone: $('#phonenumber').val(),
            email: $('#useremail').val(),
            inquiry: $('#inquiry').val()
        }
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $('#sendinquiry').html('<p>Send report</p>');
        $('#phonenumber').val('');
        $('#useremail').val('');
        $('#inquiry').val('');
        var $target = $('html,body');
        $target.animate({scrollTop: 0}, 500);
        $('#flash').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>').fadeOut(5000);
        getHelpQuestions();
    });
}

function getHelpQuestions() {
    var userid = $('#userid').val();
    if (userid != undefined && userid != '') {
        $.ajax({
            url: "command.php?action=getuserhelp",
            method: "GET"
        }).done(function (jsonresponse) {
            var jsonobject = $.parseJSON(jsonresponse);
            if (jsonobject.http_code == 200) {
                generateHelpData(jsonobject.content);
            }
        });
    }
}

function generateHelpData($data) {
    var helplist = "";
    $.map($data, function (element, index) {
        helplist += '<div class="panel-group" id="accordion-'+index+'" role="tablist" aria-multiselectable="true">' +
            '<div class="panel panel-default">' +
            '<div class="panel-heading" role="tab" id="heading-'+index+'">' +
            '<h4 class="panel-title">' +
            '<a role="button" data-toggle="collapse" data-parent="#accordion-'+index+'" href="#collapse-'+index+'" aria-expanded="true" aria-controls="collapse-'+index+'">' +
            '<i class="more-less glyphicon glyphicon-plus"></i>' +htmlEncode(element.question)+
            '</a>' +
            '</h4>' +
            '</div>' +
            '<div id="collapse-'+index+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-'+index+'">' +
            '<div class="panel-body">'+htmlEncode(element.answer)+
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
    });
    var html = '<div class="container">' +
        '<br/><label class="col-sm-2 control-label">Q & A</label><br/><br/>'+helplist+
        '</div>';
    $('.user-help-data').html(html);
}

function htmlEncode(str){
    if(str == null){
        str="";
    }
    var encodedStr = str.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return '&#'+i.charCodeAt(0)+';';
    });
    return encodedStr.replace(/&/gim, '&amp;');
}