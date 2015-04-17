<?php
if (
	isset($_POST['from_id']) &&
	isset($_POST['to_id'])
) {
	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'message/MessageHelper.php';
	require_once 'message/MessageContactHelper.php';
	require_once 'message/EmojiClass.php';
	
	if (
		!class_exists('MessageHelper') 	||
		!class_exists('Message') 		||
		!class_exists('EmojiClass') 	||
		!class_exists('CommonDb') 		||
		!class_exists('MessageContactHelper')
	) {
		echo 'fail';
		exit();
	}
	
	$messageHelper 	= new MessageHelper();
	$message 		= new Message();
	$messageContactHelper = new MessageContactHelper();
	$emoji 			= new EmojiClass();
	
	$fromId 		= $_POST['from_id'];
	$toId 			= $_POST['to_id'];
	$toType 		= $_POST['to_type'];
	$fromType 		= $_POST['from_type'];
	
	$img 			= $_POST['img'];
	$hash 			= $_POST['hash'];
//	$imgPath 		= ($toType == 1)?'/imgs/member/320x240/':'/imgs/op/320x240/';
// 	$latestDate 	= date('Y-m-d', strtotime($_POST['lastest_date']));
	$fromDate 		= isset($_POST['from_date']) ? $_POST['from_date'] : '';
	$result 		= $messageHelper->checkMessage($fromId, $toId, $fromDate);
	$html = '';

// 	$female = $messageContactHelper->getUserId($hash, $toType);
	
// 	$img = $female['img'];
	$result = $message->getConversation($fromId, $toId, $fromType);
	$conversation = array_reverse($result);
	$lastId = $conversation[0]->id;
	$last_full_date = $conversation[0]->from_date;
	$last_date = date("m/d",strtotime($conversation[0]->from_date));
	$totalMessage = $message->countMessage($fromId, $toId, $fromType);
	$html = "<input type='hidden' name='lastfulldate' id='lastfulldate' value='$last_full_date' />";
	if ($totalMessage > 10) {
		$total = $totalMessage;
		$html .= '<a class="btn_more" href="javascript:void(0)">もっと見る</a>
				<input type="hidden" name="total" id="total" value="'.$total.'" />';
	}
	
	$html .= '<div class="append-data">
				<input type="hidden" name="last-id" id="last-id" value="'.$lastId.'" />
				<input type="hidden" value="'.$last_date.'" name = "last-date" id="last-date" />
			</div>';
	
	$days = array(
			'0'	=> '日',
			'1'	=> '月',
			'2'	=> '火',
			'3'	=> '水',
			'4'	=> '木',
			'5'	=> '金',
			'6'	=> '土'
	);
	
	$date = '';
	$latestDate = '';
	$htmlside = '';
	$check_read = '';
	if ($result != null) {
		$stamp = time();
		echo date('Y-m-d H:i:s',$stamp + 1);
// 		$imageProfile = ($toType == 3) ? $img : '/imgs/op/320x240/' . $img;
		foreach ($conversation as $res) {
			$htmlread = '';
			$position = 'message_detail_item--right';
			if ($fromId != $res->from_id) {
				$position = 'message_detail_item--left';
				$htmlside = '<div class="thumb">
					<img src="/images/message/spacer.gif" style="background-image: url('.$img.')">
				</div>';
			} else {
				$check_read = 'not-read';
				if ($res->is_read == 1) {
					$htmlread = '<p>既読</p><br>';
					$check_read = '';
				}
				$htmlside = '';
			}
		
			$messageContent = (!empty($res->image)) ? "<img class='resized' src='/imgs/message/".$res->image."' />" : '';
			$messageContent .= $emoji->getEmojiHtml($res->body);
				
			$latestDate = $res->from_date;
			$message->checkIsRead($res->id, $fromId);
			$newDate = date('m/d',strtotime($latestDate));
			$dayNo = date('w', strtotime($latestDate));
			
			if ($date != $newDate || $date == '') {
				$html .= '<div class="postdate_box">
					<span class="postdate" full="'.$latestDate.'" value="'.$newDate.' ('.$days[$dayNo].')'.'">'.$newDate.' ('.$days[$dayNo].')'.'</span>
				</div>';
			}

		 	$html .= '<div message-id="'.$res->id.'" class="cf message_detail_item '.$position.'">
		 			'.$htmlside.'
				<div class="bubble">
					<p class="desc">'.$messageContent.'</p>
				</div>
				<span class="posttime '.$check_read.'">
					'.$htmlread.'		
					'.date('H:i', strtotime($res->from_date)).'
				</span>
			</div>';

			$date = $newDate;
		}
		echo $html;
	} else {
		echo 'fail';
	} 
} else {
	echo 'fail';
}