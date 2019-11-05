$(document).ready(function () {

});


function initEventTable(){
    $('#events-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=events-list",
        "drawCallback": initCompleteEvents,
        "stateSave": true,
        "columnDefs": [{
            "targets": 3,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[3] + '" data-fancybox="events' + data[3] + '" href="big_1.jpg">' +
                    '<img src="' + data[3] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
                return html;
            }
        }, {
            "className": "",
            "targets": 6,
            "data": null,
            'render': function (data, type, row, meta) {
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                var currentDate = new Date(data[6]);
                var date = currentDate.getDate();
                var month = currentDate.getMonth();
                var year = currentDate.getFullYear();

                return html = monthNames[month] + " " + date +  " " + year;
            }
        }, {
            "className": "",
            "targets": 7,
            "data": null,
            'render': function (data, type, row, meta) {
                if (data[7] == 1) {
                    return html = ' <span class="label label-primary">Active</span>';
                } else {
                    return html = ' <span class="label label-warning">Inactive</span>';
                }
            }
        }
        , {
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 8,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editevent(' + data[8] + ',\'event\')"  data-id="' + data[8] + '" title="Edit event"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="event_users.php?id='+data[8]+'"  class="btn btn-primary userlist-event-btn" data-id="' + data[8] + '" title="User list" data-toggle="tooltip">' +
                    '<i class="fa fa-user"></i>' +
                    '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-event-btn" data-id="' + data[8] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#events-list').DataTable();

    $('#refresh_events').click(function () {
        table.ajax.reload();
    });
    // $('#edit_event').on('hidden.bs.modal', function (e) {
    //     $("#currentstand option").remove();
    //     //alert($('#currentstand').html());
    //     $(this)
    //         .find("input,textarea,select")
    //         .val('')
    //         .end()
    //         .find("input[type=checkbox], input[type=radio]")
    //         .prop("checked", "")
    //         .end();
    //
    // });
}

var initCompleteEvents = function (setting) {
    $('.delete-event-btn').unbind().bind('click', function () {
        var eventId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=events-delete&event-id=" + eventId,
                        success: function (data, textStatus, jqXHR) {
                            $('#events-list').DataTable().ajax.reload();
                            jsonobject = $.parseJSON(data);
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