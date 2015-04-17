<?php
session_start();
require_once 'CommonDb.php';
require_once 'message/MessageHelper.php';
/**
 * check if id and type is set. redirect to inbox if null
 */
if (!isset($_POST['id']) || !isset($_POST['type'])) {
	echo 'fail';
	exit();
}
// initialize my helper class
$helper = new MessageHelper();
/**
 * Get values
 */
$receiverId = $_POST['id'];
$userId = $_SESSION['user_id'];
$type = $_POST['type'];

if ($helper->deleteInbox(array($receiverId), $type, $userId)) {
	echo 'success';
}