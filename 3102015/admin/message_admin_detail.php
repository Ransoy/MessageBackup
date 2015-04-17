<?php
session_start();
if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'adminlogin') {
	echo 'fail';
	exit();
}


if (isset($_POST['page']) && isset($_POST['order'])) {
	
	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'tmpl2.class_ex.inc';
	require_once 'Owner.inc';
	require_once 'admin.inc';
	require_once 'common_proc.inc';
	require_once 'common_db.inc';
	require_once 'message/EmojiClass.php';
	
	if (isset($_POST['test'])) {
		echo mb_convert_encoding(urldecode($_POST['message']), "EUC-JP", "UTF-8");
		exit();
	}
	
	$tmpl = new Tmpl22($tmpl_dir . "manage/message/message_admin_detail.html");
	$tmpl->dbgmode(0);
	$message = new Message();
	$db = new CommonDb();
	$emoji = new EmojiClass();
	$filter = '';
	
	/************************************** FILTER *************************************/
	
	if ($_POST['is_check'] != 2) {
		$filter = 'is_checked = ' . $_POST['is_check'];
	}
	//if ($_POST['message'] != '' || $_POST['performer_id'] != '' || $_POST['male_id'] != '' || $_POST['']) {
	if (isset($_POST['message']) && $_POST['message'] != '') {
		$keywordMessage = getPostData($_POST['message']);
		$filter .= " AND body like '%".$keywordMessage."%'";
		/*echo $filter;
		exit();*/
	}
	if (isset($_POST['performer_id']) && $_POST['performer_id'] != '') {
		$keywordID = getPostData($_POST['performer_id']);
		$filter .= filterUser(2, $_POST['performer_type'], $keywordID);
	}
	if (isset($_POST['male_id']) && $_POST['male_id'] != '') {
		$keywordID = getPostData($_POST['male_id']);
		$filter .= filterUser(1, $_POST['male_type'], $keywordID);	
	}
	if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
		$filter .= " AND (DATE(from_date) >= '{$_POST['start_date']}' AND DATE(from_date) <= '{$_POST['end_date']}')";
	}
		
	//}
	
	if ($_POST['select'] == 'all') {
		$select = '*';
	} else {
		$select = $_POST['select'] . ', id, owner_cd, from_type, from_id, from_ip, from_date,'
									. ' to_type, to_id, is_sent, is_read, read_date, is_checked';
		$filter .= " AND {$_POST['select']} <> ''";
	}
	
	//If view all message is_checked 0 and 1
	if ($_POST['is_check'] == 2 && !empty($filter)) {
		$filter = substr($filter, 4);
	}
	
	
	/*********************************** PAGINATION ************************************/
	$db->select('count(id) as total');
	$db->from('message');
	if($filter != '') {
		$db->where($filter);
	}
	$result = $db->get_row();
	$total = $result['total'];
	$limit = 10;
	
	if ($total > 0) { // false if nothing is found
		if ($total > 1) {
			$lastPage = ceil($total/$limit);
			$start = (($_POST['page'] - 2) > 0 ) ? $_POST['page'] - 2 : 1;
			$end = (($_POST['page'] + 2) < $lastPage) ? $_POST['page'] +  2 : $lastPage;
			
			
			$prevPage = ($_POST['page'] == 1) ? 'disable' : '';
			$nextPage = ($_POST['page'] +1 > $lastPage) ? 'disable' : '';
			
			$tmpl->assign('next_page', $nextPage);
			$tmpl->assign('prev_page', $prevPage);
			
			if ($start > 1 ) {
				$tmpl->assign('first_page', 1);
				if ($start-1 > 1) {
					$tmpl->assign('first_page_dot', '...');
				}
			}
			
			$tmpl->loopset('pagination');
			for($i = $start; $i <= $end; $i++) {
				if ($i == $_POST['page']) {
					$tmpl->assign('current', $i);
				} else {
					$tmpl->assign('page', $i);
				}
				$tmpl->loopnext();
			}
			$tmpl->loopset('');
			
			if ( $end < $lastPage ) {
				$tmpl->assign('last_page', $lastPage);
				if ($end+1 < $lastPage) {
					$tmpl->assign('last_page_dot', '...');
				}
			}
				
			
		}
		
		/************************************ RESULT ***************************************/
		$result = $message->getAll($filter, $_POST['page']-1, $select, $_POST['order'], $limit);
	
		$tmpl->loopset('message');
		
		foreach($result as $r) {
			$tmpl->assign('id', 		$r->id);
			$tmpl->assign('sender', 	$r->from_id);
			$tmpl->assign('receiver', 	$r->to_id);
			$tmpl->assign('date', 		$r->from_date);
			
			if($r->image != '' && $r->body != ''){
				$tmpl->assign('fullbody', '<img class="message-image" src="/imgs/message/'.$r->image.'"><p class="message-col">'.$emoji->getEmojiHtml($r->body).'</p>');
			}else{
				if ($r->body != '') {
					$tmpl->assign('body', 	$emoji->getEmojiHtml($r->body));
				} else {
					$tmpl->assign('image',	$r->image);
				}
			}
			
			
			$senderBG = '';
			$receiverBG = '';
			
			if ($r->from_type == 1) {
				$senderBG = 'contact-user';
				$receiverBG = 'contact-performer';
			} else if ($r->from_type == 2) {
				$senderBG = 'contact-performer';
				$receiverBG = 'contact-user';
			} else if ($r->from_type == 3) {
				$senderBG = 'contact-admin';
			
			}
			
			switch ($r->from_type) {
				case 1 :
					$senderBG = 'contact-user';
					 break;
				case 2 : 
					$senderBG = 'contact-performer';
					break;
				case 3 : 
					$senderBG = 'contact-admin';
					break;
			}
			
			switch ($r->to_type) {
				case 1 : 
					$receiverBG = 'contact-user';
					break;
				case 2 : 
					$receiverBG = 'contact-performer';
					break;
				case 3 :
					$receiverBG = 'contact-admin';
					 break;
			}
			
			if ($r->is_checked == 0) {
				$tmpl->assign('enable_checked', 'enable_checked');
			}
			
			$tmpl->assign('sender_bg', $senderBG);
			$tmpl->assign('receiver_bg', $receiverBG);
			$tmpl->assign('is_read', 	$r->is_read);
			$tmpl->loopnext();
		}
		
		$tmpl->loopset('');
	
	} else {
		$tmpl->assign('is_not_found', 'none');
	}	
	$tmpl->flush();
} else {
	echo 'invalid parameter';
}

function filterUser($userType, $filterType, $keywordID) {
	$filteredUser = " AND ( ";
	switch ($filterType) {
		case 0	: $filteredUser .= "(from_id like '%$keywordID%' AND from_type = $userType) OR ";
		case 2	: $filteredUser .= "(to_id like '%$keywordID%' AND to_type = $userType)"; break;
		case 1	: $filteredUser .= "(from_id like '%$keywordID%' AND from_type = $userType)";
	}
	$filteredUser .= " )";
	return $filteredUser;
}

function getPostData($data) {
	return addcslashes(mb_convert_encoding(urldecode($data), "EUC-JP", "UTF-8"), '!...?');
}