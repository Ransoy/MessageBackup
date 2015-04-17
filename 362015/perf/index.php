<?php
/*
 * @Title：パフォーマー管理画面トップ
 * @Description：まだ混沌としてます。
 * @Author：Satodate
 */

if($_SERVER["HTTP_SSL"] == 'YES'){
	header('Location: http://www.macherie.tv/performer/');
}

require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'common_db_in2.inc';
require_once 'operator/tmpl2.class_operator.inc';
require_once 'Owner.inc';
require_once 'operator/operator.inc';
require_once 'operator/camera_test.inc';
require_once 'HTTP/Client.php';

#---------------------------------------------------------
#	Page Setting
#---------------------------------------------------------
$tmpl = new Tmpl23();
$dairiten = "";

if($tmpl->profile['agent_code'] != "")
{
	$dairiten = "_dairi";
}


if(isset($_GET['dairi'])){
	//header("Location:/performer/?dairi");
//	header("Location:/operator/?dairi");
}

//if($tmpl->profile['world_flg'] != "0")
if(strcmp($tmpl->profile['world_flg'], "0") != 0)
{
	//$dairiten = "_world";
	header("Location:/operator/");
	break;
}

if($tmpl->profile['flv1'] == "19")
{
	$tmpl->assign("flv1_19","");
}

if($tmpl->profile['sub_agent_code'] == "")
{
	$tmpl->assign("no_sub_agent","");
}


$tmpl->fname = OP_PATH.'template/index'.$dairiten.'.html';

