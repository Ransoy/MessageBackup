<?php 


require_once 'CommonDb.php';
require_once 'message/MessageContact.php';
$db = new CommonDb();



$db->select('hash, user_id, password');
$db->from('male_member');
$db->limit(0,20);
$result2 = $db->get();

echo '<h1>MALE MEMBER</h1>';
foreach($result2 as $r) {
	echo '<br/>';
	echo 'hash: '.$r->hash;
	echo '<br/>';
	echo 'user_id:'. $r->user_id;
	echo '<br/>';
	echo 'password: '. $r->password;
	
	echo '<br/><br/>';
}

$db->select('f1.hash, fm.user_id, fm.password');
$db->from('female_member as fm');
$db->join('female_profile as f1','f1.user_id = fm.user_id', 'INNER');
$db->limit(0,20);
$result2 = $db->get();

echo '<h1>FEMALE MEMBER</h1>';
foreach($result2 as $r) {
	echo '<br/>';
	echo 'hash: '.$r->hash;
	echo '<br/>';
	echo 'user_id:'. $r->user_id;
	echo '<br/>';
	echo 'password: '. $r->password;

	echo '<br/><br/>';
}

?>