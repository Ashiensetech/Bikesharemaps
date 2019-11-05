$(document).ready(function () {

});


function initLodgingTable() {
    $('#lodging-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=lodging",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'Reserve Now:\',\'lodging\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="lodging" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#lodging-list').DataTable();

    $('#refresh_lodging').click(function () {
        table.ajax.reload();
    });
}

function initShoppingTable() {
    $('#shopping-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=shopping",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'Shop Now:\',\'shopping\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="shopping" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#shopping-list').DataTable();

    $('#refresh_shopping').click(function () {
        table.ajax.reload();
    });
}

function initAdventureTable() {
    $('#adventure-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=adventure",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'Contact Now:\',\'adventure\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="adventure" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#adventure-list').DataTable();

    $('#refresh_adventure').click(function () {
        table.ajax.reload();
    });
}

function initFoodDiningTable() {
    $('#food-dining-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=food-dining",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'View Menu/Make Reservation:\',\'food-dining\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="food-dining" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#food-dining-list').DataTable();

    $('#refresh_food_dining').click(function () {
        table.ajax.reload();
    });
}

function initGroceryFuelTable() {
    $('#grocery-fuel-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=grocery-fuel",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'View Website:\',\'grocery-fuel\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="grocery-fuel" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#grocery-fuel-list').DataTable();

    $('#refresh_grocery_fuel').click(function () {
        table.ajax.reload();
    });
}

function initServicesTable() {
    $('#services-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=services",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'Contact Now:\',\'services\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="services" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#services-list').DataTable();

    $('#refresh_services').click(function () {
        table.ajax.reload();
    });
}

function initCultureTable() {
    $('#culture-list').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": "route?action=place-list&type=culture",
        "drawCallback": initCompletePlace,
        "stateSave": true,
        "columnDefs": [{
            "targets": 2,
            "data": null,
            "render": function (data, type, row, meta) {
                var html = '<a href="' + data[2] + '" data-fancybox="place' + data[2] + '">' +
                    '<img src="' + data[2] + '" class="img-rounded" height="100" width="100" style="cursor: pointer;">' +
                    '</a>';
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
                html += '<a href="javascript:void(0)"   class="btn btn-primary"  onclick="editplace(' + data[3] + ',\'More Information:\',\'culture\')"  data-id="' + data[3] + '" title="Edit Details"  data-toggle="tooltip">'
                html += '<i class="fa fa-edit"></i></a>'
                html += '</a>'
                html += '<a href="javascript:void(0)"  class="btn btn-warning delete-place-btn" data-id="' + data[3] + '" data-type="culture" title="Delete" data-toggle="tooltip">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>'
                return html;
            }
        }
        ]
    });

    var table = $('#culture-list').DataTable();

    $('#refresh_culture').click(function () {
        table.ajax.reload();
    });
}

var initCompletePlace = function (setting) {
    $('.delete-place-btn').unbind().bind('click', function () {
        var placeId = $(this).data("id");
        var type = $(this).data("type");
        $.confirm({
            title: 'Confirm!!!',
            content: 'Are you sure you want to delete?',
            buttons: {
                confirm: function () {
                    $.ajax({
                        method: "GET",
                        url: "route?action=place-delete&place-id=" + placeId,
                        success: function (data, textStatus, jqXHR) {
                            $('#'+type+'-list').DataTable().ajax.reload();
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