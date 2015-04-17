<?
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';			//本フォームは個別の入力チェックが必要
$strWhere = " and owner_cd = " . $_SESSION['ownerCd'] . " ";


$tmpl = new Tmpl22($tmpl_dir . "manage/message/message_point_log.html");

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



if(isset($_POST['male_id'])){

}

if(isset($_POST['female_id'])){

}

if(isset($_POST['start_date'])){

}

if(isset($_POST['end_date'])){

}

$tmpl->flush();

?>