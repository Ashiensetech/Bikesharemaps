var markers = [];
var markerdata = [];
var iconsize = 60;
var watchID, circle, polyline;
var temp = "";
var oTable;

$(function () {

    $('.modal').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();

        $("#currentstand option").remove();
        //alert($('#currentstand').html());
        $(this)
            .find("input,textarea,select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    });

    $('#error').click(function () {
        // make it not dissappear
        toastr.error("Noooo oo oo ooooo!!!", "Title", {
            "timeOut": "0",
            "extendedTImeout": "0"
        });
    });
    $('#info').click(function () {
        // title is optional
        toastr.info("Info Message", "Title");
    });
    $('#warning').click(function () {
        toastr.warning("Warning");
    });
    $('#success').click(function () {
        toastr.success("YYEESSSSSSS");
    });

    $("#change-user-password-send").on('click',function(){
        $('#change-password-modal').attr('data-send',1);
    });

    $('#change-password-modal').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'],
        fields: {
            new_password: {
                validators: {
                    notEmpty: {
                        message: _enter_password
                    }
                }
            },
            re_new_password: {
                validators: {
                    identical: {
                        field: 'new_password',
                        message: _passwords_nomatch
                    },
                    notEmpty: {
                        message: _enter_password
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var sendEmail = false;
        if($('#change-password-modal').attr('data-send') == 1){
            sendEmail = true;
        }
        changeUserPassword(sendEmail);
    });
});

$(document).on('click', '[data-toggle="lightbox"]', function (event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});

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

$(document).ready(function () {
    $("#broadcast").hide();
    $("#eventbroadcast").hide();
    $("#edituser").hide();
    $("#editbicycle").hide();
    $("#editinquiry").hide();
    $("#map").hide();
    $(".progress").hide();
    $("#where").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-where');

        var adminparam = $('#adminparam').val();
        var responseMsg = "Can not be empty search!";
        if (adminparam == "") {
            eventMessage(responseMsg, 500);
            return 0;
        }
        where();
    });
    $("#revert").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-revert');
        var adminparam = $('#adminparam').val();
        var responseMsg = "Can not be empty search!";
        if (adminparam == "") {
            eventMessage(responseMsg, 500);
            return 0;
        }
        revert();
    });
    $("#last").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-last');
        var adminparam = $('#adminparam').val();
        var responseMsg = "Can not be empty search!";
        if (adminparam == "") {
            eventMessage(responseMsg, 500);
            return 0;
        }
        last();
    });

    $("#adminparam").keyup(function () {
        var adminparam = $('#adminparam').val();
        if (adminparam == "") {
            last();
        }
    });

    $("#stands").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-stands');
        stands();
    });
    $("#viewstands").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-fleet');
        viewstands();
    });
    $("#userlist").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-userlist');
        userlist();
    });
    $("#generatecoupons1").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-generatecoupons');
        generatecoupons(1);
    });
    $("#generatecoupons2").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-generatecoupons');
        generatecoupons(5);
    });
    $("#generatecoupons3").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-generatecoupons');
        generatecoupons(10);
    });
    $("#trips").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-trips');
        trips();
    });
    $('.nav-tabs a').each(function () {
        $(this).click(function () {
            activetab = $(this).attr('href');
            $(activetab).addClass('active');
        });
    });
    $("#saveuser").click(function () {
        saveuser();
        $('#refresh_users').trigger('click');
        return false;
    });

    $("#save_new_user").click(function () {
        save_new_user();
        $('#refresh_users').trigger('click');
        return false;
    });

    $("#save-help-answer").click(function () {
        saveHelpAnswer();
        return false;
    });

    $("#send").click(function () {
        sendmessage();
        return false;
    });
    $("#eventbroadcastsend").click(function () {
        sendeventmessage();
        return false;
    });
    $("#savebicycle").click(function () {
        savebicycle();
        return false;
    });
    $("#savewatercraft").click(function () {
        savewatercraft();
        return false;
    });

    $("#saveevent").click(function () {
        saveevent();
        return false;
    });

    $("#newstand").click(function () {
        $("#addstand").show();
        return false;
    });
    $("#newvideo").click(function () {
        $("#addvideo").show();
        return false;
    });
    $("#newmessage").click(function () {
        $("#broadcast").show();
        return false;
    });
    $("#eventnewmessage").click(function () {
        $("#eventbroadcast").show();
        return false;
    });
    $("#newbicycle").click(function () {
        getStands();
        //$("#addbicycle").show();
        return false;
    });
    $("#savenewstand").click(function () {
        addnewstand();
        return false;
    });
    $("#savenewvideo").click(function () {
        addnewvideo();
        return false;
    });
    $("#savenewbicycle").click(function () {
        addnewbicycle();
        return false;
    });
    $("#savenewwatercraft").click(function () {
        addnewwatercraft();
        return false;
    });

    $("#savenewevent").click(function () {
        addnewevent();
        return false;
    });

    $("#save-new-place").click(function () {
        var type = $(this).attr('data-type');
        $listElem = $("#"+type+"-list");
        addnewplace(type,$listElem);
        return false;
    });
    $("#save-place").click(function () {
        var type = $(this).attr('data-type');
        saveplace(type);
        return false;
    });

    $("#deletebicycle").click(function () {
        // deletebicycle();
        deletebicycleByType('bike');
        return false;
    });
    $("#deletewatercraft").click(function () {
        deletebicycleByType('watercraft');
        return false;
    });
    $("#deleteevent").click(function () {
        deletebicycleByType('event');
        return false;
    });
    $("#deletestand").click(function () {
        deletestand();
        return false;
    });
    $("#deletevideo").click(function () {
        deletevideo();
        return false;
    });
    $("#closeinquiry").click(function () {
        closeinquiry();
        return false;
    });
    $("#savestand").click(function () {
        savestand();
        return false;
    });
    $("#addcredit").click(function () {
        addcredit(1);
        return false;
    });
    $("#addcredit2").click(function () {
        addcredit(5);
        return false;
    });
    $("#addcredit3").click(function () {
        addcredit(10);
        return false;
    });
    last();
    getNewInquiries();
    // $('.deatepicker').datepicker();

    $("#add-lodging-details").click(function () {
        $("#placelink-label").html("Reserve Now");
        $("#save-new-place").attr("data-type", "lodging");
        $("#add_place_details").modal();
    });

    $("#add-shopping-details").click(function () {
        $("#placelink-label").html("Shop Now");
        $("#save-new-place").attr("data-type", "shopping");
        $("#add_place_details").modal();
    });

    $("#add-adventure-details").click(function () {
        $("#placelink-label").html("Contact Now");
        $("#save-new-place").attr("data-type", "adventure");
        $("#add_place_details").modal();
    });

    $("#add-food-dining-details").click(function () {
        $("#placelink-label").html("View Menu/Make Reservation");
        $("#save-new-place").attr("data-type", "food-dining");
        $("#add_place_details").modal();
    });

    $("#add-grocery-fuel-details").click(function () {
        $("#placelink-label").html("View Website");
        $("#save-new-place").attr("data-type", "grocery-fuel");
        $("#add_place_details").modal();
    });

    $("#add-services-details").click(function () {
        $("#placelink-label").html("Contact Now");
        $("#save-new-place").attr("data-type", "services");
        $("#add_place_details").modal();
    });

    $("#add-culture-details").click(function () {
        $("#placelink-label").html("More Information");
        $("#save-new-place").attr("data-type", "culture");
        $("#add_place_details").modal();
    });
});
$( function() {
    //Add script and css in your file : demo file event/editEvent.php
    $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd',minDate: 0, });

} );
function getNewInquiries() {
    $.ajax({
        url: "route/?action=get-stats&type=get-new-inquiry"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.http_code == 403) {
            eventMessage(jsonobject.content, jsonobject.http_code);
        }
    });
}

