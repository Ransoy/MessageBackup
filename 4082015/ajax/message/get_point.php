<?php
require_once 'CommonDb.php';
require_once 'message/Message.php';
require_once 'message/EmojiClass.php';

if (class_exists('Message') && isset($_POST['from_type']) && isset($_POST['from_id'])) {
	$message = new Message();
	$from_type = $_POST['from_type'];
	$from_id = $_POST['from_id'];
	if ($from_type == 1) { // from user
		$point = $message->getProfilePoint('male_point', $from_id, 'point_only');
	} else if ($from_type = 2) { //from performer
		$point = $message->getProfilePoint('female_point', $from_id, 'point_only');
	}
	echo $point['point'];
} else {
	echo 'fail';
}