// テスト生パフォの報告部分を表示
$sql  = "SELECT stat FROM female_member WHERE owner_cd = 1 AND user_id = ? ";
$sth = $dbSlave->prepare($sql);
$data = array($_SESSION["user_id"]);
$result = $dbSlave->execute($sth, $data);
if(DB::isError($result)){
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
$user_stat = $row[0];
if($user_stat == "6"){
	$tmpl->assign("test_female_disp", "1");
}

if($dairiten != ""){
	//代理店パフォ判別
	if($tmpl->profile["sub_agent_code"] != ""){
		$news_type = "1";
	}else if($tmpl->profile["sub_agent_code"] == ""){
		$news_type = "2";
	}else{
		$news_type = "1";
	}
	$tmpl->assign("news_type", $news_type);
	if($user_stat == "1"){
//--------------------代理店キャンペーン START---------------------------

		require_once 'operator/agent_login_campaign.inc';

		//申告処理
		if(isset($_POST["mode"]) && $_POST["mode"] == "agent_campaign_regist"){
			setAgentCampaignReport($_SESSION['user_id'], $_POST["campaign_hash"], $_SESSION['nick_name'], $_SESSION["agent_code"], $_SESSION["sub_agent_code"]);
		}

		//キャンペーン情報取得
		if(getAgentCampaign($_SESSION["user_id"], $campaign_data)){
			$disp_cnt = 0;
			$this_date = date("Y-m-d H:i:s");
		
			$tmpl->loopset("agent_campaign");
			foreach($campaign_data as $key => $val){
				$start = date("Y-m-d H:i:s", strtotime($val["campaign_start"]));
				$end = date("Y-m-d H:i:s", strtotime($val["campaign_end"]));
				$this_date = date("Y-m-d H:i:s");
				$w = date("w", strtotime($start));
				if($w == "0" || $w == "5" || $w == "6"){
					$camp_title = "週末ログインボーナス";
				}else{
					$camp_title = "平日ログインビーナス";
				}
				
				if(($start < $this_date && $end > $this_date) || $val["camp_stat"] != "0"){

					$tmpl->assign("start_h", date("G時i分", strtotime($val["campaign_start"])));
					$tmpl->assign("end_h", date("G時i分", strtotime($val["campaign_end"])));
					$tmpl->assign("work_time_nokori", $val["work_time_nokori"]);
					$tmpl->assign("chat_time_nokori", $val["chat_time_nokori"]);
					$tmpl->assign("campaign_hash", $val["campaign_hash"]);
					$tmpl->assign("campaign_tag", $val["campaign_tag"]);
					$tmpl->assign("campaign_title", $val["campaign_title"]);
					$tmpl->assign("camp_title", $camp_title);
					if($val["camp_stat"] == "2"){
						$tmpl->assign("camp_comp", "1");
					}else if($val["camp_stat"] == "1"){
						$tmpl->assign("camp_report", "1");
					}else{
						$tmpl->assign("camp_std", "1");
					}

					$disp_cnt++;
					$tmpl->loopnext();
				}
			}
			$tmpl->loopset("");
			if($disp_cnt > 0){
				$tmpl->assign("campaign_disp", "1");
			}
		}
//--------------------代理店キャンペーン END-----------------------------
	}
}

#============================================================================================
#  Cencter Column
#============================================================================================

#---------------------------------------------------------
#  待機ボーナス
#---------------------------------------------------------
$now = time();
$sql = "SELECT campaign_taiki_bonus.id,campaign_taiki_bonus.kaisai_from,campaign_taiki_bonus.kaisai_to, campaign_taiki_bonus.sinkoku_from,sinkoku_to, campaign_taiki_bonus.bonus,campaign_taiki_bonus.hours, campaign_taiki_bonus.point, campaign_taiki_bonus.banner FROM `campaign_taiki_bonus` LEFT JOIN `campaign_taiki_bonus_send` USING (id) WHERE `campaign_taiki_bonus_send`.owner_cd = ? AND `campaign_taiki_bonus_send`.user_id = ? AND campaign_taiki_bonus_send.stat = 0";
$data = array($ownerCd, $_SESSION['user_id']);
$result = $dbSlave->query($sql,$data);
while($row = $result->fetchRow()){
	if((strtotime($row[1]) < $now && strtotime($row[2]) > $now) || (strtotime($row[3]) < $now && strtotime($row[4]) > $now)){
		$tmpl->assign("taiki_bonus","");
		$taiki = getTaikiSec($row[1],$row[2]);
		$kadou = ceil($taiki/60);

		$chat = getChatSec($row[1],$row[2]);

		$tmpl->assign("min",max(0,($row[6]*60) - $kadou));
		$tmpl->assign("bonus",$row[5]);
		$tmpl->assign("point",max(0,$row[7]-(ceil($chat/60))));

		if(!empty($row[8])){
			if($row[8] == "A")
			{
				$tmpl->assign("banner_img","img/banner/pafo_taiki_bonus.jpg");
			}
			elseif($row[8] == "B")
			{
				$tmpl->assign("banner_img","img/banner/pafo_secret_bonus.jpg");
			}
			elseif($row[8] == "C")
			{
				$tmpl->assign("banner_img","img/banner/pafo_login_bonus.jpg");
			}
			else
			{
				$tmpl->assign("banner_img","img/banner/pafo_taiki_bonus.jpg");
			}
		}
		else
		{
			$tmpl->assign("banner_img","img/banner/pafo_taiki_bonus.jpg");
		}
	}
}
$tmpl->assign("momo01","");
function getTaikiSec($from,$to){
	global $ownerCd,$dbSlave;
	$taiki_sec = 0;
	$sql = 'SELECT SQL_CALC_FOUND_ROWS SUM( LEAST( UNIX_TIMESTAMP( logout_time ) , UNIX_TIMESTAMP(?) ) - GREATEST(UNIX_TIMESTAMP( cre_date ) , UNIX_TIMESTAMP(?)))
		FROM onair_log
		WHERE true
		AND user_id = ?
		AND owner_Cd =?
		AND ((cre_date >= ? AND cre_date <= ? ) OR ( logout_time >= ? AND logout_time <= ? ) OR ( cre_date < ? AND logout_time > ? ))
		GROUP BY user_id
	';
	$data = array($to, $from, $_SESSION['user_id'], $ownerCd, $from, $to, $from, $to, $from, $to);
	$result = $dbSlave->query($sql, $data);
	while($row = $result->fetchRow()){
		$taiki_sec += $row[0];
	}
	// 待機中
	$sql = 'SELECT LEAST( UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(?)) - GREATEST( UNIX_TIMESTAMP(CRE_DATE), UNIX_TIMESTAMP(?)) FROM `onair`
	WHERE user_id = ? AND owner_cd = ? AND ((CRE_DATE <? AND CRE_DATE >=?) OR (UPD_DATE <? AND UPD_DATE >=?) OR (CRE_DATE < ? AND UPD_DATE > ? )) ';
	$data = array($to, $from, $_SESSION['user_id'], $ownerCd, $to, $from, $to, $from , $from, $to );
	$result = $dbSlave->query($sql, $data);
	if($row = $result->fetchRow()){
		$taiki_sec += $row[0];
	}
	return $taiki_sec;
}
function getChatSec($from,$to){
	global $ownerCd,$dbSlave;
	$chat_sec = 0;
	$sql = 'SELECT SQL_CALC_FOUND_ROWS SUM( LEAST( UNIX_TIMESTAMP( upd_date ) , UNIX_TIMESTAMP(?) ) - GREATEST(UNIX_TIMESTAMP( cre_date ) , UNIX_TIMESTAMP(?)))
			FROM chat_log_all
			WHERE owner_cd = ? AND female_user_id = ?
			AND ((cre_date >= ? AND cre_date <= ?) OR (upd_date >= ? AND upd_date <= ?) OR (cre_date < ? AND upd_date > ?))
	';
	$data = array($to,$from, $ownerCd, $_SESSION['user_id'],  $from, $to, $from, $to, $from, $to);
	$result = $dbSlave->query($sql, $data);
	if($row = $result->fetchRow()){
		$chat_sec += $row[0];
	}
	// チャット中
	$sql = 'SELECT SQL_CALC_FOUND_ROWS SUM( LEAST( UNIX_TIMESTAMP( upd_date ) , UNIX_TIMESTAMP(?) ) - GREATEST(UNIX_TIMESTAMP( cre_date ) , UNIX_TIMESTAMP(?)))
			FROM chat_log
			WHERE owner_cd = ? AND female_user_id = ?
			AND ((cre_date >= ? AND cre_date <= ?) OR (upd_date >= ? AND upd_date <= ?) OR (cre_date < ? AND upd_date > ?))
	';
	$data = array($to, $from, $ownerCd, $_SESSION['user_id'], $from, $to, $from, $to, $from, $to);
	$result = $dbSlave->query($sql, $data);
	if($row = $result->fetchRow()){
		$chat_sec += $row[0];
	}
	return $chat_sec;
}
function getPoint($from,$to){
	global $ownerCd,$dbSlave;
	//ポイント参照SQL文
	$sql = "SELECT SUM(point- point_old) FROM female_point_log WHERE owner_cd = ? AND user_id = ? AND cre_date >= ? AND cre_date < ? AND upd_mode IN (11,16,13,12,14,17,40,51) GROUP BY user_id ;";
	$data = array($ownerCd,$_SESSION['user_id'],$from,$to);
	$result = $dbSlave->query($sql,$data);
	if(DB::isError($result)){
	    err_proc($result->getMessage());
	}
	$point = 0.0;
	if($row = $result->fetchRow()){
		$point += $row[0];
	}
	return $point;
}
#---------------------------------------------------------
#  News
#---------------------------------------------------------

//掛け持ちか検索
$sql  = "select flv1 from female_member where owner_cd = ? and user_id = ? ";
$sth = $dbSlave->prepare($sql);
$data = array($ownerCd,$_SESSION['user_id']);
$result = $dbSlave->execute($sth, $data);
if(DB::isError($result)){
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
if($row[0] == 21 || $row[0] == 20 || $row[0] == 19 || $row[0] == 18)
{
	$use_cd = 4;//掛け持ちだったら掛け持ち用ニュースを表示
}else{
	$use_cd = 1;//それ以外だったらオペレータへのアナウンスを表示

}

$sql  = "select ";
$sql .= "msg2 ";
$sql .= "from site_announce ";
$sql .= "where owner_cd = ? and use_type = 4 ";
$sql .= "and use_cd = ?";

$sth = $dbSlave->prepare($sql);
$data = array($ownerCd,$use_cd);
$result = $dbSlave->execute($sth, $data);
if(DB::isError($result)){
	print $sql;
	err_proc($result->getMessage());
}

//メッセージ
$row = $result->fetchRow();
$tmpl->assign("news",$row[0]);

#---------------------------------------------------------
#  担当者
#---------------------------------------------------------
$sth = $dbMaster->prepare("select tanto_main,agent_code from female_member where owner_cd = ? and user_id = ?");
$data = array($ownerCd,$_SESSION['user_id']);
$result = $dbMaster->execute($sth, $data);
if(DB::isError($result)){
	print $sql;
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
$main = "$row[0]";
$agent_code = $row[1];
$non_flg = 0;
if($main == ""){
	$non_flg = 1;
}
if($agent_code != ""){
	// 代理店に所属する女性であれば、
	// 担当者(サブ担当者)は非表示
	// 見出し＝「代理店からのコメント」
	$tmpl->assign("dairiten","1");
	$tmpl->assign("caption_comment","代理店からのコメント");
	$tmpl->assign("hash_main","$row[1]");
	//return;
} else {
	// 代理店に所属する女性でなければ、
	// 担当者(サブ担当者)は表示
	// 見出し＝「担当者より一言」
	$tmpl->assign("non_dairiten","1");
	$tmpl->assign("caption_comment","担当者より一言");
	$tmpl->assign("hash_main","$row[0]");
}

//メイン担当者表示
$sth = $dbMaster->prepare("select img,disp_name,msg2,mail,sub_name from admin where owner_cd = ? and hash = ?");
$data = array($ownerCd,$main);
$result = $dbMaster->execute($sth, $data);
if(DB::isError($result)){
	print $sql;
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
putImg2("img_main",$row[0]);
$tmpl->assign("disp_name_main","$row[1]");
$tmpl->assign("time_main","$row[2]");
$tmpl->assign("mail_main","$row[3]");

$sub = "$row[4]";

//サブ担当者表示
$sth = $dbMaster->prepare("select img,disp_name,msg2,mail from admin where owner_cd = ? and name = ?");
$data = array($ownerCd,$sub);
$result = $dbMaster->execute($sth, $data);
if(DB::isError($result)){
	print $sql;
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
putImg2("img_sub",$row[0]);
$tmpl->assign("disp_name_sub","$row[1]");
$tmpl->assign("time_sub","$row[2]");
$tmpl->assign("mail_sub","$row[3]");

if($non_flg == 1){
	$tmpl->assign("hash_main",$sub);
	putImg2("img_main",$row[0]);
	$tmpl->assign("disp_name_main","$row[1]");
	$tmpl->assign("time_main","$row[2]");
	$tmpl->assign("mail_main","$row[3]");
}

function putImg2($target,$src)
{
	global $tmpl;
	if($src == ""){
		$tmpl->assign($target,"images/news/thumb.jpg");
	}else{
		$tmpl->assign($target,"/imgs/admin/120x90/" . $src);
	}
}

#============================================================================================
#  Right Column
#============================================================================================

#---------------------------------------------------------
#  Topics
#---------------------------------------------------------
$con = ceil(time()/600);
$sql  = "SELECT ";
$sql .= "faq_category.title, ";
$sql .= "faq_question.qid, ";
$sql .= "faq_question.question, ";
$sql .= "faq_question.date, ";
$sql .= "faq_question.priority_2 ";
$sql .= "from ";
$sql .= "faq_category,faq_question ";
$sql .= "WHERE ";
$sql .= "faq_category.category_id = faq_question.category_id ";
$sql .= "ORDER BY faq_question.priority_2 DESC , RAND(?)";
$sql .= "LIMIT 0,5";
$result = $dbInnet->query($sql,$con);
if(DB::isError($result)){
	err_proc($result->getMessage());
}
$tmpl->loopset("loop_faq");
while($row = $result->fetchRow())
{
	$day = date('y/m/d H:i',$row[3]);
	$new = "";
	//new条件 10時間以内に変更
	//if($row[3]+60*60*10 > time()){
	//$new = "<font color=\"#b5304a\">NEW!!</font>";
	//}
	$tmpl->assign("day",$day);
	$tmpl->assign("new",$new);
	$tmpl->assign("title",$row[0]);
	$tmpl->assign("qid",$row[1]);
	$tmpl->assign("question",$row[2]);
	$tmpl->loopnext();
}
$tmpl->loopset("");

$sql = "select value from point_setting where id = '88'";
$result = $dbSlave->query($sql);
$row_setting = $result -> fetchRow();
//echo $row_setting[0];
if(strcmp($row_setting[0],"1.0")==0){//rookie setting is on
//	echo "x";
	$sql="select A.`rookie_flg`,B.`new_face` FROM `female_member` A,`female_profile` B  WHERE A.`user_id`='".$_SESSION['user_id']."' AND A.`user_id`=B.`user_id`";
	$res = $dbSlave->query($sql);
	$row_tmp = $res->fetchRow();

//echo $row_tmp[1];
	if(strcmp($row_tmp[1],"2")==0){//debut mark is on

		if(strcmp($row_tmp[0],"1")==0){
			if($_POST['mode']=="debut"){
				//デビューマークを外す感じで
				$tmpl->assign("mode_debut",1);
				$sql="update `female_member` SET `rookie_flg`='0' WHERE `user_id`='".$_SESSION['user_id']."'";
				$dbMaster->query($sql);
			}else{
				$tmpl->assign("debut_special",$_SESSION['user_id']);
			}
		}//もともと0に設定されてたなら何も表示しない
	}
}

#---------------------------------------------------------
#  報酬アップカウンター
#  ※iframeのままです。
#---------------------------------------------------------
$ar_crear[18] = 2000;
$ar_crear[19] = 2000;
$ar_crear[20] = 5000;
$ar_crear[21] = 10000;
$sql = "select flv1,agent_code,date_format(cre_date, '%Y/%m/%d'),CRE_DATE,stat,memo1 from female_member where female_member.owner_cd = ? and female_member.user_id = ? ";
$sth = $dbSlave->prepare($sql);
$data = array($ownerCd,$_SESSION['user_id']);
$result = $dbSlave->execute($sth, $data);
if(DB::isError($result)){
	err_proc($result->getMessage());
}
$row = $result->fetchRow();
if($row[0] == 21 || $row[0] == 20 || $row[0] == 19 || $row[0] == 18){
	$tmpl->assign("reward_up","");
	$tmpl->assign("crear_point",$ar_crear[$row[0]]);
}
$flv1 = $row[0];
$agent_code = $row[1];
$cre_date = $row[3];
$stat = $row[4];
$memo1 = $row[5];

$g = false;
$c = false;
$sql = 'SELECT `option`,stat FROM campaign_gc WHERE owner_cd = 1 AND campaign_id = 1 AND user_id = ? AND NOT(stat = 9)';
$result = $dbSlave->query($sql,$_SESSION['user_id']);
while($row = $result->fetchRow()){
	if($row[0] == "gc"){
		$g = $row[1];
		$c = $row[1];
	}
	if($row[0] == "g") $g = $row[1];
	if($row[0] == "c") $c = $row[1];
}

if(($flv1==18 || $flv1==19 || $flv1==20 || $flv1==21) && empty($agent_code) && ($g != 2 || $c != 2)) {
	$tmpl->assign("gc","1");
}

#---------------------------------------------------------
#	2010-01-06 新人ボーナス
#	wakasugi@innetwork.jp
#  ※iframeのままです。
#---------------------------------------------------------
if(time() >= strtotime('2010-01-07')){
	if(mb_ereg("(\d{2}/\d{2}/\d{2})面接済",$memo1,$reg)){
		if(!empty($reg[1])){
			$reg_day = "20".$reg[1];
			if(strtotime($reg_day) >= strtotime('2010-01-07') && strtotime($reg_day." + 2 week") >= time()){
				$tmpl->assign("newgirls_bonus","");
			}
		}
	}
}
#---------------------------------------------------------
#	2010-03-01 お客様優待券
#	wakasugi@innetwork.jp
#  ※iframeのままです。
#---------------------------------------------------------
if(empty($agent_code)){
	//if($_SESSION['user_id'] == "momo01"){
	//	$tmpl->assign("present_point","");
	//}
	$sql = "SELECT `start_date`,`end_date`,`sinkoku_end_date` FROM `campaign_present_point_set` WHERE flg = 0 LIMIT 1;";
	$result = $dbSlave->query($sql);
	$row = $result->fetchRow();
	if($now >= strtotime($row[0]) && $now < strtotime($row[2])){
		$tmpl->assign("present_point","");
	}
}
#---------------------------------------------------------
#	2009-12-16 クリスマス系
#	wakasugi@innetwork.jps
#  ※iframeのままです。
#---------------------------------------------------------
if(empty($agent_code)){
	if($now >= strtotime('2009-12-18 05:00:00') && $now < strtotime('2009-12-24 05:00:00')){
		$tmpl->assign("xmas_present_point","");
	}
	if($now >= strtotime('2009-12-24 20:00:00') && $now < strtotime('2009-12-26 05:00:00')){
		$tmpl->assign("xmas_present_point","");
		$sql = 'SELECT stat FROM `campaign_xmas_img_kako` WHERE owner_cd = ? AND user_id =? LIMIT 1;';
		$result = $dbSlave->query($sql,array($ownerCd,$_SESSION['user_id']));
		if($row = $result->fetchRow()){
			if($row[0] == "1"){
				$tmpl->assign("xmas_img_kako","");
			}
		}
	}
}
#---------------------------------------------------------
#	2010-07-20 21:00:00〜 special_week
#	wakasugi@innetwork.jp
#---------------------------------------------------------
if(empty($agent_code)){
	if($now >= strtotime('2010-07-20 21:00:00') && $now < strtotime('2010-07-27 05:00:00')){
		$tmpl->assign("special_week_counter","");
	}
}

#---------------------------------------------------------
#	2009-11-20 シークレットボーナス
#	wakasugi@innetwork.jp
#---------------------------------------------------------
if($agent_code == "" && $stat == "1"){
	$sql = 'SELECT * FROM campaign_secretbonus_set LIMIT 1;';
	$result = $dbSlave->query($sql);
	$setting = $result->fetchRow(DB_FETCHMODE_ASSOC);

	$sql = 'SELECT dec_start FROM campaign_secretbonus_date ORDER BY dec_start ASC LIMIT 1;';
	$result = $dbSlave->query($sql);
	$row = $result->fetchRow();
	$to_date = $row[0];

	$sql = 'SELECT * FROM campaign_secretbonus_date WHERE 1 ORDER BY dec_start ASC;';
	$result44 = $dbSlave->query($sql);
	while($row = $result44->fetchRow(DB_FETCHMODE_ASSOC)){
		if($now >= strtotime($row['dec_start']) && $now < strtotime($row['dec_end'])){
			if($setting['con'] != '0'){
				$con = false;
				if($setting['con'] == 'A'){
					$setting['cre_from'] = strtotime('2004-01-01');
					$setting['cre_to']   = strtotime('2007-01-01');
				}
				if($setting['con'] == 'B'){
					$setting['cre_from'] = strtotime('2007-01-01');
					$setting['cre_to']   = strtotime('2008-01-01');
				}
				if($setting['con'] == 'C'){
					$setting['cre_from'] = strtotime('2008-01-01');
					$setting['cre_to']   = strtotime('2009-01-01');
				}
				if($setting['con'] == 'D'){
					$setting['cre_from'] = strtotime('2009-01-01');
					$setting['cre_to']   = strtotime('2010-01-01');
				}
				if($setting['con'] == 'E'){
					$setting['cre_from'] = strtotime('2004-01-01');
					$setting['cre_to']   = strtotime('2009-01-01');
				}
				if(!empty($setting['cre_from']) && !empty($setting['cre_to'])){
					$cre_time = strtotime($cre_date);
					if($cre_time >= $setting['cre_from'] && $cre_time < $setting['cre_to'] ){
						$con = true;
					}
				}
				$con2 = true;
				if($setting['con2'] == '1')
				{
/*
						$sql = 'SELECT SUM(point - point_old) FROM female_point_log WHERE owner_cd = ? AND user_id =? AND CRE_DATE >= ? AND CRE_DATE < ? AND upd_mode IN (11,16,13,12,14,17,40,51);';
						$sql_item = array($ownerCd,$_SESSION['user_id'],date("Y-m-01"),$to_date);
						$result = $dbSlave->query($sql,$sql_item);
*/
					$sql = 'SELECT point FROM female_point WHERE owner_cd = ? AND user_id = ? LIMIT 1;';
					$sql_item = array($ownerCd,$_SESSION['user_id']);
					$result = $dbSlave->query($sql,$sql_item);
					if($row = $result->fetchRow()){
						if($row[0] > 500){
							$con2 = false;
						}
					}else{
						$con2 = false;
					}
				}

				if($con && $con2){
					$tmpl->assign("secret_bonus","");
				}
			}
		}
	}
}

#============================================================================================
#  攻撃されたとき用のやつ
#============================================================================================
/*
if($_SERVER['HTTP_HOST'] == "113.33.84.30")
{
	$tmpl = new Tmpl23($tmpl_dir."operator/index_sub.html");
}
*/
$tmpl->flush();
?>
