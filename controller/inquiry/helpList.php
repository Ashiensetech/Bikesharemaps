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

$table = <<<EOT
 (
    SELECT
      help.inquiryid,
      help.email,
      help.inquiry,
      help.answer,
      u.mail,
      help.updated_at
    FROM inquiries help
    LEFT JOIN users u ON help.userId=u.userId
    ORDER BY help.updated_at,help.inquiryid DESC
 ) temp
EOT;

// Table's primary key
$primaryKey = 'inquiryid';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'inquiryid', 'dt' => 0),
    array('db' => 'mail', 'dt' => 1),
    array('db' => 'inquiry', 'dt' => 2),
    array('db' => 'answer', 'dt' => 3),
    array('db' => 'updated_at', 'dt' => 4),
    array('db' => 'email', 'dt' => 5)

);
// SQL server connection information


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../external/dataTable/ssp.class.php');

$dataList = SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns);

foreach ($dataList['data'] as $key=>$data){
    if(!$dataList['data'][$key][1]){
        $dataList['data'][$key][1] = $dataList['data'][$key][5];
    }
    if(strlen($dataList['data'][$key][2]) > 80){
        $dataList['data'][$key][2] = substr($dataList['data'][$key][2], 0, 80) . "...";
    }
    if(strlen($dataList['data'][$key][3]) > 80){
        $dataList['data'][$key][3] = substr($dataList['data'][$key][3], 0, 80) . "...";
    }
}
echo json_encode($dataList);


?>