function handleresponse(elementid, jsonobject, display) {
    if (display == undefined) {
        if (jsonobject.error == 1) {
            $('#' + elementid).html('<div class="alert alert-danger" role="alert">' + jsonobject.content + '</div>').fadeIn();
        }
        else {
            $('#' + elementid).html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>');
        }
    }
}

function where() {
    if (window.ga) ga('send', 'event', 'bikes', 'where', $('#adminparam').val());
    $.ajax({
        url: "command.php?action=where&bikeno=" + $('#adminparam').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse("fleetconsole", jsonobject);
        eventMessage(jsonobject.content, jsonobject.http_code);
    });
}

function last() {
    if (window.ga) ga('send', 'event', 'bikes', 'last', $('#adminparam').val());
    $.ajax({
        url: "command.php?action=last&bikeno=" + $('#adminparam').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse("fleetconsole", jsonobject);
    });
}

function stands() {
    var code = "";
    $.ajax({
        url: "command.php?action=stands"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.length > 0) code = code + '<div class="list-group" id="standList">';//open list
        if (jsonobject.length > 0)
            for (var i = 0, len = jsonobject.length; i < len; i++) {
                var status = "active";
                if (jsonobject[i]["stand"]["active"] == 'N')
                    status = "inactive";
                code = code + '<a href="#" class="editstand list-group-item  list-group-item-action flex-column align-items-start" data-standid="' + jsonobject[i]["stand"]["standId"] + '">';//open link
                code = code + '<div class="d-flex w-100 justify-content-between">';
                code = code + '<h4 class="mb-1">' + jsonobject[i]["stand"]["standName"] + ' (' + status + ') </h4>';

                code = code + '<small>' + jsonobject[i]["stand"]["standAddress"] + '</small>';
                code = code + '</div>';
                var bikes = jsonobject[i]["bikes"];
                if (bikes.length > 0) code = code + '<ul class="list-group">';//open bike list
                if (bikes.length > 0)
                    for (var j = 0, lenj = bikes.length; j < lenj; j++) {
                        code = code + '<a href="#" class="editbicycle list-group-item" data-bicycleid="' + bikes[j]["bikeNum"] + '">' + 'Bike no: ' + bikes[j]["bikeNum"] + ' - ' + 'Code in use: ' + bikes[j]["currentCode"] + '</br>';
                        if (bikes[j]["note"] != null)
                            code = code + bikes[j]["note"];
                        else
                            code = code + '<p>No additional information available.</p>';
                        code = code + '</a>';
                    }
                if (bikes.length > 0) code = code + '</ul>';//close a list of bikes
                code = code + '<a>';//close link
            }
        if (jsonobject.length > 0) code = code + '</div>';//close list
        $('#standsconsole').html(code);
        createeditlinks();
    });
}

