$(document).ready(function () {

});

function initUserTable(){
    $('#user-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=users-list",
        "drawCallback": initCompleteUsers,
        "stateSave": true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        "columnDefs": [{
            "className": "",
            "targets": 4,
            "data": null,
            'render': function (data, type, row, meta) {
                if (data[4] == "7") {
                    return html = ' <span class="label label-primary">Admin</span>';
                } else {
                    return html = ' <span class="label label-default">User</span>';
                }
            }
        }, {
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 7,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-primary"  onclick="edituser(' + data[7] + ')"  data-id="' + data[7] + '" title="View" data-target=".edit-user-modal" data-toggle="tooltip">';
                html += '<i class="fa fa-edit"></i></a>';
                html += '</a>';

                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-success"  onclick="showChangePassword(' + data[7] + ')"  data-id="' + data[7] + '" title="View" data-target=".user-change-password-modal" data-toggle="tooltip">'+
                '<i class="fa fa-key"></i></a>'+
                '</a>';

                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-user-btn" data-id="' + data[7] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }]
    });
    var table = $('#user-list').DataTable();

    $('#refresh_users').click(function () {
        table.ajax.reload();
    });
}

var initCompleteUsers = function (setting) {
    $('.delete-user-btn').unbind().bind('click', function () {
        var userId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "POST",
                        url: "route?action=users-delete&user-id=" + userId,
                        success: function (data, textStatus, jqXHR) {
                            $('#user-list').DataTable().ajax.reload();
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