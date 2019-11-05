<?php

?>
<div id="daily_stats_report" class="report-tabcontent report-contents">
    <div class="row">
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">List of bikes rented
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>

                </div>
                <div class="panel-body">

                    <div class=" btn-group clearfix report-btn-grp">
                        <button type="button" class="btn btn-info btn-outline cstm-fbtn"
                                id="bike-rent-daterange-btn">
                            <span><i class="fa fa-calendar"></i> <i
                                        class="caret"></i>&nbsp;<?php echo $bikeReports['date_range']; ?></span>
                        </button>
                        <input type="hidden" name="start_time" id="bike-rent-start-date"
                               value="<?php echo $bikeReports['last_week']; ?>">
                        <input type="hidden" name="end_time" id="bike-rent-end-date"
                               value="<?php echo $bikeReports['today']; ?>">
                    </div>

                    <div id="graph-container-bike-rent" class="chart-container">
                        <canvas id="chart-graph-bike-rent" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel trent-panel">
                <div class="panel-heading">
                    <div class="panel-title">Total Rentals per Bike</div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped valign-middle" id="total-rentals-per-bike">
                            <thead>
                            <tr>
                                <th>Bike No.</th>
                                <th class="text-right">Total Rentals</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel trps-panel">
                <div class="panel-heading">
                    <div class="panel-title">Total Rentals per Stand</div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped valign-middle" id="total-rentals-per-stand">
                            <thead>
                            <tr>
                                <th>Stand No.</th>
                                <th class="text-right">Total Rentals</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">Total Returns per Stand</div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped valign-middle" id="total-returns-per-stand">
                            <thead>
                            <tr>
                                <th>Stand No.</th>
                                <th class="text-right">Total Returns</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">List of bikes rented By
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>

                </div>
                <div class="panel-body">
                    <div class="col-md-3">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Age</label>
                            <div class="col-sm-9">
                                <select id="bike-rent-filter-age">
                                    <option value="">Select an age</option>
                                    <option value="18">Under 18</option>
                                    <option value="18-29">18-29</option>
                                    <option value="30-44">30-44</option>
                                    <option value="45-64">45-64</option>
                                    <option value="65+">65+</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Gender</label>
                            <div class="col-sm-9">
                                <select id="bike-rent-filter-gender">
                                    <option value="">Select a gender
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                    <option value="no-answer">Prefer not to Answer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Race</label>
                            <div class="col-sm-12 ">
                                <select id="bike-rent-filter-race">
                                    <option value="">Select a race</option>
                                    <option value="white">White</option>
                                    <option value="hispanic-or-latino">Hispanic or Latino</option>
                                    <option value="black-or-african-american">Black or African American</option>
                                    <option value="native-american">Native American</option>
                                    <option value="asian-or-pacific">Asian / Pacific Islander</option>
                                    <option value="other">Other</option>
                                    <option value="no-answer">Prefer not to Answer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="clearfix btn-group report-btn-grp">
                                    <button type="button" class="cstm-fbtn btn btn-info btn-outline"
                                            id="bike-rent-filter-daterange">
                                    <span><i class="fa fa-calendar"></i> <i
                                                class="caret"></i>&nbsp;<?php echo $bikeReports['date_range']; ?></span>
                                    </button>
                                    <input type="hidden" name="start_time" id="bike-rent-filter-start-date"
                                        value="<?php echo $bikeReports['last_week']; ?>">
                                    <input type="hidden" name="end_time" id="bike-rent-filter-end-date"
                                        value="<?php echo $bikeReports['today']; ?>">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div id="graph-container-bike-rent-filter" class="chart-container">
                        <canvas id="chart-graph-bike-rent-filter" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">List of bikes rental Time(minutes) By
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>

                </div>
                <div class="panel-body">
                    <div class="col-md-2">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Age</label>
                            <div class="col-sm-12 ">
                                <select id="bike-rental-filter-age">
                                    <option value="">Select an age</option>
                                    <option value="18">Under 18</option>
                                    <option value="18-29">18-29</option>
                                    <option value="30-44">30-44</option>
                                    <option value="45-64">45-64</option>
                                    <option value="65+">65+</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-2">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Gender</label>
                            <div class="col-sm-12 ">
                                <select id="bike-rental-filter-gender">
                                    <option value="">Select a gender
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                    <option value="no-answer">Prefer not to Answer</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-3">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Race</label>
                            <div class="col-sm-12 ">
                                <select id="bike-rental-filter-race">
                                    <option value="">Select a race</option>
                                    <option value="white">White</option>
                                    <option value="hispanic-or-latino">Hispanic or Latino</option>
                                    <option value="black-or-african-american">Black or African American</option>
                                    <option value="native-american">Native American</option>
                                    <option value="asian-or-pacific">Asian / Pacific Islander</option>
                                    <option value="other">Other</option>
                                    <option value="no-answer">Prefer not to Answer</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-2">
                        <div class="row clearfix">
                           <label class="col-sm-12 cstm-label">Bike</label>
                            <div class="col-sm-12 ">
                                <select id="bike-rental-filter-bike">
                                    <option value="">Select a Bike</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group report-btn-grp">
                            <button type="button" class="btn btn-info btn-outline cstm-fbtn"
                                    id="bike-rental-filter-daterange">
                            <span><i class="fa fa-calendar"></i> <i
                                        class="caret"></i>&nbsp;<?php echo $bikeReports['date_range']; ?></span>
                            </button>
                            <input type="hidden" name="start_time" id="bike-rental-filter-start-date"
                                   value="<?php echo $bikeReports['last_week']; ?>">
                            <input type="hidden" name="end_time" id="bike-rental-filter-end-date"
                                   value="<?php echo $bikeReports['today']; ?>">
                        </div>
                    </div>

                    <div id="graph-container-bike-rental-filter" class="chart-container">
                        <canvas id="chart-graph-bike-rental-filter" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="user_stats_report" class="report-tabcontent report-contents">
    <div class="row">
        <div class="col-md-4">
            <div class="row clearfix">
               <label class="col-sm-12 cstm-label">User</label>
                <div class="col-sm-12 ">
                    <select id="total-rental-time-user">
                        <option value="">Select an user</option>
                    </select>
                </div>
            </div>
            
        </div>
        <div class="col-md-3">
            <div class="btn-group report-btn-grp">
                <button type="button" class="btn btn-info btn-outline cstm-fbtn"
                        id="user-bike-rental-daterange-btn">
                                <span><i class="fa fa-calendar"></i> <i
                                            class="caret"></i>&nbsp;<?php echo $bikeReports['date_range']; ?></span>
                </button>
            </div>
        </div>
        <input type="hidden" name="start_time" id="user-bike-rental-start-date"
               value="<?php echo $bikeReports['last_week']; ?>">
        <input type="hidden" name="end_time" id="user-bike-rental-end-date"
               value="<?php echo $bikeReports['today']; ?>">
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">Number of bikes rented
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="graph-container-user-bike-rent" class="chart-container">
                        <canvas id="chart-graph-user-bike-rent" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">Total Time of Rental (minutes)
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row clearfix">
                        <label class="col-sm-1 cstm-label">Bike</label>
                        <div class="col-md-4">
                            <select id="total-rental-time-bike">
                                <option value="">Select a Bike</option>
                            </select>
                        </div>
                    </div>
                    <div id="graph-container-total-bike-rental-time">
                        <canvas id="chart-graph-total-bike-rental-time" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel trent-panel">
                <div class="panel-heading">
                    <div class="panel-title">Total Rentals per Bike</div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped valign-middle" id="user-total-rentals-per-bike">
                            <thead>
                            <tr>
                                <th>Bike No.</th>
                                <th class="text-right">Total Rentals</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel trent-panel">
                <div class="panel-heading">
                    <div class="panel-title">All Time Rentals per Bike</div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped valign-middle" id="user-all-time-rentals-per-bike">
                            <thead>
                            <tr>
                                <th>Bike No.</th>
                                <th class="text-right">Total Rentals</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="weather_stats_report" class="report-tabcontent report-contents">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">Weather
                        <div class="pull-right" style="display: none">
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="graph-container-weather">
                        <canvas id="chart-graph-weather" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="application/javascript" src="js/reports.js"></script>