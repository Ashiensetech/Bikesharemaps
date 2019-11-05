$(document).ready(function () {

});

function initWatercraftTable(){
    $('#watercrafts-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=watercrafts-list",
        "drawCallback": initCompleteWatercrafts,
        "stateSave": true,
        "columnDefs": [{
            "targets": 6,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[6] + '" data-fancybox="watercrafts' + data[6] + '" href="big_1.jpg">' +
                    '<img src="' + data[6] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
                return html;
            }
        }, {
            "className": "",
            "targets": 7,
            "data": null,
            'render': function (data, type, row, meta) {
                if (data[7] == "Y") {
                    return html = ' <span class="label label-primary">Active</span>';
                } else {
                    return html = ' <span class="label label-warning">Inactive</span>';
                }
            }
        }, {
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 8,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-primary"  onclick="editbicycle(' + data[8] + ',\'watercraft\')"  data-id="' + data[8] + '" title="Edit Watercraft" data-target=".edit-watercraft-modal" data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-watercraft-btn" data-id="' + data[8] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }]
    });

    var table = $('#watercrafts-list').DataTable();

    $('#refresh_watercrafts').click(function () {
        table.ajax.reload();
    });
}

var initCompleteWatercrafts = function (setting) {
    $('.delete-watercraft-btn').unbind().bind('click', function () {
        var bikeId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=watercrafts-delete&watercraft-id=" + bikeId,
                        success: function (data, textStatus, jqXHR) {
                            $('#watercrafts-list').DataTable().ajax.reload();
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