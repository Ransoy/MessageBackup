﻿<?php 
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
	
	printOuterFrame (&$tmpl,"¥Þ¥·¥§¥ê¤È¤Ï¡©",$db,$ownerCd);
	
	$fromId = $_SESSION['user_id'];
	$fromType = 1;
	$hash = $_GET['id'];
	
	$female = $messageContactHelper->getUserId($hash, $toType);
	//print_r($female);
	//$result = $messageContactHelper->getContactDetail($fromId, $hash, $toType, $fromType);
	$result = '';
	$contactId = 0;
	$img = '';
	$toId = '';
	$userName = '';
	
	if (is_member_logged()) {
		if (empty($female)) {
			header('Location: inbox.php');
			exit();
		}
		$img = $female['img'];
		$toId = $female['to_id'];
		$userName = $female['nick_name'];
		$result = $messageContactHelper->getContactDetail2($fromId, $toId, $toType, $fromType);
		if ($result) {
			$contactId = $result['id'];
		}
	} else {
		header('Location: ../imacherie.php');
	}
	
	$canSend = 1; //Default is can send message
	if (!$messageHelper->canSend($fromId, $fromType, $toId) && $toType != 3) {
		$tmpl->assign('is_notify',  1);
		// Cannot send message!
		$canSend = 0; //User or performercant send message, textbox and buttons are disabled;
		$messageResult .= 'Áê¼ê¤ÎÅÔ¹ç¤Ë¤è¤ê¥á¥Ã¥»¡¼¥¸¤¬Á÷¿®¤Ç¤­¤Þ¤»¤ó¡£<br/>'; //Message can not be sent
	}
	
	// Submit Message
	if (isset($_POST['submit_message']) || isset($_POST['formImage'])) {
		// Checks whether it was submitted by Image or Message Text
		if (isset($_POST['submit_message'])) { // Submitted by Message Text
			$messageBody = trim($_POST['message_body']);
			if ($messageBody != '') {
				if (strlen($messageBody) > 500) {
					$messageResult .= 'Ê¸»ú¿ô¤Î¾å¸Â¤òÄ¶¤¨¤Æ¤¤¤Þ¤¹¡£<br/>'; //Message has reached the limit
				} else {
					$message->setBody($messageBody);
				}
			} else {
				$messageResult .= '¥á¥Ã¥»¡¼¥¸¤¬¶õ¤Ç¤¢¤ë¡£<br/>'; //Message body is empty
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
				$messageResult = '¥á¥Ã¥»¡¼¥¸Á÷¿®¤¬¼ºÇÔ¤·¤Þ¤·¤¿¡£'; //Message unsuccessful
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
		'0' => 'Æü',
		'1' => '·î',
		'2' => '²Ð',
		'3' => '¿å',
		'4' => 'ÌÚ',
		'5' => '¶â',
		'6' => 'ÅÚ'
	);
	$imageProfile = ($toType == 3) ? $img : '/imgs/op/320x240/' . $img;
	foreach ($conversation as $con) {
		$position = 'message_detail_item--right';
		if ($fromId != $con->from_id) {
			$tmpl->assign('image_profile', $imageProfile);
			$position = 'message_detail_item--left';
		} else {
			if ($con->is_read == 1) {
				$tmpl->assign('read', '´ûÆÉ');
			} else {
				$tmpl->assign('not_read', 'not-read');
			}
		}
		
		$body = (!empty($con->image)) ? "<img class='resized' src='/imgs/message/$con->image' >" : nl2br($emoji->getEmojiHtml($con->body));
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





