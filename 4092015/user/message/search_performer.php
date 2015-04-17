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
	
	$tmpl = new Tmpl2 ( $tmpl_dir . 'message/message_search_performer.html' );
	
	
	$contactHelper 	= new MessageContactHelper();
	$message 		= new MessageHelper();
	
	//check old mail unread mail
	$myInfo = myInformation();
	$oldUnread = $myInfo['midoku_num'];
	
	//initiate variables
	$id 	= $_SESSION['user_id'];
	$img 	= '';
	$nick 	= '';
	$url 	= '';
	$limit	= 10;
	$result	= '';
	
	
	$getName = (isset($_GET['name']))? $_GET['name'] : ''; 
	$getPage = (isset($_GET['page']))? $_GET['page'] : 0;
	
	if ($getName != '' || $getPage != '') {
		
		$url = "page=$getPage&name=$getName";
		$result = $contactHelper->searchPerformer($getName, '', '', $id);
		$contact = $contactHelper->paginateContact('searchPerformer', $result, $getName, $limit, 1, 2);
	}
	
	$tmpl->assign('searchVal', $getName);
	$tmpl->assign('url', $url);
	$tmpl->assign('old_unread', $oldUnread);
	
	if (!empty($contact['data'])) {
		
		$tmpl->loopset('all_set');
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		$name = ($getName) ? '&name=' . $getName : '';
		$fromUrl = $page . $name;
		foreach($contact['data'] as $row) {
			$nick = '';
			$tmpl->assign('id', $row->hash);
			$statProf = $contactHelper->checkInvalidStatus(2, $row->performer_id);
			$nick = $row->nick_name;
			$img = '/imgs/op/320x240/'.$row->img;
			if($statProf){
				$nick = '退会したユーザー';
				$img = '/img/noimage.gif';
			}
			$tmpl->assign('name', $nick);
			$tmpl->assign('image', $img);
			$tmpl->assign('contact_id', $row->id);
			$tmpl->assign('from_page', $fromUrl);
			$stat = $message->checkPerformerStatus($row->performer_id);
			if($stat == 1){
				$tmpl->assign('status', '<span class="user_status online">オンライン</span>');
			}elseif ($stat == 2){
				$tmpl->assign('status', '<span class="user_status onchat">チャット中</span>');
			}
			$tmpl->loopnext();
	
		}
		$tmpl->loopset('');
	
		//set assign nav
		$tmpl->assign('nav', $contact['nav']);
		
		
	} else {
		$tmpl->assign('no_result', 1);
	}
	
	
	
	/*
	$contact = $contactHelper->paginateContact($funcName, $contact, $options, 10, 1);
	
	if($contact['data']){
	
		$tmpl->loopset('all_set');
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		foreach($contact['data'] as $row) {
			$nick = '';
			$tmpl->assign('id', $row->hash);
			$statProf = $contacts->checkInvalidStatus(2, $row->performer_id);
			$nick = $row->nick_name;
			$img = '/imgs/op/320x240/'.$row->img;
			if($statProf){
				$nick = '退会したユーザー';
				$img = '/img/noimage.gif';
			}
			$tmpl->assign('name', $nick);
			$tmpl->assign('image', $img);
			$tmpl->assign('contact_id', $row->id);
			$tmpl->assign('from_page', $page);
			$stat = $message->checkPerformerStatus($row->performer_id);
			if($stat == 1){
				$tmpl->assign('status', '<span class="user_status online">オンライン</span>');
			}elseif ($stat == 2){
				$tmpl->assign('status', '<span class="user_status onchat">チャット中</span>');
			}
			$tmpl->loopnext();
	
		}
		$tmpl->loopset('');
	
		//set assign nav
		$tmpl->assign('nav', $contact['nav']);
	
	}else{
		$tmpl->assign('result', '1');
	}*/
	
	
	
	// DB
	$db = new mcDB ( getRandomSlave () );
	$dbSlave = $db->get_resource ();
	
	printOuterFrame ( &$tmpl, "マシェリとは？", $db, $ownerCd );
	
	$tmpl->flush ( 0 );
	exit ();
	
	
?>