<?php
session_start();
require_once 'tmpl2.class_ex.inc';
require_once 'common_proc.inc';
require_once 'mc_session_routines.inc';
require_once 'mc_common.inc';
require_once 'mc_db.inc';
require_once 'message/MessageContactHelper.php';
require_once 'message/MessageContact.php';
require_once 'imacherie_male.inc';

if(!is_member_logged()) {
	$_SESSION['temp_flg'] = "line_mail";
	header('Location: ./../imacherie.php');
}
$myInfo = myInformation();
$userId = $_SESSION['user_id'];
//template
$tmpl = new Tmpl2($tmpl_dir . '/message/message_block_list_edit.html');

$contactHelper = new MessageContactHelper();
$contacts = new MessageContact();
$oldUnread = $myInfo['midoku_num'];

$tmpl->assign('old_unread', $oldUnread);
/**
 * Block contacts
 * Go back to page 1
*/
if(isset($_POST['ids'])) {
	$count = count($_POST['ids']);
    $ids = $_POST['ids'];
	for($i = 0; $i < $count; $i++) {
		$id = explode(':', $ids[$i]);
		$contacts->unblockUser($id[0], $userId, $id[1]);
	}
	unset($_GET['page']);
}


//default block contacts
$count = $contactHelper->getAllBlockedBy($userId, 1, null);
$contact = $contactHelper->paginateContact('getAllBlockedBy', $count, '', 10, 1);

//Loop $contact array data object
if($contact['data']){
	$tmpl->loopset('all_set');
	foreach($contact['data'] as $row) {
		$tmpl->assign('contactId', $row->id);
		$tmpl->assign('performerId', $row->performer_id);
		$tmpl->assign('id', $row->hash);
		$tmpl->assign('name', $row->nick_name);
		$tmpl->assign('image', $row->img);
		$tmpl->loopnext();
	}
	$tmpl->loopset('');

	//set assign nav
	$tmpl->assign('nav', $contact['nav']);

}else{
	$tmpl->assign('result', '1');
}

// DB
$db = new mcDB ( getRandomSlave () );
$dbSlave = $db->get_resource ();

printOuterFrame ( &$tmpl, "マシェリとは？", $db, $ownerCd );

$tmpl->flush ( 0 );
exit ();