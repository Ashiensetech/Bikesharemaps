$(document).ready(function () {

});

function initCouponTable() {
    $('#coupon-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "drawCallback": initSoldCoupon,
        "ajax": "route?action=coupon-list",
        "columnDefs": [{
            "orderable": false,
            "searchable": false,
            "className": "",
            "targets": 3,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = ''
                html += '<button type="button" class="btn btn-warning sellcoupon" data-coupon-id="' + data[3] + '"><span class="glyphicon glyphicon-share-alt"></span>  Mark as sold</button>'
                return html;
            }
        }]
    });

    var table = $('#coupon-list').DataTable();

    $('#refresh_coupon').click(function () {
        table.ajax.reload();
    });
}


var initSoldCoupon = function (setting) {
    $('.sellcoupon').unbind().bind('click', function () {
        var couponId = $(this).data("coupon-id");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to sell?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "POST",
                        url: "command.php?action=sellcoupon&couponid=" + couponId,
                        success: function (data, textStatus, jqXHR) {
                            $('#coupon-list').DataTable().ajax.reload();
                            var jsonobject = $.parseJSON(data);
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