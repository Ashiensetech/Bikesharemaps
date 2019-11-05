$(document).ready(function () {
    checksubscription($('#loguserid').val());
    editprofile($('#userid').val());

    $('#profile').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            smscode: {
                validators: {
                    callback: {
                        message: _sms_code,
                        callback: function (value, validator) {
                            smscode = $("#smscode").val();
                            smscode = smscode.replace(/ /g, "");
                            return true;
                            // if (smscode.search('[a-zA-Z]{2}[0-9]{6}') == 0) return true;
                            // else return false;
                        }
                    },
                    notEmpty: {
                        message: _enter_sms_code
                    }
                }
            },
            fullname: {
                validators: {
                    notEmpty: {
                        message: _enter_names
                    }
                }
            },
            mailingaddress: {
                validators: {
                    notEmpty: {
                        message: 'Enter mailing address'
                    }
                }
            },
            userage: {
                validators: {
                    notEmpty: {
                        message: 'Enter Age'
                    }
                }
            },
            usergender: {
                validators: {
                    notEmpty: {
                        message: 'Enter Gender'
                    }
                }
            },
            userrace: {
                validators: {
                    notEmpty: {
                        message: 'Enter Race'
                    }
                }
            },
            physicaladdress: {
                validators: {
                    notEmpty: {
                        message: 'Enter physical address'
                    }
                }
            },
            city: {
                validators: {
                    notEmpty: {
                        message: 'Enter city'
                    }
                }
            },
            state: {
                validators: {
                    notEmpty: {
                        message: 'Enter state'
                    }
                }
            },
            zipcode: {
                validators: {
                    notEmpty: {
                        message: 'Enter zipcode'
                    }
                }
            },
            useremail: {
                validators: {
                    emailAddress: {
                        message: _email_incorrect
                    },
                    notEmpty: {
                        message: _enter_email
                    }
                }
            },
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        saveprofile();
    });
    $("#closeprofile").click(function () {
        window.top.close();
    });
    $('.stripe-unsubscription-button').unbind().bind('click', function () {
        var userid1 = $('#loguserid').val();
        var subtype1 = $(this).data('subtype');

        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to unsubscribe?',
            buttons: {
                confirm: function () {
                    var result = stripeUnsubscription(userid1, subtype1);
                    // if(subtype1 == 'annually') {
                    //     // $('.stripe-unsubscription-button.stripe-unsubscription-button-annually').attr("disabled", "disabled");
                    //     $('.stripe-unsubscription-button.stripe-unsubscription-button-annually').hide();
                    // }else {
                    //     $('.stripe-unsubscription-button.stripe-unsubscription-button-monthly').hide();
                    // }
                    return true;
                },
                cancel: function () {

                }
            }
        });

    });
});

//profile editing
function editprofile(userid) {
    if (userid != undefined && userid != '') {
        $.ajax({
            url: "command.php?action=editprofile&edituserid=" + userid
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            if (jsonobject) {
                $('#userid').val(jsonobject["userid"]);
                $('#username').val(jsonobject["username"]);
                $('#useremail').val(jsonobject["email"]);
                $('#userage').val(jsonobject["age"]);
                $('#usergender').val(jsonobject["gender"]);
                $('#userrace').val(jsonobject["race"]);
                $('#mailingaddress').val(jsonobject["mailingaddress"]);
                $('#physicaladdress').val(jsonobject["physicaladdress"]);
                $('#city').val(jsonobject["city"]);
                $('#state').val(jsonobject["state"]);
                $('#zipcode').val(jsonobject["zipcode"]);
                $('#phonenumber').val(jsonobject["phone"]);
                $('#profile').show();
            }
        });
    }
}

//profile editing
function checksubscription(userid) {
    if (userid != undefined && userid != '') {
        $.ajax({
            url: "command.php?action=check_subscription&userid=" + userid
        }).done(function (jsonresponse) {
            var jsonobject = $.parseJSON(jsonresponse);
            jsonobject.forEach(function ($index, $key) {
                //console.log($index);
                var payment_info = $.parseJSON($index.payment_info);
                var sub_id = (payment_info.sub_id) ? payment_info.sub_id : '';
                var sub_cancel = (payment_info.unsubscription_request) ? payment_info.unsubscription_request : 0;
                if ($index.subscription_type == 'monthly') {
                    //console.log(' hide monthly-subscription');
                    $('#monthly-subscription-form').hide();
                    $('#monthly-subscription-notice .notice-content').html("<p class='alert alert-success'>Already subscribed to 'Monthly Subscription'</p>");
                    $('#stripe-unsubscribe-monthly').css('display', 'block');
                    if(sub_cancel !=0){
                        $('#stripe-unsubscribe-monthly').html("<p class='alert alert-warning'>Recurring subscription has been turned off.</p>");
                    }
                } else if ($index.subscription_type == 'annually') {
                    //console.log(' hide annual-subscription');
                    $('#annual-subscription-form').hide();
                    $('#annual-subscription-notice .notice-content').html("<p class='alert alert-success'>Already subscribed to 'Annual Subscription'</p>");
                    $('#stripe-unsubscribe-yearly').css('display', 'block');
                    if(sub_cancel !=0){
                        $('#stripe-unsubscribe-yearly').html("<p class='alert alert-warning'>Recurring subscription has been turned off.</p>");
                    }
                } else if ($index.subscription_type == 'family_weekend') {
                    //console.log(' hide family-weekend');
                    $('#family-weekend-form').hide();
                    $('#family-weekend-notice .notice-content').html("<p class='alert alert-success'>Already subscribed to 'Family Weekend'</p>");
                }
            });
        });
    }
}

//profile editing
function stripeUnsubscription(userid, subtype) {
    if (userid != undefined && userid != '' && subtype != undefined && subtype != '') {
        $.ajax({
            url: "command.php?action=stripe_unsubscription&userid=" + userid + "&subtype=" + subtype
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            //console.log(jsonobject.error);
            eventMessage(jsonobject.content, jsonobject.http_code);
            if(jsonobject.error == 0){
                console.log("Great");
                if(subtype == 'monthly') {
                    $('#stripe-unsubscribe-monthly').html("<p class='alert alert-warning'>Recurring subscription has been turned off.</p>");
                }else if(subtype == 'annually'){
                    $('#stripe-unsubscribe-yearly').html("<p class='alert alert-warning'>Recurring subscription has been turned off.</p>");
                }
            }
            console.log("www");
            return true;
        });
    }
}

function saveprofile() {
    $.ajax({
        url: "command.php?action=saveprofile",
        method: "POST",
        data: {
            edituserid: $('#userid').val(),
            username: $('#username').val(),
            email: $('#useremail').val(),
            age: $('#userage').val(),
            gender: $('#usergender').val(),
            race: $('#userrace').val(),
            mailingaddress: $('#mailingaddress').val(),
            physicaladdress: $('#physicaladdress').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            zipcode: $('#zipcode').val()
        }
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        var $target = $('html,body');
        $target.animate({scrollTop: 0}, 500);
        $('#messageboard').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>').fadeOut(5000);
    });
}


function eventMessage(message, messageCode) {
    switch (messageCode) {
        case 201:
            toastr.success(message);
            break;
        case 500:
            toastr.error(message);
            break;
        case 403:
            toastr.warning(message);
            break;
    }
}