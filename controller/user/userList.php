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

// DB table to use
$table = 'users_demo';


$table = <<<EOT
 (
    SELECT
      u.userId,
      u.userName,
      u.mail,
      u.number,
      u.privileges,
      ltm.userLimit,
      cr.credit
    FROM users u
    LEFT JOIN limits ltm ON u.userId=ltm.userId
    LEFT JOIN credit cr ON u.userId=cr.userId
 ) temp
EOT;

// Table's primary key
$primaryKey = 'userId';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'userId', 'dt' => 0),
    array('db' => 'userName', 'dt' => 1),
    array('db' => 'mail', 'dt' => 2),
    array('db' => 'number', 'dt' => 3),
    array('db' => 'privileges', 'dt' => 4),
    array('db' => 'userLimit', 'dt' => 5),
    array(
        'db' => 'credit',
        'dt' => 6,
        'formatter' => function ($d, $row) {
            return '$' . number_format($d);
        }
    ),
    array('db' => 'userId', 'dt' => 7),

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