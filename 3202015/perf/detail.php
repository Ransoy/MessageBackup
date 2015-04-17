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
require_once 'message/MessageHelper.php';
require_once 'message/MessageContact.php';
require_once 'message/MessageContactHelper.php';
require_once 'message/Message.php';
require_once 'common_db.inc';
require_once 'Owner.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'message/EmojiClass.php';
	
$message = new Message();
$messageHelper = new MessageHelper();
$messageContactHelper = new MessageContactHelper();
$messageContact = new MessageContact();
$emoji = new EmojiClass();

$messageResult = '';

$tmpl = new Tmpl23(OP_PATH . 'template/message/message_detail.html');

if (isset($_GET['from']) && ($_GET['from'] == 'inbox' || $_GET['from'] == 'contact_list')) {
	$fromBack = $_GET['from'] .'.php';
} else {
	$fromBack = 'inbox.php';
}
$fromPage = (isset($_GET['page'])) ? '?page=' . $_GET['page'] : '';
$tmpl->assign('from_back', $fromBack . $fromPage);

$hash = $_GET['id'];
$fromId = $_SESSION['user_id'];
$fromType = 2;
$contactId = 0;
$canSend = 1; //Default is can send message

if ($toType != 3 ) {
	$male = $messageContactHelper->getUserId($hash, $toType);
	if (!$male) {
		header('Location: inbox.php');
		exit();
	}
	$toId = $male['to_id'];
	$img = $male['img'];
	$userName = $male['nick_name'];
	$result = $messageContactHelper->getContactDetail($fromId, $toId, $toType, $fromType);
	if ($result) {
		$contactId = $result['id'];
	}
	if ((!$messageHelper->canSend($fromId, $fromType, $toId) || !$messageContactHelper->checkStatus($hash))) {
		$tmpl->assign('is_notify',  1);
		$canSend = 0; //User or performer cant send message, textbox and buttons are disabled;
		$messageResult .= '相手の都合によりメッセージが送信できません。<br/>'; //Message can not be sent
	}
} else {
	$img = '/images/message/ui/ic_cs.png';
	$userName = 'マシェリスタッフ';
	$toId = 'admin';
	$canSend = 0;
}

// Submit Message
if (isset($_POST['submit_message'])) {
	// Checks whether it was submitted by Image or Message Text
	//if (isset($_POST['submit_message'])) { // Submitted by Message Text
	$messageBody = trim($_POST['message_body']);
	$hasImage = !empty($_FILES['input_photo']['tmp_name']);
	if ($messageBody != '' || $hasImage) {
		if (mb_strlen($messageBody, 'UTF-8') > 500) {
			$messageResult .= '文字数の上限を超えています。<br/>'; //Message has reached the limit
		} else {
			$message->setBody($messageBody);
		}
		if ($hasImage) {
			$imageValidate= $message->validateImage('input_photo');
			if ($imageValidate === true) {
				$message->setImage('input_photo');
			} else {
				$messageResult .= $imageValidate;
			}
		}
	} else {
		$messageResult .= 'メッセージが空である。<br/>'; //Message body is empty
	}

	if ($messageResult == '') {
		if ($contactId == 0 && $toType != 3) {
			$messageContact->setUserId($toId);
			$messageContact->setPerformerId($fromId);
			$contact = $messageContact->getContact();
			if ($contact) {
				$contactId = $contact['id'];
			} else {
				$messageContact->setOwnerId($ownerCd);
				$messageContact->setIsFromChat(0);
				$contactId = $messageContact->create();
			}
		}
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
		$fave 	= 'disable';
		$block 	= 'disable';
	}
	$tmpl->assign('is_not_admin', true);
	$tmpl->assign('fave_on', $fave);
	$tmpl->assign('block_on', $block);
	$tmpl->assign('contact_id', $contactId);
	
	if ($toType == 1) {
		$disable = (empty($male['prof_open_flg']))?'disabled':'';
		$tmpl->assign('disable', $disable);
	}
}

$totalMessage = $message->countMessage($fromId, $toId, $fromType, $toType);

if ($totalMessage > 10) {
	$tmpl->assign('total_message', $totalMessage);
}

$result = $message->getConversation($fromId, $toId, $fromType, $toType);

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
	
	$body = (!empty($con->image)) ? "<img class='resized' src='/imgs/message/$con->image' >" : '';
	$body .= $emoji->getEmojiHtml($con->body);
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