<?
require_once 'common_proc.inc';
require_once 'common_db_slave104.inc';
require_once 'Owner.inc';
require_once 'xxsatotmpl2.class_ex.inc';
require_once 'xxsatoboy_pop_buy.inc';

	//広告コード
	//setcookie ("Advc", "",time()-3600, "/");
	if(isset($_GET)){
		foreach($_GET as $key => $value){
			if($key != "m"){
				setcookie ("Advc", "$key",time()+30*24*60*60, "/");
			}
		}
	}
	if(isset($_POST['logout'])){
		if($_POST['logout'] == "1"){
			session_destroy();
			if(isset($_SESSION['stat'])){
				unset($_SESSION['stat']);
				}
			}
		}
	//オフライン
	$top_left[""][""] = "offline_head.gif";
	$img_bg[""][""] = "offline02_bg.jpg";
	$top_bg[""][""] = "offline01_bg.gif";

	$ctop[""][""] = "offh";
	$css[""][""] = "off";
	$tag[""][""] = "images/xxsato/g/offh.gif";
	
	
	//オンライン中：２ショット
	$top_left[1][0] = "online03_head.gif";
	$img_bg[1][0] = "online02_bg.jpg";
	$top_bg[1][0] = "online01_bg.gif";

	$ctop[1][0] = "on2h";
	$css[1][0] = "on";
	$tag[1][0] = "images/xxsato/g/on2h.gif";

	//オンライン中：パーティー
	$top_left[1][1] = "online04_head.gif";
	$img_bg[1][1] = "online02_bg.jpg";
	$top_bg[1][1] = "online01_bg.gif";

	$ctop[1][1] = "onph";
	$css[1][1] = "pon";
	$tag[1][1] = "images/xxsato/g/ph.gif";

	//ツーショット中
	$top_left[2][0] = "2shot_head.gif";
	$img_bg[2][0] = "2shot02_bg.jpg";
	$top_bg[2][0] = "2shot01_bg.gif";

	$ctop[2][0] = "2h";
	$css[2][0] = "ch";
	$tag[2][0] = "images/xxsato/g/2h.gif";

	//パーティー中
	$top_left[2][1] = "party_head.gif";
	$img_bg[2][1] = "party02_bg.jpg";
	$top_bg[2][1] = "party01_bg.gif";

	$ctop[2][1] = "ph";
	$css[2][1] = "pc";
	$tag[2][1] = "images/xxsato/g/ph.gif";

			
	$sql_where1[0] = "";
	$sql_where1[1] = " and ((onair.stat = 1) or (onair.stat = 2 and onair.chat_mode = 1)) ";
	$sql_where1[2] = " and onair.chat_mode != 0";
	$sql_where1[3] = " and female_profile.area in (1,9,10,11,12,13,14,15)";
	$sql_where1[4] = " and female_profile.area in (2,16,17,18,19,20,21,22,23)";
	$sql_where1[5] = " and female_profile.area in (3,24,25,26,27,28)";
	$sql_where1[6] = " and female_profile.area in (4,29,30,31,32)";
	$sql_where1[7] = " and female_profile.area in (5,33,34,35,36,37,38)";
	$sql_where1[8] = " and female_profile.area in (6,39,40,41,42,43,44,45,46,47)";
	$sql_where1[9] = " and female_profile.area in (7,48,49,50,51,52,53,54,55)";
	$sql_where1[10] = " and female_profile.area = 8";


	$tag1[0] = "tag_online.gif";
	$tag1[1] = "tag_online.gif";
	$tag1[2] = "tag_party.gif";
	$tag1[3] = "tag_locate1.gif";
	$tag1[4] = "tag_locate2.gif";
	$tag1[5] = "tag_locate3.gif";
	$tag1[6] = "tag_locate4.gif";
	$tag1[7] = "tag_locate5.gif";
	$tag1[8] = "tag_locate6.gif";
	$tag1[9] = "tag_locate7.gif";
	$tag1[10] = "tag_locate8.gif";


	$opt[0] = "通常のまま";
	$opt[1] = "オンラインをピックアップ";
	$opt[2] = "各ルームごと";
	$opt[3] = "北海道・東北をピックアップ";
	$opt[4] = "関東をピックアップ";
	$opt[5] = "甲信越・北陸をピックアップ";
	$opt[6] = "東海をピックアップ";
	$opt[7] = "関西をピックアップ";
	$opt[8] = "中国・四国をピックアップ";
	$opt[9] = "九州・沖縄をピックアップ";
	$opt[10] = "海外をピックアップ";


