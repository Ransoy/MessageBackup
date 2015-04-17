<?php
	if (isset($_GET['is_admin']) && $_GET['is_admin'] == 1) {
		$toType = 3;
	} else if (isset($_GET['id'])) {
		$toType = 1;
	} else {
		header('Location: inbox.php');
		exit();
	}
	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'message/MessageHelper.php';
	require_once 'message/MessageContact.php';
	require_once 'message/MessageContactHelper.php';
    require_once 'sp/sp_performer_tmpl.class.inc';
    require_once 'message/EmojiClass.php';
	
	$message = new Message();
	$messageHelper = new MessageHelper();
	$messageContactHelper = new MessageContactHelper();
	$messageContact = new MessageContact();
	$emoji = new EmojiClass();
	
	$messageResult = '';
	
	$tmpl = new TmplSPPerformer($sp_performer_dir . 'message/message_detail.html');
	
	$fromId = $_SESSION['user_id'];
	$fromType = 2;
	$hash = $_GET['id'];
	
	$male = $messageContactHelper->getUserId($hash, $toType);
	//$result = $messageContactHelper->getContactDetail($fromId, $hash, $toType, $fromType);
	
	if (!$male) {
		header('Location: inbox.php');
		exit();
	}
	
	$fromBack = (isset($_GET['from']) && ($_GET['from'] != 'inbox' || $_GET['from'] != 'contact_list')) ? $_GET['from'] .'.php' : 'inbox.php';
	
	$tmpl->assign('from_back', $fromBack);
	
	$toId = $male['to_id'];
	$img = $male['img'];
	$userName = $male['nick_name'];
	
	$contactId = 0;
	$result = $messageContactHelper->getContactDetail($fromId, $toId, $toType, $fromType);
	
	if ($result) {
		$contactId = $result['id'];
	}
	
	$canSend = 1; //Default is can send message
	if ((!$messageHelper->canSend($fromId, $fromType, $toId) || !$messageContactHelper->checkStatus($hash)) && $toType != 3) {
		$tmpl->assign('is_notify',  1);
		// Cannot send message!
		$canSend = 0; //User or performercant send message, textbox and buttons are disabled;
		$messageResult .= '相手の都合によりメッセージが送信できません。<br/>'; //Message can not be sent
	}
	
	if (isset($_POST['submit_message']) || isset($_POST['formImage'])) {
		// Checks whether it was submitted by Image or Message Text
		if (isset($_POST['submit_message'])) { // Submitted by Message Text
			$messageBody = trim($_POST['message_body']);
			if ($messageBody != '') {
				if (strlen($messageBody) > 500) {
					$messageResult .= '文字数の上限を超えています。<br/>';
				} else {
					$message->setBody($messageBody);
				}
			} else {
				$messageResult .= 'メッセージが空である。<br/>'; //Message body is empty
			}
		} else { // Submitted by Image
			$imgSource = (empty($_FILES['imageFile']['tmp_name'])) ? 'imageCam' : 'imageFile';
			$imageValidate= $message->validateImage($imgSource);
			if ($imageValidate === true) {
				$message->setImage($imgSource);
			} else {
				$messageResult .= $imageValidate;
			}
		}
	
		if ($messageResult == '') {
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
	$tmpl->assign('user_hash', $hash);
	$tmpl->assign('image', $img);
	$tmpl->assign('current_date_time', date('Y-m-d H:i:s'));
	
	if ($toType != 3) {
		if (!empty($contactId)) {
			$fave 	= ($result['fave_id'] == $contactId) ? 'on' : '';
			$block	= ($result['block_id'] == $contactId) ? 'on' : '';
		} else {
			$fave 	= 'disabled';
			$block 	= 'disabled';
		}
		$tmpl->assign('is_not_admin', true);
		$tmpl->assign('fave_on', $fave);
		$tmpl->assign('block_on', $block);
		$tmpl->assign('contact_id', $contactId);
		
		if ($toType == 1) {
			$disable = (empty($result['prof_open_flg']))?'disabled':'';
			$tmpl->assign('disable', $disable);
		}
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
	$dayTransform = array(
		'0' => '日',
		'1' => '月',
		'2' => '火',
		'3' => '水',
		'4' => '木',
		'5' => '金',
		'6' => '土'
	);
	
	$imageProfile = ($toType == 3) ? $img : '/imgs/member/320x240/' . $img;
	foreach ($conversation as $con) {
		$position = 'message_detail_item--right';
		$tmpl->assign('not_read', '');
		if ($fromId != $con->from_id) {
			$tmpl->assign('image_profile', $imageProfile);
			$position = 'message_detail_item--left';
		} else {
			if ($con->is_read == 1) {
				$tmpl->assign('read', '既読');
			} else {
				$tmpl->assign('not_read', 'not-read');
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
		
		$tmpl->assign('message_id', $con->id);
		$tmpl->assign('position', $position);
		$tmpl->assign('time_send', date("H:i",strtotime($con->from_date)));
		$tmpl->assign('body', $body);
		
		$tmpl->loopnext();
		$message->checkIsRead($con->id, $fromId);
		
		$date = $newDate;
	}	
	
	$tmpl->loopset('');
	$tmpl->flush();