<?php
require_once 'Owner.inc';
require_once 'sp/common_proc.inc';
require_once 'sp/boy_login.inc';
require_once 'sp/boy_sp.inc';
require_once 'sp/tmpl2.class_ex.inc';
require_once 'message/MessageInbox.php';
require_once 'message/MessageContactHelper.php';
require_once 'message/EmojiClass.php';
require_once 'imacherie_male.inc';

//Default Values 
$prev = 'disable';
$next = 'disable';
$limit = 10;
$page = 1;
$userId = $_SESSION['user_id'];
$messageCount = 0;
$numPages = 0;
$type = 1; // 1 for user 2 for performer
$msgContactHelper = new MessageContactHelper();

//template
$tmpl = new Tmpl22($sp_tmpl_dir . '/message/message_inbox_edit.html');
$tmpl->dbgmode(0);

//Initialzie MessageHelper class and Inbox class
$inboxEdit = new MessageInbox($userId,$type);
$helper = new MessageHelper();
$emoji = new EmojiClass();

if (isset($_GET['page'])) {
  //page number
  $page = $_GET['page'];
}

/**
 * get inbox from_ids.
 * delete inboxes.
 */
if(isset($_POST['ids'])) {  
  $helper->deleteInbox($_POST['ids'], $type, $userId);    
  $page = 1;
}

$tmpl->assign('page', $page);

$myInfo = myInformation();
$oldUnread = $myInfo['midoku_num'];
$tmpl->assign('old_unread', $oldUnread);

//query inbox messages
$resultAdmin = $inboxEdit->displayAdmin2($userId) ;
$adminMinus = (!empty($resultAdmin)) ? 1 : 0;
//Initialize offset & limit
$offset = $inboxEdit->getOffset($page, $limit, $adminMinus);
$limit2 = $inboxEdit->getLimit($page, $limit, $adminMinus);
$limit = $limit - $adminMinus;

$result = $inboxEdit->displayAll($offset, $limit2);

$messageCount = $inboxEdit->countAllMessage();

//Count inbox messages
$numPages = ceil($messageCount/$limit);

if(empty($result) && (empty($resultAdmin) || $page != 1)) {
  $tmpl->assign('hasNoMessages', true);
}
else {
  $tmpl->assign('hasMessages', true);
  
  if ($resultAdmin && $page == 1 ) {
  	$tmpl->loopset('loop_admin_set');
  	foreach ($resultAdmin as $row) {
  		$tmpl->assign('img', '/images/message/ui/ic_cs.png');
  		$tmpl->assign('name', 'マシェリスタッフ');
  		$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
  		$tmpl->assign('id', 'admin');
  		$count = $helper->countUnreadMessage($userId, $type, 0, 'admin');
  		$countUnread = ($count > 0 ) ? '<span class="cnt_unread">' . $count . '</span>' : '';
  		$tmpl->assign('count', $countUnread);
  
  		$tmpl->loopnext();
  
  	}
  	$tmpl->loopset('');
  }
  //display inbox messages.
  $tmpl->loopset('loop_set');
  foreach ($result as $row) {
  	if ($row->from_id == $userId) {
  		$tmpl->assign('sender', 'replied');
  		$receiverId = $row->to_id;
  	}
  	else {
  		$tmpl->assign('sender', '');
  		$receiverId = $row->from_id;
  	}
  	$sender = $helper->getUserInfo($receiverId, $type);
  	
  	if ($msgContactHelper->checkInvalidStatus(2, $receiverId)) {
  		$img = '/img/noimage.gif';
  		$nickname = '退会したユーザー';
  	} else {
  		$img = '/imgs/op/320x240/'.$sender->img;
  		$nickname = $sender->contact_name;
  	}
  	
  	$tmpl->assign('img', $img);
  	$tmpl->assign('name', $nickname);
  	$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
  	$tmpl->assign('id', $receiverId);
  	// unread count
  	$count = $helper->countUnreadMessage($userId, $type, $receiverId);
  
  	$countUnread = ($count > 0 ) ? '<span class="cnt_unread">' . $count . '</span>' : '';
  	$tmpl->assign('count', $countUnread);
  	$tmpl->loopnext();
  }
  $tmpl->loopset('');
  
  //Paginator
  if ($numPages > 1) {
  	if ($page > 1) {
  		$prev = 'on';
  	}
  	$tmpl->assign('prev', $prev);
  	if ($page < $numPages) {
  		$next = 'on';
  	}
  	$tmpl->assign('next', $next);
  	$tmpl->assign('pagination', $messageCount);
  	$pageNumber = $inboxEdit->paginator($page, $numPages);
  	$tmpl->assign('pages', $pageNumber);
  }
}

//Print my template
$tmpl->flush();
 
 
 
