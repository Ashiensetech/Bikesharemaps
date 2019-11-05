$(document).ready(function () {
    var eventusers_id = $('#eventusers_id').val();
    if(eventusers_id !=''){
        initEventusersTable(eventusers_id);
    }

});

function initEventusersTable(eventusers_id){
    $('#eventusers-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=eventusers-list&eventusers_id="+eventusers_id,
        "drawCallback": initCompleteEventusers,
        "stateSave": true,
        "columnDefs": [
            {
                "targets": 4 ,
                "visible": false,
                "searchable": false
            },
            {
                "orderable": false,
                "searchable": false,
                "className": "",
                "targets": 5,
                "data": null,
                'render': function (data, type, row, meta) {
                    var html = '';

                    var selectedDate = new Date(data[4]);
                    var now = new Date(); //Format not matched;
                    //now.setHours(0,0,0,0);
                    // console.log(now.getTime());
                    // console.log(selectedDate.getTime());
                    //rsvp must be bigger than today = upcoming rsvp date

                    // var same = d1.getTime() === d2.getTime();
                    // var notSame = d1.getTime() !== d2.getTime();
                    var disablestr = '';
                    var expiredEvent = '';
                    if (selectedDate.getTime() < now.getTime()) {
                        disablestr = 'disabled';
                        expiredEvent = '(Expired event)';
                    }
                    html += '<a href="javascript:void(0)"  class="btn btn-warning delete-eventuser-btn" data-id="' + data[5] + '" title="Delete ' + expiredEvent + '" data-toggle="tooltip" ' + disablestr + '>' +
                        '<i class="fa fa-trash"></i>' +
                        '</a>';
                    return html;
                }
            }
        ]
    });

    var table = $('#eventusers-list').DataTable();

    $('#refresh_eventusers').click(function () {
        table.ajax.reload();
    });
}

var initCompleteEventusers = function (setting) {
    $('.delete-eventuser-btn').unbind().bind('click', function () {
        var eventuser = $(this).data("id");
        var eventid = $('#eventusers_id').val();
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=eventusers-delete&eventuser-id=" + eventuser + "&eventid="+eventid,
                        success: function (data, textStatus, jqXHR) {
                            $('#eventusers-list').DataTable().ajax.reload();
                            jsonobject = $.parseJSON(data);
                            // console.log(jsonobject);
                            eventMessage(jsonobject.content, jsonobject.http_code);
                        },
                        error: function (data) {
                        }
                    });
                    return true;
                },
                cancel: function () {
                }
            }
        });
    });

}