<?php 
require_once 'CommonDb.php';
require_once 'message/MessageContact.php';
$messageContact =  new MessageContact();
/************************************ 
 *use to block the user or the performer  
 *@params post type 1 = from user 2 = from performer
 *
 *************************************/
$fromId = mb_convert_encoding(urldecode($_POST['from_id']), "EUC-JP","UTF-8") ;
$toId	=	mb_convert_encoding(urldecode($_POST['to_id']), "EUC-JP","UTF-8") ;

if(isset($_POST['block'])){
	$contact_id = $_POST['block'];
	if (empty($_POST['block'])) {
		$contact_id = getContactId($fromId, $toId);
	}	
	switch ($_POST['type']){
		case 1:
			$messageContact->blockUser($contact_id);
			break;
		case 2:
			$messageContact->blockPerformer($contact_id);
			break;
	}
}
/************************************
 *use to unblock the user or the performer
 *@params post type 1 = from user 2 = from performer
 *************************************/

if(isset($_POST['unblock'])){
	$contact_id = $_POST['unblock'];
	if (empty($_POST['unblock'])) {
		$contact_id = getContactId($fromId, $toId);
	}
	switch($_POST['type']){
		case 1:
			$messageContact->unblockUser($contact_id,$fromId,$toId);
			break;
		case 2 :
			$messageContact->unblockPerformer($contact_id,$fromId,$toId);
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

echo $fromId . ' - ' . $toId . ' - ' .$contact_id;