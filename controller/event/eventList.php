<?php
require("../config.php");

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

$table = 'events';

$table = <<<EOT
 (  
    SELECT
      ev.id,
      ev.event_num,
      st.standName,
      ev.image_path,
      ev.total_rides,
      COUNT(eu.user_id) AS total_users, 
      ev.rsvp_date,
      ev.is_active
    FROM events ev
    LEFT JOIN event_users eu ON ev.id=eu.event_id
    LEFT JOIN stands st ON ev.current_stand=st.standId
    WHERE ev.is_deleted=0
    GROUP BY  ev.id
    
 ) temp
EOT;
//(SELECT COUNT(eu.user_id) AS total_users WHERE eu.event_id = ev.id AND eu.is_active=1),
// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'event_num', 'dt' => 1),
    array('db' => 'standName', 'dt' => 2),
    array('db' => 'image_path', 'dt' => 3),
    array('db' => 'total_rides', 'dt' => 4),
    array('db' => 'total_users', 'dt' => 5),
    array('db' => 'rsvp_date', 'dt' => 6),
    array('db' => 'is_active', 'dt' => 7),
    array('db' => 'id', 'dt' => 8)
);

// SQL server connection information


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../external/dataTable/ssp.class.php');

echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
);


?>