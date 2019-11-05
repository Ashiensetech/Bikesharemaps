$(document).ready(function () {

    if (window.location.hash) {
        hash = window.location.hash.replace("#reset", "");
        $('#number').val(hash);
        getsmscode();
    }
    var current_page = window.location.pathname.split("/").slice(-1)[0];
    if (getUrlParameter('key') == undefined && current_page != 'agree.php') {
        $('#step1').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                number: {
                    validators: {
                        notEmpty: {
                            message: 'Enter Phone Number'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            // $("#step1").submit(function (e) {
            e.preventDefault();
            getsmscode();
            // });
        });
        $('#step2').bootstrapValidator({
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
                password: {
                    validators: {
                        notEmpty: {
                            message: _enter_password
                        }
                    }
                },
                password2: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: _passwords_nomatch
                        },
                        notEmpty: {
                            message: _enter_password
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            // e.preventDefault();
            // $("#step2").submit(function (e) {
            e.preventDefault();
            register();
            // $("#step2").fadeOut();
            // $("#step3").fadeIn();

            // });
        });

        $('#step3').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                number: {
                    validators: {
                        notEmpty: {
                            message: 'Enter Phone Number'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            // $("#step1").submit(function (e) {
            e.preventDefault();
            //register();
            // });
        });
    } else {
        $("*").find('div').not(".container").css('opacity', '0.1');
        $('video').on('ended', function () {
            $("*").find('div').not(".container").css('opacity', '1');
            var systemURL = document.getElementById('systemURL').value;
            window.setTimeout(function(){
                window.location.href = systemURL;
            }, 3000);
        });
    }

});

function getsmscode() {

    $.ajax({
        url: "command.php?action=smscode&number=" + $('#number').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $('#console').html('');
        $("#validatednumber").val(jsonobject.content);
        $("#checkcode").val(jsonobject.checkcode);
        $("#existing").val(jsonobject.existing);
        $("#step1").fadeOut();
        if (jsonobject.existing == 1) {
            $('h1').html(_existing_user).fadeIn();
            $('#step2title').html(_step2).fadeIn();
            $('#register').html(_set_password);
            $('#regonly').fadeOut();
        }
    });
}

function register() {
    $("#console").fadeOut();
    $("#register").prop("disabled", true);
    $.ajax({
        url: "command.php?action=register&validatednumber=" + $('#validatednumber').val() + "&checkcode=" + $('#checkcode').val() + "&smscode=" + $('#smscode').val() + "&fullname=" + $('#fullname').val() + "&useremail=" + $('#useremail').val() + "&userage=" + $('#userage').val() + "&usergender=" + $('#usergender').val() + "&userrace=" + $('#userrace').val() + "&password=" + $('#password').val() + "&password2=" + $('#password2').val() + "&existing=" + $('#existing').val() + "&mailingaddress=" + $('#mailingaddress').val() + "&physicaladdress=" + $('#physicaladdress').val() + "&city=" + $('#city').val() + "&state=" + $('#state').val() + "&status=pending" + "&zipcode=" + $('#zipcode').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        console.log(jsonobject);
        if (jsonobject.error == 1) {
            $('#console').html('<div class="alert alert-danger" role="alert">' + jsonobject.content + '</div>');
            $("#console").fadeIn();
            $("#register").prop("disabled", false);
        }
        else {
             $("#step2").fadeOut();
             $("#steplast").fadeIn();
             console.log(jsonobject);
            $("#steplast #user_id_opt1").val(jsonobject.user_id);
            $("#steplast #user_id_opt2").val(jsonobject.user_id);
            $("#steplast #user_id_opt3").val(jsonobject.user_id);
             // $("#console").fadeIn();
             //  $('#console').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>');
        }
    });
}

function register_paypal_return() {
    $.ajax({
        url: "command.php?action=register_paypal_return&paypal_amt=" + $('#paypal_amt').val() + "&paypal_cc=" + $('#paypal_cc').val() + "&paypal_cm=" + $('#paypal_cm').val() + "&paypal_item_name=" + $('#paypal_item_name').val() + "&paypal_item_number=" + $('#paypal_item_number').val() + "&paypal_st=" + $('#paypal_st').val() + "&paypal_tx=" + $('#paypal_tx').val() + "&paypal_info=" + $('#paypal_info').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        console.log(jsonobject);
        if (jsonobject.error == 1) {
            $('#console').html('<div class="alert alert-danger" role="alert">' + jsonobject.content + '</div>');
            $("#console").fadeIn();
            $("#register").prop("disabled", false);
        }
        else {
             console.log(jsonobject.user_id);
             $("#console").fadeIn();
             $('#console').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>');
        }
    });
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};


