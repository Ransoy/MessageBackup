<?php
session_start();
require_once 'tmpl2.class_ex.inc';
require_once 'common_proc.inc';
require_once 'mc_session_routines.inc';
require_once 'mc_common.inc';
require_once 'mc_db.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';

if(!is_member_logged()) {
	header('Location: ../../imacherie.php');
}

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
$type = 1; //male user

if (isset($_GET['tab'])) {
	if ($_GET['tab'] == 'all') {
		$unread = false;
	}
	else {
		$all = false;
	}
	$_GET['keyword'] = trim($_GET['keyword']);
	$keyword = strlen($_GET['keyword']) > 0 ? $_GET['keyword'] : '';
	$page = 1;
}

/**
 * Initialize MessageHelper class and MessageInbox class
 */
$inbox = new MessageInbox($userId,$type);
$helper = new MessageHelper();
$emoji = new EmojiClass();
/**
 * template
*/
$tmpl = new Tmpl2($tmpl_dir . '/message/message_inbox.html');

$tmpl->assign('all', $all);
$tmpl->assign('unread', $unread);
$tmpl->assign('type', $type);

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
if ($all) { 
	//Initialize offset
	$offset = ($page-1) * $limit;
	
	$resultAdmin = $inbox->displayAdmin($userId);
	
	$result = $inbox->displayAll($offset, $limit, $keyword);
	
	
	$messageCount = $inbox->countAllMessage();
	// Count messages
	$numPages = ceil($messageCount/$limit);
	if ($page > $numPages || ($messageCount == 0 && empty($resultAdmin))) {
		$tmpl->assign('no_mail', '');
	}
	$tmpl->assign('all', 'on');
	$tmpl->assign('unread', '');
	
}
elseif ($unread) {
	//Initialize offset
	$offset = ($page-1) * $limit;
	
	$resultAdmin = $inbox->displayAdmin($userId, 1);
	
	$result = $inbox->displayAll($offset, $limit, $keyword, 0);
	
	
	$messageCount = $inbox->countAllMessage();
	// Count messages
	$numPages = ceil($messageCount/$limit);
	if ($page > $numPages || ($messageCount == 0 && empty($resultAdmin))) {
		$tmpl->assign('no_mail', '');
	}
	$tmpl->assign('all', '');
	$tmpl->assign('unread', 'on');

}
$tmpl->assign('keyword', htmlentities($keyword, ENT_COMPAT, 'EUC-JP'));
$tmpl->assign('old_keyword', addslashes($keyword));

$tmpl->assign('page', $page);


if ($resultAdmin && $page == 1 ) {
	$tmpl->loopset('loop_admin_set');
	foreach ($resultAdmin as $row) {
		$receiverId = $row->admin_id;
		$sender = $helper->getUserInfo($receiverId, 3);
		if (!empty($sender)) {
			$tmpl->assign('img', '/images/message/ui/ic_cs.png');
			$tmpl->assign('name', $sender->admin_name);
			$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
			$tmpl->assign('date', date('Y-m-d H:i', strtotime($row->from_date)));
			$tmpl->assign('hash', $sender->hash);
			$tmpl->assign('id', $receiverId);
			$count = $helper->countUnreadMessage($userId, $type, $receiverId);
			if ($count > 0) {
				$tmpl->assign('count', $count);
			}
			$tmpl->loopnext();
		}
	}
	$tmpl->loopset('');
}

// display message list.
if ($messageCount > 0) {
	$tmpl->loopset('loop_set');
	foreach ($result as $row) {
		$status = '';
		if ($row->from_id == $userId) {
			$tmpl->assign('sender', 'replied');
			$receiverId = $row->to_id;
		}
		else {
			$tmpl->assign('sender', '');
			$receiverId = $row->from_id;
		}
		$stat = $helper->checkPerformerStatus($receiverId);
		if ($stat == 1) {
			$status = '<span class="user_status online">オンライン</span>';
		}
		elseif ($stat == 2) {
			$status = '<span class="user_status onchat">チャット中</span>';
		}
		$tmpl->assign('status', $status);
		$sender = $helper->getUserInfo($receiverId, $type);
		$tmpl->assign('img', '/imgs/op/320x240/'.$sender->img);
		$tmpl->assign('name', $sender->contact_name);
		$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
		$tmpl->assign('date', date('Y-m-d H:i', strtotime($row->from_date)));
		$tmpl->assign('hash', $sender->hash);
		$tmpl->assign('id', $receiverId);
			
		//unread count
		$count = $helper->countUnreadMessage($userId, $type, $receiverId);
		if ($count > 0) {
			$tmpl->assign('count', $count);
		}
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
		
		$getTab = ($all == true) ? '&tab=all': '&tab=unread';
		$getKeyword = ($keyword != '') ? '&keyword='.$keyword : '';
		
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



// DB
$db = new mcDB ( getRandomSlave () );
$dbSlave = $db->get_resource ();

printOuterFrame ( &$tmpl, "マシェリとは？", $db, $ownerCd );

$tmpl->flush ( 0 );
exit ();