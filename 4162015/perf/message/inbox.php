<?php
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';
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
  $type = 2; //performer
  
  $msgContactHelper = new MessageContactHelper();
  
  if (isset($_GET['tab'])) {
  	if ($_GET['tab'] == 'all') {
  		$unread = false;
  	}
  	else {
  		$all = false;
  	}
  	//page number
  	$keyword = strlen($_GET['keyword']) > 0 ? $_GET['keyword'] : '';
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
  $oldUnread = $tmpl->checkMailBox();
  $tmpl->assign('old_unread', $oldUnread);
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
  $nomail = ($keyword != '') ? '該当のメールがありません。' : 'まだメールの送受信履歴がありません。';
  if ($all) {
  	
  	$resultAdmin = $inbox->displayAdmin2($userId, 0, $keyword);
  	$adminMinus = (!empty($resultAdmin)) ? 1 : 0;
  	//Initialize offset & limit
  	$offset = $inbox->getOffset($page, $limit, $adminMinus);
  	$limit2 = $inbox->getLimit($page, $limit, $adminMinus);
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
  	
  } else if ($unread) {
	
	$resultAdmin = $inbox->displayAdmin2($userId, 1, $keyword);
	$adminMinus = (!empty($resultAdmin)) ? 1 : 0;
	//Initialize offset & limit
	$offset = $inbox->getOffset($page, $limit, $adminMinus);
	$limit2 = $inbox->getLimit($page, $limit, $adminMinus);
	$limit = $limit - $adminMinus;
	
	$result = $inbox->displayAll($offset, $limit2, $keyword, 0);
	
	$messageCount = $inbox->countAllMessage();
	// Count messages
	$numPages = ceil($messageCount/$limit);
	if (empty($result) && (empty($resultAdmin) || $page != 1)) {
		$tmpl->assign('no_mail', $nomail);
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
  		$tmpl->assign('img', '/images/message/ui/ic_cs.png');
  		$tmpl->assign('name', 'マシェリスタッフ');
  		$tmpl->assign('body', $emoji->getEmojiHtml($row->body));
  		$tmpl->assign('date', date('Y-m-d H:i', strtotime($row->from_date)));
  		$count = $helper->countUnreadMessage($userId, $type, 0, 'admin');
  		$countUnread = (intval($count) > 0) ? '<span class="cnt_unread">' . $count . '</span>' : '';
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
  		
  		if ($msgContactHelper->checkInvalidStatus(1, $receiverId)) {
  			$img = '/img/noimage.gif';
  			$nickname = '退会したユーザー';
  		} else {
  			$img = '/imgs/member/320x240/'.$sender->img;
  			$nickname = $sender->contact_name;
  		}
  		
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
  
$tmpl->flush();
exit();
?>
  
  