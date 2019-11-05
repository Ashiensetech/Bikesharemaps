var map;
var markers = [];
var markerdata = [];
var placedata = [];
var iconsize = 60;
var sidebar;
var sidebarList;
var firstrun = 1;
var watchID, circle, polyline;
var temp = "";

$(document).ready(function () {

    function note() {
        $('#notetext').slideToggle();
        $('#notetext').val('');
    }

    $('#overlay').hide();
    $('#standactions').hide();
    $('.bicycleactions').hide();
    $('#profile').hide();
    $('#report').hide();
    $('#notetext').hide();
    $('#couponblock').hide();
    $('#passwordresetblock').hide();
    $("#rent").hide();
    $("#bike").hide();
    $(document).ajaxStart(function () {
        $('#overlay').show();
    });
    $(document).ajaxStop(function () {
        $('#overlay').hide();
    });
    $("#password").focus(function () {
        $('#passwordresetblock').show();
    });
    // $(".toggleprofile").click(function(e) { if (window.ga) ga('send', 'event', 'buttons', 'click', 'toggle-profile'); e.preventDefault(); editprofile($('#userid').val()); validateprofile();  });
    // $(".togglehelp").click(function() { if (window.ga) ga('send', 'event', 'buttons', 'click', 'toggle-help'); $('#report').toggle(); validateinquiry(); });
    // $("#saveprofile").click(function(e) { e.preventDefault(); if (window.ga) ga('send', 'event', 'buttons', 'click', 'save-profile'); saveprofile();  });
    // $("#sendinquiry").click(function(e) { e.preventDefault(); if (window.ga) ga('send', 'event', 'buttons', 'click', 'send-inquiry'); sendreport();  });
    // $("#closeprofile").click(function() { if (window.ga) ga('send', 'event', 'buttons', 'click', 'close-profile'); $('#profile').hide();  });
    $("#resetpassword").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'password-reset');
        resetpassword();
    });
    $("#rent").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'bike-rent');
        rent();
    });
    $("#rentevent").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'event-rent');
        rentevent();
    });
    $("#cbtn").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', '');
        toggle();
    });
    $("#return").click(function (e) {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'bike-return');
        // returnbike();
        returnbikebytype('bike');
    });
    $("#returnwatercraft").click(function (e) {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'watercraft-return');
        //returnbike();
        returnbikebytype('watercraft');
    });
    $("#returnevent").click(function (e) {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'watercraft-return');
        returnevent();
    });
    $("#note").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'bike-note');
        note();
    });

    $("#notewatercraft").click(function () {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'watercraft-note');
        note();
    });

    $('#stands').change(function () {
        var type =$('#stands option:selected').attr('data-type');
        if (type != 'bike_stand' && type != 'watercraft_stand' & type != 'event_stand'){
            showplace($('#stands').val());
        }else{
            showstand($('#stands').val());
        }
    }).keyup(function () {
        var type = $('#stands option:selected').attr('data-type');
        if (type != 'bike_stand' && type != 'watercraft_stand' & type != 'event_stand'){
            showplace($('#stands').val());
        }else{
            showstand($('#stands').val());
        }
    });
    if ($('usercredit')) {
        $("#opencredit").click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'credit-enter');
            $('#couponblock').toggle();
        });
        $("#validatecoupon").click(function () {
            if (window.ga) ga('send', 'event', 'buttons', 'click', 'credit-add');
            validatecoupon();
        });
    }
    mapinit();
    setInterval(getmarkers, 60000); // refresh map every 60 seconds
    setInterval(getuserstatus, 60000); // refresh map every 60 seconds
    // if ("geolocation" in navigator) {
    //     navigator.geolocation.getCurrentPosition(showlocation, function () {
    //         return;
    //     }, {enableHighAccuracy: true, maximumAge: 30000});
    //     watchID = navigator.geolocation.watchPosition(changelocation, function () {
    //         return;
    //     }, {enableHighAccuracy: true, maximumAge: 15000});
    // }
});

function mapinit() {
    $("body").data("mapcenterlat", maplat);
    $("body").data("mapcenterlong", maplon);
    $("body").data("mapzoom", mapzoom);
    var customControl = L.Control.extend({

        options: {
            position: 'topleft'
        },

        onAdd: function (map) {
            var container = L.DomUtil.create('a','collapse-left');
            container.title = "No cat";
            container.innerHTML = "<i class='fa fa-bars'></i>";

            container.style.backgroundColor = 'black';
            // container.style.backgroundImage = "url(https://t1.gstatic.com/images?q=tbn:ANd9GcR6FCUMW5bPn8C4PbKak2BJQQsmC-K9-mbYBeFZm1ZM2w2GRy40Ew)";
            container.style.backgroundSize = "30px 30px";
            container.style.width = '30px';
            container.style.height = '30px';

            container.onmouseover = function () {
                container.style.backgroundColor = 'pink';
            }
            container.onmouseout = function () {
                container.style.backgroundColor = 'white';
            }

            container.onclick = function () {
                sidebar.toggle();
            }

            return container;
        }
    });

    map = new L.Map('map');

    // create the tile layer with correct attribution
    var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var osmAttrib = 'Map data (c) <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
    var osm = new L.TileLayer(osmUrl, {minZoom: 8, maxZoom: 19, attribution: osmAttrib});

    var today = new Date();
    if (today.getMonth() + '.' + today.getDate() == '3.1') // april fools
    {
        var osm = new L.StamenTileLayer("toner");
    }

    map.setView(new L.LatLng($("body").data("mapcenterlat"), $("body").data("mapcenterlong")), $("body").data("mapzoom"));
    map.addLayer(osm);
    sidebarList = new customControl();
    sidebar = L.control.sidebar('sidebar', {
        position: 'left'
    });
    map.addControl(sidebar);
    map.addControl(sidebarList);
    getmarkers();
    $('link[rel="points"]').each(function () {
        geojsonurl = $(this).attr("href");
        $.getJSON(geojsonurl, function (data) {
            var geojson = L.geoJson(data, {
                onEachFeature: function (feature, layer) {
                    layer.bindPopup(feature.properties.name);
                },
                pointToLayer: function (feature, latlng) {
                    return L.circleMarker(latlng, {
                        radius: 8,
                        fillColor: "#ff7800",
                        color: "#000",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    });
                }
            });
            geojson.addTo(map);
        });
    });
    getuserstatus();
    resetconsole();
    rentedbikes();
    registeredevents();
    sidebar.show();

}

