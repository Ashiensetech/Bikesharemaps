$(document).ready(function () {

});

function initVideoTable() {
    $('#video-list').DataTable({
        "processing": true,
        "serverSide": true,
        "drawCallback": initCompleteVideos,
        "ajax": "route?action=video-list",
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<div class="image-holder"><a type="button" data-toggle="modal" data-target="#myModal' + data[0] + '" class="video-thumb">' +
                    '<i class="fa fa-play-circle"></i><img style="width:200px;" src="'+ data[3] +'" ></a></div>' +
                    '<div class="modal fade video-modal" id="myModal' + data[0] + '" role="dialog">' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body">' +
                    '<video name="videoview"  width="100%" height="400" controls> +' +
                    '<source src="  ' + data[2] + ' " type="video/mp4"> +' +
                    '</video>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                return html;
            }
        }, {
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 3,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<a href="javascript:void(0)" class="btn btn-warning delete-video-btn" data-id="' + data[4] + '" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'

                return html;
            }
        }]
    });

    var table = $('#video-list').DataTable();
    $('#videolistRefresh').click(function () {
        table.ajax.reload();
    });
}


var initCompleteVideos = function (setting) {
    $('.delete-video-btn').unbind().bind('click', function () {
        var videoId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "POST",
                        url: "route?action=video-delete&video-id=" + videoId,
                        success: function (data, textStatus, jqXHR) {
                            $('#video-list').DataTable().ajax.reload();
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