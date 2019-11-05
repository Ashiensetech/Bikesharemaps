$(function () {

    // Change User for user Stats
    $('body').on('change', '#total-rental-time-user', function () {
        resetCanvasUserBikeRent();
        getUserBikeRentByDate($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
        resetCanvasUserBikeRental();
        getTotalUserBikeRentalTime($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());

        $('#user-all-time-rentals-per-bike').DataTable().ajax.reload();
        $('#user-total-rentals-per-bike').DataTable().ajax.reload();
    });

    // Change Bike for User stats
    $('body').on('change', '#total-rental-time-bike', function () {
        resetCanvasUserBikeRental();
        getTotalUserBikeRentalTime($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
    });

    // init All Select2 field
    $('#total-rental-time-user').select2({
        allowClear: true,
        placeholder: 'Select an user',
        ajax: {
            url: 'route/?action=get-stats&type=get-users',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                }
                // Query parameters will be ?search=[term]
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    $('#total-rental-time-bike').select2({
        allowClear: true,
        placeholder: 'Select a bike',
        ajax: {
            url: 'route/?action=get-stats&type=get-bikes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                }
                // Query parameters will be ?search=[term]
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    $('#bike-rental-filter-bike').select2({
        allowClear: true,
        placeholder: 'Select a bike',
        ajax: {
            url: 'route/?action=get-stats&type=get-bikes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                }
                // Query parameters will be ?search=[term]
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    $("#bike-rent-filter-age").select2();
    $("#bike-rent-filter-race").select2();
    $("#bike-rent-filter-gender").select2();

    $("#bike-rental-filter-age").select2();
    $("#bike-rental-filter-race").select2();
    $("#bike-rental-filter-gender").select2();


    // Change age for bike rent stats
    $('body').on('change', '#bike-rent-filter-age', function () {
        reloadBikeRentByFilter();
    });
    // Change race for bike rent stats
    $('body').on('change', '#bike-rent-filter-race', function () {
        reloadBikeRentByFilter();
    });
    // Change gender for bike rent stats
    $('body').on('change', '#bike-rent-filter-gender', function () {
        reloadBikeRentByFilter();
    });
    // Change age for bike rental stats
    $('body').on('change', '#bike-rental-filter-age', function () {
        reloadBikeRentalByFilter();
    });
    // Change race for bike rental stats
    $('body').on('change', '#bike-rental-filter-race', function () {
        reloadBikeRentalByFilter();
    });
    // Change gender for bike rental stats
    $('body').on('change', '#bike-rental-filter-gender', function () {
        reloadBikeRentalByFilter();
    });
    // Change bike for bike rental stats
    $('body').on('change', '#bike-rental-filter-bike', function () {
        reloadBikeRentalByFilter();
    });
});

function reloadBikeRentByFilter() {
    resetCanvasBikeRentfilter();
    getBikeRentByFilter($('#bike-rent-filter-start-date').val(), $('#bike-rent-filter-end-date').val());
}

function reloadBikeRentalByFilter() {
    resetCanvasBikeRentalfilter();
    getBikeRentalByFilter($('#bike-rental-filter-start-date').val(), $('#bike-rental-filter-end-date').val());
}

// Daily stats and User stats tab on click
function openStat(evt, reportType) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("report-tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("report-tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" report-active", "");
    }
    document.getElementById(reportType).style.display = "block";
    evt.currentTarget.className += " report-active";
}

// Initialize all dailystats functionalities
function getDailyStats() {
    getBikeRentByDate($("#bike-rent-start-date").val(), $("#bike-rent-end-date").val());

    initTotalRentalsPerBike();
    initTotalRentalsPerStand();
    initTotalReturnsPerStand();

    getBikeRentByFilter($('#bike-rent-filter-start-date').val(), $('#bike-rent-filter-end-date').val());
    getBikeRentalByFilter($('#bike-rental-filter-start-date').val(), $('#bike-rental-filter-end-date').val());

    initUserAllTimeRentalsPerBike();
    initUserTotalRentalsPerBike();

    getUserBikeRentByDate($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
    getTotalUserBikeRentalTime($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
    getWeatherStats();

    // Initialize all bike rent daterangepicker
    $('#bike-rent-daterange-btn').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function (start, end) {
            $('#bike-rent-daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#bike-rent-start-date').val(start.format('YYYY-MM-DD'));
            $('#bike-rent-end-date').val(end.format('YYYY-MM-DD'));
            resetCanvasBikeRent();
            getBikeRentByDate($('#bike-rent-start-date').val(), $('#bike-rent-end-date').val());
        }
    );

    // Initialize users all bike rental daterangepicker
    $('#user-bike-rental-daterange-btn').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        },
        function (start, end) {
            $('#user-bike-rental-daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#user-bike-rental-start-date').val(start.format('YYYY-MM-DD'));
            $('#user-bike-rental-end-date').val(end.format('YYYY-MM-DD'));
            resetCanvasUserBikeRental();
            getTotalUserBikeRentalTime($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
            resetCanvasUserBikeRent();
            getUserBikeRentByDate($('#user-bike-rental-start-date').val(), $('#user-bike-rental-end-date').val());
            $('#user-total-rentals-per-bike').DataTable().ajax.reload();
        }
    );

    // Initialize all bike rent daterangepicker
    $('#bike-rent-filter-daterange').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function (start, end) {
            $('#bike-rent-filter-daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#bike-rent-filter-start-date').val(start.format('YYYY-MM-DD'));
            $('#bike-rent-filter-end-date').val(end.format('YYYY-MM-DD'));
            resetCanvasBikeRentfilter();
            getBikeRentByFilter($('#bike-rent-filter-start-date').val(), $('#bike-rent-filter-end-date').val());
        }
    );

    // Initialize all bike rental daterangepicker
    $('#bike-rental-filter-daterange').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function (start, end) {
            $('#bike-rental-filter-daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#bike-rental-filter-start-date').val(start.format('YYYY-MM-DD'));
            $('#bike-rental-filter-end-date').val(end.format('YYYY-MM-DD'));
            resetCanvasBikeRentalfilter();
            getBikeRentalByFilter($('#bike-rental-filter-start-date').val(), $('#bike-rental-filter-end-date').val());
        }
    );

    // Get bike Rent By Date
    var start_date = new Date($('#bike-rent-start-date').val());
    var end_date = new Date($('#bike-rent-end-date').val());
    $("#bike-rent-daterange-btn").data('daterangepicker').setStartDate((start_date.getMonth() + 1) + "/" + start_date.getDate() + "/" + start_date.getFullYear());
    $("#bike-rent-daterange-btn").data('daterangepicker').setEndDate((end_date.getMonth() + 1) + "/" + end_date.getDate() + "/" + end_date.getFullYear());

    // Get bike Rent By Filter
    var filter_start_date = new Date($('#bike-rent-filter-start-date').val());
    var filter_end_date = new Date($('#bike-rent-filter-end-date').val());
    $("#bike-rent-filter-daterange").data('daterangepicker').setStartDate((filter_start_date.getMonth() + 1) + "/" + filter_start_date.getDate() + "/" + filter_start_date.getFullYear());
    $("#bike-rent-filter-daterange").data('daterangepicker').setEndDate((filter_end_date.getMonth() + 1) + "/" + filter_end_date.getDate() + "/" + filter_end_date.getFullYear());

    // Get bike Rent By Filter
    var filter_rental_start_date = new Date($('#bike-rent-filter-start-date').val());
    var filter_rental_end_date = new Date($('#bike-rent-filter-end-date').val());
    $("#bike-rental-filter-daterange").data('daterangepicker').setStartDate((filter_rental_start_date.getMonth() + 1) + "/" + filter_rental_start_date.getDate() + "/" + filter_rental_start_date.getFullYear());
    $("#bike-rental-filter-daterange").data('daterangepicker').setEndDate((filter_rental_end_date.getMonth() + 1) + "/" + filter_rental_end_date.getDate() + "/" + filter_rental_end_date.getFullYear());

    // Get Bike rent by user and date
    var user_rental_start_date = new Date($('#user-bike-rental-start-date').val());
    var user_rental_end_date = new Date($('#user-bike-rental-end-date').val());
    $("#user-bike-rental-daterange-btn").data('daterangepicker').setStartDate((user_rental_start_date.getMonth() + 1) + "/" + user_rental_start_date.getDate() + "/" + user_rental_start_date.getFullYear());
    $("#user-bike-rental-daterange-btn").data('daterangepicker').setEndDate((user_rental_end_date.getMonth() + 1) + "/" + user_rental_end_date.getDate() + "/" + user_rental_end_date.getFullYear());


}

// reset all bike rent canvas
function resetCanvasBikeRent() {
    $('#graph-container-bike-rent').html("");
    $('#chart-graph-bike-rent').remove();
    $('#graph-container-bike-rent').append('<canvas id="chart-graph-bike-rent"><canvas>');
}

// reset all filtered bike rent canvas
function resetCanvasBikeRentfilter() {
    $('#graph-container-bike-rent-filter').html("");
    $('#chart-graph-bike-rent-filter').remove();
    $('#graph-container-bike-rent-filter').append('<canvas id="chart-graph-bike-rent-filter"><canvas>');
}

// reset all filtered bike rental canvas
function resetCanvasBikeRentalfilter() {
    $('#graph-container-bike-rental-filter').html("");
    $('#chart-graph-bike-rental-filter').remove();
    $('#graph-container-bike-rental-filter').append('<canvas id="chart-graph-bike-rental-filter"><canvas>');
}

// reset user all bike rent canvas
function resetCanvasUserBikeRent() {
    $('#graph-container-user-bike-rent').html("");
    $('#chart-graph-user-bike-rent').remove();
    $('#graph-container-user-bike-rent').append('<canvas id="chart-graph-user-bike-rent"><canvas>');
}

// reset user all bike rental canvas
function resetCanvasUserBikeRental() {
    $('#graph-container-total-bike-rental-time').html("");
    $('#chart-graph-total-bike-rental-time').remove();
    $('#graph-container-total-bike-rental-time').append('<canvas id="chart-graph-total-bike-rental-time"><canvas>');
}

// Get all bike rent data
function getBikeRentByDate(start_time, end_time) {
    try {
        var data = {
            start_time: start_time,
            end_time: end_time
        };
        // alert("See");
        $.ajax({
            url: 'route/?action=daily-stats&type=get-bike-rent-by-date',
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (response) {
                var cartdata = {
                    labels: response.time,
                    datasets: [{
                        label: response.label[0],
                        data: response.rent_by_date,
                        borderWidth: 1,
                        backgroundColor: "#58C2E1",
                        borderColor: "#142536",
                        borderDash: [5, 2],
                        fill: false
                    }],
                };

                new Chart(document.getElementById('chart-graph-bike-rent').getContext("2d"), {
                    type: 'bar',
                    data: cartdata,
                    responsive: true,
                    maintainAspectRatio: false,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });

            },
            error: function (e) {
                console.log(e);
            }
        });
    }
    catch (err) {
        console.log(err.message);
    }
}

// Get users all bike rent data
function getUserBikeRentByDate(start_time, end_time) {
    var user_id = $('#total-rental-time-user').val();
    if (user_id != '') {
        try {
            var data = {
                start_time: start_time,
                end_time: end_time
            };
            data['user_id'] = user_id;

            $.ajax({
                url: 'route/?action=daily-stats&type=get-bike-rent-by-date',
                type: 'POST',
                data: data,
                dataType: "json",
                success: function (response) {
                    var cartdata = {
                        labels: response.time,
                        datasets: [{
                            label: response.label[0],
                            data: response.rent_by_date,
                            borderWidth: 1,
                            backgroundColor: "#58C2E1",
                            borderColor: "#142536",
                            borderDash: [5, 2],
                            fill: false
                        }],
                    };

                    new Chart(document.getElementById('chart-graph-user-bike-rent').getContext("2d"), {
                        type: 'bar',
                        data: cartdata,
                        responsive: true,
                        maintainAspectRatio: false,
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });

                },
                error: function (e) {
                    console.log(e);
                }
            });
        }
        catch (err) {
            console.log(err.message);
        }
    } else {
        new Chart(document.getElementById('chart-graph-user-bike-rent').getContext("2d"), {
            type: 'bar',
            data: {},
            responsive: true,
            maintainAspectRatio: false,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }

}

// Get all bike rent data by filter
function getBikeRentByFilter(start_time, end_time) {
    try {
        var data = {
            start_time: start_time,
            end_time: end_time
        };
        var filter_age = $('#bike-rent-filter-age').val();
        var filter_race = $('#bike-rent-filter-race').val();
        var filter_gender = $('#bike-rent-filter-gender').val();
        data['filter_age'] = filter_age;
        data['filter_race'] = filter_race;
        data['filter_gender'] = filter_gender;

        $.ajax({
            url: 'route/?action=daily-stats&type=get-bike-rent-by-filter',
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (response) {
                var cartdata = {
                    labels: response.time,
                    datasets: [{
                        label: response.label[0],
                        data: response.rent_by_filter,
                        borderWidth: 1,
                        backgroundColor: "#58C2E1",
                        borderColor: "#142536",
                        borderDash: [5, 2],
                        fill: false
                    }],
                };

                new Chart(document.getElementById('chart-graph-bike-rent-filter').getContext("2d"), {
                    type: 'bar',
                    data: cartdata,
                    responsive: true,
                    maintainAspectRatio: false,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });

            },
            error: function (e) {
                console.log(e);
            }
        });
    }
    catch (err) {
        console.log(err.message);
    }
}

// Get all bike rental data by filter
function getBikeRentalByFilter(start_time, end_time) {
    try {
        var data = {
            start_time: start_time,
            end_time: end_time
        };
        var filter_age = $('#bike-rental-filter-age').val();
        var filter_race = $('#bike-rental-filter-race').val();
        var filter_gender = $('#bike-rental-filter-gender').val();
        var filter_bike_id = $('#bike-rental-filter-bike').val();
        data['filter_age'] = filter_age;
        data['filter_race'] = filter_race;
        data['filter_gender'] = filter_gender;
        data['filter_bike_id'] = filter_bike_id;

        $.ajax({
            url: 'route/?action=daily-stats&type=get-bike-rental-by-filter',
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (response) {
                var cartdata = {
                    labels: response.time,
                    datasets: response.items,
                };

                new Chart(document.getElementById('chart-graph-bike-rental-filter').getContext("2d"), {
                    type: 'bar',
                    data: cartdata,
                    responsive: true,
                    maintainAspectRatio: false,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });

            },
            error: function (e) {
                console.log(e);
            }
        });
    }
    catch (err) {
        console.log(err.message);
    }
}

// Get users all bike rental data
function getTotalUserBikeRentalTime(start_time, end_time) {
    var user_id = $('#total-rental-time-user').val();
    if (user_id != '') {
        try {
            var data = {
                start_time: start_time,
                end_time: end_time
            };
            var bike_id = $('#total-rental-time-bike').val();
            data['user_id'] = user_id;
            data['bike_id'] = bike_id;

            $.ajax({
                url: 'route/?action=daily-stats&type=get-total-bike-rental-time',
                type: 'POST',
                data: data,
                dataType: "json",
                success: function (response) {
                    var cartdata = {
                        labels: response.time,
                        datasets: response.items,
                    };

                    new Chart(document.getElementById('chart-graph-total-bike-rental-time').getContext("2d"), {
                        type: 'bar',
                        data: cartdata,
                        responsive: true,
                        maintainAspectRatio: false,
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });

                },
                error: function (e) {
                    console.log(e);
                }
            });
        }
        catch (err) {
            console.log(err.message);
        }
    } else {
        new Chart(document.getElementById('chart-graph-total-bike-rental-time').getContext("2d"), {
            type: 'bar',
            data: [],
            responsive: true,
            maintainAspectRatio: false,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
}

// Get Total rentals data per bike
function initTotalRentalsPerBike() {
    $('#total-rentals-per-bike').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "pageLength": 7,
        "searching": false,
        "ajax": "route/?action=daily-stats&type=get-total-rentals-per-bike",
        "columnDefs": [{
            "className": "",
            "targets": 0,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = 'Bike: ' + data[0];
                return html;
            }
        },{
            "className": "text-right",
            "targets": 1,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = data[1];
                return html;
            }
        }]
    });
}

// Get User Total rentals data per bike
function initUserTotalRentalsPerBike() {
    $('#user-total-rentals-per-bike').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "pageLength": 7,
        "searching": false,
        "ajax": {
            "url": "route/?action=daily-stats&type=get-user-total-rentals-per-bike",
            "data": function ( d ) {
                d.start_time = $('#user-bike-rental-start-date').val();
                d.end_time = $('#user-bike-rental-end-date').val();
                var user_id = $('#total-rental-time-user').val();
                if(user_id == ''){
                    user_id = 0;
                }
                d.user_id = user_id;
            }
        },
        "columnDefs": [{
            "className": "",
            "targets": 0,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = 'Bike: ' + data[0];
                return html;
            }
        },{
            "className": "text-right",
            "targets": 1,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = data[1];
                return html;
            }
        }]
    });
}

// Get User All Time rentals data per bike
function initUserAllTimeRentalsPerBike() {
    $('#user-all-time-rentals-per-bike').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "pageLength": 7,
        "searching": false,
        "ajax": {
            "url": "route/?action=daily-stats&type=get-user-all-time-rentals-per-bike",
            "data": function ( d ) {
                var user_id = $('#total-rental-time-user').val();
                if(user_id == ''){
                    user_id = 0;
                }
                d.user_id = user_id;
            }
        },
        "columnDefs": [{
            "className": "",
            "targets": 0,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = 'Bike: ' + data[0];
                return html;
            }
        },{
            "className": "text-right",
            "targets": 1,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = data[1];
                return html;
            }
        }]
    });
}

