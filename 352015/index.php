<?
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'tmpl2.class_ex.inc';;
require_once 'Owner.inc';

	$tmpl = new Tmpl22($tmpl_dir . "manage/main.html");
	$tmpl->dbgmode(0);
	$tmpl->flush();
?>
