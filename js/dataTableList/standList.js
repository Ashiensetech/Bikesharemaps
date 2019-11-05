$(document).ready(function () {

});


function initStandTable(){
    $('#stands-list').DataTable({
        "responsive": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=stands-list",
        "drawCallback": initCompleteStands,
        "stateSave": true,
        "columnDefs": [{
            "targets": 3,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[3] + '" data-fancybox="stands' + data[3] + '" href="big_1.jpg">' +
                    '<img src="' + data[3] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
                return html;
            }
        },{

            "targets": 2,
            "width": "5px"


        },{

            "targets": 1,
            "width": "5px"


        },{

            "targets": 4,
            "width": "5px"


        },{

            "targets": 5,
            "width": "5px"


        },{

            "targets": 7,
            "width": "5px"


        },{

            "targets": 8,
            "width": "5px"


        }, {
            "className": "",
            "targets": 8,
            "data": null,
            'render': function (data, type, row, meta) {
                if (data[8] == "Y") {
                    return html = ' <span class="label label-primary">Active</span>';
                } else {
                    return html = ' <span class="label label-warning">Inactive</span>';
                }
            }
        }, {
            "className": "",
            "targets": 4,
            "data": null,
            'render': function (data, type, row, meta) {
                if (data[4] == "watercraft_stand") {
                    return html = 'Watercraft Stand';
                } else if (data[4] == "bike_stand") {
                    return html = 'Bike Stand';
                } else if (data[4] == "event_stand") {
                    return html = 'Event Stand';
                }else {
                    return html = '';
                }
            }
        }, {
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 9,
            "data": null,
            'render': function (data, type, row, meta) {

                var html = ''

                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-primary"  onclick="editstand(' + data[9] + ')"  data-id="' + data[9] + '" title="Edit Stand" data-target=".edit-stand-modal" data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'

                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-stand-btn" data-id="' + data[9] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'

                return html;
            }
        }]
    });

    var table = $('#stands-list').DataTable();

    $('#refresh_stands').click(function () {
        table.ajax.reload();
    });
}

var initCompleteStands = function (setting) {
    $('.delete-stand-btn').unbind().bind('click', function () {
        var standId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=stands-delete&stand-id=" + standId,
                        success: function (data, textStatus, jqXHR) {
                            $('#stands-list').DataTable().ajax.reload();
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