//////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['m'])){
		if(preg_match ("/[0-9]+/i",$_GET['m'],$regs)){
			$opt_mode = 1;
			$v = $regs[0];
			$where1 = $sql_where1[$regs[0]];
		if($regs[0] == "0"){
		$opt_mode = 0;
		}
		}else{
			$opt_mode = 0;
			$v = 0;
			$where1 = $sql_where1[0];
			}
	}else{
		$opt_mode = 0;
		$where1 = "";
		$v = 0;
		}


	$fobj = new FormObject("トップページ"); 			//本フォームは個別の入力チェックが必要なし
	$tmp = "";
	
	for($i=0;$i<=10;$i++){
		if($v == $i){
			$tmp .= "<option value=\"{$i}\" selected=\"selected\">{$opt[$i]}</option>\n";
		}else{
			$tmp .= "<option value=\"{$i}\">{$opt[$i]}</option>\n";
			}
		}

//ニューフェイスの表示
//------------------------------------------------------------------------------------------------------------------------------------	
	define ("USER_CD_NEW",1);
	$sql  = "select female_profile.hash,female_profile.nick_name,onair.stat, female_profile.new_face, female_profile.img ,onair.chat_mode,female_profile.mic from (site_announce left join onair on site_announce.msg1 = onair.user_id and onair.owner_cd = 1 and (onair.start_date is null or onair.start_date < now())) LEFT JOIN female_profile ON site_announce.msg1 = female_profile.user_id and female_profile.owner_cd = 1 where site_announce.owner_cd = 1 and site_announce.use_type = ?";
	$data = array(USER_CD_NEW);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$data);
	//$result = $dbSlave33->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
		}
	$i = 1;
	$line = "";
	$tmp_str = "{\"newFace\":[";
	while($row = $result->fetchRow()){
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "./imgs/op/jyunbi.gif";
		}else{
			$data['img'] = "./imgs/op/120x90/" .$row[4];
			}
		$data['nick_name'] = mb_strimwidth($row[1], 0, 14, "");
		$data['top_left'] = $top_left[$row[2]][$row[5]];
		$data['img_bg'] = $img_bg[$row[2]][$row[5]];
		$data['top_bg'] = $top_bg[$row[2]][$row[5]];
		$data['css'] = $css[$row[2]][$row[5]];
		$data['tag'] = $tag[$row[2]][$row[5]];
		$data['ctop'] = $ctop[$row[2]][$row[5]];
		if($row[3] == 1){
			$data['cnew'] = "1";
		}elseif($row[3] == 2){
			$data['cnew'] = "2";
		}else{
			$data['cnew'] = "0";
			}
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		if($row[0] == '3086054eb3220d1'){
				$line .= makePersonSp($data);
			}else{
				$line .= makePerson($data);
				}
			$tmp_str .= $line;
			$line = "";
			$i=0;
		$i++;
		}
		$tmp_str .= "]}";
		echo (str_replace(',]}',']}',$tmp_str));
//--------------------------------------------------------------------------------------------------------------------------------------

