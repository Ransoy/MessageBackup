<?php

	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'sp/boy_login.inc';
	if (isset($_GET['sp_login'])) {
		require_once 'sp/boy_sp.inc';
	} else {
		require_once 'sp/boy_sp_top.inc';
	}
	$userId = isset($_SESSION['user_id'])?$_SESSION['user_id']: 'wala';
	//require_once 'mc_session_routines.inc';
	require_once 'sp/tmpl2.class_ex.inc';
	$message = new Message();
	//$messageHelper = new MessageHelper();
	
	// Submit Message
	if (isset($_POST['submit_message']) || isset($_POST['submit_image'])) {
		$messageResult = '';
		
		// Check if user can send a message
		/*if (!$messageHelper->canSend($id, $type, $toId)) {
			$messageResult .= 'You dont have enough Points to send this message!!<br/>';
		}*/
		
		// Checks whether it was submitted by Image or Message Text
		if (isset($_POST['submit_message'])){ // Submitted by Message Text
			$messageBody = trim($_POST['message_body']);
			if ($messageBody != '') {
				$message->setBody($messageBody);	
			} else {
				$messageResult .= 'Message body is empty!!<br/>';
			}
		} else { // Submitted by Image
			$imgSource = 'image';
			$imageValidate= validateImage($imgSource);
			if ($imageValidate == true) {
				$message->setImage($imgSource);
			} else {
				$messageResult .= $imageValidate;
			}
		}
		
		if ($fromId == $toId) {
			$messageResult .= 'Invalid ID <br/>';
		}
		
		if ($messageResult == '') {
			$message->setFromType($fromType);
			$message->setFromId($fromId);
			$message->setToType($toType);
			$message->setToId($toId);
			$result = $message->create();
			if ($result) {
				$messageResult = 'Message successfuly created...';
			} else {
				$messageResult = 'Message Create unsuccessful...';
			}
		} 
		
		echo $messageResult;
	}
	
	//Check points
	/*if (!$messageHelper->canSend($id, $type, $toId)) {
		$tmpl->assign('notifyPoints',  true);
	}*/
	
	$tmpl = new Tmpl22($sp_tmpl_dir . 'message/message_detail.html');
	$tmpl->flush();
	echo '<br/><br/>' . $userId;
	exit();
	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 										FUNCTIONS											 *
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */  
	
	/**
	 * Validates a file, returns true if file is valid otherwise returns error message.
	 * @param String $src
	 * @return Ambigous <boolean, string>
	 */
	function validateImage($src) {
		$imageResult = '';
		if ($_FILES[$src]['tmp_name'] == '') {
			if (1 == $_FILES[$src]['error']) {
				$imageResult = 'Image Size is above server max upload size';
			}
			$imageResult .= '<br/>Image is empty!';
		}
		if (!is_uploaded_file($_FILES[$src]['tmp_name'])) {
			$imageResult .= '<br/>Image not uploaded';
		}
		if ($_FILES[$src]['size'] == 0) {
			$imageResult .= '<br/>Image size is 0';
		}
		if ($_FILES[$src]['size'] > 8388608) {
			$imageResult .= '<br/>Image size is greater than 8mb';
		}
		$size = GetImageSize($_FILES[$src]['tmp_name']);
		if ($size[2] != 1 && $size[2] != 2 && $size[2] != 3) {
			$imageResult .= '<br/>File Not an image';
		}
		return ($imageResult == '') ? true : $imageResult;
	}
	
	