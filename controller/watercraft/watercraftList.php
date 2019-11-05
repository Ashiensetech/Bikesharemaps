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

$table = 'bikes';

$table = <<<EOT
 (
    SELECT
      bk.bikeNum,
      bk.bike_num,
      u.userName,
      st.standName,
      bk.currentCode,
      bk.note,
      bk.image_path,
      bk.active
    FROM bikes bk
    LEFT JOIN users u ON bk.currentUser=u.userId
    LEFT JOIN stands st ON bk.currentStand=st.standId
    WHERE bk.type = 'watercraft'
 ) temp
EOT;

// Table's primary key
$primaryKey = 'bikeNum';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'bikeNum', 'dt' => 0),
    array('db' => 'bike_num', 'dt' => 1),
    array('db' => 'userName', 'dt' => 2),
    array('db' => 'standName', 'dt' => 3),
    array('db' => 'currentCode', 'dt' => 4),
    array('db' => 'note', 'dt' => 5),
    array('db' => 'image_path', 'dt' => 6),
    array('db' => 'active', 'dt' => 7),
    array('db' => 'bikeNum', 'dt' => 8)
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