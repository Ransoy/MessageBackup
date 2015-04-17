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
	
	$tmpl = new Tmpl22($sp_tmpl_dir .'message/message_contact_list.html' );
	
	$myInfo = myInformation();
	$oldUnread = $myInfo['midoku_num'];
	$tmpl->assign('old_unread', $oldUnread);
	
	$contacts = new MessageContactHelper();
	$message = new MessageHelper();
	
	$id = $_SESSION['user_id'];
	
	$options = '';
	$url = '';
	
	/*
	 * set search , display by all, fave and chat
	 */
	$getSort = (isset($_GET['v']))? $_GET['v'] : '' ;
	if(isset($_GET['q'])){
		
		$options = $_GET['q'];
		$url = 'q='.$options.'&'. $url;
		
	}

	$tmpl->assign('searchVal', $options);
	$tmpl->assign('url', $url);
	
	/*
	 * set display all favorite performer
	 */
	if ($getSort == 'fave') {
		
		$contact = $contacts->getAllFavedBy($id, 1, $options);
		$tmpl->assign('fave', 'on');
		$tmpl->assign('v', $getSort);
		
		$funcName = 'getAllFavedBy';
	
	}
	
	/*
	 * set display all from chat performer
	 */
	if ($getSort == 'chat') {
		
		$contact = $contacts->getAllFromChatOf($id, 1, $options);
		$tmpl->assign('chat','on');
		$tmpl->assign('v', $getSort);
		
		$funcName = 'getAllFromChatOf';
		
	} 
	
	/*
	 * default display all data
	 */
	if($getSort != 'chat' && $getSort != 'fave'){
		
		if(isset($_GET['q'])){
		    $options = array('name'=>$options, 'is_search' => 1);	 
		}
		    
		$contact = $contacts->getAllFor($id, 1, $options);
		$tmpl->assign('v', '');
		$tmpl->assign('all', 'on');
		
		$funcName = 'getAllFor';
		
	}
	
	/**
	 * Display all record set by given paramater
	 * @param $funcName - call method in class e.g 'getAllFor'
	 * @param $contact - get all record
	 * @oaram $options - set options for search by keyword
	 * @param 10 - this is set for limit pagination
	 * @param 1 - this is set for type 1 = user / 2 = performer
	 * @return data array object
	 */
	$contact = $contacts->paginateContact($funcName, $contact, $options, 10, 1);

	if($contact['data']){
		
		$tmpl->loopset('all_set');
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		foreach($contact['data'] as $row) {
			
			$tmpl->assign('id', $row->hash);
			$tmpl->assign('name', $row->nick_name);
			$tmpl->assign('image', $row->img);
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
	}
	
	$tmpl->flush();

?>