function videos() {
    var code = "";
    $.ajax({
        url: "command.php?action=videolist"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.length > 0) code = '<table class="table table-striped" id="videotable"><thead><tr><th>Video </th><th>Filename</th> <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th> </tr></thead>';
        if (jsonobject.length > 0)
            for (var i = 0, len = jsonobject.length; i < len; i++) {
                code = code + '<tr><td><img src="' + jsonobject[i]["thumbnailPath"] + '" class="img-thumbnail" width="120px" height="120px" ></td> <td><a href="#" class="editvideo" data-videoid="' + jsonobject[i]["videoId"] + '">' + jsonobject[i]["fileName"] + '</a></td><td></td><td></td></td><td></td><td></td><td></td><td></td></td><td></td><td></td></tr>';
            }
        if (jsonobject.length > 0) code = code + '</table>';
        $('#videoconsole').html(code);
        createeditlinks();
        oTable = $('#videotable').dataTable({
            "dom": 'f<"filtertoolbar">prti',
            "paging": false,
            "ordering": false,
            "info": false
        });
    });
}

function inquiries() {
    var code = "";
    $.ajax({
        url: "command.php?action=inquirylist"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.length > 0) code = '<table class="table table-striped" id="inquirytable"><thead><tr><th>Inquiry</th> <th>Phone of Rep.</th> <th>Email of Rep.</th> <th>Solved</th> </tr></thead>';
        if (jsonobject.length > 0)
            for (var i = 0, len = jsonobject.length; i < len; i++) {
                code = code + '<tr> <td><a href="#" class="editinquiry" data-inquiryid="' + jsonobject[i]["inquiryid"] + '">' + jsonobject[i]["inquiry"].substring(0, 40) + '</a></td> <td>' + jsonobject[i]["phone"] + '</td> <td>' + jsonobject[i]["email"] + '</td> <td>' + jsonobject[i]["solved"] + '</td> </tr>';
            }
        if (jsonobject.length > 0) code = code + '</table>';
        $('#inquiryconsole').html(code);
        createeditlinks();
        oTable = $('#inquirytable').dataTable({
            "dom": 'f<"filtertoolbar">prti',
            "paging": false,
            "ordering": false,
            "info": false
        });
    });
}

function userlist() {
    var code = "";
    $.ajax({
        url: "command.php?action=userlist"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.length > 0) code = '<table class="table table-striped" id="usertable"><thead><tr><th>' + _user + '</th><th>' + _privileges + '</th><th>' + _limit + '</th>';
        if (creditenabled == 1) code = code + '<th>' + _credit + '</th>';
        code = code + '</tr></thead>';
        for (var i = 0, len = jsonobject.length; i < len; i++) {
            code = code + '<tr><td><a href="#" class="edituser" data-userid="' + jsonobject[i]["userid"] + '">' + jsonobject[i]["username"] + '</a><br />' + jsonobject[i]["number"] + '<br />' + jsonobject[i]["mail"] + '</td><td>' + jsonobject[i]["privileges"] + '</td><td>' + jsonobject[i]["limit"] + '</td>';
            if (creditenabled == 1) {
                code = code + '<td>' + jsonobject[i]["credit"] + creditcurrency + '</td></tr>';
            }
        }
        if (jsonobject.length > 0) code = code + '</table>';
        $('#userconsole').html(code);
        createeditlinks();
        oTable = $('#usertable').dataTable({
            "dom": 'f<"filtertoolbar">prti',
            "paging": false,
            "ordering": false,
            "info": false
        });
        /*$('div.filtertoolbar').html('<select id="columnfilter"><option></option></select>');
        $('#usertable th').each(function() { $('#columnfilter').append($("<option></option>").attr('value',$(this).text()).text($(this).text())); } );
        $('#usertable_filter input').keyup(function() { x=$('#columnfilter').prop("selectedIndex")-1; if (x==-1) fnResetAllFilters(); else oTable.fnFilter( $(this).val(), x ); });
        $('#columnfilter').change(function() { x=$('#columnfilter').prop("selectedIndex")-1; if (x==-1) fnResetAllFilters(); else oTable.fnFilter( $('#usertable_filter input').val(), x ); });*/
    });
}

function createeditlinks() {
    $('.editinquiry').each(function () {
        $(this).click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-editinquiry', $(this).attr('data-inquiryid'));
            editinquiry($(this).attr('data-inquiryid'));
        });
    });

    $('.edituser').each(function () {
        $(this).click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-edituser', $(this).attr('data-userid'));
            edituser($(this).attr('data-userid'));
        });
    });

    $('.editbicycle').each(function () {
        $(this).click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-editbicycle', $(this).attr('data-bicycleid'));
            editbicycle($(this).attr('data-bicycleid'));
        });
    });

    $('.editstand').each(function () {
        $(this).click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-editstand', $(this).attr('data-standid'));
            editstand($(this).attr('data-standid'));
        });
    });

    $('.editvideo').each(function () {
        $(this).click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-editvideo', $(this).attr('data-videoid'));
            editvideo($(this).attr('data-videoid'));
        });
    });
}

