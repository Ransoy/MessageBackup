<?
require_once 'common_proc.inc';
require_once 'common_db_slave.inc';
require_once 'tmpl2.class.inc';
require_once 'Owner.inc';

	$tmpl = new Tmpl2($tmpl_dir . "chat/gallery_main.html");

// 2013-05-05
// パフォの"状態"も取得するように変更して、
// その値によってパフォ写真が画面に表示されるか否か判断する vj_okamoto

//	$sth = $dbSlave33->prepare("select img,nick_name from female_profile where owner_cd = ? and hash = ?");

	$sql = "select fp.img,fp.nick_name,fm.stat from female_profile fp, female_member fm where fp.owner_cd = ? and fp.hash = ? and fp.user_id=fm.user_id and fm.stat<>'9'";
	$sth = $dbSlave33->prepare( $sql );

	$data = array($ownerCd,$_GET['id']);
	$result = $dbSlave33->execute($sth,$data);
	if( !$row = $result->fetchRow() ){
		print "不正なアクセスです。";
		exit;
	}
	$tmpl->assign("photo_main",$row[0]);
	$tmpl->assign("nick_name",str_replace(" ", "&nbsp;", $row[1]) );
	$tmpl->assign("hash",$_GET['id']);
	$tmpl->flush();
?>
