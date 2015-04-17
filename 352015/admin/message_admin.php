<?
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';
require_once 'CommonDb.php';
require_once 'message/Message.php';

$tmpl = new Tmpl22($tmpl_dir . "manage/message/message_admin.html");

if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'adminlogin') {
	header('Location: http://mache-dev.vjsol.jp/139coco1ban_eva_plusone_qpgold/');
	exit();
}

$startYear = 2014;
$currentYear = intval(date('Y'));
$tmpl->loopset('year');
while ($startYear <= $currentYear) {
	$tmpl->assign('year_val', $startYear);
	$startYear++;
	$tmpl->loopnext();
}
$tmpl->loopset('');

$startMonth = 1;
$tmpl->loopset('month');
while ($startMonth <= 12) {
	$tmpl->assign('month_val', str_pad($startMonth, 2, "0", STR_PAD_LEFT));
	$startMonth++;
	$tmpl->loopnext();
}
$tmpl->loopset('');

$startDay = 1;
$tmpl->loopset('day');
while ($startDay <= 31) {
	$tmpl->assign('day_val', str_pad($startDay, 2, "0", STR_PAD_LEFT));
	$startDay++;
	$tmpl->loopnext();
}

$tmpl->flush();