function getStands() {
    $.ajax({
        url: "command.php?action=standsmin"
    }).done(function (jsonresponse) {
        $("#editbicycle #currentstand").empty();
        $("#addbicycle #currentstand").empty();
        jsonobject = $.parseJSON(jsonresponse);
        $.each(jsonobject, function (i) {
            $('#editbicycle #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
            $('#addbicycle #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
        });
    });
}

function getStandsByType(stand_type) {
    $.ajax({
        url: "command.php?action=standsminbytype",
        data: {
            stand_type: stand_type,
        },
    }).done(function (jsonresponse) {
        if(stand_type=='bike_stand'){
            $("#editbicycle #currentstand").empty();
            $("#addbicycle #currentstand").empty();

            jsonobject = $.parseJSON(jsonresponse);
            $.each(jsonobject, function (i) {
                $('#editbicycle #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
                $('#addbicycle #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
            });

        }else if (stand_type=='watercraft_stand'){
            $("#editwatercraft #currentstand").empty();
            $("#addwatercraft #currentstand").empty();

            jsonobject = $.parseJSON(jsonresponse);
            $.each(jsonobject, function (i) {
                $('#editwatercraft #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
                $('#addwatercraft #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
            });
        }else if(stand_type=='event_stand'){
            $("#editevent #currentstand").empty();
            $("#addevent #currentstand").empty();

            jsonobject = $.parseJSON(jsonresponse);
            $.each(jsonobject, function (i) {
                $('#editevent #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
                $('#addevent #currentstand').append('<option value="' + jsonobject[i]["standId"] + '">' + jsonobject[i]["standName"] + '</option>');
            });
        }
    });
}


function getStandsByTypeWithSelected(stand_type,bikeOrEventId) {
    $.ajax({
        url: "command.php?action=standsminbytypewithselected",
        data: {
            stand_type: stand_type,
            bikeOrEventId: bikeOrEventId,
        },
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        //console.log(jsonobject);

        var divTargetted = "#editevent #currentstand";
        if(stand_type=='bike_stand'){
            divTargetted = "#editbicycle #currentstand";
        }else if(stand_type=='watercraft_stand'){
            divTargetted = "#editwatercraft #currentstand";
        }
        $(divTargetted).empty();

        var selectedAttr = '';
        $.each(jsonobject['allStands'], function (i) {
            //console.log(jsonobject['allStands'][i]);
            if(jsonobject['standSelected'] == jsonobject['allStands'][i]["standId"]){
                selectedAttr = ' selected ';
            }else {
                selectedAttr = '';
            }
            $(divTargetted).append('<option value="' + jsonobject['allStands'][i]["standId"] + '" ' + selectedAttr + '>' + jsonobject['allStands'][i]["standName"] + '</option>');
        });

    });
}

function editvideo(videoid) {
    $.ajax({
        url: "command.php?action=editvideo&editvideoid=" + videoid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#editvideo #videoid').val(jsonobject["videoId"]);
            $('#editvideo #filename').val(jsonobject["fileName"]);
            if (jsonobject["thumbnailPath"] != '')
                $('#vimageview').attr('src', jsonobject["thumbnailPath"]);
            else
                $('#vimageview').hide();
            $('#vvideoview').attr('src', jsonobject["videoPath"]);
            $('#editvideo').show();
            $('a[href="#videos"]').trigger('click');
        }
    });
}

function editstand(standid) {

    $.ajax({
        url: "command.php?action=editstand&editstandid=" + standid,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#editstand #standid').val(jsonobject["standId"]);
            $('#editstand #standname').val(jsonobject["standName"]);
            $('#editstand #description').val(jsonobject["standAddress"]);
            $('#editstand #standdescription').val(jsonobject["standDescription"]);
            $('#editstand #type').val(jsonobject["type"]);
            $('#editstand #active').val(jsonobject["active"]);
            $('#editstand #longitude').val(jsonobject["longitude"]);
            $('#editstand #latitude').val(jsonobject["latitude"]);
            if (jsonobject["standPhoto"] != null)
                $('#editstand #fileview').attr('src', jsonobject["standPhoto"]);
            else
                $('#editstand #fileview').hide();
            $('#editstand').show();
        }
    });
}

function editbicycle(bicycleid,bike_type='') {
    if(bike_type && bike_type!=''){
        (bike_type == 'watercraft') ? stand_type = 'watercraft_stand' : stand_type = 'bike_stand';
        // getStandsByType(stand_type);
        getStandsByTypeWithSelected(stand_type,bicycleid);
    }

    $.ajax({
        url: "command.php?action=editbicycle&editbicycleid=" + bicycleid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#editbicycle #bicycleid').val(jsonobject["bikeNum"]);
            $('#editbicycle #bike_no').val(jsonobject["bike_num"]);
            $('#editbicycle #currentstand').val(jsonobject["currentStand"]);
            $('#editbicycle #bike_status').val(jsonobject["bike_status"]);
            if (jsonobject["image_path"] != null)
                $('#editbicycle #fileview').attr('src', jsonobject["image_path"]);
            else
                $('#editbicycle #fileview').hide();
            $('#editbicycle #note').val(jsonobject["note"]);
            $('#editbicycle').show();


            $('#editwatercraft #watercraftid').val(jsonobject["bikeNum"]);
            $('#editwatercraft #bike_no').val(jsonobject["bike_num"]);
            $('#editwatercraft #currentstand').val(jsonobject["currentStand"]);
            $('#editwatercraft #watercraft_status').val(jsonobject["bike_status"]);
            if (jsonobject["image_path"] != null)
                $('#editwatercraft #fileview').attr('src', jsonobject["image_path"]);
            else
                $('#editwatercraft #fileview').hide();
            $('#editwatercraft #note').val(jsonobject["note"]);
            $('#editwatercraft').show();
            //$('a[href="#stands"]').trigger('click');
        }
    });
}

function editevent(eventid, bike_type) {
    stand_type = bike_type + '_stand';
    getStandsByTypeWithSelected(stand_type,eventid);

    $.ajax({
        url: "command.php?action=editevent&editeventid=" + eventid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#editevent #eventid').val(jsonobject["id"]);
            $('#editevent #event_num').val(jsonobject["event_num"]);
            $('#editevent #event_description').val(jsonobject["event_description"]);
            $('#editevent #currentstand').val(jsonobject["current_stand"]);
            $('#editevent #edittotalrides').val(jsonobject["total_rides"]);
            $('#editevent #is_active').val(jsonobject["is_active"]);
            $('#editevent #startdate').val(jsonobject["start_date"]);
            $('#editevent #enddate').val(jsonobject["end_date"]);
            $('#editevent #rsvpdate').val(jsonobject["rsvp_date"]);

            if (jsonobject["image_path"] != null)
                $('#editevent #fileview').attr('src', jsonobject["image_path"]);
            else
                $('#editevent #fileview').hide();

        }
    });
    $('#editevent').show();
    $('#edit_event').modal('show');
}

function editplace(placeid, link_label, type) {
    $.ajax({
        url: "command.php?action=editplace&editplaceid=" + placeid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#editplace #placeid').val(jsonobject["id"]);
            $('#editplace #placename').val(jsonobject["name"]);
            $('#editplace #place_description').val(jsonobject["description"]);
            $('#editplace #placelat').val(jsonobject["latitude"]);
            $('#editplace #placelon').val(jsonobject["longitude"]);
            $('#editplace #placelink-label').html(link_label);
            $('#editplace #placelink').val(jsonobject["link"]);
            $('#editplace #save-place').attr('data-type',type);

            if (jsonobject["photo"] != null)
                $('#editplace #fileview').attr('src', jsonobject["photo"]);
            else
                $('#editplace #fileview').hide();
        }
    });
    $('#editplace').show();
    $('#edit_place').modal('show');
}

function edituser(userid) {
    $.ajax({
        url: "command.php?action=edituser&edituserid=" + userid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#userid').val(jsonobject["userid"]);
            $('#username').val(jsonobject["username"]);
            $('#email').val(jsonobject["email"]);
            $('#mailingaddress').val(jsonobject["mailingaddress"]);
            $('#physicaladdress').val(jsonobject["physicaladdress"]);
            $('#city').val(jsonobject["city"]);
            $('#state').val(jsonobject["state"]);
            $('#zipcode').val(jsonobject["zipcode"]);
            if ($('#phone'))
                $('#phone').val(jsonobject["phone"]);
            $('#privileges').val(jsonobject["privileges"]);
            $('#limit').val(jsonobject["limit"]);
            $('#age').val(jsonobject["age"]);
            $('#gender').val(jsonobject["gender"]);
            $('#race').val(jsonobject["race"]);
            $('#edituser').show();
            $('a[href="#users"]').trigger('click');
        }
    });
}

function showChangePassword(userid){
    $("#change-password-modal").data('bootstrapValidator').resetForm();
    $('#change-password-modal').attr('data-send',0);
    $('#new_password').val("");
    $('#re_new_password').val("");
    $('#change-password-userid').val(userid);
    $("#user-change-password-modal").show();
}

function changeUserPassword(sendEmail){
    var userid = $('#change-password-userid').val();
    var newPassword = $('#new_password').val();
    $.ajax({
        url: "command.php?action=change-user-password",
        method: "POST",
        data:{
            userid: userid,
            password: newPassword,
            send_email: sendEmail
        }
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            handleresponse("standsconsole", jsonobject);
            setTimeout(stands, 2000);
            closeModal();
            eventMessage(jsonobject.content, jsonobject.http_code);
        }
    });
}

