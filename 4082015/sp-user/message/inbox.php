<?php
require_once 'Owner.inc';
require_once 'sp/common_proc.inc';
require_once 'sp/boy_login.inc';
require_once 'sp/boy_sp.inc';
require_once 'sp/tmpl2.class_ex.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';
require_once 'imacherie_male.inc';
require_once 'message/MessageContactHelper.php';

/**
 *  Default Values 
 */
$prev = '';
$next = '';
$limit = 10;
$page = isset($_GET['page'])? $_GET['page'] : 1;
$keyword = (isset($_GET['keyword'])) ? $_GET['keyword'] : '';
$all = true;
$unread = true;
$userId = $_SESSION['user_id'];
$messageCount = 0;
$numPages = 0;
$type = 1; // 1 for user 2 for performer

$msgContactHelper = new MessageContactHelper();

if (isset($_GET['tab'])) {
	if ($_GET['tab'] == 'all') {
		$unread = false;
	} 
	else {
		$all = false;
	}
	$keyword = strlen($_GET['keyword']) > 0 ? $_GET['keyword'] : '';
}

/**
 * Initialzie MessageHelper class and Inbox class
 */
$inbox = new MessageInbox($userId,$type);
$helper = new MessageHelper();
$emoji = new EmojiClass();
/** 
 * template
 */
$tmpl = new Tmpl22($sp_tmpl_dir . '/message/message_inbox.html');

$tmpl->assign('all', $all);
$tmpl->assign('unread', $unread);

$myInfo = myInformation();
$oldUnread = $myInfo['midoku_num'];
$tmpl->assign('old_unread', $oldUnread);

if (strlen($keyword) > 0) {
	$tmpl->assign('searched', 1);
} 
else {
	$tmpl->assign('searched', 0);
}

if ($all) {
	$tmpl->assign('tab', 'all');
} 
else {
	$tmpl->assign('tab', 'unread');
}

/**
 * query for selected tab
 */

$nomail = ($keyword != '') ? '�����Υ᡼�뤬����ޤ���' : '�ޤ��᡼������������򤬤���ޤ���';
$resultAdmin = '';
$adminMinus = 0;
if ($all) { 
	$resultAdmin = $inbox->displayAdmin2($userId, 0, $keyword);
	if (!empty($resultAdmin)) {
		$adminMinus = 1;
	}
	//Initialize offset & limit
	$offset = getOffset($page, $limit, $adminMinus);
	$limit2 = getLimit($page, $limit, $adminMinus);
	$limit = $limit - $adminMinus;
	$result = $inbox->displayAll($offset, $limit2, $keyword);
	
	$messageCount = $inbox->countAllMessage();
	// Count messages
	$numPages = ceil($messageCount/$limit);
	if (empty($result) && (empty($resultAdmin) || $page != 1)) {
			$tmpl->assign('no_mail', $nomail);
	}
	$tmpl->assign('all', 'on');
	$tmpl->assign('unread', '');
} 
elseif ($unread) {
	$resultAdmin = $inbox->displayAdmin2($userId, 1, $keyword);
	if (!empty($resultAdmin)) {
		$adminMinus = 1;
	}
	//Initialize offset & limit
	$offset = getOffset($page, $limit, $adminMinus);
	$limit2 = getLimit($page, $limit, $adminMinus);
	$limit = $limit - $adminMinus;
	
	$result = $inbox->displayAll($offset, $limit2, $keyword, 0);
	
	$messageCount = $inbox->countAllMessage();
	// Count messages
	$numPages = ceil($messageCount/$limit);
	if (empty($result) && (empty($resultAdmin) || $page != 1)) {	
		$tmpl->assign('no_mail', $nomail);
	}
	$tmpl->assign('all', '');
	$tmpl->assign('unread','on');
}
$tmpl->assign('page', $page);
$tmpl->assign('keyword', htmlentities($keyword, ENT_COMPAT, "EUC-JP"));
$tmpl->assign('old_keyword', addslashes($keyword));