//オンエアー中の人の表示（上側）--------------------------------------------------------------------------------------------------------
	$sql  = "";
	$sql  = "select ";
	$sql .= "female_profile.hash,"; 						//0
	$sql .= "female_profile.nick_name,";					//1
	$sql .= "onair.stat, "; 								//2
	$sql .= "female_profile.new_face, ";					//3
	$sql .= "female_profile.img, "; 						//4
	$sql .= "onair.chat_mode, ";							//2
	$sql .= "female_profile.mic "; 							//6
	$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and (onair.start_date is null or onair.start_date < now())) LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
	$sql .= "where female_profile.owner_cd = ".$ownerCd." and onair.owner_cd = ".$ownerCd." and female_member.owner_cd = ".$ownerCd." and female_member.stat = 1 $where1 ";
	$sql .= "order by rand() ";
	$result = $dbSlave33->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
		}
	$line = "";
	$tmp_str = "{\"gNode\":[";
	$i=1;
	if($result->numRows() < 1){
		$tmp = "";
		$line .= "<p>オンライン中の女性がいません</p>\n";
		$tmp .= $line;
	}else{
		while($row = $result->fetchRow()){
			$data['hash'] = $row[0];
			if($row[4] == ""){
				$data['img'] = "./imgs/op/jyunbi.gif";
			}else{
				$data['img'] = "./imgs/op/120x90/" .$row[4];
				}
			$data['nick_name'] = mb_strimwidth($row[1], 0, 14);
			$data['top_left'] = $top_left[$row[2]][$row[5]];
			$data['img_bg'] = $img_bg[$row[2]][$row[5]];
			$data['top_bg'] = $top_bg[$row[2]][$row[5]];
			$data['css'] = $css[$row[2]][$row[5]];
			$data['tag'] = $tag[$row[2]][$row[5]];
			$data['ctop'] = $ctop[$row[2]][$row[5]];
			if($row[6] == 1){
				$data['voice'] = "1";
			}else{
				$data['voice'] = "0";
				}
			if($row[3] == 1){
				$data['cnew'] = "1";
			}elseif($row[3] == 2){
				$data['cnew'] = "2";
			}else{
				$data['cnew'] = "0";
				}
			if($row[0] == '3086054eb3220d1'){
				$line .= makePersonSp($data);
			}else{
				$line .= makePerson($data);
				}
				$tmp_str .= $line;
				$line = "";
				$i=0;
			$i++;
			}
		if($i != 1){
			$str = "";
			$str1 = "";
			}
		$tmp_str .= "]}";
		echo (str_replace(',]}',']}',$tmp_str));
		}
	if($opt_mode == "0"){
		exit;
		}
	
