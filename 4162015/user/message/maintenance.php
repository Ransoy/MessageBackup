<?php
session_start();
require_once 'tmpl2.class_ex.inc';
require_once 'common_proc.inc';
require_once 'mc_session_routines.inc';
require_once 'mc_common.inc';
require_once 'mc_db.inc';
require_once 'message/MessageInbox.php';
require_once 'message/EmojiClass.php';
require_once 'message/MessageContactHelper.php';
require_once 'imacherie_male.inc';

$tmpl = new Tmpl2($tmpl_dir . '/message/maintenance.html');


// DB
$db = new mcDB ( getRandomSlave () );
$dbSlave = $db->get_resource ();

printOuterFrame ( &$tmpl, "マシェリとは？", $db, $ownerCd );

$tmpl->flush ( 0 );
exit ();