<?php
	session_start ();
	require_once 'tmpl2.class_ex.inc';
	require_once 'common_proc.inc';
	require_once 'mc_session_routines.inc';
	require_once 'mc_common.inc';
	require_once 'mc_db.inc';
	require_once 'CommonDb.php';
	require_once 'imacherie_male.inc';
	
	require_once 'message/Message.php';
	require_once 'message/MessageContact.php';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/MessageHelper.php';
	
	
	if(!is_member_logged()) {
		$_SESSION['temp_flg'] = "line_mail";
		header('Location: ./../imacherie.php');
	}
	$nick = '';
	$img = '';
	$myInfo = myInformation();
	$oldUnread = $myInfo['midoku_num'];
	$id = $_SESSION['user_id'];		
	$tmpl = new Tmpl2 ( $tmpl_dir . 'message/message_contact_list_edit.html' );
	$contactHelper = new MessageContactHelper();
	$contacts = new MessageContact();
	
	/**
	 * Block contacts
	 * Go back to page 1
	*/
	if(isset($_POST['contact_ids'])) {
		$contact_ids = $_POST['contact_ids'];
		for($i = 0; $i < count($contact_ids); $i++) {
			$contacts->blockUser($contact_ids[$i]);
		}
		unset($_GET['page']);
	}
	
	$tmpl->assign('old_unread', $oldUnread);
	//default display all data
	$contact = $contactHelper->getAllFor($id, 1, '');
	$contact = $contactHelper->paginateContact('getAllFor', $contact, '', 10, 1);
	
	//Loop $contact array data object
	if($contact['data']){
		$tmpl->loopset('all_set');
		foreach($contact['data'] as $row) {
			$tmpl->assign('contactId', $row->id);
			$tmpl->assign('id', $row->hash);
			$stat = $contactHelper->checkInvalidStatus(2, $row->performer_id);
			$nick = $row->nick_name;
			$img = '/imgs/op/320x240/'.$row->img;
			if($stat){
				$nick = '退会したユーザー';
				$img = '/img/noimage.gif';
			}
			$tmpl->assign('name', $nick);
			$tmpl->assign('image', $img);
			
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