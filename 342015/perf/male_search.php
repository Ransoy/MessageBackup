<?php
// -----------------------------------------------
//
// 管理：男性プロフィール検索
//
// 作成日：2008/03/14
// 作成者：T.Fukuda
//
// -----------------------------------------------
//
// @satodate 新しい管理画面変このため修正 2010.12.7

require_once 'common_proc.inc';
//require_once 'common_db_slave2.inc';
require_once 'common_db_slave_batch.inc';
require_once 'FormObject.inc';
require_once 'Owner.inc';

// 新しい画面用のファイル読み込み　@satodate
require_once 'operator/tmpl2.class_operator.inc';
require_once 'operator/operator.inc';
require_once 'operator/camera_test.inc';

////////////////////////////////////////////////////////////////////////////////
	$fobj = new FormObject("女性：男性サーチ",10);
	// $tmpl = new Tmpl23($tmpl_dir . "operator/male_search.html");
	// テンプレートpathの変更 @satodate
	$tmpl = new Tmpl23(OP_PATH.'template/male_search.html');

/*
	//直接呼ばれた場合
	if( ! isset($_POST['mode'])){
		$tmpl->dbgmode(0);
		$tmpl->setFormInstance($fobj);
		$tmpl->setFormObjectItems();
		$tmpl->assign("hidden_data","");

		$tmpl->flush();
		exit;
	}
*/

	//データ反映処理
	$fobj->importFormData();
	$tmpl->dbgmode(0);
	$tmpl->setFormInstance($fobj);
	$tmpl->setFormObjectItems();
	$tmpl->assign("result","1");
	$tmpl->assign("hidden_data","");
	$tmpl->assign("mode_set","search");

	// -------------------
	// 検索条件の作成
	// -------------------
	$con = "";
	//年齢
	if(isset($_POST['age_con']) && $_POST['age_con'] != "0"){
		if($_POST['age_con'] == "1"){
			$con .= " and p2.age >= 18 and p2.age <= 25";
		}elseif($_POST['age_con'] == "2"){
			$con .= " and p2.age >= 26 and p2.age <= 35";
		}elseif($_POST['age_con'] == "3"){
			$con .= " and p2.age >= 36 and p2.age <= 45";
		}elseif($_POST['age_con'] == "4"){
			$con .= " and p2.age >= 46 and p2.age <= 55";
		}elseif($_POST['age_con'] == "5"){
			$con .= " and p2.age >= 56 and p2.age < 99";
		}else{
			$con .= " and p2.age = " . $dbSlave33->quote($_POST['age_con']);
		}
	}

	// 身長
	if(isset($_POST['height_con']) && $_POST['height_con'] != "0"){
		$con .= " and p2.height = " . $dbSlave33->quote($_POST['height_con']);
	}

	// 地域
	if(isset($_POST['area_con']) && $_POST['area_con'] != "99"){

		if($_POST['area_con'] >= "9" && $_POST['area_con'] <= "15"){
			//北海道・東北
			$area_flg = 1;
		}else if($_POST['area_con'] >= "16" && $_POST['area_con'] <= "23"){
			//関東
			$area_flg = 2;
		}else if($_POST['area_con'] >= "24" && $_POST['area_con'] <= "28"){
			//甲信越・北陸
			$area_flg = 3;
		}else if($_POST['area_con'] >= "29" && $_POST['area_con'] <= "32"){
			//東海
			$area_flg = 4;
		}else if($_POST['area_con'] >= "33" && $_POST['area_con'] <= "38"){
			//関西
			$area_flg = 5;
		}else if($_POST['area_con'] >= "39" && $_POST['area_con'] <= "47"){
			//中国・四国
			$area_flg = 6;
		}else if($_POST['area_con'] >= "48" && $_POST['area_con'] <= "55"){
			//九州・沖縄
			$area_flg = 7;
		}else if($_POST['area_con'] == "8"){
			//海外
			$area_flg = 8;
		}else if($_POST['area_con'] == "56"){
			//その他
			$area_flg = 9;
		}else{
			$area_flg = $_POST['area_con'];
		}
		if($_POST['area_con'] == "1"){ 		// 1:北海道・東北
			$con .= " and p2.area_flg = 1";
		}elseif($_POST['area_con'] == "2"){	// 2:関東
			$con .= " and p2.area_flg = 2";
		}elseif($_POST['area_con'] == "3"){	// 3:甲信越・北陸
			$con .= " and p2.area_flg = 3";
		}elseif($_POST['area_con'] == "4"){	// 4:東海
			$con .= " and p2.area_flg = 4";
		}elseif($_POST['area_con'] == "5"){	// 5:関西
			$con .= " and p2.area_flg = 5";
		}elseif($_POST['area_con'] == "6"){	// 6:中国・四国
			$con .= " and p2.area_flg = 6";
		}elseif($_POST['area_con'] == "7"){	// 7:九州・沖縄
			$con .= " and p2.area_flg = 7";
		}elseif($_POST['area_con'] == "8"){	// 8:海外
			$con .= " and p2.area_flg = 8 and p2.area = 8";
		}else {								// 上記以外
			$con .= " and p2.area_flg = " . $dbSlave33->quote($area_flg) ."";
			$con .= " and p2.area = " . $dbSlave33->quote($_POST['area_con']) ."";
		}
	}

	// 体型
	if(isset($_POST['body_type_con']) && $_POST['body_type_con'] != "0"){
		$con .= " and p2.body_type = " . $dbSlave33->quote($_POST['body_type_con']);
	}

	// 寝る時間
	if(isset($_POST['sleep_time_con']) && $_POST['sleep_time_con'] != "0"){
		if($_POST['sleep_time_con'] == "25"){
			$con .= " and (p2.sleep_time >= 1 and p2.sleep_time <= 5)";

		}else if($_POST['sleep_time_con'] == "26"){
			$con .= " and (p2.sleep_time >= 6 and p2.sleep_time <= 10)";

		}else if($_POST['sleep_time_con'] == "27"){
			$con .= " and (p2.sleep_time >= 11 and p2.sleep_time <= 18)";

		}else if($_POST['sleep_time_con'] == "28"){
			$con .= " and (p2.sleep_time >= 19 and p2.sleep_time <= 24)";

		}else{
			$con .= " and p2.sleep_time = " .$dbSlave33->quote($_POST['sleep_time_con'])."";

		}
	}

	// 血液型
	if(isset($_POST['blood_type_con']) && $_POST['blood_type_con'] != "0"){
		$con .= " and p2.blood_type = " . $dbSlave33->quote($_POST['blood_type_con']);
	}

	// チャットネーム
	if(isset($_POST['chat_name_con']) && $_POST['chat_name_con'] != ""){
		$con .= " and m.nick_name like " . $dbSlave33->quote("%{$_POST['chat_name_con']}%");
	}

	// 誕生月
	if(isset($_POST['birthday_mon_con']) && $_POST['birthday_mon_con'] != "0"){
		$con .= " and p2.birthday_mon = " . $dbSlave33->quote($_POST['birthday_mon_con']);
	}

	// 誕生日 〜 すべての日の場合は、選択しない。
	if(isset($_POST['birthday_day_con']) && $_POST['birthday_day_con'] != "0" && $_POST['birthday_day_con'] != "99"){
		$con .= " and p2.birthday_day = " . $dbSlave33->quote($_POST['birthday_day_con']);
	}
	// マイキーワード検索
	$from = " male_profile2 p2 ";
	if(isset($_POST['my_keyword_con']) && $_POST['my_keyword_con'] != "0"){
		$con .= " and mk.my_keyword = " . $dbSlave33->quote($_POST['my_keyword_con']);
		$from = " my_keyword mk inner join male_profile2 p2 on mk.owner_cd = p2.owner_cd and mk.user_id = p2.user_id ";
	}

	// 会員種別
	if(isset($_POST['auth_type_con']) && $_POST['auth_type_con'] != "0"){

		if($_POST['auth_type_con'] == "1"){
			// ゴールド会員
			$con .= " and m.gold_flg = 1";
		}
		elseif($_POST['auth_type_con'] == "2"){
			// 有料会員
			$con .= " and m.assortment = 1";
		}
		elseif($_POST['auth_type_con'] == "3"){
			// 無料会員
			$con .= " and m.assortment = 0";
		}
	}

	// 最終ログイン日
	if( isset($_POST['last_login_con']) && $_POST['last_login_con'] != "0" ){

		if($_POST['last_login_con'] == "1"){
			// 1時間
			$con .= " and m.last_login >= now() - interval 1 hour";
		}
		elseif($_POST['last_login_con'] == "2"){
			// 3時間
			$con .= " and m.last_login >= now() - interval 3 hour";
		}
		elseif($_POST['last_login_con'] == "3"){
			// 6時間
			$con .= " and m.last_login >= now() - interval 6 hour";
		}
		elseif($_POST['last_login_con'] == "4"){
			// 12時間
			$con .= " and m.last_login >= now() - interval 12 hour";
		}
		elseif($_POST['last_login_con'] == "5"){
			// 昨日
			$con .= " and m.last_login >= now() - interval 1 day";
		}
		elseif($_POST['last_login_con'] == "6"){
			// ３日以内
			$con .= " and m.last_login >= now() - interval 3 day";
		}
		elseif($_POST['last_login_con'] == "7"){
			// １週間以内
			$con .= " and m.last_login >= now() - interval 7 day";
		}
		elseif($_POST['last_login_con'] == "8"){
			// ２週間以内
			$con .= " and m.last_login >= now() - interval 14 day";
		}
		elseif($_POST['last_login_con'] == "9"){
			// １ヶ月以内
			$con .= " and m.last_login >= now() - interval 1 month";
		}
	}
	$tmpl->assign("g1","_on");
	$tmpl->assign("g2","");
	$tmpl->assign("g3","");
	if(isset($_POST['tab'])){
		if(!empty($_POST['tab'])){
			if($_POST['tab'] == "1"){
				$tmpl->assign("g1","_on");
				$tmpl->assign("tab","1");
				$con .= "";
			}
			if($_POST['tab'] == "2"){
				$tmpl->assign("g1","");
				$tmpl->assign("g2","_on");
				$con .= " AND m.assortment = '0' ";
				$tmpl->assign("tab","2");
			}
			if($_POST['tab'] == "3"){
				$tmpl->assign("g1","");
				$tmpl->assign("g3","_on");
				$con .= " AND (m.cm_code LIKE 'act%' OR m.cm_code LIKE 'jorf%' OR m.cm_code LIKE '1ami9%' OR m.cm_code LIKE 'pp%' OR m.cm_code LIKE '%ppp%' OR m.cm_code LIKE '%fitp%' OR m.cm_code LIKE '%A8AV%' OR m.cm_code LIKE '%hotaru%')";
				$tmpl->assign("tab","3");
			}
		}
	}

	// 初期表示画面用
	if(!isset($_POST['last_login_con'])){
		$con .= " and m.last_login >= now() - interval 1 hour";
	}
	// 被お気に入り
	//$tmpl->assign("favorites_con_0",'');
	$tmpl->assign("favorites_con_1",'');
	$from2 = "";
	if(isset($_POST['favorites_con']) && $_POST['favorites_con'] != ""){
		if($_POST['favorites_con'] == "1"){
			$from2 = "INNER JOIN male_address_book ON p2.owner_cd = male_address_book.owner_cd AND p2.user_id = male_address_book.user_id AND male_address_book.female_user_id = '{$_SESSION['user_id']}' ";
			$con .= " AND male_address_book.address_type = 1 ";
		}elseif($_POST['favorites_con'] == "0"){
			//$from2 = "LEFT JOIN male_address_book ON p2.owner_cd = male_address_book.owner_cd AND p2.user_id = male_address_book.user_id AND male_address_book.female_user_id = '{$_SESSION['user_id']}' ";
			//$con .= " AND (male_address_book.address_type = 0 OR male_address_book.address_type is NULL) ";
		}
		$tmpl->assign("favorites_con_{$_POST['favorites_con']}",' selected="selected"');
	}
	// 現在時刻
	$now = time();

	//１ページあたりの表示件数
	$page_num = 50;					// 1ページの表示件数
	$page_max_num = 10;				// 一度に表示したいページリンク数
	if(!isset($_POST['pos'])){
		$pos = 0;
	}else{
		if($_POST['pos'] == ""){
			$pos = 0;
		}else{
			$pos = $_POST['pos'];
		}
	}

	$sql  = "select ";
	$sql .= " p2.user_id,";                      // 0
	$sql .= " p1.img,";                           // 1
	$sql .= " m.nick_name,";                      // 2
	$sql .= " m.hash,";                           // 3
	$sql .= " p1.tenso_mail2,";                   // 4
	$sql .= " p1.kyohi_time4,";                   // 5
	$sql .= " m.last_login,";                     // 6
	$sql .= " p2.welcome_flg,";                  // 7
	$sql .= " m.black_flg, ";                      // 8
	$sql .= " m.CRE_DATE ";                      // 9
	$sql .= "from";
	$sql .= " {$from}";
	$sql .= " inner join male_member m on p2.owner_cd = m.owner_cd and p2.user_id = m.user_id";
	$sql .= " inner join male_profile p1 on p2.owner_cd = p1.owner_cd and p2.user_id = p1.user_id ";
	$sql .= " {$from2} ";
	$sql .= "where";
	$sql .= " p2.owner_cd = '$ownerCd' and p2.prof_open_flg = '1' and m.stat = '1'";
	$sql .= $con." ";
	$sql .= "order by m.last_login DESC ";
	$sql .= "LIMIT $page_num OFFSET $pos";
	$result = $dbSlave->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
	}
	//検索結果表示
	$tmpl->loopset("boys_list");
	while ($row = $result->fetchRow()){

		if($row[1] != ""){
			$img = "/imgs/member/120x90/" . $row[1];
		}else{
			$img = "images/search/now_printing.gif";
		}
		if($row[1] == "1"){
			$img = "images/search/now_printing.gif";
		}
		$nick_name = $row[2];
		$hash = $row[3];

		//今の時間から最終ログインを引いて差の差を取得
		if($row[6] == ""){
			$row[6] = "2005-01-01";
		}
		$login_time = $now - strtotime($row[6]);
		$frame="normal";
		/*
			今までと同じ色のフレーム:normal
			ログイン30分以内:r30m  1800
			ログイン1時間以内:r1h  3600
			ログイン3時間以内:r3h  10800
			ログイン6時間以内:r6h  21600
		*/
		if($login_time <= 1800){
			$frame = "r30m";
		}else if($login_time <= 3600){
			$frame = "r1h";
		}else if($login_time <= 10800){
			$frame = "r3h";
		}else if($login_time <= 21600){
			$frame = "r6h";
		}

		// 転送メール２が設定されていれば、ケータイアイコンを表示する。
		$tenso_icon = "";
		$tenso_mail2 = $row[4];
		$kyohi_time4 = $row[5];
		if($tenso_mail2 != "" && $kyohi_time4 != "99"){
			$tenso_icon = "<img src=\"images/search/keitaiG_16.gif\" />";
		}

		// ブラックリスト=怒りアイコン、ウェルカム=スマイルアイコン
		$face_icon = "";
		$welcome = $row[7];
		$black = $row[8];
		if($welcome != "0"){
			$face_icon = "<img src=\"images/search/smile.gif\" />";
		}
		if ($black != "0"){
			$face_icon = "<img src=\"images/search/angry.gif\" />";
		}

		// 若葉・クローバーアイコン
		$face_icon = "";
		$cre_date = strtotime($row[9]);
		$now = time();
		$new_icon = "";
		if($now - $cre_date < 604800*2){ // 二週間以内
			$new_icon = "<img src=\"images/search/clover.gif\" style=\"float:right\" />";
		}
		if($now - $cre_date < 604800 ){ // 一週間以内
			$new_icon = "<img src=\"images/search/new.gif\" style=\"float:right\" />";
		}

		$tmpl->assign("frame",$frame);
		$tmpl->assign("image",$img);
		$tmpl->assign("nick_name",mb_strimwidth($nick_name, 0, 16, "..."));
		$tmpl->assign("hash",$hash);
		$tmpl->assign("new_icon",$new_icon);
		$tmpl->assign("face_icon",$face_icon);
		$tmpl->assign("tenso_icon",$tenso_icon);
		$tmpl->loopnext() ;
	}
	$tmpl->loopset("");

	$sql  = "select count(1) ";
	$sql .= "from";
	$sql .= " {$from}";
	$sql .= " inner join male_member m on p2.owner_cd = m.owner_cd and p2.user_id = m.user_id";
	$sql .= " inner join male_profile p1 on p2.owner_cd = p1.owner_cd and p2.user_id = p1.user_id ";
	$sql .= " {$from2} ";
	$sql .= "where";
	$sql .= " p2.owner_cd = '$ownerCd' and p2.prof_open_flg = '1' and m.stat = '1'";
	$sql .= $con;
	$result = $dbSlave->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	$num = $row[0];

	if($num == 0){
		$tmpl->assign("data_zero","1");
	}else{
		$tmpl->assign("data_num",$num);
		$tmpl->assign("page_feed",getPageFeedCreHtml_search($num, $pos));
	}


	//----------------------------------
	// ３時間以内のログイン人数取得
	$male_loginCntFile = "./male_count.txt";
	$fp = fopen($male_loginCntFile,'r');
	$time_old = fgets($fp);
	$num = fgets($fp);
	fclose($fp);

	$time_now = time();
	if(($time_now - $time_old) > 120){
		$sql  = "select count(1) from male_member where owner_cd = ? and stat = '1' and last_login > now() - interval 3 hour ";
		$sth = $dbSlave->prepare($sql);
		$data = array($ownerCd);
		$result = $dbSlave->execute($sth, $data);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		$row = $result->fetchRow();
		$num = $row[0];

		$fp = fopen($male_loginCntFile,'w');
		flock($fp, LOCK_EX);
		fputs($fp,$time_now."\n");
		fputs($fp,$num."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	$tmpl->assign("num",$num);

	$sql = "
		SELECT
		agent_code
		FROM female_member
		WHERE owner_cd = ? and user_id = ? and stat IN (1,6,7)
	";

	$sth = $dbSlave->prepare($sql);
	$data = array($ownerCd,$_SESSION['user_id']);
	$result = $dbSlave->execute($sth, $data);
	if(DB::isError($result))
	{
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	$agent_code = $row[0];

	if(!$agent_code){
		$tmpl->assign("disp_when_not_dairiten",1);
	}

	$tmpl->flush();
	exit;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getPageFeedCreHtml_search($num,$pos){
	global $page_max_num;		// 一度に表示したいページリンク数
	global $page_num;			// 1ページのライン数

	$strHtml = "";

	$page = (int)($pos / ($page_num * $page_max_num)) * $page_max_num + 1;
	if($pos < ($page_num * $page_max_num)){
		$strHtml = "<td width='80'></td>";
	}
	else{
		$tmp = (($page - 1) * $page_num) - ($page_num * $page_max_num);
		$strHtml = "<td width='80'><a href='javascript:goto_page(".$tmp.")'>前の".$page_max_num."ページ</a></td>";
	}

	if($pos == 0){
		$strHtml .= "<td width='45'></td>";
	}
	else{
		$tmp = ($pos - $page_num);
		$strHtml .= "<td width='45'><a href='javascript:goto_page(".$tmp.")'>前へ</a></td>";
	}

	$i=($page - 1) * $page_num;
	$max_page = ($page - 1) + $page_max_num;

	$strHtml .= "\n<td width='200' align='center'>";
	$pageLink = $page;
	while($i<$num){
		if($pageLink > $max_page){
			break;
		}
		if($i == $pos){
			$strHtml .= "<font color=\"#ff0000\">".$pageLink . "</font>　";
		}
		else{
			$strHtml .= "<a href='javascript:goto_page(".$i.")'>".$pageLink."</a>　";
		}
		$i+=$page_num;
		$pageLink++;
	}
	$strHtml .= "</td>\n";

	if($num > ($pos + $page_num)){
		$tmp = ($pos + $page_num);
		$strHtml .= "<td width='45'><a href='javascript:goto_page(".$tmp.")'>次へ</a></td>";
	}
	else{
		$strHtml .= "<td width='45'></td>";
	}

	$max = (int)($num / $page_num);
	if((int)($num % $page_num) > 0){
		$max += 1;
	}

	if(($page + $page_max_num) > $max){
		$strHtml .= "<td width='80' align='right'></td>";
	}
	else{
		$tmp = (($page - 1) * $page_num) + ($page_num * $page_max_num);
		$strHtml .= "<td width='80' align='right'><a href='javascript:goto_page(".$tmp.")'>次の".$page_max_num."ページ</a></td>";
	}

	return $strHtml;
}
?>
