<?php
require_once 'Owner.inc';
require_once 'sp/common_proc.inc';
require_once 'sp/boy_login.inc';
require_once 'sp/boy_sp.inc';
require_once 'sp/tmpl2.class_ex.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';
require_once 'imacherie_male.inc';
require_once 'message/MessageContactHelper.php';

$tmpl = new Tmpl22($sp_tmpl_dir . '/message/maintenance.html');

 $tmpl->flush();
 exit();
 

 ?>