function getmarkers() {
    $.ajax({
        global: false,
        url: "command.php?action=map:markers"
    }).done(function (jsonresponse) {
        var response = $.parseJSON(jsonresponse);
        var jsonobject = response['stands'];
        for (var i = 0, len = jsonobject.length; i < len; i++) {

            if(jsonobject[i].standtype == "bike_stand"){
                if (jsonobject[i].bikecount == 0) {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icondesc  none icon-bike-none" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].bikecount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
                else {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icon-bike" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].bikecount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
            }else if(jsonobject[i].standtype == "watercraft_stand"){
                if (jsonobject[i].bikecount == 0) {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icondesc  none icon-watercraft-none" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].bikecount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
                else {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icon-watercraft" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].bikecount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
            }else{
                if (jsonobject[i].eventcount == 0) {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icondesc  none icon-event-none" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].eventcount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
                else {
                    tempicon = L.divIcon({
                        iconSize: [iconsize, iconsize],
                        iconAnchor: [25, 50],
                        html: '<dl class="icon-event" id="stand-' + jsonobject[i].standName + '"><dt class="bikecount">' + jsonobject[i].eventcount + '</dt><dd class="standname arrow_box">' + jsonobject[i].standName + '</dd></dl>',
                        standid: jsonobject[i].standId
                    });
                }
            }

            markerdata[jsonobject[i].standId] = {
                name: jsonobject[i].standName,
                desc: jsonobject[i].standAddress,
                photo: jsonobject[i].standPhoto,
                count: jsonobject[i].bikecount,
                ecount: jsonobject[i].eventcount,
                type: jsonobject[i].standtype
            };
            markers[jsonobject[i].standId] = L.marker([jsonobject[i].lat, jsonobject[i].lon], {icon: tempicon}).addTo(map).on("click", showstand);
        }
        var placesobject = response['places'];
        for (var j = 0; j < placesobject.length; j++) {

            if(placesobject[j].type == "lodging"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-lodging" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "shopping"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-shopping" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "adventure"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-adventure" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "food-dining"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-food-dining" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "grocery-fuel"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-grocery-fuel" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "services"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-services" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }
            if(placesobject[j].type == "culture"){
                tempicon = L.divIcon({
                    iconSize: [iconsize, iconsize],
                    iconAnchor: [25, 50],
                    html: '<dl class="icon-culture" id="place-' + placesobject[j].name + '"><dd class="standname arrow_box">' + placesobject[j].name + '</dd></dl>',
                    placeid: placesobject[j].id
                });
            }

            placedata[placesobject[j].id] = {
                name: placesobject[j].name,
                desc: placesobject[j].description,
                photo: placesobject[j].photo,
                link: placesobject[j].link,
                type: placesobject[j].type
            };
            markers["place_"+placesobject[j].id] = L.marker([placesobject[j].lat, placesobject[j].lon], {icon: tempicon}).addTo(map).on("click", showplace);
        }
        $('body').data('markerdata', markerdata);
        if (firstrun == 1) {
            createstandselector();
            firstrun = 0;
        }
    });
}

function showplace(e, clear){
    standselected = 1;
    sidebar.show();
    if (/^place_/.test(e)) {
        standid = e.split("_")[1]; // passed via manual call
        lat = markers[e]._latlng.lat;
        long = markers[e]._latlng.lng;
    }
    else {
        standid = e.target.options.icon.options.placeid; // passed via event call
        lat = e.latlng.lat;
        long = e.latlng.lng;
    }
    resetconsole();

    $('#stands').val("place_"+standid);
    $('#stands option[value="del"]').remove();
    $('#rent').hide();
    $('#rentevent').hide();

    var standtype = placedata[standid].type;
    var btnText = "Reserve now"
    if(standtype == 'shopping'){
        btnText = "Shop Now"
    }else if(standtype == 'adventure'){
        btnText = "Contact Now"
    }else if(standtype == 'food-dining'){
        btnText = "View Menu/Make Reservation"
    }else if(standtype == 'grocery-fuel'){
        btnText = "View Website"
    }else if(standtype == 'services'){
        btnText = "Contact Now"
    }else if(standtype == 'culture'){
        btnText = "More Information"
    }
    walklink = '';
    if ("geolocation" in navigator) // if geolocated, provide link to walking directions
    {
        walklink = '<a href="https://www.google.com/maps?q=' + $("body").data("mapcenterlat") + ',' + $("body").data("mapcenterlong") + '+to:' + lat + ',' + long + '&saddr=' + $("body").data("mapcenterlat") + ',' + $("body").data("mapcenterlong") + '&daddr=' + lat + ',' + long + '&output=classic&dirflg=w&t=m" target="_blank" title="' + _open_map + '">' + _walking_directions + '</a>';
    }
    if (loggedin == 1 && placedata[standid].photo) {
        walklink
        $('#standinfo').html(placedata[standid].desc + ' (' + walklink + ' )');
        $('#standphoto').html('<img src="' + placedata[standid].photo + '" alt="' + placedata[standid].name + '" width="100%" />');
        $('#standphoto').show();
        $('#standbikes').html('<a class="btn btn-'+standtype+'" href="'+placedata[standid].link+'" target="_blank">'+btnText+'</a>');
    }
    else if (loggedin == 1) {
        $('#standinfo').html(placedata[standid].desc);
        if (walklink) $('#standinfo').html(placedata[standid].desc + ' (' + walklink + ')');
        $('#standphoto').hide();
    }
    else {
        $('#standinfo').hide();
        $('#standphoto').hide();
    }
    $('#bike').hide();
    $('.bicycleactions').hide();

}

function getuserstatus() {
    $.ajax({
        global: false,
        url: "command.php?action=map:status"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        $('body').data('limit', jsonobject.limit);
        $('body').data('rented', jsonobject.rented);
        if ($('usercredit')) $('#usercredit').html(jsonobject.usercredit);
        togglebikeactions();
    });
}

function createstandselector() {
    var selectdata = '<option value="del">-- ' + _select_stand + ' --</option>';
    markerdata = $('body').data('markerdata');
    var bike_options = "";
    var watercraft_options = "";
    var event_options = "";
    $.map(markerdata, function (elementOfArray, indexInArray) {
        if (elementOfArray != undefined) {
            //console.log(elementOfArray.standtype);
            if(elementOfArray.type == "bike_stand"){
                bike_options += '<option value="' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="'+elementOfArray.type+'">' + elementOfArray.name + ' ('+ elementOfArray.count +')</option>';
            } else if(elementOfArray.type == "watercraft_stand"){
                watercraft_options += '<option value="' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="'+elementOfArray.type+'">' + elementOfArray.name + ' ('+ elementOfArray.count +')</option>';
            }else if(elementOfArray.type == "event_stand"){
                event_options += '<option value="' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="'+elementOfArray.type+'">' + elementOfArray.name + ' ('+ elementOfArray.ecount +')</option>';
            } else if (elementOfArray.type == "lodging") {
                lodging_options += '<option value="' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            }
        }
    });
    selectdata = '<optgroup label="Bike stands">'+bike_options+'</optgroup>'+
        '<optgroup label="Watercraft stands">'+watercraft_options+'</optgroup>'+
        '<optgroup label="Event stands">'+event_options+'</optgroup>';
    var lodging_options = "";
    var shopping_options = "";
    var adventure_options = "";
    var food_dining_options = "";
    var grocery_fuel_options = "";
    var services_options = "";
    var culture_options = "";
    $.map(placedata, function (elementOfArray, indexInArray) {
        if (elementOfArray != undefined) {
            if (elementOfArray.type == "lodging") {
                lodging_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "shopping") {
                shopping_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "adventure") {
                adventure_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "food-dining") {
                food_dining_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "grocery-fuel") {
                grocery_fuel_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "services") {
                services_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            } else if (elementOfArray.type == "culture") {
                culture_options += '<option value="place_' + indexInArray + '" data-value="' + elementOfArray.name + '" data-type="' + elementOfArray.type + '">' + elementOfArray.name +'</option>';
            }
        }
    });
    selectdata = selectdata +
        '<optgroup label="Lodging">'+lodging_options+'</optgroup>'+
        '<optgroup label="Shopping">'+shopping_options+'</optgroup>'+
        '<optgroup label="Adventure">'+adventure_options+'</optgroup>'+
        '<optgroup label="Food/Dining">'+food_dining_options+'</optgroup>'+
        '<optgroup label="Grocery/Fuel">'+grocery_fuel_options+'</optgroup>'+
        '<optgroup label="Services">'+services_options+'</optgroup>'+
        '<optgroup label="Culture">'+culture_options+'</optgroup>';
    $('#stands').html(selectdata);
    // var options = $('#stands option');
    // var arr = options.map(function (_, o) {
    //     return {t: $(o).text(), v: o.value};
    // }).get();
    // arr.sort(function (o1, o2) {
    //     return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
    // });
    // options.each(function (i, o) {
    //     o.value = arr[i].v;
    //     $(o).text(arr[i].t);
    // });
}

function showstand(e, clear) {
    standselected = 1;
    sidebar.show();
    rentedbikes();
    registeredevents();
    checkonebikeattach();
    // checkoneeventattach();
    if ($.isNumeric(e)) {
        standid = e; // passed via manual call
        lat = markers[e]._latlng.lat;
        long = markers[e]._latlng.lng;
    }
    else {
        if (window.ga) ga('send', 'event', 'buttons', 'click', 'stand-select');
        standid = e.target.options.icon.options.standid; // passed via event call
        lat = e.latlng.lat;
        long = e.latlng.lng;
    }
    if (clear != 0) {
        resetconsole();
    }
    resetbutton("rent");
    markerdata = $('body').data('markerdata');

    $('#stands').val(standid);
    $('#stands option[value="del"]').remove();
    $('#rent').hide();
    $('#rentevent').hide();

    var bicycletext = 'bicycle';
    var standtype = markerdata[standid].type;
    var countOrEcount = markerdata[standid].count;

    if(standtype == 'watercraft_stand'){
        bicycletext = 'watercraft';
    }else if(standtype == 'event_stand'){
        bicycletext = 'event';
        countOrEcount = markerdata[standid].ecount
    }

    if (countOrEcount > 0) {
        $('#standcount').removeClass('label label-danger').addClass('label label-success');

        if (countOrEcount == 1) {
            $('#standcount').html(countOrEcount + ' ' + bicycletext + ':');
        }
        else {
            $('#standcount').html(countOrEcount + ' ' + bicycletext + 's' + ':');
        }
        $.ajax({
            global: false,
            url: "command.php?action=listbytype&stand=" + markerdata[standid].name + "&standtype="+standtype
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            handleresponse(jsonobject, 0);
            bikelist = "";
            if (jsonobject.content != "") {
                for (var i = 0, len = jsonobject.content.length; i < len; i++) {
                    bikeissue = 0;
                    if (jsonobject.content[i][0] == "*") {
                        bikeissue = 1;
                        jsonobject.content[i] = jsonobject.content[i].replace("*", "");
                    }
                    if (jsonobject.stacktopbike == false) // bike stack is disabled, allow renting any bike
                    {
                        if (bikeissue == 1) {
                            bikelist = bikelist + ' <button type="button" class="btn btn-warning bikeid" data-id="' + jsonobject.content[i] + '" data-standtype="' + standtype + '"  data-note="' + jsonobject.notes[i] + '">' + jsonobject.content[i] + '</button>';
                        }
                        // else if (bikeissue == 1 && $("body").data("limit") == 0) {
                        //     bikelist = bikelist + ' <button type="button" class="btn btn-default bikeid" data-id="' + jsonobject.content[i] + '">' + jsonobject.content[i] + '</button>';
                        // }
                        else bikelist = bikelist + ' <button type="button" class="btn btn-success bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '" data-standtype="' + standtype + '" >' + jsonobject.content[i] + '</button>';
                        // else bikelist = bikelist + ' <button type="button" class="btn btn-default bikeid">' + jsonobject.content[i] + '</button>';
                    }
                    else  // bike stack is enabled, allow renting top of the stack bike only
                    {
                        if (jsonobject.stacktopbike == jsonobject.content[i] && bikeissue == 1) {
                            bikelist = bikelist + ' <button type="button" class="btn btn-warning bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '" data-standtype="' + standtype + '" data-note="' + jsonobject.notes[i] + '">' + jsonobject.content[i] + '</button>';
                        }
                        // else if (jsonobject.stacktopbike == jsonobject.content[i] && bikeissue == 1 && $("body").data("limit") == 0) {
                        //     bikelist = bikelist + ' <button type="button" class="btn btn-default bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '">' + jsonobject.content[i] + '</button>';
                        // }
                        else if (jsonobject.stacktopbike == jsonobject.content[i]) bikelist = bikelist + ' <button type="button" class="btn btn-success bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '" data-standtype="' + standtype + '">' + jsonobject.content[i] + '</button>';
                        else bikelist = bikelist + ' <button type="button" class="btn btn-default bikeid" data-standtype="' + standtype + '">' + jsonobject.content[i] + '</button>';
                    }
                }
                $('#standbikes').html('<div class="btn-group number-nav">' + bikelist + '</div>');

                var renteventid = 'rent';
                if(standtype == 'event_stand') {
                    renteventid = 'rentevent';
                }else {
                    renteventid = 'rent';
                }
                //console.log(renteventid);
                if (jsonobject.stacktopbike != false) // bike stack is enabled, allow renting top of the stack bike only
                {
                    $('.b' + jsonobject.stacktopbike).click(function () {
                        if (window.ga) ga('send', 'event', 'buttons', 'click', 'bike-number');
                        // attachbicycleinfo(this, "rent");
                        attachbicycleinfobytype(this, renteventid);
                    });
                    $('body').data('stacktopbike', jsonobject.stacktopbike);
                }
                else // bike stack is disabled, allow renting any bike
                {
                    $('#standbikes .bikeid').click(function () {
                        if (window.ga) ga('send', 'event', 'buttons', 'click', 'bike-number');
                        // attachbicycleinfo(this, "rent");
                        //console.log(renteventid);

                        //boom watercraft_stand
                        var datastandtype = $(this).attr("data-standtype");
                        // console.log(datastandtype);
                        //attachbicycleinfobytype(this, renteventid);
                        var biketype ='';
                        if(datastandtype == 'watercraft_stand'){
                            biketype = 'watercraft';
                        }else {
                            biketype = 'bike';
                        }
                        attachbicycleinfobybiketype(this, renteventid,biketype);

                    });
                }
            }
            else // no bicyles at stand
            {
                var noBicycleByType = _no_bicycles;
                if(standtype == 'watercraft_stand'){
                    noBicycleByType = _no_watercrafts;
                }else if(standtype == 'event_stand'){
                    noBicycleByType = _no_events;
                }

                $('#standcount').html(noBicycleByType);
                $('#standcount').removeClass('label label-success').addClass('label label-danger');
                resetstandbikes();

            }

        });
    }
    else {
        var noBicycleByType = _no_bicycles;
        if(standtype == 'watercraft_stand'){
            noBicycleByType = _no_watercrafts;
        }else if(standtype == 'event_stand'){
            noBicycleByType = _no_events;
        }
        $('#standcount').html(noBicycleByType);
        $('#standcount').removeClass('label label-success').addClass('label label-danger');
        resetstandbikes();
    }
    walklink = '';
    if ("geolocation" in navigator) // if geolocated, provide link to walking directions
    {
        walklink = '<a href="https://www.google.com/maps?q=' + $("body").data("mapcenterlat") + ',' + $("body").data("mapcenterlong") + '+to:' + lat + ',' + long + '&saddr=' + $("body").data("mapcenterlat") + ',' + $("body").data("mapcenterlong") + '&daddr=' + lat + ',' + long + '&output=classic&dirflg=w&t=m" target="_blank" title="' + _open_map + '">' + _walking_directions + '</a>';
    }
    if (loggedin == 1 && markerdata[standid].photo) {
        walklink = walklink + ' | ';
        $('#standinfo').html(markerdata[standid].desc + ' (' + walklink + ' <a href="' + markerdata[standid].photo + '" id="photo' + standid + '" title="' + _display_photo + '">' + _photo + '</a>)');
        $('#standphoto').hide();
        $('#standphoto').html('<img src="' + markerdata[standid].photo + '" alt="' + markerdata[standid].name + '" width="100%" />');
        $('#photo' + standid).click(function () {
            $('#standphoto').slideToggle();
            return false;
        });
    }
    else if (loggedin == 1) {
        $('#standinfo').html(markerdata[standid].desc);
        if (walklink) $('#standinfo').html(markerdata[standid].desc + ' (' + walklink + ')');
        $('#standphoto').hide();
    }
    else {
        $('#standinfo').hide();
        $('#standphoto').hide();
    }
    togglestandactions(markerdata[standid].count);
    togglebikeactions();
    $('#bike').hide();
}

function rentedbikes() {
    $.ajax({
        global: false,
        url: "command.php?action=userbikes"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse(jsonobject, 0);
        bikelist = "";
        if (jsonobject.content != "") {
            for (var i = 0, len = jsonobject.content.length; i < len; i++) {
                bikelist = bikelist + ' <button type="button" class="btn btn-info userbike bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '" data-standtype="'+jsonobject.standtypes[i]+'" title="' + _currently_rented + '">' + jsonobject.content[i] + '<br /><span class="label label-primary">(' + jsonobject.codes[i] + ')</span><span class="label"><s>(' + jsonobject.oldcodes[i] + ')</s></span></button> ';
            }
            $('#rentedbikes').html('<div class="btn-group">' + bikelist + '</div>');
            var returnbikeandwatercraftid = 'return';


            $('#rentedbikes .bikeid').click(function () {
                // attachbicycleinfo(this, "return");
                // attachbicycleinfo(this, "return");
                if($(this).data("standtype") == 'watercraft_stand') {
                    returnbikeandwatercraftid = 'returnwatercraft';
                }else {
                    returnbikeandwatercraftid = 'return';
                }
                attachbicycleinfobytype(this, returnbikeandwatercraftid);
            });
            checkonebikeattach();
        }
        else {
            resetrentedbikes();
        }
    });
}

function registeredevents() {
    $.ajax({
        global: false,
        url: "command.php?action=userevents"
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        handleresponse(jsonobject, 0);
        console.log(jsonobject);

        bikelist = "";
        if (jsonobject.content != "") {
            for (var i = 0, len = jsonobject.content.length; i < len; i++) {
                bikelist = bikelist + ' <button type="button" class="btn btn-info userevent bikeid b' + jsonobject.content[i] + '" data-id="' + jsonobject.content[i] + '" data-standtype="event_stand"  title="' + _currently_rented + '">' + "RSVP-" + jsonobject.content[i] + '<br /></button> ';
            }
            $('#rentedevents').html('<div class="btn-group">' + bikelist + '</div>');
            $('#rentedevents .bikeid').click(function () {
                attachbicycleinfobytype(this, "returnevent");
            });
            if ($("#rentedbikes .btn-group").length == 0){
                checkoneeventattach();
            }

        }
        else {
            resetrentedevents();
        }
    });
}


function togglestandactions(count) {
    if (loggedin == 0) {
        $('#standactions').hide();
        return false;
    }
    if (count == 0) {
        $('#standactions').hide();
    }
    else {
        $('#standactions').show();
    }
}

function togglebikeactions() {
    if (loggedin == 0) {
        $('.bicycleactions').hide();
        return false;
    }
    if ($('body').data('rented') == 0 || standselected == 0) {
        $('.bicycleactions').hide();
    }
    else {
        $('.bicycleactions').show();
    }
}

function rent() {
    if ($('#rent .bikenumber').html() == "") return false;
    bootbox.confirm({
        title: "<span class='alert-sign'></span> Are you sure?",
        message: "You can be billed if this unit is not rented and returned properly.",
        className: "rent-confirmation-popup",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-danger pull-left'
            },
            confirm: {
                label: 'Confirm',
                className: 'btn-success'
            }
        },
        size: "small",
        callback: function (result) {
            if (result) {
                console.log('This was logged in the callback: ' + result);
                if (window.ga) ga('send', 'event', 'bikes', 'rent', $('#rent .bikenumber').html());
                $.ajax({
                    url: "command.php?action=rentbytype&bikeno=" + $('#rent .bikenumber').html()+"&biketype="+$('#rent').attr('data-biketype')
                }).done(function (jsonresponse) {
                    jsonobject = $.parseJSON(jsonresponse);
                    handleresponse(jsonobject);
                    resetbutton("rent");
                    $('body').data("limit", $('body').data("limit") - 1);
                    if ($("body").data("limit") < 0) $("body").data("limit", 0);
                    standid = $('#stands').val();
                    markerdata = $('body').data('markerdata');
                    standbiketotal = markerdata[standid].count;
                    if (jsonobject.error == 0) {
                        $('.b' + $('#rent .bikenumber').html()).remove();
                        standbiketotal = (standbiketotal * 1) - 1;
                        markerdata[standid].count = standbiketotal;
                        $('body').data('markerdata', markerdata);
                    }
                    if (standbiketotal == 0) {
                        $('#standcount').removeClass('label-success').addClass('label-danger');
                    }
                    else {
                        $('#standcount').removeClass('label-danger').addClass('label-success');
                    }
                    $('#notetext').val('');
                    $('#notetext').hide();
                    getmarkers();
                    getuserstatus();
                    showstand(standid, 0);
                });
            }
        }
    });
}

function rentevent() {
    if ($('#rentevent .bikenumber').html() == "") return false;
    bootbox.confirm({
        title: "<span class='alert-sign'></span> See You There",
        message: " ",
        className: "rent-confirmation-popup",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-danger pull-left'
            },
            confirm: {
                label: 'Confirm',
                className: 'btn-success'
            }
        },
        size: "small",
        callback: function (result) {
            if (result) {
                console.log('This was logged in the callback: ' + result);
                if (window.ga) ga('send', 'event', 'bikes', 'rent', $('#rentevent .bikenumber').html());
                $.ajax({
                    url: "command.php?action=rentevent&bikeno=" + $('#rentevent .bikenumber').html()
                }).done(function (jsonresponse) {
                    jsonobject = $.parseJSON(jsonresponse);
                    handleresponse(jsonobject);
                    resetbutton("rentevent");
                    $('body').data("limit", $('body').data("limit") - 1);
                    if ($("body").data("limit") < 0) $("body").data("limit", 0);
                    standid = $('#stands').val();
                    markerdata = $('body').data('markerdata');
                    standbiketotal = markerdata[standid].ecount;
                    if (jsonobject.error == 0) {
                        $('.b' + $('#rentevent .bikenumber').html()).remove();
                        standbiketotal = (standbiketotal * 1) - 1;
                        markerdata[standid].count = standbiketotal;
                        $('body').data('markerdata', markerdata);
                    }
                    if (standbiketotal == 0) {
                        $('#standcount').removeClass('label-success').addClass('label-danger');
                    }
                    else {
                        $('#standcount').removeClass('label-danger').addClass('label-success');
                    }
                    $('#notetext').val('');
                    $('#notetext').hide();
                    getmarkers();
                    getuserstatus();
                    showstand(standid, 0);
                });
            }
        }
    });
}

function returnbike() {
    bootbox.confirm({
        title: "",
        message: "Did you reset the lock to the new code provided?<br>click <a href='user_video.php' target='_blank'>here</a> to see the lock video",
        className: "return-bike-modal",
        buttons: {
            cancel: {
                label: 'No',
                className: 'btn-danger pull-left'
            },
            confirm: {
                label: 'Yes',
                className: 'btn-success pull-right'
            }
        },
        size: "small",
        callback: function (result) {
            if(result){
                note = "";
                standname = $('#stands option:selected').attr('data-value');
                standid = $('#stands').val();
                if (window.ga) ga('send', 'event', 'bikes', 'return', $('#return .bikenumber').html());
                if (window.ga) ga('send', 'event', 'stands', 'return', standname);
                if ($('#notetext').val()) note = "&note=" + $('#notetext').val();
                $.ajax({
                    url: "command.php?action=return&bikeno=" + $('#return .bikenumber').html() + "&stand=" + standname + note
                }).done(function (jsonresponse) {
                    jsonobject = $.parseJSON(jsonresponse);
                    handleresponse(jsonobject);
                    if(jsonobject.error != 1) {
                        $('.b' + $('#return .bikenumber').html()).remove();
                        resetbutton("return");
                        markerdata = $('body').data('markerdata');
                        standbiketotal = markerdata[standid].count;
                        if (jsonobject.error == 0) {
                            standbiketotal = (standbiketotal * 1) + 1;
                            markerdata[standid].count = standbiketotal
                            $('body').data('markerdata', markerdata);
                        }
                        if (standbiketotal == 0) {
                            $('#standcount').removeClass('label-success');
                            $('#standcount').addClass('label-danger');
                        }
                        $('#notetext').val('');
                        $('#notetext').hide();
                        getmarkers();
                        getuserstatus();
                        showstand(standid, 0);
                        bootbox.hideAll();
                        bootbox.alert({
                            message: "Thanks",
                            size: "small",
                            className: "thanks-modal",
                            backdrop: true
                        });
                    }
                });
            }
            // else{
            //     $.ajax({
            //         url: "command.php?action=notreturn&bikeno=" + $('#return .bikenumber').html()
            //     }).done(function (jsonresponse) {
            //         jsonobject = $.parseJSON(jsonresponse);
            //         handleresponse(jsonobject);
            //     });
            // }

        }
    });

}


function returnbikebytype(biketype) {
    bootbox.confirm({
        title: "",
        message: "Did you reset the lock to the new code provided?<br>click <a href='user_video.php' target='_blank'>here</a> to see the lock video",
        className: "return-bike-modal",
        buttons: {
            cancel: {
                label: 'No',
                className: 'btn-danger pull-left'
            },
            confirm: {
                label: 'Yes',
                className: 'btn-success pull-right'
            }
        },
        size: "small",
        callback: function (result) {
            if(result){
                note = "";
                var returbikeid = "";
                var resetbuttonid = "";
                if(biketype == 'watercraft'){
                    returbikeid = $('#returnwatercraft .bikenumber').html();
                    resetbuttonid = 'returnwatercraft';
                }else if(biketype == 'bike'){
                    returbikeid = $('#return .bikenumber').html();
                    resetbuttonid = 'return';
                }
                standname = $('#stands option:selected').attr('data-value');
                standid = $('#stands').val();
                if (window.ga) ga('send', 'event', 'bikes', 'return', returbikeid);
                if (window.ga) ga('send', 'event', 'stands', 'return', standname);
                if ($('#notetext').val()) note = "&note=" + $('#notetext').val();
                $.ajax({
                    url: "command.php?action=returnbytype&biketype="+biketype+"&bikeno=" + returbikeid + "&stand=" + standname + note
                }).done(function (jsonresponse) {
                    console.log(jsonresponse);
                    jsonobject = $.parseJSON(jsonresponse);
                    handleresponse(jsonobject);
                    if(jsonobject.error != 1){
                        console.log("okk");
                        $('.b' + returbikeid).remove();
                        // resetbutton("return");
                        resetbutton(resetbuttonid);
                        markerdata = $('body').data('markerdata');
                        standbiketotal = markerdata[standid].count;

                        standbiketotal = (standbiketotal * 1) + 1;
                        markerdata[standid].count = standbiketotal
                        $('body').data('markerdata', markerdata);

                        if (standbiketotal == 0) {
                            $('#standcount').removeClass('label-success');
                            $('#standcount').addClass('label-danger');
                        }
                        $('#notetext').val('');
                        $('#notetext').hide();
                        getmarkers();
                        getuserstatus();
                        showstand(standid, 0);
                        bootbox.hideAll();
                        bootbox.alert({
                            message: "Thanks",
                            size: "small",
                            className: "thanks-modal",
                            backdrop: true
                        });
                    } else {
                        // console.log("error occured");
                    }

                });
            }
            // else{
            //     $.ajax({
            //         url: "command.php?action=notreturn&bikeno=" + $('#return .bikenumber').html()
            //     }).done(function (jsonresponse) {
            //         jsonobject = $.parseJSON(jsonresponse);
            //         handleresponse(jsonobject);
            //     });
            // }

        }
    });

}


function returnevent() {
    bootbox.confirm({
        title: "",
        message: "Are you sure?",
        className: "return-bike-modal",
        buttons: {
            cancel: {
                label: 'No',
                className: 'btn-danger pull-left'
            },
            confirm: {
                label: 'Yes',
                className: 'btn-success pull-right'
            }
        },
        size: "small",
        callback: function (result) {
            if(result){
                note = "";
                standname = $('#stands option:selected').attr('data-value');
                standid = $('#stands').val();
                if (window.ga) ga('send', 'event', 'bikes', 'return', $('#returnevent .bikenumber').html());
                if (window.ga) ga('send', 'event', 'stands', 'return', standname);
                // if ($('#notetext').val()) note = "&note=" + $('#notetext').val();
                $.ajax({
                    url: "command.php?action=returnevent&bikeno=" + $('#returnevent .bikenumber').html() + "&stand=" + standname + note
                }).done(function (jsonresponse) {
                    console.log(jsonresponse);
                    jsonobject = $.parseJSON(jsonresponse);
                    handleresponse(jsonobject);
                    if(jsonobject.error != 1) {
                        $('.b' + $('#returnevent .bikenumber').html()).remove();
                        resetbutton("returnevent");
                        markerdata = $('body').data('markerdata');
                        standbiketotal = markerdata[standid].count;
                        if (jsonobject.error == 0) {
                            standbiketotal = (standbiketotal * 1) + 1;
                            markerdata[standid].count = standbiketotal
                            $('body').data('markerdata', markerdata);
                        }
                        if (standbiketotal == 0) {
                            $('#standcount').removeClass('label-success');
                            $('#standcount').addClass('label-danger');
                        }
                        $('#notetext').val('');
                        $('#notetext').hide();
                        getmarkers();
                        getuserstatus();
                        showstand(standid, 0);
                        bootbox.hideAll();
                        bootbox.alert({
                            message: "Thanks",
                            size: "small",
                            className: "thanks-modal",
                            backdrop: true
                        });
                    }
                });
            }
            // else{
            //     $.ajax({
            //         url: "command.php?action=notreturn&bikeno=" + $('#return .bikenumber').html()
            //     }).done(function (jsonresponse) {
            //         jsonobject = $.parseJSON(jsonresponse);
            //         handleresponse(jsonobject);
            //     });
            // }

        }
    });

}

function validatecoupon() {
    $.ajax({
        url: "command.php?action=validatecoupon&coupon=" + $('#coupon').val()
    }).done(function (jsonresponse) {
        jsonobject = $.parseJSON(jsonresponse);
        temp = $('#couponblock').html();
        if (jsonobject.error == 1) {
            $('#couponblock').html('<div class="alert alert-danger" role="alert">' + jsonobject.content + '</div>');
            setTimeout(function () {
                $('#couponblock').html(temp);
                $("#validatecoupon").click(function () {
                    if (window.ga) ga('send', 'event', 'buttons', 'click', 'credit-add');
                    validatecoupon();
                });
            }, 2500);
        }
        else {
            $('#couponblock').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>');
            getuserstatus();
            setTimeout(function () {
                $('#couponblock').html(temp);
                $('#couponblock').toggle();
                $("#validatecoupon").click(function () {
                    if (window.ga) ga('send', 'event', 'buttons', 'click', 'credit-add');
                    validatecoupon();
                });
            }, 2500);
        }
    });
}

function resetpassword() {
    $('#passwordresetblock').hide();
    if (sms == 0 && $('#number').val() > 0) {
        $.ajax({
            url: "command.php?action=resetpassword&number=" + $('#number').val()
        }).done(function (jsonresponse) {
            jsonobject = $.parseJSON(jsonresponse);
            handleresponse(jsonobject);
        });
    }
    else if (sms == 1 && $('#number').val() > 0) {
        window.location = "register.php#reset" + $('#number').val();
    }
}

function attachbicycleinfo(element, attachto) {
    $('#' + attachto + ' .bikenumber').html($(element).attr('data-id'));
    // show warning, if exists:
    if ($(element).hasClass('btn-warning')) $('#console').html('<div class="alert alert-warning" role="alert">' + _reported_problem + ' ' + $(element).attr('data-note') + '</div>');
    // or hide warning, if bike without issue is clicked
    else if ($(element).hasClass('btn-warning') == false && $('#console div').hasClass('alert-warning')) resetconsole();
    if ($(element).is('[data-id]')) {
        placebicyclephoto($(element).attr('data-id'));
    }
    $('#bike').show();
    // $('#rent').show();
}

function attachbicycleinfobytype(element, attachto) {
    $('#' + attachto + ' .bikenumber').html($(element).attr('data-id'));
    // show warning, if exists:
    if ($(element).hasClass('btn-warning')) $('#console').html('<div class="alert alert-warning" role="alert">' + _reported_problem + ' ' + $(element).attr('data-note') + '</div>');
    // or hide warning, if bike without issue is clicked
    else if ($(element).hasClass('btn-warning') == false && $('#console div').hasClass('alert-warning')) resetconsole();
    if ($(element).is('[data-id]')) {
        placebicyclephotobytype($(element).attr('data-id'),$(element).attr('data-standtype'));
    }
    $('#bike').show();
    // $('#return').show();
    //$('#returnevent').hide();

    // console.log($(element).attr('data-standtype'));
    // console.log(attachto);

    if(attachto == 'rentevent'){
        $('#rentevent').show();
        $('#standactions').show();
    }else if(attachto == 'returnevent'){
        $('#return').hide();
        $('#returnwatercraft').hide();
        $('.display-br-hide').hide();
        $('.display-br-hide-watercraft').hide();


        $('.bicycleactions').show();
        $('#returnevent').show();
    }else if(attachto == 'return'){
        $('#returnevent').hide();
        $('#returnwatercraft').hide();
        $('.display-br-hide-watercraft').hide();

        $('.bicycleactions').show();
        $('.display-br-hide').show();
        $('#return').show();

    }else if(attachto == 'returnwatercraft'){
        $('#returnevent').hide();
        $('#return').hide();
        $('.display-br-hide').hide();

        $('.display-br-hide-watercraft').show();
        $('#returnwatercraft').show();
        $('.bicycleactions').show();

    }else {

        $('#rent').show();
    }
}

function attachbicycleinfobybiketype(element, attachto,biketype) {
    $('#' + attachto + ' .bikenumber').html($(element).attr('data-id'));
    // show warning, if exists:
    if ($(element).hasClass('btn-warning')) $('#console').html('<div class="alert alert-warning" role="alert">' + _reported_problem + ' ' + $(element).attr('data-note') + '</div>');
    // or hide warning, if bike without issue is clicked
    else if ($(element).hasClass('btn-warning') == false && $('#console div').hasClass('alert-warning')) resetconsole();
    if ($(element).is('[data-id]')) {
        placebicyclephotobytype($(element).attr('data-id'),$(element).attr('data-standtype'));
    }
    $('#bike').show();
    // $('#return').show();
    //$('#returnevent').hide();

    // console.log($(element).attr('data-standtype'));
    // console.log(attachto);

    if(attachto == 'rentevent'){
        $('#rentevent').show();
        $('#standactions').show();
    }else if(attachto == 'returnevent'){
        $('#return').hide();
        $('#returnwatercraft').hide();
        $('.display-br-hide').hide();
        $('.display-br-hide-watercraft').hide();


        $('.bicycleactions').show();
        $('#returnevent').show();
    }else if(attachto == 'return'){
        $('#returnevent').hide();
        $('#returnwatercraft').hide();
        $('.display-br-hide-watercraft').hide();

        $('.bicycleactions').show();
        $('.display-br-hide').show();
        $('#return').show();

    }else if(attachto == 'returnwatercraft'){
        $('#returnevent').hide();
        $('#return').hide();
        $('.display-br-hide').hide();

        $('.display-br-hide-watercraft').show();
        $('#returnwatercraft').show();
        $('.bicycleactions').show();

    }else {
        if( biketype=='watercraft') {
            $('#rent').attr('data-biketype','watercraft');
        }else {
            $('#rent').attr('data-biketype','bike');
        }
        $('#rent').show();
    }
}

function placebicyclephoto(bicycleid) {
    $.ajax({
        url: "command.php?action=getbicyclephoto&bicycleid=" + bicycleid
    }).done(function (jsonresponse) {
        try{
            jsonobject = $.parseJSON(jsonresponse);
        }catch (e) {
            // console.log(e);
            jsonobject = false;
        }
        if (jsonobject) {
            $('#bike p').html("Bike " + jsonobject["bikeNum"]);
            if (jsonobject["image_path"] != null){
                $('#bike .bikepic').attr('src', jsonobject["image_path"]);
                $('#bike .bike-status-pic').attr('src', jsonobject["status_image_path"]);
                $('#eventTotalRides .total-rides').hide();
                $('#eventTotalRides .total-bikes').hide();
                $('#eventTotalRides').removeClass('bike-name-new');

                $('#bike .bike-status-pic').show();
            }
            else{
                $('#bike .bikepic').hide();
                $('#bike .bike-status-pic').hide();
            }


        }
    });
}

function placebicyclephotobytype(bicycleid,standtype) {
    $.ajax({
        url: "command.php?action=getbicyclephotobytype&bicycleid=" + bicycleid + "&standtype=" + standtype
    }).done(function (jsonresponse) {
        try{
            jsonobject = $.parseJSON(jsonresponse);
        }catch (e) {
            // console.log(e);
            jsonobject = false;
        }
        if (jsonobject) {
            var bikestr = 'Bike ';
            var bikEventNum = jsonobject["bikeNum"];
            if(standtype == 'watercraft_stand'){
                bikestr = 'Watercraft ';
            }else if (standtype == 'event_stand'){
                bikestr = 'RSVP ';
                bikEventNum = jsonobject["id"];
            }
            $('#bike p').html(bikestr + bikEventNum);
            if (jsonobject["image_path"] != null){
                if(standtype == 'event_stand'){
                    //show total bikes and total rides
                    $('#bike .bike-status-pic').hide();
                    $('#eventTotalRides .total-rides').show();
                    $('#eventTotalRides .total-bikes').show();
                    $('#eventTotalRides').addClass('bike-name-new');

                    $('#bike .bikepic').attr('src', jsonobject["image_path"]);
                    $('#eventTotalRides .total-rides').html("Total Bikes: " + jsonobject["total_rides"]);
                    $('#eventTotalRides .total-bikes').html("Current Attendees Planned: " + jsonobject["total_bikes"]);


                }else {
                    $('#bike .bike-status-pic').show();
                    $('#eventTotalRides .total-rides').hide();
                    $('#eventTotalRides .total-bikes').hide();
                    $('#eventTotalRides').removeClass('bike-name-new');

                    $('#bike .bikepic').attr('src', jsonobject["image_path"]);
                    $('#bike .bike-status-pic').attr('src', jsonobject["status_image_path"]);
                }
            }
            else{
                $('#bike .bikepic').hide();
                $('#bike .bike-status-pic').hide();
                $('#eventTotalRides .total-rides').hide();
                $('#eventTotalRides .total-bikes').hide();
                $('#eventTotalRides').removeClass('bike-name-new');
            }


        }
    });
}

function checkonebikeattach() {
    if ($("#rentedbikes .btn-group").length == 1) {
        element = $("#rentedbikes .btn-group .btn");
        var returntypewiseid = 'return';

        if (element.hasOwnProperty('dataset')) {
            if(element[0]['dataset']['standtype'] == 'watercraft_stand'){
                returntypewiseid = 'returnwatercraft';
            }else {
                returntypewiseid = 'return';
            }
        }else {
            if(element.data('standtype') == 'watercraft_stand'){
                returntypewiseid = 'returnwatercraft';
            }else {
                returntypewiseid = 'return';
            }
        }


        attachbicycleinfobytype(element, returntypewiseid);
    }
}

function checkoneeventattach() {
    //console.log($("#rentedbikes .btn-group").length);
    if ($("#rentedevents .btn-group").length == 1) {
        element = $("#rentedevents .btn-group .btn");
        attachbicycleinfobytype(element, "returnevent");
    }
}

function handleresponse(jsonobject, display) {
    if (display == undefined) {
        if (jsonobject.error == 1) {
            $('#console').html('<div class="alert alert-danger" role="alert">' + jsonobject.content + '</div>').fadeIn();
        }
        else {
            $('#console').html('<div class="alert alert-success" role="alert">' + jsonobject.content + '</div>');
        }
    }
    if (jsonobject.limit) {
        if (jsonobject.limit) $("body").data("limit", jsonobject.limit);
    }
}

function resetconsole() {
    $('#console').html('');
}

function resetbutton(attachto) {
    $('#' + attachto + ' .bikenumber').html('');
}

function resetstandbikes() {
    $('body').data('stacktopbike', false);
    $('#standbikes').html('');
}

function resetrentedbikes() {
    $('#rentedbikes').html('');
}

function resetrentedevents() {
    $('#rentedevents').html('');
}

function savegeolocation() {
    $.ajax({
        url: "command.php?action=map:geolocation&lat=" + $("body").data("mapcenterlat") + "&long=" + $("body").data("mapcenterlong")
    }).done(function (jsonresponse) {
        return;
    });
}

function showlocation(location) {
    $("body").data("mapcenterlat", location.coords.latitude);
    $("body").data("mapcenterlong", location.coords.longitude);
    $("body").data("mapzoom", $("body").data("mapzoom") + 1);

    // 80 m x 5 mins walking distance
    circle = L.circle([$("body").data("mapcenterlat"), $("body").data("mapcenterlong")], 10 * 5, {
        color: 'green',
        fillColor: '#0f0',
        fillOpacity: 0.1
    }).addTo(map);

    map.setView(new L.LatLng($("body").data("mapcenterlat"), $("body").data("mapcenterlong")), $("body").data("mapzoom"));
    if (window.ga) ga('send', 'event', 'geolocation', 'latlong', $("body").data("mapcenterlat") + "," + $("body").data("mapcenterlong"));
    savegeolocation();
}

function changelocation(location) {
    if (location.coords.latitude != $("body").data("mapcenterlat") || location.coords.longitude != $("body").data("mapcenterlong")) {
        $("body").data("mapcenterlat", location.coords.latitude);
        $("body").data("mapcenterlong", location.coords.longitude);
        map.removeLayer(circle);
        circle = L.circle([$("body").data("mapcenterlat"), $("body").data("mapcenterlong")], 10 * 5, {
            color: 'green',
            fillColor: '#0f0',
            fillOpacity: 0.1
        }).addTo(map);
        map.setView(new L.LatLng($("body").data("mapcenterlat"), $("body").data("mapcenterlong")), $("body").data("mapzoom"));
        if (window.ga) ga('send', 'event', 'geolocation', 'latlong', $("body").data("mapcenterlat") + "," + $("body").data("mapcenterlong"));
        savegeolocation();
    }
}

function videos() {
    var code = "";
    $.ajax({
        url: "command.php?action=videolist"
    }).done(function (jsonresponse) {

        jsonobject = $.parseJSON(jsonresponse);
        if (jsonobject.length > 0)
            for (var i = 0, len = jsonobject.length; i < len; i++) {
                code = code + '<div class="carousel-item active"><video class="d-block w-100" src="' + jsonobject[i]["videoPath"] + '" height="95%" width="95%"  controls></video></div>';
            }

        $('#videocarousel .carousel-inner').html(code);
    });
}

function toggle() {
    $('.collapse').collapse('toggle')
}
