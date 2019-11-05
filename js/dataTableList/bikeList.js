$(document).ready(function () {

});

function initBikeTable(){
    $('#bikes-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=bikes-list",
        "drawCallback": initCompleteBikes,
        "stateSave": true,
        "columnDefs": [{
            "targets": 6,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[6] + '" data-fancybox="bikes' + data[6] + '" href="big_1.jpg">' +
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
                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-primary"  onclick="editbicycle(' + data[8] + ',\'bike\')"  data-id="' + data[8] + '" title="Edit Bike" data-target=".edit-bike-modal" data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-bike-btn" data-id="' + data[8] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }]
    });

    var table = $('#bikes-list').DataTable();

    $('#refresh_bikes').click(function () {
        table.ajax.reload();
    });

}

var initCompleteBikes = function (setting) {
    $('.delete-bike-btn').unbind().bind('click', function () {
        var bikeId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=bikes-delete&bike-id=" + bikeId,
                        success: function (data, textStatus, jqXHR) {
                            $('#bikes-list').DataTable().ajax.reload();
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