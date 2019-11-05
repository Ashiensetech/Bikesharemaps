$(document).ready(function () {

});

function initInquiryTable() {
    $('#inquiry-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "order": [[5, "desc"]],
        "drawCallback": initCompleteInquiries,
        "ajax": "route?action=inquiry-list",
        "columnDefs": [{
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 5,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                var checked = "";
                if (data[5] == "Y") {
                    checked = "checked";
                }
                html += '<label class="switch">\n' +
                    '            <input class="inquiry_status" data-id="' + data[6] + '" type="checkbox"  ' + checked + ' >\n' +
                    '            <span class="slider"></span>\n' +
                    '        </label>';

                return html;
            }
        }
        // , {
        //     "orderable": false,
        //     "searchable": false,
        //     "className": "",
        //     "targets": 5,
        //     "data": null,
        //     'render': function (data, type, row, meta) {
        //         var html = ''
        //         html += '<a href="javascript:void(0)" class="btn btn-warning delete-inquiry-btn" data-id="' + data[5] + '" title="Delete" data-toggle="tooltip">' +
        //             '<i class="fa fa-trash"></i>' +
        //             '</a>'
        //
        //         return html;
        //     }
        // }
        ]
    });
    var table = $('#inquiry-list').DataTable();

    $('#refresh_inquiries').click(function () {
        table.ajax.reload();
    });
}

var initCompleteInquiries = function (setting) {
    $('.delete-inquiry-btn').unbind().bind('click', function () {
        var inquiryId = $(this).data("id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "POST",
                        url: "route?action=inquiry-delete&inquiry-id=" + inquiryId,
                        success: function (data, textStatus, jqXHR) {
                            $('#inquiry-list').DataTable().ajax.reload();
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

    $('.inquiry_status').click(function () {
        var status = ($(this).is(':checked')) ? "Y" : "N";
        var inquiryId = $(this).data("id");
        $.ajax({
            method: "GET",
            url: "route?action=inquiry-status&inquiry-id=" + inquiryId + "&status=" + status,
            success: function (data, textStatus, jqXHR) {
                // $('#inquiry-list').DataTable().ajax.reload();
                jsonobject = $.parseJSON(data);
                eventMessage(jsonobject.content, jsonobject.http_code);
            },
            error: function (data) {
            }
        });

    });

}

function initHelpTable() {
    $('#help-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "order": [[4, "desc"]],
        // "drawCallback": initCompleteInquiries,
        "ajax": "route?action=help-list",
        "columnDefs": [{
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 4,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<a href="javascript:void(0)"  data-toggle="modal" class="btn btn-primary help-answer-btn"  onclick="editHelpAnswer(' + data[0] + ')"  data-id="' + data[0] + '" title="View" data-target=".help-answer-modal" data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>';
                return html;
            }
        }]
    });
    var table = $('#help-list').DataTable();
    $('#refresh_inquiries').click(function () {
        table.ajax.reload();
    });
}