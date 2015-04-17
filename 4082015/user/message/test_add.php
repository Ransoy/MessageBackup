<?php
require_once 'CommonDb.php';
require_once 'message/MessageContact.php';
$db = new CommonDb();

/*$db->select('*');
$db->from('male_address_book');
$db->limit(0, 5);
$result = $db->results();*/

/*$db->select('*');
$db->from('female_address_book');
$db->limit(0, 5);
$result = $db->get();
echo '<pre>';
echo print_r($result);
echo '</pre>';
exit();*/

/* --------------------------------------------------------------------- */


echo ' Female Address book insert to contact <br/>';

$db->select('user_id, male_user_id');
$db->from('female_address_book');

$result = $db->get();
$count = 0;
$queryCount = 0;
$contact = new MessageContact();

foreach($result as $r) {
	$db->select('id');
	$db->from('message_contact');
	$db->where("performer_id = '{$r->user_id}'");
	$db->where("user_id = '{$r->male_user_id}'");
	$result2 = $db->get_row();
	if(!$result2){
		echo 'inserted! <br/>';
		$count++;
		$contact->setOwnerId(1);
		$contact->setUserId($r->male_user_id);
		$contact->setPerformerId($r->user_id);
		$contact->setIsFromChat(0);
		$contact->create();
	}
	$queryCount++;
}

echo 'Total Inserted : ' . $count;
echo '<br/> Total query : ' . $queryCount;

echo '<br/>------------------------------------------------------------------------------------ <br/>';
$db->select('id, user_id, performer_id');
$db->from('message_contact');
$result = $db->results();
echo '<pre>';
print_r($result);
echo '</pre>';

echo '<br/> Male Address book insert to contact <br/>';

$db->select('user_id, female_user_id');
$db->from('male_address_book');

$result = $db->get();
$count = 0;
$queryCount = 0;
$contact = new MessageContact();

foreach($result as $r) {
	
	$db->select('id');
	$db->from('message_contact');
	$db->where("user_id = '{$r->user_id}'");
	$db->where("performer_id = '{$r->female_user_id}'");
	$result2 = $db->get_row();
	if(!$result2){
		echo 'inserted! <br/>';
		$count++;
		$contact->setOwnerId(1);
		$contact->setUserId($r->user_id);
		$contact->setPerformerId($r->female_user_id);
		$contact->setIsFromChat(0);
		$contact->create();
	}
	$queryCount++;
}

echo 'Total Inserted : ' . $count;
echo '<br/> Total query : ' . $queryCount;
echo '<br/> ------------------------------------------------------------------------------------ <br/>';



