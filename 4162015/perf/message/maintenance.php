<?php
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';
require_once 'message/MessageContactHelper.php';

$tmpl = new Tmpl23(OP_PATH . 'template/message/maintenance.html');
  
$tmpl->flush();
exit();
?>
  
  