function editinquiry(inquiryid) {
    $.ajax({
        url: "command.php?action=editinquiry&inquiryid=" + inquiryid
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#userid').val(jsonobject["userid"]);
            $('#inquiryid').val(jsonobject["inquiryid"]);
            $('#phonenumber').val(jsonobject["phone"]);
            $('#inquiry').val(jsonobject["inquiry"]);
            $('#useremail').val(jsonobject["email"]);
            $('#solved').val(jsonobject["solved"]);
            $('#editinquiry').show();
            $('a[href="#inquiries"]').trigger('click');
        }
    });
}

function addnewstand() {

    var formData = new FormData($("#addstand")[0]);
    formData.append("file", $('#addstand #file')[0].files[0]);
    formData.append("standname", $('#addstand #standname').val());
    formData.append("description", $('#addstand #description').val());
    formData.append("standtype", $('#addstand #standtype').val());
    formData.append("standdescription", $('#addstand #standdescription').val());
    formData.append("longitude", $('#addstand #longitude').val());
    formData.append("latitude", $('#addstand #latitude').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=addnewstand",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        var table = $('#stands-list').DataTable();
        table.ajax.reload();
        // $('#refresh_stands').trigger('click');
    });
}

function savestand() {
    var formData = new FormData($("#editstand")[0]);
    formData.append("file", $('#editstand #file')[0].files[0]);
    formData.append("editstandid", $('#editstand #standid').val());
    formData.append("standname", $('#editstand #standname').val());
    formData.append("description", $('#editstand #description').val());
    formData.append("standdescription", $('#editstand #standdescription').val());
    formData.append("active", $('#editstand #active').val());
    formData.append("longitude", $('#editstand #longitude').val());
    formData.append("latitude", $('#editstand #latitude').val());
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-savestand', $('#standid').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=savestand",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        //$("#editstand").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#refresh_stands').trigger('click');
    });
}

