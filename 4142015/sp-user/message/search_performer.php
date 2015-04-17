<?php

	session_start ();
	require_once 'Owner.inc';
	require_once 'common_proc.inc';
	require_once 'db_con.inc';
	require_once 'sp/boy_login.inc';
	require_once 'sp/boy_sp.inc';
	require_once 'sp/tmpl2.class_ex.inc';
	require_once 'sp/common_proc.inc';
	require_once 'mc_session_routines.inc';
	
	require_once 'CommonDb.php';
	
	require_once 'message/Message.php';
	require_once 'message/MessageContact.php';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/MessageHelper.php';
	require_once 'imacherie_male.inc';
	
	$tmpl = new Tmpl22($sp_tmpl_dir .'message/message_search_performer.html' );
	
	
	$myInfo 	= myInformation();
	$oldUnread 	= $myInfo['midoku_num'];
	$tmpl->assign('old_unread', $oldUnread);
	
	$contactHelper 	= new MessageContactHelper();
	$message 		= new MessageHelper();
	
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
	
	if (!empty($contact['data'])) {
	
		$tmpl->loopset('all_set');
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		$name = ($getName) ? '&name=' . str_replace(' ','+',$getName) : '';
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
			$contactId = ($row->id) ? $row->id : 0;
			$tmpl->assign('contact_id', $contactId);
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
	
	$tmpl->flush();

?>