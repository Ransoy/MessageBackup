<?php 
require_once 'CommonDb.php';
require_once 'message/MessageContact.php';
$messageContact =  new MessageContact();
/************************************ 
 *use to mark as favorite the user or the performer
 *@params post type 1 = from user 2 = from performer
 *
 *************************************/

$fromId = mb_convert_encoding(urldecode($_POST['from_id']), "EUC-JP","UTF-8") ;
$toId	=	mb_convert_encoding(urldecode($_POST['to_id']), "EUC-JP","UTF-8") ;

if(isset($_POST['favorite'])){
	$contact_id = $_POST['favorite'];
	if (empty($contact_id)) {
		$contact_id = getContactId($fromId, $toId);
	}
	switch ($_POST['type']){
		case 1:
			$messageContact->favePerformer($contact_id);
			break;
		case 2:
			$messageContact->faveUser($contact_id);
			break;
	}
	
}
/************************************
 *use to mark as unfavorite the user or the performer
 *@params post type 1 = from user 2 = from performer
 *
 *************************************/
if(isset($_POST['unfavorite'])){
	$contact_id = $_POST['unfavorite'];
	if (empty($contact_id)) {
		$contact_id = getContactId($fromId, $toId);
	}
	switch ($_POST['type']){
		case 1:
			$messageContact->unfavePerformer($contact_id);
			break;
		case 2:
			$messageContact->unfaveUser($contact_id);
			break;
	}
	
}

function getContactId($fromId, $toId) {
	$mc = new MessageContact();
	if ($_POST['type'] == 1) {
		$mc->setUserId($fromId);
		$mc->setPerformerId($toId);
	} else {
		$mc->setPerformerId($fromId);
		$mc->setUserId($toId);
	}
	$contactResult = $mc->getContact();
	return $contactResult['id'];

}



