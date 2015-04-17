<?php
require_once 'Owner.inc';
require_once 'sp/common_proc.inc';
require_once 'sp/boy_login.inc';
if (isset($_GET['sp_login'])) {
	require_once 'sp/boy_sp.inc';
}
else {
	require_once 'sp/boy_sp_top.inc';
}
require_once 'sp/tmpl2.class_ex.inc';
require_once 'message/MessageHelper.php';

$tmpl = new Tmpl22($sp_tmpl_dir . '/message/message_inbox.html');
$tmpl->flush();
class Inbox {
	
	var $helper;
	
	function Inbox() {
		$this->helper = new MessageHelper();
	}
	
	/**
	 * @return multitype:object  All message 
	 */
	function displayAll() {
		$options = array('group_by' => 'contact_name');
		$hideDeleted = true;
		$id = $_SESSION['user_id'];
		$type = 1;
		return $this->helper->getAllFor(
			$id,
			$type,
			$hidedDeleted,
			 $options
		);	
	}
	/**
	 * 
	 * @return multitype:object All unread.
	 */
	function displayUnread() {
		$id = $_SESSION['user_id'];
		$type = 1;
		$options = array('is_read' => false);
		 return $this->helper->getAllUnreadFor(
		 	$id, 
		 	$type,
		 	 $options);
	}
	
	/**
	 * Search messages using contact_name.
	 * @param string $contactName
	 * @return multitype:object Message search result.
	 */
	function searchMessage($contactName = '') {
		if ( length(trim($contactName)) == 0) {
			print 'Contact name is empty.';
			return false;
		} 
		$id = $_SESSION['user_id'];
		$type = 1;
		$hideDeleted = true;
		$options = array('contact_name' => $contactName);
		return $this->helper->getAllFor(
			$id,
			$type,
			$hideDeleted,
			$options
		);
	}
	
	/**
	 * 
	 */
	function showUnreadCount() {
		$sql = ' SELECT message.from_id , female_profile.nick_name, Count(*) FROM message INNER JOIN female_profile ON message.from_id = female_profile.user_id AND message.is_read = 0 GROUP BY nick_name';
	}
}