if (!empty($resultAdmin) && $page == 1 ) {
	$tmpl->loopset('loop_admin_set');
	foreach ($resultAdmin as $row) {
		$tmpl->assign('img', '/images/message/ui/ic_cs.png');
		$tmpl->assign('name', '�ޥ����ꥹ���å�');
		$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
		$tmpl->assign('date', date('Y-m-d H:i', strtotime($row->from_date)));
		$count = $helper->countUnreadMessage($userId, $type, 0, 'admin');
		$countUnread = ($count > 0 ) ? '<span class="cnt_unread">' . $count . '</span>' : '';
		$tmpl->assign('count', $countUnread);
		$tmpl->assign('from_page', $page);
		$tmpl->loopnext();
	}
	$tmpl->loopset('');
}

// display message list.
if ($messageCount > 0) {
	$tmpl->loopset('loop_set');
	foreach ($result as $row) {
		//echo '<br>'; print_r($row);
		$status = '';
		if ($row->from_id == $userId) {
			$tmpl->assign('sender', 'replied');
			$receiverId = $row->to_id;
		}
		else {
			$tmpl->assign('sender', '');
			$receiverId = $row->from_id;
		}
	
		$sender = $helper->getUserInfo($receiverId, $type);
		$stat = $helper->checkPerformerStatus($receiverId);
		if ($stat == 1) {
			$status = '<span class="user_status online">����饤��</span>';
		}
		elseif ($stat == 2) {
			$status = '<span class="user_status onchat">����å���</span>';
		}
		
		if ($msgContactHelper->checkInvalidStatus(2, $receiverId)) {
			$img = '/img/noimage.gif';
			$nickname = '��񤷤��桼����';
		} else {
			$img = '/imgs/op/320x240/'.$sender->img;
			$nickname = $sender->contact_name;
		}
		
		$tmpl->assign('status' , $status);
		$tmpl->assign('img', $img);
		$tmpl->assign('name', $nickname);
		$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
		$tmpl->assign('date', date('Y-m-d H:i', strtotime($row->from_date)));
		$tmpl->assign('hash', $sender->hash);
		$tmpl->assign('id', $receiverId);
		$tmpl->assign('from_page', $page);
		
		//unread count
		$count = $helper->countUnreadMessage($userId, $type, $receiverId);
		$countUnread = (intval($count) > 0) ? '<span class="cnt_unread">' . $count . '</span>' : '';
		$tmpl->assign('count', $countUnread);
		
		$tmpl->loopnext();
	}
	$tmpl->loopset('');
	
	//pager
	if ($page <= $numPages && $numPages > 1) {
		$tmpl->assign('show_pages', '');
		$lastPage = ceil($messageCount/$limit);
		$start = (($page - 2) > 0 ) ? $page - 2 : 1;
		$end = (($page + 2) < $lastPage) ? $page +  2 : $lastPage;
		
		$prevPage = ($page == 1) ? 'disable' : '';
		$nextPage = ($page +1 > $lastPage) ? 'disable' : '';
		$prevVal = ($page == 1) ? '' : $page - 1;
		$nextVal = ($page + 1 > $lastPage) ? '' : $page + 1;
		
		$getKeyword = ($keyword != '') ? '&keyword='.$keyword : '';
		$getTab = ($all == true) ? '&tab=all': '&tab=unread';
		$tmpl->assign('get_keyword', $getKeyword.$getTab);
		$tmpl->assign('next', $nextPage);
		$tmpl->assign('prev', $prevPage);
		$tmpl->assign('next_val', $nextVal.$getKeyword.$getTab);
		$tmpl->assign('prev_val', $prevVal.$getKeyword.$getTab);
		
		if ($start > 1 ) {
			$tmpl->assign('first_page', 1);
			if ($start-1 > 1) {
				$tmpl->assign('first_page_dot', '...');
			}
		}
		
		$tmpl->loopset('pagination');
		for($i = $start; $i <= $end; $i++) {
			if ($i == $page) {
				$tmpl->assign('current', $i);
			} else {
				$tmpl->assign('get_keyword', $getKeyword.$getTab);
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
		$tmpl->loopset('');
	}
}

 $tmpl->flush();
 exit();
 
 function getOffset($page, $limit, $adminMinus) {
 	$adminMinus = ($adminMinus == 0 || $page == 1) ? 0 : $adminMinus;
 	$offset = (($page-1) * $limit) - $adminMinus;
 	return $offset;
 }
 function getLimit($page, $limit, $adminMinus) {
 	return ($page == 1) ? $limit - $adminMinus: $limit;
 }
 
 ?>
