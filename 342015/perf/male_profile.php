<?
// -----------------------------------------------
//
// 管理：男性プロフィール詳細
//
// 作成日：2008/03/17
// 作成者：T.Fukuda
//
// -----------------------------------------------
require_once 'common_proc.inc';
require_once 'common_db_slave.inc';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'operator/operator.inc';
require_once 'Owner.inc';

//////////////////////////////////////////////////////////////////////////////////////

	$hash="";
	if(isset($_POST['hash'])){
		$hash = $_POST['hash'];
	}else{
		$hash = $_GET['hash'];
	}
	if($hash == ""){
		print "不正なアクセスです。";
		exit;
	}

	$fobj = new FormObject("新・男性プロフィール");
	$tmpl = new Tmpl22($tmpl_dir . "performer/male_profile.html");

	$sql  = "select";
	$sql .= " male_member.user_id,";				// [ 0]
	$sql .= " male_member.nick_name,";				// [ 1]
	$sql .= " male_member.hash,";					// [ 2]
	$sql .= " male_profile2.height,";				// [ 3]
	$sql .= " male_profile2.area,";					// [ 4]
	$sql .= " male_profile2.body_type,";			// [ 5]
	$sql .= " male_profile2.job,";					// [ 6]
	$sql .= " male_profile2.blood_type,";			// [ 7]
	$sql .= " male_profile2.age,";					// [ 8]
	$sql .= " male_profile2.birthday_mon,";			// [ 9]
	$sql .= " male_profile2.birthday_day,";			// [10]
	$sql .= " male_profile2.sleep_time,";			// [11]
	$sql .= " male_profile2.holiday_sun,";			// [12]
	$sql .= " male_profile2.holiday_mon,";			// [13]
	$sql .= " male_profile2.holiday_tue,";			// [14]
	$sql .= " male_profile2.holiday_wed,";			// [15]
	$sql .= " male_profile2.holiday_thu,";			// [16]
	$sql .= " male_profile2.holiday_fri,";			// [17]
	$sql .= " male_profile2.holiday_sat,";			// [18]
	$sql .= " male_profile2.comment,";				// [19]
	$sql .= " male_profile2.comment_check_flg,";	// [20]
	$sql .= " male_profile.img,";					// [21]
	$sql .= " male_profile2.category,";				// [22]
	$sql .= " male_profile2.f_body_type,";			// [23]
	$sql .= " male_profile2.like_genojin,";			// [24]
	$sql .= " male_profile2.funiki, ";				// [25]
	$sql .= " male_profile2.myblog, ";				// [26]
	$sql .= " male_profile2.prof_open_flg, ";		// [27]
	$sql .= " male_member.last_login ";				// [28]
	$sql .= " FROM male_member ";
	$sql .= " INNER JOIN male_profile ";
	$sql .= " ON male_member.user_id = male_profile.user_id ";
	$sql .= " LEFT JOIN male_profile2 ";
	$sql .= " ON male_member.user_id = male_profile2.user_id ";
	$sql .= " where male_member.owner_cd = ? and male_member.hash = ? ";
	$sth = $dbSlave33->prepare($sql);
	$data = array($ownerCd, $hash);
	$result = $dbSlave33->execute($sth, $data);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$data = array();
	if(!$row = $result->fetchRow()){
		print "SID エラー";
		exit;
	}

	if($row[27] == "0"){
		print "お客様のプロフィールは非公開です。";
		exit;
	}

	// 設定されていない場合、デフォルト値に変こする
	//身長9
	if ( $row[3] == 0 ){
		$row[3] = 999; //秘密
	}
	//地域
	if ( $row[4] == 0 ||  $row[4] == 99 ){
		$row[4] = 56; //秘密
	}
	//体型
	if ( $row[5] == 0 ){
		$row[5] = 99; //
	}

	//職業
	if ( $row[6] == 0 ){
		$row[6] = 18; //その他
	}
	//血液型
	if ( $row[7] == 0 ){
		$row[7] = 9; //秘密
	}
	//年齢
	if ( $row[8] == 0 ){
		$row[8] = 99; //秘密
	}
	//寝る時間
	if ( $row[11] == null || $row[11] == 0 ){
		$row[11] = 99; //秘密
	}

	$user_id = $row[0];
	$tmpl->assign('nick_name',$row[1]);
	$tmpl->assign('hash', $row[2]);
	$fobj->setValue('height',$row[3]);
	$fobj->setValue('area',$row[4]);
	$fobj->setValue('body_type',$row[5]);
	$fobj->setValue('job',$row[6]);
	$fobj->setValue('blood_type',$row[7]);
	$fobj->setValue('age',$row[8]);
	$fobj->setValue('sleep_time',$row[11]);
	$myblog = $row[26];
	$birthday = $row[9] . "月" . $row[10] . "日";
	$holiday = "";
	if($row[12] == 1){ $holiday .= "<font color='#ff0000'>日</font>"; }
	if($row[13] == 1){ $holiday .= "月"; }
	if($row[14] == 1){ $holiday .= "火"; }
	if($row[15] == 1){ $holiday .= "水"; }
	if($row[16] == 1){ $holiday .= "木"; }
	if($row[17] == 1){ $holiday .= "金"; }
	if($row[18] == 1){ $holiday .= "<font color='#0000ff'>土</font>"; }

	if($row[28]){
		$ll = strtotime($row[28]);
		$time = time();
		if($ll > strtotime("-6 hour")){
			$img = '<img src="./images/search/photo_icon_6h.gif">';
		}
		if($ll > strtotime("-3 hour")){
			$img = '<img src="./images/search/photo_icon_3h.gif">';
		}
		if($ll > strtotime("-1 hour")){
			$img = '<img src="./images/search/photo_icon_1h.gif">';
		}
		if($ll > strtotime("-30 minute")){
			$img = '<img src="./images/search/photo_icon_30m.gif">';
		}
		if(!empty($img))
			$tmpl->assign("llimg",$img);
	}

	$fobj->setValue("category",$row[22]);
	$fobj->setValue("f_body_type",$row[23]);
	$fobj->setValue("like_genojin",$row[24]);
	$fobj->setValue("funiki",$row[25]);
	$tmpl->setFormInstance($fobj);
	$tmpl->setFormObjectValues();

	$tmpl->assign('birthday', $birthday);
	$tmpl->assign('holiday', $holiday);

	if($row[20] == "1"){
		$comment = htmlspecialchars($row[19]);
	}else{
		$comment = "　";
	}
	$img = $row[21];
	if($img != ""){
		$image = "/imgs/member/120x90/" . $img;
	}else{
		$image = "images/search/now_printing.gif";
	}
	if($img == "1"){
		$image = "images/search/now_printing.gif";
	}
	$tmpl->setFormInstance($fobj);
	$tmpl->setFormObjectValues();

	//マイキーワード取得
	$sql  = "select my_keyword from my_keyword where owner_cd = ? and user_id = ?";
	$sth = $dbSlave33->prepare($sql);
	$con = array($ownerCd,$user_id);
	$result = $dbSlave33->execute($sth, $con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$cnt = 1;
	while ($row = $result -> fetchRow()){
		$data['my_keyword'.$cnt] =$row[0];
		$cnt++;
	}
	for($i=1; $i<=3; $i++){
		if(isset($data['my_keyword'.$i]) && $data['my_keyword'.$i] != ""){
			$fobj->setValue('my_keyword'.$i ,$data['my_keyword'.$i]);
			$tmpl->setFormInstance($fobj);
			$tmpl->setFormObjectValues();
		}else{
			$tmpl->assign('my_keyword'.$i, "");
		}
	}

	$tmpl->assign('comment', $comment);
	$tmpl->assign('image', $image);
	$tmpl->assign("msg","");
	// HTML出力
	if($myblog != "　"){
		$tmpl->assign("myblog",$myblog);
	}else{
		$tmpl->assign("myblog","");
	}
	$tmpl->flush();

	$sql = "select stat from female_member where owner_cd = ? and user_id = ?";
	$sth = $dbSlave->prepare($sql);
	$con = array($ownerCd,$_SESSION['user_id']);
	$result = $dbSlave->execute($sth, $con);
	$row = $result->fetchRow();
	if($row[0] != 1){
		exit;
	}

	//--
	// 男性への足あと
	$sql  = "select";
	$sql .= " male_asiato.user_id,";		//0
	$sql .= " male_asiato.female_id1,";		//1
	$sql .= " male_asiato.upd_date1,";		//2
	$sql .= " male_asiato.female_id2,";		//3
	$sql .= " male_asiato.upd_date2,";		//4
	$sql .= " male_asiato.female_id3,";		//5
	$sql .= " male_asiato.upd_date3,";		//6
	$sql .= " male_asiato.female_id4,";		//7
	$sql .= " male_asiato.upd_date4,";		//8
	$sql .= " male_asiato.female_id5,";		//9
	$sql .= " male_asiato.upd_date5,";		//10
	$sql .= " male_asiato.female_id6,";		//11
	$sql .= " male_asiato.upd_date6,";		//12
	$sql .= " male_asiato.female_id7,";		//13
	$sql .= " male_asiato.upd_date7,";		//14
	$sql .= " male_asiato.female_id8,";		//15
	$sql .= " male_asiato.upd_date8,";		//16
	$sql .= " male_asiato.female_id9,";		//17
	$sql .= " male_asiato.upd_date9,";		//18
	$sql .= " male_asiato.female_id10,";	//19
	$sql .= " male_asiato.upd_date10 ";		//20
	$sql .= "from male_asiato ";
	$sql .= "where male_asiato.owner_cd = ? and male_asiato.user_id = ? ";
	$sth = $dbMaster->prepare($sql);
	$data = array($ownerCd, $user_id);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	if(!$row = $result->fetchRow()){
		// 新規足あと
		$data = array();
		$data['owner_cd'] = $ownerCd;
		$data['user_id'] = $user_id;
		$data['female_id1'] = $_SESSION['user_id'];
		$data['#upd_date1#'] = "now()";
		iTSinsert($data,"male_asiato",$_SESSION['user_id'], true);
	}else{
		// 足あと更新
		for($i=1; $i<=19; $i+=2){
			if($row[$i] == $_SESSION['user_id']){
				break;
			}
		}
		while($i>=3){
			$row[$i] = $row[$i-2];
			$row[$i+1] = $row[$i-1];
			$i -= 2;
		}
		$sql  = "update male_asiato set";
		$sql .= " female_id1 = ?,";
		$sql .= " upd_date1 = now(),";
		$sql .= " female_id2 = ?,";
		$sql .= " upd_date2 = ?,";
		$sql .= " female_id3 = ?,";
		$sql .= " upd_date3 = ?,";
		$sql .= " female_id4 = ?,";
		$sql .= " upd_date4 = ?,";
		$sql .= " female_id5 = ?,";
		$sql .= " upd_date5 = ?,";
		$sql .= " female_id6 = ?,";
		$sql .= " upd_date6 = ?,";
		$sql .= " female_id7 = ?,";
		$sql .= " upd_date7 = ?,";
		$sql .= " female_id8 = ?,";
		$sql .= " upd_date8 = ?,";
		$sql .= " female_id9 = ?,";
		$sql .= " upd_date9 = ?,";
		$sql .= " female_id10 = ?,";
		$sql .= " upd_date10 = ?,";
		$sql .= " upd_ip = ?,";
		$sql .= " upd_id = ?,";
		$sql .= " upd_date = now() ";
		$sql .= "where owner_cd = ".$ownerCd." ";
		$sql .= "and user_id = ? ";
		$data = array($_SESSION['user_id'],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],$row[18],$row[19],$row[20],$_SERVER['REMOTE_ADDR'],$_SESSION['user_id'],$row[0]);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $data);
		if(DB::isError($result)){
			print $sql;
			err_proc($result -> getMessage());
		}
	}


?>