//--------------------------------------------------------------------------------------------------------------------------

	//オンエアー中の人の表示（下）
	$sql_where2[0] = "";
	$sql_where2[1] = " and (onair.stat = 2 and onair.chat_mode = 0) ";
	$sql_where2[2] = " and onair.chat_mode = 0";
	$sql_where2[3] = " and female_profile.area not in (1,9,10,11,12,13,14,15)";
	$sql_where2[4] = " and female_profile.area not in (2,16,17,18,19,20,21,22,23)";
	$sql_where2[5] = " and female_profile.area not in (3,24,25,26,27,28)";
	$sql_where2[6] = " and female_profile.area not in (4,29,30,31,32)";
	$sql_where2[7] = " and female_profile.area not in (5,33,34,35,36,37,38)";
	$sql_where2[8] = " and female_profile.area not in (6,39,40,41,42,43,44,45,46,47)";
	$sql_where2[9] = " and female_profile.area not in (7,48,49,50,51,52,53,54,55)";
	$sql_where2[10] = " and female_profile.area != 8";

	$where2 = $sql_where2[$regs[0]];

	$tag2[0] = "";
	$tag2[1] = "tag_chat.gif";
	$tag2[2] = "tag_2shot.gif";
	$tag2[3] = "tag_locate9.gif";
	$tag2[4] = "tag_locate9.gif";
	$tag2[5] = "tag_locate9.gif";
	$tag2[6] = "tag_locate9.gif";
	$tag2[7] = "tag_locate9.gif";
	$tag2[8] = "tag_locate9.gif";
	$tag2[9] = "tag_locate9.gif";
	$tag2[10] = "tag_locate9.gif";
	$tag2[11] = "tag_locate9.gif";
	$tag2[12] = "tag_locate9.gif";
	$tag2[13] = "tag_locate9.gif";
	$tag2[14] = "tag_locate9.gif";
	$tag2[15] = "tag_locate9.gif";
	$tag2[16] = "tag_locate9.gif";
	$tag2[17] = "tag_locate9.gif";
	$tag2[18] = "tag_locate9.gif";
	$tag2[19] = "tag_locate9.gif";
	$tag2[20] = "tag_locate9.gif";
	$tag2[21] = "tag_locate9.gif";
	$tag2[22] = "tag_locate9.gif";
	$tag2[23] = "tag_locate9.gif";
	$tag2[24] = "tag_locate9.gif";
	$tag2[25] = "tag_locate9.gif";
	$tag2[26] = "tag_locate9.gif";
	$tag2[27] = "tag_locate9.gif";
	$tag2[28] = "tag_locate9.gif";
	$tag2[29] = "tag_locate9.gif";
	$tag2[30] = "tag_locate9.gif";
	$tag2[31] = "tag_locate9.gif";
	$tag2[32] = "tag_locate9.gif";
	$tag2[33] = "tag_locate9.gif";
	$tag2[34] = "tag_locate9.gif";
	$tag2[35] = "tag_locate9.gif";
	$tag2[36] = "tag_locate9.gif";
	$tag2[37] = "tag_locate9.gif";
	$tag2[38] = "tag_locate9.gif";
	$tag2[39] = "tag_locate9.gif";
	$tag2[40] = "tag_locate9.gif";
	$tag2[41] = "tag_locate9.gif";
	$tag2[42] = "tag_locate9.gif";
	$tag2[43] = "tag_locate9.gif";
	$tag2[44] = "tag_locate9.gif";
	$tag2[45] = "tag_locate9.gif";
	$tag2[46] = "tag_locate9.gif";
	$tag2[47] = "tag_locate9.gif";
	$tag2[48] = "tag_locate9.gif";
	$tag2[49] = "tag_locate9.gif";
	$tag2[50] = "tag_locate9.gif";
	$tag2[51] = "tag_locate9.gif";
	$tag2[52] = "tag_locate9.gif";
	$tag2[53] = "tag_locate9.gif";
	$tag2[54] = "tag_locate9.gif";
	$tag2[55] = "tag_locate9.gif";
	$tag2[56] = "tag_locate9.gif";
	$tag2[57] = "tag_locate9.gif";

	$tmpl->assign( "tag2" , $tag2[$v] ) ;

	$sql  = "";
	$sql  = "select ";
	$sql .= "female_profile.hash,"; 						//0
	$sql .= "female_profile.nick_name,";					//1
	$sql .= "onair.stat, "; 								//2
	$sql .= "female_profile.new_face, ";					//3
	$sql .= "female_profile.img, "; 						//4
	$sql .= "onair.chat_mode, ";							//2
	$sql .= "female_profile.mic "; 							//6
	$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and (onair.start_date is null or onair.start_date < now())) LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
	$sql .= "where female_profile.owner_cd = ".$ownerCd." and onair.owner_cd = ".$ownerCd." and female_member.owner_cd = ".$ownerCd." and female_member.stat = 1 $where2 ";
	$sql .= "order by rand() ";
	$result = $dbSlave33->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result->getMessage());
		}
	$line = "";
	$tmp_str = "{\"bNode\":[";
	$i=1;
	$tmpl->loopset( "online2" ) ;
	if($result->numRows() < 1){
		$tmp = "";
		$line .= "<p>オンライン中の女性がいません</p>\n";
		$tmp .= $line;
		$tmpl->assign( "girls_body" , $tmp ) ;
		$tmpl->loopnext() ;
		$tmpl->loopset("") ;
		$tmpl->flush();
		return;
		}
	while($row = $result->fetchRow()){
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "./imgs/op/jyunbi.gif";
		}else{
			$data['img'] = "./imgs/op/120x90/" .$row[4];
			}
		$data['nick_name'] = mb_strimwidth($row[1], 0, 14);
		$data['top_left'] = $top_left[$row[2]][$row[5]];
		$data['img_bg'] = $img_bg[$row[2]][$row[5]];
		$data['top_bg'] = $top_bg[$row[2]][$row[5]];
		$data['css'] = $css[$row[2]][$row[5]];
		$data['tag'] = $tag[$row[2]][$row[5]];
		$data['ctop'] = $ctop[$row[2]][$row[5]];
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		if($row[3] == 1){
			$data['cnew'] = "1";
		}elseif($row[3] == 2){
			$data['cnew'] = "2";
		}else{
			$data['cnew'] = "0";
			}
		if($row[0] == '3086054eb3220d1'){
			$line .= makePersonSp($data);
		}else{
			$line .= makePerson($data);
			}
		$tmp_str .= $line;
		$line = "";
		$i=0;
		}
		$i++;
		}
		$tmp_str .= "]}";
		echo (str_replace(',]}',']}',$tmp_str));

function makePerson($data){
$str = <<<EOM
{"hash":"{$data['hash']}","gname":"{$data['nick_name']}","gstatus":"{$data['ctop']}","box":"{$data['css']}","cnew":"{$data['cnew']}","phot":"{$data['img']}","voice":"{$data['voice']}"},
EOM;
	return $str;
	}
?>