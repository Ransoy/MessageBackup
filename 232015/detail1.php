<?php 
	if (isset($_GET['is_admin']) && $_GET['is_admin'] == 1) {
		$toId = ''; // only for admin id
		$toType = 3;
		$hash = null;
	} else if (isset($_GET['id'])) {
		$hash = $_GET['id'];
		$toType = 2;
	} else {
		header('Location: inbox.php');
		exit();
	}
	require_once 'CommonDb.php';
	require_once 'message/Messagetest1.php';
	require_once 'message/MessageHelper.php';
	require_once 'message/MessageContact.php';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/EmojiClass.php';
	require_once 'tmpl2.class_ex.inc';
	require_once 'common_proc.inc';
	require_once 'mc_session_routines.inc';
	require_once 'mc_common.inc';
	require_once 'mc_db.inc';
	
	session_start();
	//is_logged()
	
	$message = new Message();
	$messageHelper = new MessageHelper();
	$messageContactHelper = new MessageContactHelper();
	$messageContact = new MessageContact();
	$emoji = new EmojiClass();
	
	$messageResult = '';
	
	$tmpl = new Tmpl2 ($tmpl_dir . 'message/message_detail.html');
	
	// DB
	$db = new mcDB(getRandomSlave ());
	$dbSlave = $db->get_resource();
	
	printOuterFrame (&$tmpl,"マシェリとは？",$db,$ownerCd);
	
	$fromId = $_SESSION['user_id'];
	
	$fromType = 1;
	
	$result = $messageContactHelper->getContactDetail($fromId, $hash, $toType, $fromType);
	
	$contactId = '';
	$img = '';
	$toId = '';
	$userName = '';
	
	if (is_logged()) {
		if (!$result) {
			header('Location: inbox.php');
			exit();
		}
		$contactId = $result['id'];
		$img = $result['img'];
		$toId = $result['to_id'];
		$userName = $result['nick_name'];
	} else {
		header('Location: ../imacherie.php');
	}
	
	$canSend = 1; //Default is can send message
	if (!$messageHelper->canSend($fromId, $fromType, $toId)) {
		$tmpl->assign('is_notify',  1);
		// Cannot send message!
		$canSend = 0; //User or performercant send message, textbox and buttons are disabled;
		$messageResult .= '相手の都合によりメッセージが送信できません。<br/>'; //Message can not be sent
	}
	
	// Submit Message
	if (isset($_POST['submit_message']) || isset($_POST['formImage'])) {
		// Checks whether it was submitted by Image or Message Text
		if (isset($_POST['submit_message'])) { // Submitted by Message Text
			$messageBody = trim($_POST['message_body']);
			if ($messageBody != '') {
				if (strlen($messageBody) > 500) {
					$messageResult .= '文字数の上限を超えています。<br/>'; //Message has reached the limit
				} else {
					$message->setBody($messageBody);
				}
			} else {
				$messageResult .= 'メッセージが空である。<br/>'; //Message body is empty
			}
		} else { // Submitted by Image
			$imageValidate= $message->validateImage('input_photo');
			if ($imageValidate === true) {
				$message->setImage('input_photo');
			} else {
				$messageResult .= $imageValidate;
			}
		}
	
		if ($messageResult == '') {
			//$messageResult = "From : $fromId - $fromType <br/> To : $toId - $toType";
			if (!$messageContact->isBlockedBy($contactId, $toType)) {
				//$messageResult = "You are block by $userNickname!!";
				$message->setIsSent(1);
			}
			$message->setFromType($fromType);
			$message->setFromId($fromId);
			$message->setToType($toType);
			$message->setToId($toId);
			$result = $message->create();
			if ($result) {
				header('Location:'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
				die;
			} else {
				$messageResult = 'メッセージ送信が失敗しました。'; //Message unsuccessful
			}
		}
		$tmpl->assign('msg_error',  $messageResult);
	}
	
	/* ===================================================================================== */
	
	$tmpl->assign('from_type', $fromType);
	$tmpl->assign('to_type', $toType);
	$tmpl->assign('user_name', $userName);
	$tmpl->assign('to_id', $toId);
	$tmpl->assign('from_id', $fromId);
	$tmpl->assign('can_send', $canSend);
	
	if ($toType != 3 && is_logged()) {
		$fave 	= ($result['fave_id'] == $contactId) ? 'on' : '';
		$block	= ($result['block_id'] == $contactId) ? 'on' : '';
		$tmpl->assign('is_not_admin', true);
		$tmpl->assign('fave_on', $fave);
		$tmpl->assign('block_on', $block);
		$tmpl->assign('contact_id', $contactId);
		$tmpl->assign('hash', $hash);
		$tmpl->assign('image', $img);
	}
	
	$totalMessage = $message->countMessage($fromId, $toId, $fromType);
	
	if ($totalMessage > 10) {
		$tmpl->assign('total_message', $totalMessage);
	}
	
	$result = $message->getConversation($fromId, $toId, $fromType);
	$conversation = array_reverse($result);
	$lastId = $conversation[0]->id;
	
	$tmpl->assign('last_id', $lastId);
	$tmpl->assign('last_date', date("m/d",strtotime($conversation[0]->from_date)));
	$tmpl->assign('last_full_date', $conversation[0]->from_date);
	$tmpl->loopset('conversation');
	
	$date = '';
	$latestDate = '';
	$dayTransform = array(
		'0' => '日',
		'1' => '月',
		'2' => '火',
		'3' => '水',
		'4' => '木',
		'5' => '金',
		'6' => '土'
	);
	
	foreach ($conversation as $con) {
		$position = 'message_detail_item--right';
		if ($fromId != $con->from_id) {
			$imageProfile = '/imgs/op/320x240/' . $img;
			$tmpl->assign('image_profile', $imageProfile);
			$position = 'message_detail_item--left';
		} else {
			if ($con->is_read == 1) {
				$tmpl->assign('read', '既読');
			}
		}
		
		$body = (!empty($con->image)) ? "<img class='resized' src='/imgs/message/$con->image' >" : nl2br($emoji->getEmojiHtml($con->body));
		$latestDate = $con->from_date;
		$newDate = date('m/d',strtotime($latestDate));
		$dayNo = date('w', strtotime($latestDate));
		
		if ($date== '' || $date != $newDate) {
			$tmpl->assign('date', "$newDate ({$dayTransform[$dayNo]})");
			$tmpl->assign('full_date', $latestDate);
		}
			
		$tmpl->assign('position', $position);
		$tmpl->assign('time_send', date("H:i",strtotime($con->from_date)));
		$tmpl->assign('body', $body);
			
		$tmpl->loopnext();
			
		$date = $newDate;
		$message->checkIsRead($con->id, $fromId);
		
	}
	
	$tmpl->loopset('');
	
	$tmpl->flush();





