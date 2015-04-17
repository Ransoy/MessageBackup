<?php
require_once 'CommonDb.php';
require_once 'message/MessageHelper.php';
require_once 'message/Message2.php';
require_once 'message/MessageContact.php';
require_once 'message/EmojiClass.php';

$messageResult = '';
global $ownerCd;
if (
	isset($_POST['from_type']) 	&& 
	isset($_POST['from_id']) 	&&
	isset($_POST['to_type']) 	&&
	isset($_POST['to_id']) 		&&
	isset($_POST['contact_id'])
) {
	if (
		!class_exists('MessageHelper') 	||
		!class_exists('MessageContact') ||
		!class_exists('Message') 		||
		!class_exists('EmojiClass') 	||
		!class_exists('CommonDb')
	) {
		echo '0'.'システムメンテナンス。';
		exit();
	}
	$message = new Message();
	$messageContact = new MessageContact();
	$messageHelper = new MessageHelper();
	
	$fromType 	= $_POST['from_type'];
	$fromId 	= $_POST['from_id'];
	$toType 	= $_POST['to_type'];
	$toId 		= $_POST['to_id'];
	$contactId	= $_POST['contact_id'];
	$latestDate = date('Y-m-d', strtotime($_POST['latest_date']));
	
	// Checks whether it was submitted by Image and Message Text
	$hasBody = (isset($_POST['message_body']) && $_POST['message_body'] != '');
	$hasImage = (!empty($_FILES['input_photo']['tmp_name']) || !empty($_FILES['imageFile']['tmp_name']) || !empty($_FILES['imageCam']['tmp_name']));
	
	// For checking points
	$bodyIndex = '';
	$imageIndex = '';
	
	$checkCanSend = true; 
	
	if ($hasBody || $hasImage) {
		if ($hasBody) { // Submitted by Message Text
			$msg = unicode_decode(urldecode($_POST['message_body']));
			$messageBody = mb_convert_encoding(htmlentities($msg, ENT_QUOTES, 'UTF-8'), 'HTML-ENTITIES', 'UTF-8');
			if (trim($messageBody) != '') {
				$message->setBody($messageBody);
				if (mb_strlen($msg, 'UTF-8') > 500) {
					$messageResult .= '文字数の上限を超えています。 <br/>'; //Message has reached the limit
				} else {
					$message->setBody($messageBody);
					$bodyIndex = 'message';
				}
			} else {
				$messageResult .= 'メッセージが空である。  <br/>'; //Message body is empty
			}
		} 
		if ($hasImage) { // Submitted by Image
			$imgSource = '';
			if (isset($_FILES['input_photo']['tmp_name'])) {
				$imgSource = 'input_photo';
			} else {
				$imgSource = ($_FILES['imageFile']['tmp_name'] == '') ? 'imageCam' : 'imageFile';
			}
			$imageValidate = $message->validateImage($imgSource);
			if ($imageValidate === true) {
				$message->setImage($imgSource);
				$imageIndex = 'image';
			} else {
				$messageResult .= $imageValidate;
			}
		}
		if ($fromType == 1) {
			$message->setFromType($fromType);
			$message->setFromId($fromId);
			$message->setToType($toType);
			$message->setToId($toId);
			$message->ManagePoints($bodyIndex, $imageIndex);
			if ($message->getToSendPoints() < 0) {
				$messageResult .= ' <p>ポイントが足りません。ポイントを購入しますか？<a class="link_purchase" href="/settlement/bank.php">ポイントを購入＞</a></p>';
				$checkCanSend = false;
			}
		}
	}
	if ($checkCanSend && (!$messageHelper->canSend($fromId, $fromType, $toId) || $toType == 3)) {
		//$messageResult .= 'ポイントが足りない為、送信できませんでした。<br/>'; //Message can not be sent
		$messageResult .= '相手側の都合により、メール送信できません。<br/>';
	}
	if ($messageResult == '') {
		if ($contactId == 0 && $toType != 3) {
			$userId = ($toType == 1) ? $toId : $fromId;
			$performerId = ($toType == 2) ? $toId : $fromId;	
			$messageContact->setUserId($userId);
			$messageContact->setPerformerId($performerId);
			$contact = $messageContact->getContact();
			if ($contact) {
				$contactId = $contact['id'];
			} else {
				$messageContact->setOwnerId($ownerCd);
				$messageContact->setIsFromChat(0);
				$contactId = $messageContact->create();
			}
		}
		if ($toType != 3 && !$messageContact->isBlockedBy($contactId, $toType) ) {
			//$messageResult = "You are block by $userNickname!!";
			$message->setIsSent(1);
		}
		$message->setFromType($fromType);
		$message->setFromId($fromId);
		$message->setToType($toType);
		$message->setToId($toId);
		$result = $message->create();
		if ($result != null) {
			echo 1;
			$messageId = $message->getLastMessage($fromId, $toId);
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
				<div class='postdate_box post_date'>
					<span class='postdate' full='<?php echo  date('Y-m-d H:i:s'); ?>'><?php echo "$newDate ($day)";?> </span>
				</div>
		<?php
			}
		?>
		<div message-id="<?php echo $messageId; ?>" class="cf message_detail_item message_detail_item--right">
			<div class="bubble">
			<p class="desc"><?php echo $result; ?></p>
			</div>
			<span class="posttime not-read">
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
	echo '0不足しているパラメータ。';
}

function replace_unicode_escape_sequence($match) {
	return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

function unicode_decode($str) {
	return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}