function addnewvideo() {
    var formData = new FormData($("#addvideo")[0]);
    var formData = new FormData();
    formData.append("file", $('#video_file').prop('files')[0]);
    formData.append("thumbnail", $('#video_thumbnail').prop('files')[0]);
    formData.append("filename", $('#video_filename').val());

    $.ajax({
        method: "POST",
        url: "command.php?action=addnewvideo",
        data: formData,
        processData: false,
        contentType: false,
        async: true,
        beforeSend: function () {
            $(".progress").show();
        },
        success: function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            $(".progress").hide();
            //$("#addvideo").hide();
            handleresponse("videoconsole", jsonobject);
            setTimeout(videos, 2000);
            closeModal();
            eventMessage(jsonobject.content, jsonobject.http_code);
            var table = $('#video-list').DataTable();
            table.ajax.reload();

        }
    });
}

function addnewbicycle() {
    var formData = new FormData($("#addbicycle")[0]);
    formData.append("file", $('#addbicycle #file')[0].files[0]);
    formData.append("currentstand", $('#addbicycle #currentstand').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=addnewbicycle",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        //$("#addbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#bikes-list').DataTable().ajax.reload();
    });
}

function addnewwatercraft() {
    var formData = new FormData($("#addwatercraft")[0]);
    formData.append("file", $('#addwatercraft #file')[0].files[0]);
    formData.append("currentstand", $('#addwatercraft #currentstand').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=addnewwatercraft",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        //$("#addbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#watercrafts-list').DataTable().ajax.reload();
    });
}

function addnewevent() {
    var formData = new FormData($("#addevent")[0]);
    formData.append("file", $('#addevent #file')[0].files[0]);
    formData.append("currentstand", $('#addevent #currentstand').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=addnewevent",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        //$("#addbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#events-list').DataTable().ajax.reload();
    });
}

function addnewplace(type,$listElem) {
    var name = $('#addplace #placename').val();
    var description = $('#addplace #place_description').val();
    var latitude = $('#addplace #placelat').val();
    var longitude = $('#addplace #placelon').val();
    var link = $('#addplace #placelink').val();
    var image = $('#addplace #place_image')[0].files[0];
    if(name != "" && latitude != "" && longitude != "" && link != "" && image.length != 0) {
        var formData = new FormData($("#addplace")[0]);
        formData.append("image", image);
        formData.append("name", name);
        formData.append("description", description);
        formData.append("latitude", latitude);
        formData.append("longitude", longitude);
        formData.append("link", link);
        formData.append("type", type);
        $.ajax({
            method: "POST",
            url: "command.php?action=addnewplace",
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            //$("#addbicycle").hide();
            handleresponse("standsconsole", jsonobject);
            setTimeout(stands, 2000);
            closeModal();
            eventMessage(jsonobject.content, jsonobject.http_code);
            $listElem.DataTable().ajax.reload();
        });
    }
}

function saveplace(type) {
    var name = $('#editplace #placename').val();
    var id = $('#editplace #placeid').val();
    var description = $('#editplace #place_description').val();
    var latitude = $('#editplace #placelat').val();
    var longitude = $('#editplace #placelon').val();
    var link = $('#editplace #placelink').val();
    var image = $('#editplace #place_image')[0].files[0];
    if(name != "" && latitude != "" && longitude != "" && link != "") {
        var formData = new FormData($("#editplace")[0]);
        formData.append("editplaceid", id);
        formData.append("image", image);
        formData.append("name", name);
        formData.append("description", description);
        formData.append("latitude", latitude);
        formData.append("longitude", longitude);
        formData.append("link", link);
        $.ajax({
            method: "POST",
            url: "command.php?action=saveplace",
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            //$("#addbicycle").hide();
            handleresponse("standsconsole", jsonobject);
            setTimeout(stands, 2000);
            closeModal();
            eventMessage(jsonobject.content, jsonobject.http_code);
            $('#refresh_'+type).trigger('click');
        });
    }
}

function deletebicycle() {

    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-savebicycle', $('#editbicycle #bicycleid').val());
    $.ajax({
        url: "command.php?action=deletebicycle&deleteid=" + $('#editbicycle #bicycleid').val(),
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#bikes-list').DataTable().ajax.reload();
    });
}

function deletebicycleByType(biketype) {

    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-savebicycle', $('#editbicycle #bicycleid').val());
    var deleteid;
    if(biketype =='event'){
        deleteid = $('#editevent #eventid').val();
    }else if(biketype =='watercraft'){
        deleteid = $('#editwatercraft #watercraftid').val();
    }else{
        deleteid = $('#editbicycle #bicycleid').val();
    }
    $.ajax({
        url: "command.php?action=deletebicycleByType&deleteid=" + deleteid +"&biketype="+biketype,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        var datatableId = '#'+biketype+'s-list';
        $(datatableId).DataTable().ajax.reload();

    });
}

