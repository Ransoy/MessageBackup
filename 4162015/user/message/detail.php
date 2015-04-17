<?php 
if (isset($_GET['is_admin']) && $_GET['is_admin'] == 1) {
	$toType = 3;
} else if (isset($_GET['id'])) {
	$toType = 2;
} else {
	header('Location: inbox.php');
	exit();
}
require_once 'CommonDb.php';
require_once 'message/Message.php';
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

if (!is_member_logged()) {
	header('Location: inbox.php');
	exit();
}

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
$hash = $_GET['id'];

$result = '';
$contactId = 0;
$img = '';
$nick = '';
$toId = '';
$userName = '';
$canSend = 1;  //Default is can send message
$isNotify = '';

if ($toType != 3) {
	$female = $messageContactHelper->getUserId($hash, $toType);
	if (empty($female)) {
		header('Location: inbox.php');
		exit();
	}
	$img = '/imgs/op/320x240/' . $female['img'];
	$toId = $female['to_id'];
	
	$stat = $messageContactHelper->checkInvalidStatus(2,$toId);
	$userName = $female['nick_name'];
	if($stat){
		$userName = '退会したユーザー';
		$img = '/img/noimage.gif';
	}
	
	$result = $messageContactHelper->getContactDetail($fromId, $toId, $toType, $fromType);
	if ($result) {
		$contactId = $result['id'];
	} else {
		$messageContact->setUserId($fromId);
		$messageContact->setPerformerId($toId);
		$messageContact->setOwnerId($ownerCd);
		$messageContact->setIsFromChat(0);
		$contactId = $messageContact->create();
		
		
	}
	
	$error = '';
	if (!$messageHelper->canSend($fromId, $fromType, $toId)) {
		//$tmpl->assign('is_notify',  1);
		$messageResult .= 'ポイントが足りません。ポイントを購入しますか？<a class="link_purchase" href="/settlement/bank.php">ポイントを購入＞</a><br/>'; //Message can not be sent
		$error = "ポイントが足りません。\n";
	}
	
	if ($contactId != 0 && $messageContact->isBlockedBy($contactId, $toType)) {
		$messageResult .= '相手のご都合によりメッセージを送ることができません。<br/>';
		$error .= "相手のご都合によりメッセージを送ることができません。\n";
	}
	
	if ($messageContactHelper->checkInvalidStatus(1, $fromId) || $messageContactHelper->checkInvalidStatus(2, $toId)) {
		$messageResult .= '退会したユーザーのため、送信できません。<br/>';
		$error .= "退会したユーザーのため、送信できません。";
	}
	
	if (!empty($messageResult)) {
		$canSend = 0; //Cant send
		$tmpl->assign('msg_error', $messageResult);
		$tmpl->assign('error', $error);
	}
} else {
	$img = '/images/message/ui/ic_cs.png';
	$userName = 'マシェリスタッフ';
	$toId = 'admin';
	$canSend = 0;
	$messageResult .= 'マシェリ管理にメールを送信できません。  <br/>';
	$error .= "マシェリ管理にメールを送信できません。";
	$tmpl->assign('msg_error', $messageResult);
	$tmpl->assign('error', $error);
}	

if (isset($_GET['from']) && 
	(	
		$_GET['from'] == 'inbox' 		|| 
		$_GET['from'] == 'contact_list' ||
		$_GET['from'] == 'search_performer'
	)
) {
	$fromBack = $_GET['from'] .'.php';
	$fromPage = (isset($_GET['page'])) ? '?page=' . $_GET['page'] : '';
	$fromName = (isset($_GET['name'])) ? '&name=' . $_GET['name'] : '';
	$fromName = str_replace(' ', '+', $fromName);
} else {
	$fromBack = 'inbox.php';
}

$tmpl->assign('from_back', $fromBack . $fromPage . $fromName);

// Submit Message
if ($canSend != 0 && isset($_POST['submit_message'])) {
	$messageBody = $_POST['message_body'];
	$hasImage = !empty($_FILES['input_photo']['tmp_name']);
	$hasBody = trim(mb_trim($messageBody)) != '';
	$bodyIndex = '';
	$imageIndex = '';
	if ($hasBody || $hasImage) {
		if ($hasBody) {
			if (mb_strlen($messageBody, 'UTF-8') > 500) {
				$messageResult .= '文字数の上限を超えています。<br/>'; //Message has reached the limit
			} else {
				$message->setBody($messageBody);
				$bodyIndex = 'message';
			}
		}
		if ($hasImage) {
			$imageValidate = $message->validateImage('input_photo');
			if ($imageValidate === true) {
				$message->setImage('input_photo');
				$imageIndex = 'image';
			} else {
				$messageResult .= $imageValidate;
			}
		}
	} else if (isset($_POST['has_image']) && $_POST['has_image'] == '1') {
		$messageResult .= 'ファイルサイズの上限を超えています。<br/>';
	} else {
		$messageResult .= 'メッセージが空です。<br/>';
	}
	
	if ($messageContactHelper->checkInvalidStatus(2, $toId)) {
		$messageResult .= '退会したユーザーのため、送信できません。<br/>';
	}
	
	if ($messageResult == '') {
		//$messageResult = "From : $fromId - $fromType <br/> To : $toId - $toType";
		if ($contactId == 0 && $toType != 3) {
			$messageContact->setUserId($fromId);
			$messageContact->setPerformerId($toId);
			$contact = $messageContact->getContact();
			if ($contact) {
				$contactId = $contact['id'];
			} else {
				$messageContact->setOwnerId($ownerCd);
				$messageContact->setIsFromChat(0);
				$contactId = $messageContact->create();
			}
		}
		if ($messageContact->isBlockedBy($contactId, $toType)) {
			$messageResult .= '相手のご都合によりメッセージを送ることができません。<br/>';
		} else {
			$message->setIsSent(1);
			$message->setFromType($fromType);
			$message->setFromId($fromId);
			$message->setToType($toType);
			$message->setToId($toId);
			
			$message->ManagePoints($bodyIndex, $imageIndex);
			if ($message->getToSendPoints() < 0) {
				$isNotify = 1;
				$result = false;
				$tmpl->assign('is_notify',  $isNotify);
			} else {
				$result = $message->create();
				$parm = "l_id=$toId"; 
				$message->check_ng_word($messageBody);
				$message->tenso_check($toId, $parm, $fromId, $fromType, $messageBody, $hasImage);
				$_POST = array();
			}
			
			if ($result) {
				header('Location:'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
				die;
			} else {
				$messageResult = 'メッセージ送信が失敗しました。'; //Message unsuccessful
			}
		}
	}
	if ($isNotify == '') {
		$tmpl->assign('msg_error',  $messageResult);
	}
}

/* ===================================================================================== */

$tmpl->assign('from_type', $fromType);
$tmpl->assign('to_type', $toType);
$tmpl->assign('user_name', $userName);
$tmpl->assign('to_id', $toId);
$tmpl->assign('from_id', $fromId);
$tmpl->assign('can_send', $canSend);
$tmpl->assign('contact_id', $contactId);
$tmpl->assign('image', $img);
$tmpl->assign('current_date_time', date('Y-m-d H:i:s'));

if ($toType != 3 && is_member_logged()) {
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
	$tmpl->assign('hash', $hash);
	$tmpl->assign('is_not_admin', '');
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
$imageProfile = $img;
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
	
	if ($date != $newDate || $date== '') {
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

function mb_trim($string){
	$str = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
	$str = str_replace(array("\n","\r\n","\r"), '', $str);
    return preg_replace('/^\p{Z}+|\p{Z}+$/u','',$str);
}





