<?php

require_once 'CommonDb.php';
require_once 'message/Message.php';
require_once 'message/EmojiClass.php';
if (isset($_POST['message_id'])) {
	$message = new Message();
	$id = $_POST['message_id'];
	$result = $message->isRead($id);
	echo ($result) ? 'true' : 'fail';
} else {
	echo 'fail';
}
?>