function closeinquiry() {
    $.ajax({
        url: "command.php?action=closeinquiry&inquiryid=" + $('#inquiryid').val(),
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editinquiry").hide();
        handleresponse("inquiryconsole", jsonobject);
        setTimeout(inquiries, 2000);
    });
}

function deletestand() {
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-saveStand', $('#editstand #standid').val());
    $.ajax({
        url: "command.php?action=deletestand&deleteid=" + $('#editstand #standid').val(),
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editstand").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
    });
}

function deletevideo() {

    $.ajax({
        method: "GET",
        url: "command.php?action=deletevideo&deleteid=" + $('#editvideo #videoid').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editvideo").hide();
        handleresponse("videoconsole", jsonobject);
        setTimeout(videos, 2000);
    });
}

function savebicycle() {
    var formData = new FormData($("#editbicycle")[0]);
    formData.append("file", $('#editbicycle #file')[0].files[0]);
    formData.append("editbicycleid", $('#editbicycle #bicycleid').val());
    formData.append("currentstand", $('#editbicycle #currentstand').val());
    formData.append("bike_status", $('#editbicycle #bike_status').val());
    formData.append("bike_type", $('#editbicycle #bike_type').val());
    formData.append("note", $('#editbicycle #note').val());
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-savebicycle', $('#bicycleid').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=savebicycle",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editbicycle").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#bikes-list').DataTable().ajax.reload();
    });
}

function savewatercraft() {
    var formData = new FormData($("#editwatercraft")[0]);
    formData.append("file", $('#editwatercraft #file')[0].files[0]);
    formData.append("editbicycleid", $('#editwatercraft #watercraftid').val());
    formData.append("currentstand", $('#editwatercraft #currentstand').val());
    formData.append("bike_status", $('#editwatercraft #watercraft_status').val());
    formData.append("bike_type", $('#editwatercraft #bike_type').val());
    formData.append("note", $('#editwatercraft #note').val());
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-savebicycle', $('#watercraftid').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=savebicycle",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editwatercraft").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#watercrafts-list').DataTable().ajax.reload();
    });
}

