<?php
if (
	isset($_POST['from_id']) &&
	isset($_POST['to_id'])
) {
	require_once 'CommonDb.php';
	require_once 'message/Message.php';
	require_once 'message/MessageHelper.php';
	require_once 'message/EmojiClass.php';
	
	$messageHelper 	= new MessageHelper();
	$message 		= new Message();
	$emoji 			= new EmojiClass();
	
	$fromId 		= $_POST['from_id'];
	$toId 			= $_POST['to_id'];
	$toType 		= $_POST['to_type'];
	$img 			= $_POST['img'];
	$imgPath 		= ($toType == 1)?'/imgs/member/320x240/':'/imgs/op/320x240/';
	$latestDate 	= date('Y-m-d', strtotime($_POST['lastest_date']));
	$fromDate 		= isset($_POST['from_date']) ? $_POST['from_date'] : '';
	$result 		= $messageHelper->checkMessage($fromId, $toId, $fromDate);
	
	
	if ($result != null) {
		echo date('Y-m-d H:i:s');
		foreach ($result as $res) {
			//$message .= "$res->body | $res->image | $res->from_date <br/> ";
			$messageContent = ($res->body == '') ? "<img class='resized' src='/imgs/message/".$res->image."' />" : nl2br($emoji->getEmojiHtml($res->body));
			$message->checkIsRead($res->id, $fromId);
			$newDate = date('Y-m-d', strtotime($res->from_date));
			if($newDate > $latestDate) {
				//$newDate = date('m/d', strtotime($res->from_date));
				$days = array(
					'Sun'	=> '日',
					'Mon'	=> '月',
					'Tue'	=> '火',
					'Wed'	=> '水',
					'Thu'	=> '木',
					'Fri'	=> '金',
					'Sat'	=> '土'
				);
				$day = $days[date('D', strtotime($newDate))];
			?>
				<div class='postdate_box'>
					<span class='postdate' full='<?php echo  date('Y-m-d H:i:s'); ?>'><?php echo  date('m/d', strtotime($newDate)) ." ". $day;?></span>
				</div>
			<?php
			}
	?>
			<div class="cf message_detail_item message_detail_item--left">
				<div class="thumb">
					<img src="/images/message/spacer.gif" style="background-image: url('<?php echo $imgPath . $img;?>')">
				</div>
				<div class="bubble">
					<p class="desc"><?php echo $messageContent ?></p>
				</div>
				<span class="posttime">
					<?php echo date('H:i', strtotime($res->from_date));?>
				</span>
			</div>
	<?php
			$latestDate = $newDate;
		}
	} else {
		echo 'fail';
	} 
} else {
	echo 'fail';
}