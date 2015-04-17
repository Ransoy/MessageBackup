<?php
require_once 'CommonDb.php';

$db = new CommonDb();

/*$db->select('*');
$db->from('message');
$db->order_by('1', 'DESC');
$db->limit(0, 5);
$result = $db->get();
*/

/*$query = 'ALTER TABLE message ' .
		 'ADD ( is_checked TinyInt(1), ' .
		 'chk_ip varchar(64), ' .
		 'chk_id varchar(64), ' .
		 'chk_date datetime ) ';
		 

$db->query($query);*/
/*
$data = array('chk_ip'=>'', 'is_checked' => 0);
$db->update('message', $data);
*/
//print_r($db->sqlQuery('DESCRIBE message', array()));

/*$db->select('*');
$db->from('reward');
$db->order_by('1', 'DESC');
$db->limit(0, 5);
$result = $db->get();
 

echo '<pre>';
print_r($result);
echo '</pre>';*/

$dbname = "macherie2";
$sql = "SHOW TABLES FROM $dbname";
$result = mysql_query($sql);

if (!$result) {
	echo "DB Error, could not list tables\n";
	echo 'MySQL Error: ' . mysql_error();
	exit;
}
echo '<pre>';
while ($row = mysql_fetch_row($result)) {
	echo "Table: {$row[0]}\n";
}

echo '</pre>';

/* $db->select('*');
$db->from('message');
//$db->where('ID > 190 AND ID < 196');
$db->order_by('1', 'DESC');
$db->limit(0, 5);
$result = $db->get();
 */

echo '<pre>';
print_r($result);
echo '</pre>';