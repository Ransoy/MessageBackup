<?php

	require_once 'sp/operator.inc';

	require_once 'common_db.inc';
	require_once 'CommonDb.php';
	require_once 'sp/sp_performer_tmpl.class.inc';

	
	require_once 'message/Message.php';
	require_once 'message/MessageContact.php';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/MessageHelper.php';
	
	$tmpl = new TmplSPPerformer($sp_performer_dir .'message/message_contact_list.html' );
	
	$contacts = new MessageContactHelper();
	
	$id = $_SESSION['user_id'];
	$nick = '';
	$img = '';
	
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
	$tmpl->assign('url', '?'.$url);
		
	/*
	 * set display all from chat performer
	 */
	if ($getSort == 'chat') {
		
		$contact = $contacts->getAllFromChatOf($id, 2, $options);
		$tmpl->assign('chat','on');
		$tmpl->assign('v', $getSort);

		$funcName = 'getAllFromChatOf';
		
	} 
	
	/*
	 * default display all data
	 */
	if($getSort != 'chat' ){
		
		if(isset($_GET['q'])){
		    $options = array('name'=>$options, 'is_search' => 1);	 
		}
		
		$contact = $contacts->getAllFor($id, 2, $options);
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
	 * @param 2 - this is set for type 1 = user / 2 = performer
	 * @return data array object
	 */
	$contact = $contacts->paginateContact($funcName, $contact, $options, 10, 2);
	
	if($contact['data']){
		
		$tmpl->loopset('all_set');
		$page = (isset($_GET['page']))? $_GET['page'] : 1 ;
		foreach($contact['data'] as $row) {
			
			$tmpl->assign('id', $row->hash);
			$tmpl->assign('contact_id', $row->id);
			$tmpl->assign('from_page', $page);
			$stat = $contacts->checkInvalidStatus(1,$row->user_id);
			$nick = $row->nick_name;
			$img = '/imgs/member/320x240/'.$row->img;
			if($stat){
				$nick = '退会したユーザ';
				$img = '/img/noimage.gif';
			}
			$tmpl->assign('name', $nick);
			//Check male profile if private
			if($row->prof_open_flg == 0 ){
				$flag = '<a class="btn_profile private" href="javascript:void(0);"><img src="/sp/performer/img/common/spacer.gif" style="background-image: url('.$img.')" /></a>';
			}else{
				$flag = '<a class="btn_profile" rel="leanModal" data-type="ajax" href="/sp/performer/male_profile.php?hash='.$row->hash.'"><img src="/sp/performer/img/common/spacer.gif" style="background-image: url('.$img.')" /></a>';
			}
			$tmpl->assign('image', $flag);
			
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