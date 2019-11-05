$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if ($(e.target).attr('has-opened') != '1') {
            $(e.target).attr('has-opened', '1');
            if (target == '#stands') {
                initStandTable();
            }
            else if (target == '#bikes') {
                initBikeTable();
            }
            else if (target == '#watercrafts') {
                initWatercraftTable();
            }
            else if (target == '#events') {
                initEventTable();
            }
            else if (target == '#lodging') {
                initLodgingTable();
            }
            else if (target == '#shopping') {
                initShoppingTable();
            }
            else if (target == '#adventure') {
                initAdventureTable();
            }
            else if (target == '#food-dining') {
                initFoodDiningTable();
            }
            else if (target == '#grocery-fuel') {
                initGroceryFuelTable();
            }
            else if (target == '#services') {
                initServicesTable();
            }
            else if (target == '#culture') {
                initCultureTable();
            }
            else if (target == '#users') {
                initUserTable();
            }
            else if (target == '#inquiries') {
                $("#inquiries-data").addClass("report-active");
                $("#inquiry_list_tab").show();
                initInquiryTable();
                initHelpTable();
            }
            else if (target == '#videos') {
                initVideoTable();
            }
            else if (target == '#credit') {
                // $("#listcoupons").click();
                initCouponTable();
            }
            else if (target == '#reports') {
                $("#daily_stats").addClass("report-active");
                $("#daily_stats_report").show();
                getDailyStats();
            }
            else if (target == '#maintenance') {
                $("#maintenance_settings").addClass("report-active");
                $("#maintenance_settings_tab").show();
                initMaintenanceTable();
                initWatercraftMaintenanceTable();
            }
        }
    });
});