<?php
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';

  /**
   *  Default Values
   */
  $prev = '';
  $next = '';
  $limit = 10;
  $page = 1;
  $keyword = '';
  $all = true;
  $unread = true;
  $userId = $_SESSION['user_id'];
  $messageCount = 0;
  $numPages = 0;
  $type = 2; //performer
  
  if (isset($_POST['tab'])) {
  	if ($_POST['tab'] == 'all') {
  		$unread = false;
  	}
  	else {
  		$all = false;
  	}
  	//page number
  	$page = $_POST['page'];
  	$_POST['keyword'] = trim($_POST['keyword']);
  	$keyword = strlen($_POST['keyword']) > 0 ? $_POST['keyword'] : '';
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
  $tmpl = new Tmpl23(OP_PATH . 'template/message/message_inbox.html');
  
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
  	if ($messageCount == 0 && empty($resultAdmin)) {
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
	if ($messageCount == 0 && empty($resultAdmin)) {
		$tmpl->assign('no_mail', '');
	}
	$tmpl->assign('all', '');
	$tmpl->assign('unread', 'on');
  }
  $tmpl->assign('keyword', htmlentities($keyword, ENT_COMPAT, "EUC-JP"));
  $tmpl->assign('old_keyword', addslashes($keyword));
  $tmpl->assign('page', $page);
  
  if ($resultAdmin && $page == 1 ) {
  	$tmpl->loopset('loop_admin_set');
  	foreach ($resultAdmin as $row) {
  		if ($row->from_id == $userId) {
  			$tmpl->assign('sender', 'replied');
  		} else {
  			$tmpl->assign('sender', '');
  		}
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

  		$tmpl->assign('status', $status);
  		$sender = $helper->getUserInfo($receiverId, $type);
  		$tmpl->assign('img', '/imgs/member/320x240/'.$sender->img);
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
  	if ($numPages > 1) {
  		if ($page == 1) {
  			$prev = 'disable';
  		}
  	
  		$tmpl->assign('prev', $prev);
  		if ($page == $numPages) {
  			$next = 'disable';
  		}
  	
  		$tmpl->assign('next', $next);
  		$tmpl->assign('pagination', $messageCount);
  		$pageNumber = $inbox->paginator($page, $numPages);
  		$tmpl->assign('pages', $pageNumber);
  	}
  	
  }
  
$tmpl->flush();
exit();
?>
  
  