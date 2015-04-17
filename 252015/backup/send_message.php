<?php
require_once 'CommonDb.php';
require_once 'message/Message.php';
require_once 'message/MessageContact.php';
require_once 'message/EmojiClass.php';
if (
	isset($_POST['from_type']) 	&& 
	isset($_POST['from_id']) 	&&
	isset($_POST['to_type']) 	&&
	isset($_POST['to_id']) 		&&
	isset($_POST['contact_id'])
) {
	$fromType 	= $_POST['from_type'];
	$fromId 	= $_POST['from_id'];
	$toType 	= $_POST['to_type'];
	$toId 		= $_POST['to_id'];
	$contactId	= $_POST['contact_id'];
	$latestDate = date('Y-m-d', strtotime($_POST['latest_date']));
	
	$message = new Message();
	$messageContact = new MessageContact();
	// Checks whether it was submitted by Image or Message Text
	if (!empty($_POST['message_body'])) { // Submitted by Message Text
		$messageBody = htmlentities(mb_convert_encoding(urldecode(trim($_POST['message_body'])), "EUC-JP", "UTF-8"), ENT_NOQUOTES, "EUC-JP");
		if ($messageBody != '') {
			$message->setBody($messageBody);
			if (strlen($messageBody) > 500) {
				$messageResult = '文字数の上限を超えています。'; //Message has reached the limit
			} else {
				$message->setBody($messageBody);
			}
		} else {
			$messageResult = 'メッセージが空である。'; //Message body is empty
		}
	} else { // Submitted by Image
		$imgSource = '';
		if (isset($_FILES['input_photo']['tmp_name'])) {
			$imgSource = 'input_photo';
		} else {
			$imgSource = (empty($_FILES['imageFile']['tmp_name'])) ? 'imageCam' : 'imageFile';
		}
		$imageValidate= $message->validateImage($imgSource);
		if ($imageValidate === true) {
			$message->setImage($imgSource);
		} else {
			$messageResult .= $imageValidate;
		}
	}

	if ($messageResult == '') {
		if ($toType != 3 && !$messageContact->isBlockedBy($contactId, $toType) ) {
			//$messageResult = "You are block by $userNickname!!";
			$message->setIsSent(1);
		}
		$message->setFromType($fromType);
		$message->setFromId($fromId);
		$message->setToType($toType);
		$message->setToId($toId);
		$result = $message->create();
		if ($result) {
			echo 1;
			if(date('Y-m-d') != $latestDate) {
				$newDate = date('m/d');
				$days = array(
					'Sun'	=> '日',
					'Mon'	=> '月',
					'Tue'	=> '火',
					'Wed'	=> '水',
					'Thu'	=> '木',
					'Fri'	=> '金',
					'Sat'	=> '土'
				);
				$day = $days[date('D')];
				
			?>
				<div class='postdate_box'>
					<span class='postdate' full='<?php echo  date('Y-m-d H:i:s'); ?>'><?php echo "$newDate $day";?></span>
				</div>
			<?php
			}
?>
		<div class="cf message_detail_item message_detail_item--right">
			<div class="bubble">
			<p class="desc"><?php echo $result; ?></p>
			</div>
			<span class="posttime">
			<?php echo date('H:i'); ?>
			</span>
		</div>
<?php
			exit();
		} else {
			$messageResult = 'メッセージ送信が失敗しました。'; //Message unsuccessful
		}
	}
	//$tmpl->assign('msg_error',  $messageResult);
	echo '0' . $messageResult;
} else {
	echo '0fail';
}