function saveevent() {
    var formData = new FormData($("#editevent")[0]);
    formData.append("currentstand", $('#editevent #currentstand').val());
    formData.append("total_rides", $('#editevent #edittotalrides').val());
    formData.append("is_active", $('#editevent #is_active').val());
    formData.append("file", $('#editevent #file')[0].files[0]);
    formData.append("event_description", $('#editevent #event_description').val());
    formData.append("editeventid", $('#editevent #eventid').val());
    formData.append("bike_type", $('#editevent #bike_type').val());
    formData.append("event_num", $('#editevent #event_num').val());
    formData.append("startdate", $('#editevent #startdate').val());
    formData.append("enddate", $('#editevent #enddate').val());
    formData.append("rsvpdate", $('#editevent #rsvpdate').val());


    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-saveevent', $('#eventid').val());
    $.ajax({
        method: "POST",
        url: "command.php?action=saveevent",
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#editevent").hide();
        handleresponse("standsconsole", jsonobject);
        setTimeout(stands, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#events-list').DataTable().ajax.reload();
    });
}

function saveuser() {
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-saveuser', $('#userid').val());
    var phone = "";
    if ($('#phone')) phone = "&phone=" + $('#phone').val();

    var age = "&age=" + $('#age').val()
    var gender = "&gender=" + $('#gender').val()
    var race = "&race=" + $('#race').val()
    $.ajax({
        url: "command.php?action=saveuser&edituserid=" + $('#userid').val() + "&username=" + $('#username').val() + "&email=" + $('#email').val() + "&mailingaddress=" + $('#mailingaddress').val() + "&physicaladdress=" + $('#physicaladdress').val() + "&city=" + $('#city').val() + "&state=" + $('#state').val() + "&zipcode=" + $('#zipcode').val() + "&privileges=" + $('#privileges').val() + "&limit=" + $('#limit').val() + phone + race + age + gender
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#edituser").hide();
        handleresponse("userconsole", jsonobject);
        setTimeout(userlist, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
    });


}

function save_new_user() {
    // if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-saveuser', $('#userid').val());
    var phone = "";
    if ($('#add_phone')) phone = "&phone=" + $('#add_phone').val();

    var age = "&age=" + $('#add_age').val()
    var gender = "&gender=" + $('#add_gender').val()
    var race = "&race=" + $('#add_race').val()
    $.ajax({
        url: "command.php?action=save_new_user&username=" + $('#add_username').val() + "&email=" + $('#add_email').val() + "&mailingaddress=" + $('#add_mailingaddress').val() + "&physicaladdress=" + $('#add_physicaladdress').val() + "&city=" + $('#add_city').val() + "&state=" + $('#add_state').val() + "&zipcode=" + $('#add_zipcode').val() + "&privileges=" + $('#add_privileges').val() + "&limit=" + $('#add_limit').val() + phone + race + age + gender
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#edituser").hide();
        handleresponse("userconsole", jsonobject);
        setTimeout(userlist, 2000);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
    });
}

function saveHelpAnswer() {
    var answer = $.trim($('#help-modal-answer').val());
    if(answer != ''){
        var inquiryId = $('#inquiryid').val();
        $.ajax({
            url: "command.php?action=savehelp",
            data:{
                "inquiryid":inquiryId,
                "answer":answer
            }
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            $("#help-answer").hide();
            handleresponse("userconsole", jsonobject);
            setTimeout(userlist, 2000);
            closeModal();
            eventMessage(jsonobject.content, jsonobject.http_code);
            $('#refresh_inquiries').trigger('click');
        });
    }
}

function sendmessage() {
    $('#send').html('<span>Sending...</span>');
    $.ajax({
        url: "command.php?action=message&message=" + $('#message').val()
    }).done(function (jsonresponse) {
        $('#send').html('<span>Send</span>');
        jsonobject = $.parseJSON(jsonresponse);
        $("#broadcast").hide();
        handleresponse("userconsole", jsonobject);
        setTimeout(userlist, 2000);
    });

}
function sendeventmessage() {
    $('#eventbroadcastsend').html('<span>Sending...</span>');
    var broadcast_eventid = $('#eventusers_id').val();
    $.ajax({
        url: "command.php?action=eventmessage&message=" + $('#eventmessage').val()+"&eventid="+ broadcast_eventid
    }).done(function (jsonresponse) {
        $('#eventbroadcastsend').html('<span>Send</span>');
        jsonobject = $.parseJSON(jsonresponse);
        $("#eventbroadcast").hide();
        handleresponse("userconsole", jsonobject);
        setTimeout(userlist, 2000);
    });

}

function addcredit(creditmultiplier) {
    if (window.ga) ga('send', 'event', 'buttons', 'click', 'admin-addcredit', $('#userid').val());
    $.ajax({
        url: "command.php?action=addcredit&edituserid=" + $('#userid').val() + "&creditmultiplier=" + creditmultiplier
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $("#edituser").hide();
        handleresponse("userconsole", jsonobject);
        closeModal();
        eventMessage(jsonobject.content, jsonobject.http_code);
        $('#refresh_users').trigger('click');
    });
}

function generatecoupons(multiplier) {
    var code = "";
    $.ajax({
        url: "command.php?action=generatecoupons&multiplier=" + multiplier
    }).done(function (jsonresponse) {
        $('#coupon-list').DataTable().ajax.reload();
    });
}

function trips() {
    if (window.ga) ga('send', 'event', 'bikes', 'trips', $('#adminparam').val());
    $.ajax({
        url: "command.php?action=trips&bikeno=" + $('#adminparam').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.error == 1) {
            handleresponse(elementid, jsonobject);
        }
        else {
            if (jsonobject[0]) // concrete bike requested
            {
                if (polyline != undefined) map.removeLayer(polyline);
                polyline = L.polyline([[jsonobject[0].latitude * 1, jsonobject[0].longitude * 1], [jsonobject[1].latitude * 1, jsonobject[1].longitude * 1]], {color: 'red'}).addTo(map);
                for (var i = 2, len = jsonobject.length; i < len; i++) {
                    if (jsonobject[i].longitude * 1 && jsonobject[i].latitude * 1) {
                        polyline.addLatLng([jsonobject[i].latitude * 1, jsonobject[i].longitude * 1]);
                    }
                }
            }
            else // all bikes requested
            {
                var polylines = [];
                for (var bikenumber in jsonobject) {
                    var bikecolor = '#' + ('00000' + (Math.random() * 16777216 << 0).toString(16)).substr(-6);
                    polylines[bikenumber] = L.polyline([[jsonobject[bikenumber][0].latitude * 1, jsonobject[bikenumber][0].longitude * 1], [jsonobject[bikenumber][1].latitude * 1, jsonobject[bikenumber][1].longitude * 1]], {color: bikecolor}).addTo(map);
                    for (var i = 2, len = jsonobject[bikenumber].length; i < len; i++) {
                        if (jsonobject[bikenumber][i].longitude * 1 && jsonobject[bikenumber][i].latitude * 1) {
                            polylines[bikenumber].addLatLng([jsonobject[bikenumber][i].latitude * 1, jsonobject[bikenumber][i].longitude * 1]);
                        }
                    }
                }
            }

        }
    });
}


function editHelpAnswer(helpId) {
    $.ajax({
        url: "command.php?action=edithelp&edithelpid=" + helpId
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject) {
            $('#inquiryid').val(jsonobject["id"]);
            $('.help-modal-question').html(jsonobject["question"]);
            $('#help-modal-answer').val(jsonobject["answer"]);
            $('#help-answer').show();
        }
    });
}

function revert() {
    if (window.ga) ga('send', 'event', 'bikes', 'revert', $('#adminparam').val());
    $.ajax({
        url: "command.php?action=revert&bikeno=" + $('#adminparam').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse("fleetconsole", jsonobject);
    });
}

function fnResetAllFilters() {
    var oSettings = oTable.fnSettings();
    for (iCol = 0; iCol < oSettings.aoPreSearchCols.length; iCol++) {
        oSettings.aoPreSearchCols[iCol].sSearch = '';
    }
    oTable.fnDraw();
}

var closeModal = function () {
    $('.close').trigger('click');
};