// Get Total rentals data per stand
function initTotalRentalsPerStand() {
    $('#total-rentals-per-stand').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "pageLength": 7,
        "searching": false,
        "ajax": "route/?action=daily-stats&type=get-total-rentals-per-stand",
        "columnDefs": [{
            "className": "",
            "targets": 0,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = 'Stand: ' + data[0];
                return html;
            }
        },{
            "className": "text-right",
            "targets": 1,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = data[1];
                return html;
            }
        }]
    });
}

// Get Total returns data per stand
function initTotalReturnsPerStand() {
    $('#total-returns-per-stand').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "pageLength": 7,
        "searching": false,
        "ajax": "route/?action=daily-stats&type=get-total-returns-per-stand",
        "columnDefs": [{
            "className": "",
            "targets": 0,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = 'Stand: ' + data[0];
                return html;
            }
        },{
            "className": "text-right",
            "targets": 1,
            "data": null,
            'render': function (data, type, row, meta) {
                var html = data[1];
                return html;
            }
        }]
    });
}

function getWeatherStats() {
    var params = {
        q: "kansas",
        units: "imperial",
        appid: "96c7d8a14bc8629bd178a8bf0bb0de00"
        // appid: "fd3150a661c1ddc90d3aefdec0400de4"
    }
    var dates = [];
    var temps = [];
    $.ajax({
        url: 'https://api.openweathermap.org/data/2.5/forecast',
        type: 'GET',
        data: params,
        dataType: "json",
        success: function (response) {
            dates = response.list.map(list => {
                return list.dt_txt;
            });

            temps = response.list.map(list => {
                return list.main.temp;
            })
            console.log(dates);

            var ctx = document.getElementById("chart-graph-weather");
            this.chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: "Avg. Temp",
                            backgroundColor: "rgba(54, 162, 235, 0.5)",
                            borderColor: "rgb(54, 162, 235)",
                            fill: false,
                            data: temps
                        }
                    ]
                },
                options: {
                    title: {
                        display: true,
                        text: "Temperature with Chart.js"
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].label || "";

                                if (label) {
                                    label += ": ";
                                }

                                label += Math.floor(tooltipItem.yLabel);
                                return label + "°F";
                            }
                        }
                    },
                    scales: {
                        xAxes: [
                            {
                                type: "time",
                                time: {
                                    unit: "day",
                                    // displayFormats: {
                                    //     hour: "M/DD @ hA"
                                    // },
                                    // tooltipFormat: "MMM. DD @ hA"
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: "Date/Time"
                                }
                            }
                        ],
                        yAxes: [
                            {
                                scaleLabel: {
                                    display: true,
                                    labelString: "Temperature (°F)"
                                },
                                ticks: {
                                    callback: function (value, index, values) {
                                        return value + "°F";
                                    }
                                }
                            }
                        ]
                    }
                }
            });
        },
        error: function (e) {
            console.log(e);
        }
    });
}
