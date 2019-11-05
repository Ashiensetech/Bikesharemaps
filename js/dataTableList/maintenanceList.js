$(document).ready(function () {

});

function initMaintenanceTable() {
    // $.fn.dataTable.enum(['Red', 'Orange', 'Yellow', 'Green']);
    $('#maintenance-list-table').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
         "order": [[3, "desc"]],
        // "drawCallback": initCompleteInquiries,
        "ajax": "route?action=maintenance-list",
        "columnDefs": [
            { "orderData": [ 5 ],    "targets": 3 },
            {
                "targets": [ 5 ],
                "visible": false,
                "searchable": false
            },
            {
                "orderable": false,
                "searchable": false,
                "className": "",
                "targets": 4,
                "data": null,
                'render': function (data, type, row, meta) {
                    var html = ''
                    html += '<button type="button" class="btn btn-primary reset-total-rental" data-id="'+data[0]+'" title="Reset total rental."><span class="glyphicon glyphicon-repeat"></span></button>';
                    return html;
                }
            },
            {
                "orderable": true,
                "searchable": false,
                "className": "",
                "targets": 3,
                "data": null,
                'render': function (data, type, row, meta) {
                    var html = '';
                    if(data[3]=='Green'){
                        html += '<img src="images/green.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Yellow'){
                        html += '<img src="images/yellow.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Orange'){
                        html += '<img src="images/orange.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Red'){
                        html += '<img src="images/red.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else {
                        html += '<img src="http://via.placeholder.com/50x50?text=No+Image+Found" alt="No Image Found"/>';
                    }
                    return html;
                }
            },

        ],
        "drawCallback": function( row, data ) {
            $('.reset-total-rental').bind('click', function () {
                var maintenanceId = $(this).data("id");

                $.confirm({
                    title: 'Confirm!!!',
                    content: 'Are you sure you want to reset?',
                    buttons: {
                        confirm: function () {
                            $.ajax({
                                method: "GET",
                                url: "route?action=maintenance-reset&maintenanceId=" + maintenanceId,
                                success: function (data, textStatus, jqXHR) {
                                    $('#maintenance-list-table').DataTable().ajax.reload();
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
    });

    var table = $('#maintenance-list-table').DataTable();

    $('#refresh_maintenance').click(function () {
        table.ajax.reload();
    });
    getMaintenanceSettings();

}

//Watercraft Maintenance Datatable
function initWatercraftMaintenanceTable() {
    $('#watercraft-maintenance-list-table').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
         "order": [[3, "desc"]],
        // "drawCallback": initCompleteInquiries,
        "ajax": "route?action=watercraft-maintenance-list",
        "columnDefs": [
            { "orderData": [ 5 ],    "targets": 3 },
            {
                "targets": [ 5 ],
                "visible": false,
                "searchable": false
            },
            {
                "orderable": false,
                "searchable": false,
                "className": "",
                "targets": 4,
                "data": null,
                'render': function (data, type, row, meta) {
                    var html = ''
                    html += '<button type="button" class="btn btn-primary reset-total-rental-watercraft" data-id="'+data[0]+'" title="Reset total rental."><span class="glyphicon glyphicon-repeat"></span></button>';
                    return html;
                }
            },
            {
                "orderable": true,
                "searchable": false,
                "className": "",
                "targets": 3,
                "data": null,
                'render': function (data, type, row, meta) {
                    var html = '';
                    if(data[3]=='Green'){
                        html += '<img src="images/green.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Yellow'){
                        html += '<img src="images/yellow.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Orange'){
                        html += '<img src="images/orange.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else if(data[3]=='Red'){
                        html += '<img src="images/red.png" style="width: 50px;" alt="'+data[3]+'"/>';
                    }else {
                        html += '<img src="http://via.placeholder.com/50x50?text=No+Image+Found" alt="No Image Found"/>';
                    }
                    return html;
                }
            },

        ],
        "drawCallback": function( row, data ) {
            $('.reset-total-rental-watercraft').bind('click', function () {
                var maintenanceId = $(this).data("id");

                $.confirm({
                    title: 'Confirm!!!',
                    content: 'Are you sure you want to reset?',
                    buttons: {
                        confirm: function () {
                            $.ajax({
                                method: "GET",
                                url: "route?action=maintenance-reset&maintenanceId=" + maintenanceId,
                                success: function (data, textStatus, jqXHR) {
                                    $('#watercraft-maintenance-list-table').DataTable().ajax.reload();
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
    });

    var table = $('#watercraft-maintenance-list-table').DataTable();

    $('#refresh_maintenance').click(function () {
        table.ajax.reload();
    });
    

}

function getMaintenanceSettings(){
    $.ajax({
        method: "GET",
        url: "command.php?action=get-maintenance-settings",
        success: function (data, textStatus, jqXHR) {
            jsonobject = $.parseJSON(data);
            eventMessage(jsonobject.content, jsonobject.http_code);
            var setting_value = jsonobject.content;
            console.log(setting_value);
            $('#number-of-rentals-input').val(setting_value);
        },
        error: function (data) {
        }
    });
}