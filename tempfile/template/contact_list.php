<?php

	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'sp/boy_login.inc';
	if (isset($_GET['sp_login'])) {
		require_once 'sp/boy_sp.inc';
	} else {
		require_once 'sp/boy_sp_top.inc';
	}

require_once 'sp/tmpl2.class_ex.inc';

require_once 'CommonDb.php';

require_once 'message/Message.php';

require_once 'message/MessageContact.php';
require_once 'message/MessageContactHelper.php';
require_once 'message/MessageHelper.php';

$tmpl = new Tmpl22($sp_tmpl_dir .'message/message_contact_list.html' );

$contacts = new MessageContactHelper ();

$id = $_SESSION ['user_id'];

/*
 * set display all favorite performer
 */
/* if (isset ( $_GET ['fave'] )) {
	
	$tmpl->loopset ( 'fave_set' );
	
	while ( $contact = $contacts->getAllFavedBy ( $id, 1, '' ) ) {
		
		$tmpl->assign ( 'id', $contact ['user_id'] );
		$tmpl->assign ( 'name', $contact ['nick_name'] );
		$tmpl->assign ( 'image', $contact ['image'] );
	}
	
	$tmpl->loopset ( '' );
	exit ();
} */

/*
 * set display all from chat performer
 */
/* if (isset ( $_GET ['chat'] )) {
	
	$tmpl->loopset ( 'chat_set' );
	
	while ( $contact = $contacts->getAllFromChatOf ( $id, 1, '' ) ) {
		
		$tmpl->assign ( 'id', $contact ['user_id'] );
		$tmpl->assign ( 'name', $contact ['nick_name'] );
		$tmpl->assign ( 'image', '' );
	}
	
	$tmpl->loopset ( '' );
	exit ();
} */

/*
 * default display all data
 */

$tmpl->loopset ( 'all_set' );
while ( $contact = $contacts->getAllFor($id, 1, '' ) ) {
	
	$tmpl->assign ( 'id', $contact ['user_id'] );
	$tmpl->assign ( 'name', $contact ['nick_name'] );
	$tmpl->assign ( 'image', '' );
}

$tmpl->loopset ( '' );
$tmpl->flush();
exit;

?>