<?
require_once 'common_proc.inc';
require_once 'common_db_slave104.inc';
require_once 'mc_db.inc';

if(isset($_SERVER['REMOTE_ADDR']) &&
	($_SERVER['REMOTE_ADDR'] == "38.98.55.59"
	)
){
//	exit;
}

//オフライン
$ctop[""][""] = "offh";
$css[""][""] = "off";
//オフラインじゃないんだけど、onairログとのずれでonair.chat_modeが空になることがある
$ctop[1][""] = "offh";
$css[1][""] = "off";
$ctop[2][""] = "offh";
$css[2][""] = "off";
$ctop[""][1] = "offh";
$css[""][1] = "off";
$ctop[""][2] = "offh";
$css[""][2] = "off";
//２ショット
$ctop[1][0] = "on2h";
$css[1][0] = "on";
//パーティー
$ctop[1][1] = "onph";
$css[1][1] = "pon";
//ツーショット中
$ctop[2][0] = "2h";
$css[2][0] = "ch";
//パーティー中
$ctop[2][1] = "ph";
$css[2][1] = "pc";
//CM(world)
$ctop[1]['cm_info'] = "wh";
$css[1]['cm_info'] = "cm";

//Eventファイルオープン
$fp = fopen('/var/www/livechat/htdocs/include/event/event.csv','r');
$e_dat = fgetcsv($fp, 32);
$eventType = $e_dat[0];
fclose($fp);

if(isset($_POST['m'])){
	if(preg_match ("/[0-9]+/i",$_POST['m'],$regs)){
		$mval = $regs[0];
		$opt_mode = 1;
		if($regs[0] == "0"){
			$opt_mode = 0;
		}
	} else {
		$mval = 0;
		$opt_mode = 0;
	}
}else{
	$mval = 0;
	$opt_mode = 0;
}

if(isset($_POST['w'])){
	if(strcmp($_POST['w'],"true") == 0){
		$wShowFlg = true;
	} else {
		$wShowFlg = false;
	}
} else {
	$wShowFlg = false;
}

$tmp_str = "{";
//	RoomEventNode
//--------------------------------------------------------
$line = "\"eventNode\":[";
$i=1;

$list_data = get_eventNode2(&$result, $ownerCd, $aDomainFlg, $e_dat, $mval, $dbSlave33);
if (!$list_data || !is_array($list_data)) {
	$tmp_str .= $line;
	$tmp_str = str_replace('"eventNode":[','"eventNode":',$tmp_str);

	//イベントルームバー表示判定
	$csv=fopen('/var/www/livechat/htdocs/include/event/topImg.csv','r');
	while($row = fgetcsv($csv,1024)){
		$roomtag[intval($row[0])] = $row[1];
	}
	if($roomtag[$eventType]!="" && $_SERVER["HTTP_HOST"] != "world.macherie.tv"){
		$tmp_str .= "\"showNode\"";	//イベントがあるときは表示にしておく
	} else {
		$tmp_str .= "\"notNode\"";
	}

	$tmp_str .= ",";
} else {
	foreach ($list_data as $row) {
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "/imgs/op/jyunbi.gif";
		}else{
			$data['img'] = $row[4];
		}
		$data['type'] = 1;
		$data['nick_name'] = addslashes(mb_strimwidth($row[1], 0, 14, ""));
		$chstat = $row[2];
    $data['css'] = $css[$chstat][$row[5]];
    $data['ctop'] = $ctop[$chstat][$row[5]];
    if(2 != $chstat && $row[12] == 1) {
		  $data['css'] = $data['ctop'] = "machi2h";
		}
		if($row[3] == 1){
			$data['cnew'] = ($row[7] == "1") ? "4" : "1";
		}elseif($row[3] == 2){
			$data['cnew'] = ($row[7] == "1") ? "5" : "2";
		}elseif($row[7] == 1){
			$data['cnew'] = "3";
		}else{
			$data['cnew'] = "0";
		}
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		// パフォ写真画質アップ対応
//		if( isset($row[9]) ){
		if( isset($row[9]) && $row[9] != "-" ){
			$data['clip'] = "1";
		}else{
			$data['clip'] = null;
		}


		if ($row[10]>0) {
			$data['evcnt'] = $row[10];
		} else {
			$data['evcnt'] = '0';
		}

        $data['onair_hash'] = $row[11];

        // どこのデータか？
        $data['zn'] = 'event';

		$line .= makePerson($data);
		$tmp_str .= $line;
		$line = "";
		$i=0;
		$i++;
	}
	if($i != 1){
		$str = "";
	}
	$tmp_str .= "],";
	$tmp_str = str_replace('},]','}]',$tmp_str);

}

//	PatyRoomNode
//--------------------------------------------------------
$line = "\"partyNode\":[";
$i=1;

$list_data = get_partyNode2(&$result, $ownerCd, $aDomainFlg, $e_dat, $mval, $dbSlave33);
if (!$list_data || !is_array($list_data)) {
	$tmp_str .= $line;
	$tmp_str = str_replace('"partyNode":[','"partyNode":',$tmp_str);
	$tmp_str .= "\"notNode\"";
	$tmp_str .= ",";
} else {
	foreach ($list_data as $row) {
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "/imgs/op/jyunbi.gif";
		}else{
			$data['img'] = $row[4];
		}
		$data['type'] = 1;
		$data['nick_name'] = addslashes(mb_strimwidth($row[1], 0, 14, ""));
		$data['css'] = $css[$row[2]][$row[5]];
		$data['ctop'] = $ctop[$row[2]][$row[5]];
		if($row[3] == 1){
			$data['cnew'] = ($row[7] == "1") ? "4" : "1";
		}elseif($row[3] == 2){
			$data['cnew'] = ($row[7] == "1") ? "5" : "2";
		}elseif($row[7] == 1){
			$data['cnew'] = "3";
		}else{
			$data['cnew'] = "0";
		}
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		// パフォ写真画質アップ対応
//		if( isset($row[9]) ){
		if( isset($row[9]) && $row[9] != "-" ){
			$data['clip'] = "1";
		}else{
			$data['clip'] = null;
		}

        // どこのデータか？
        $data['zn'] = 'party';

		$line .= makePerson($data);
		$tmp_str .= $line;
		$line = "";
		$i=0;
		$i++;
	}
	if($i != 1){
		$str = "";
	}
	$tmp_str .= "],";
	$tmp_str = str_replace('},]','}]',$tmp_str);

}

//	PageFirstNode
//--------------------------------------------------------
$line = "\"firstNode\":[";
$i=1;
$list_data = get_firstNode2(&$result, $ownerCd, $aDomainFlg, $e_dat, $mval, $wShowFlg, $dbSlave33);
if (!$list_data || !is_array($list_data)) {
	$tmp_str .= $line;
	$tmp_str = str_replace('"firstNode":[','"firstNode":',$tmp_str);
	$tmp_str .= "\"notNode\"";

	if($opt_mode == "0"){
		$tmp_str .= ",\"secondNode\":\"notNode\"}";
		echo($tmp_str);
		exit;
	} else {
		$tmp_str .= ",";
	}
} else {
	foreach ($list_data as $row) {
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "/imgs/op/jyunbi.gif";
		}else{
			$data['img'] = $row[4];
		}
		$data['type'] = 1;
		$data['nick_name'] = addslashes(mb_strimwidth($row[1], 0, 14, ""));
		$chstat = $row[2];
    $data['css'] = $css[$chstat][$row[5]];
    $data['ctop'] = $ctop[$chstat][$row[5]];
    if(2 != $chstat && $row[10] == 1) {
	    $data['css'] = $data['ctop'] = "machi2h";
		}
		if($row[3] == 1){
			$data['cnew'] = ($row[7] == "1") ? "4" : "1";
		}elseif($row[3] == 2){
			$data['cnew'] = ($row[7] == "1") ? "5" : "2";
		}elseif($row[7] == 1){
			$data['cnew'] = "3";
		}else{
			$data['cnew'] = "0";
		}
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		// パフォ写真画質アップ対応
//		if( isset($row[9]) ){
		if( isset($row[9]) && $row[9] != "-" ){
			$data['clip'] = "1";
		}else{
			$data['clip'] = null;
		}

        // どこのデータか？
        $data['zn'] = 'first';

		$line .= makePerson($data);
		$tmp_str .= $line;
		$line = "";
		$i=0;
		$i++;
	}
	if($i != 1){
		$str = "";
	}
	$tmp_str .= "],";
	if($opt_mode == "0"){
		$tmp_str = str_replace('},],','}],',$tmp_str);
		$tmp_str .= "\"secondNode\":\"notNode\"}";
		echo($tmp_str);
		exit;
	}else{
		$tmp_str = str_replace('},]','}]',$tmp_str);
	}
}

//	PageSecondNode
//--------------------------------------------------------
$line = "\"secondNode\":[";
$i=1;
$list_data = get_secondNode2(&$result, $ownerCd, $aDomainFlg, $e_dat, $mval, $dbSlave33);
if (!$list_data || !is_array($list_data)) {
	$tmp_str .= $line;
	$tmp_str = str_replace('\"firstNode\":[','\"firstNode\":',$tmp_str);
	$tmp_str .= "\"notNode\"";
	if($opt_mode == "0"){
		$tmp_str .= "]}";
	}
} else {
	foreach ($list_data as $row) {
		$data['hash'] = $row[0];
		if($row[4] == ""){
			$data['img'] = "/imgs/op/jyunbi.gif";
		}else{
			$data['img'] = $row[4];
		}
		$data['type'] = 1;
		$data['nick_name'] = addslashes(mb_strimwidth($row[1], 0, 14, ""));
		$chstat = $row[2];
    $data['css'] = $css[$chstat][$row[5]];
    $data['ctop'] = $ctop[$chstat][$row[5]];
    if(2 != $chstat && $row[10] == 1) {
	    $data['css'] = $data['ctop'] = "machi2h";
		}
		if($row[3] == 1){
			$data['cnew'] = ($row[7] == "1") ? "4" : "1";
		}elseif($row[3] == 2){
			$data['cnew'] = ($row[7] == "1") ? "5" : "2";
		}elseif($row[7] == 1){
			$data['cnew'] = "3";
		}else{
			$data['cnew'] = "0";
		}
		if($row[6] == 1){
			$data['voice'] = "1";
		}else{
			$data['voice'] = "0";
		}
		// パフォ写真画質アップ対応
//		if( isset($row[9]) ){
		if( isset($row[9]) && $row[9] != "-" ){
			$data['clip'] = "1";
		}else{
			$data['clip'] = null;
		}

        // どこのデータか？
        $data['zn'] = 'second';

		$line .= makePerson($data);
		$tmp_str .= $line;
		$line = "";
		$i=0;
	}
	$i++;
	$tmp_str .= "]}";
}

echo (str_replace(',]}',']}',$tmp_str));

//	NodeCleate
//-----------------------------------------------------------------
function makePerson($data){
	$nick_name = $data['nick_name'];
	$nick_name = str_replace(" ", "&nbsp;", $nick_name);
	$nick=stripslashes($nick_name);
	$nick=str_replace('"','&quot;',$nick);
	$nick=str_replace('\\','&yen;',$nick);
	$str = <<<EOM
{"hs":"{$data['hash']}","cn":"{$nick}","st":"{$data['ctop']}","cs":"{$data['css']}","cf":"{$data['cnew']}","ph":"{$data['img']}","vo":"{$data['voice']}","cl":"{$data['clip']}","evcnt":"{$data['evcnt']}","zn":"{$data['zn']}","ohs":"{$data['onair_hash']}"},
EOM;
	return $str;

}


function get_eventNode2(&$result, $ownerCd, $aDomainFlg, $eventType, $mval, $db){

	if($eventType[0] == "0"){
		$result = null;
		return null;
	} else {

		if($aDomainFlg == 1){
		    //ワールドマシェリ
		    $world_where = "and female_member.world_flg = 1 ";
		}else{
		    $world_where = "and female_member.world_flg = 0 ";
		}

		//	RoomEventNode
		//--------------------------------------------------------
		$sql  = "";
		$sql  = "select ";
		$sql .= "female_profile.hash,"; 						//0
		$sql .= "female_profile.nick_name,";					//1
		$sql .= "onair.stat, "; 								//2
		$sql .= "female_profile.new_face, ";					//3
		$sql .= "female_profile.img, "; 						//4
		$sql .= "onair.chat_mode, "; 							//5
		$sql .= "female_profile.mic, "; 						//6
		$sql .= "female_member.fine_flg, ";						//7
		$sql .= "onair.mizugi_flg, ";							//8
		$sql .= "female_profile.clip ";							//9 パフォ写真画質アップ対応
		$sql .= ", (SELECT COUNT(*) FROM `chat_log` AS on2 WHERE on2.CHAT_HASH=onair.CHAT_HASH AND on2.CHAT_MODE IN(1,2)) AS online_count ";	// 10
		$sql .= ", onair.chat_hash, ";							// 11
		$sql .= "IFNULL(onair.machiawase_flg, 0) ";						//12
		$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and ";
		$sql .= "(onair.start_date is null or onair.start_date < now())) ";
		$sql .= "LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
		$sql .= "where female_profile.owner_cd = ".$ownerCd." and ";
		$sql .= "onair.owner_cd = ".$ownerCd." and ";
		$sql .= "female_member.owner_cd = ".$ownerCd." and ";
		$sql .= "female_member.stat = 1 and ";
		$sql .= "onair.mizugi_flg = 1  and ";
		$sql .= "female_member.flv2 = 0 $world_where ";
		$sql .= "ORDER BY onair.owner_cd DESC,onair.stat asc, ";
		$sql .= "(case when onair.stat is NULL then female_member.last_login end)desc,onair.chat_mode asc,onair.machiawase_flg,rand()";

		// データキャッシュ(2013.06.10)
		$cache_file = "./data/top_img_event" . $mval . ".cache";
		if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 1 ))) {
			$list_data = unserialize(file_get_contents($cache_file));
		} else {
			$result = $db->query($sql);
			if(DB::isError($result)){
				return null;
			}
			$list_data = array();
			while($row = $result->fetchRow()) {
// clip情報[9]がNullの場合 serialize されないみたいなので
// その対応
				if( !isset($row[9]) || is_null($row[9]) ){
					$row[9] = "-";
				}
				$list_data[] = $row;
			}
			$fp = fopen($cache_file, "w");
			fwrite($fp, serialize($list_data));
			fclose($fp);
		}

		return $list_data;

	}


}



/**
* @name get_partyNode
* @brief partyNode表示用のパフォーマー情報を収集
* @return クエリー実行結果
* @param  result: クエリー実行後のオブジェクト参照
* @param  ownerCd: オーナーコード
* @param  aDomainFlg: ドメイン判定用フラグ
* @param  eventType: イベント開催フラグ
*/
function get_partyNode2(&$result, $ownerCd, $aDomainFlg, $eventType, $mval, $db){

	if($eventType[0] != 0 && $eventType[2] != 9){
		$noroom = " and onair.mizugi_flg = 0 ";
	}else{
		$noroom = "";
	}

	if($aDomainFlg == 1){
	    //ワールドマシェリ
	    $world_where = "and female_member.world_flg = 1 ";
	}else{
	    $world_where = "and female_member.world_flg = 0 ";
	}

	//	PatyRoomNode
	//--------------------------------------------------------
	$sql  = "";
	$sql  = "select ";
	$sql .= "female_profile.hash,"; 						//0
	$sql .= "female_profile.nick_name,";					//1
	$sql .= "onair.stat, "; 								//2
	$sql .= "female_profile.new_face, ";					//3
	$sql .= "female_profile.img, "; 						//4
	$sql .= "onair.chat_mode, "; 							//5
	$sql .= "female_profile.mic, "; 						//6
	$sql .= "female_member.fine_flg, ";						//7
	$sql .= "onair.mizugi_flg, ";						//8
	$sql .= "female_profile.clip ";						//9 パフォ写真画質アップ対応
	$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and ";
	$sql .= "(onair.start_date is null or onair.start_date < now())) ";
	$sql .= "LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
	$sql .= "where female_profile.owner_cd = ".$ownerCd." and ";
	$sql .= "onair.owner_cd = ".$ownerCd." and ";
	$sql .= "female_member.owner_cd = ".$ownerCd." and ";
	$sql .= "female_member.stat = 1 and ";
	$sql .= "onair.chat_mode != 0  and ";
	$sql .= "onair.mizugi_flg <> 1  and ";
	$sql .= "female_member.flv2 = 0 $noroom $world_where";
	$sql .= "order by rand() ";


	// データキャッシュ(2013.06.10)
	$cache_file = "./data/top_img_party" . $mval . ".cache";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 1 ))) {
		$list_data = unserialize(file_get_contents($cache_file));
	} else {
		$result = $db->query($sql);
		if(DB::isError($result)){
			return null;
		}
		$list_data = array();
		while($row = $result->fetchRow()) {
// clip情報[9]がNullの場合 serialize されないみたいなので
// その対応
			if( !isset($row[9]) || is_null($row[9]) ){
				$row[9] = "-";
			}
			$list_data[] = $row;
		}
		$fp = fopen($cache_file, "w");
		fwrite($fp, serialize($list_data));
		fclose($fp);
	}

	return $list_data;

}


/**
* @name get_firstNode
* @brief firstNode表示用のパフォーマー情報を収集
* @return クエリー実行結果
* @param  result: クエリー実行後のオブジェクト参照
* @param  ownerCd: オーナーコード
* @param  aDomainFlg: ドメイン判定用フラグ
* @param  eventType: イベント開催フラグ
* @param  mval: フィルタリングコードを指定
*/
function get_firstNode2(&$result, $ownerCd, $aDomainFlg, $eventType, $mval, $wShowFlg=false, $db){

	if($eventType[0] != 0 &&
	   ( ( $eventType[1] != 9 && $eventType[2] != 9 ) ||
	     ( $eventType[1] != 9 && $eventType[2] != 0 ) ||
	     ( $eventType[2] != 0 && $eventType[2] != 9 ) ) ){
		$sql_where1[0] = " and onair.mizugi_flg = 0 and onair.chat_mode = 0";
	}else{
		$sql_where1[0] = "and onair.chat_mode = 0";
	}

	if($aDomainFlg == 1){
	    //ワールドマシェリ
	    $world_where = "and female_member.world_flg = 1 ";
	}else{
	    $world_where = "and female_member.world_flg = 0 ";
	}

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

	if(preg_match ("/[0-9]+/i",$mval,$regs)){
		$where1 = $sql_where1[$regs[0]];
	}else{
		$where1 = $sql_where1[0];
	}

	//	PageFirstNode
	//--------------------------------------------------------
	$sql  = "";
	    $sql  = "select ";
		$sql .= "female_profile.hash,"; 						//0
		$sql .= "female_profile.nick_name,";					//1
		$sql .= "onair.stat, "; 								//2
		$sql .= "female_profile.new_face, ";					//3
		$sql .= "female_profile.img, "; 						//4
		$sql .= "onair.chat_mode, "; 							//5
		$sql .= "female_profile.mic, "; 						//6
		$sql .= "female_member.fine_flg, ";						//7
		$sql .= "onair.mizugi_flg, ";						//8
		$sql .= "female_profile.clip, ";						//9 パフォ写真画質アップ対応
		$sql .= "if(onair.stat=2, 0, onair.machiawase_flg) as machiawase_flg ";						//10
		$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and ";
		$sql .= "(onair.start_date is null or onair.start_date < now())) ";
		$sql .= "LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
		$sql .= "where female_profile.owner_cd = ".$ownerCd." and ";
		$sql .= "onair.owner_cd = ".$ownerCd." and ";
		$sql .= "female_member.owner_cd = ".$ownerCd." and ";
		$sql .= "female_member.stat = 1 and ";
		$sql .= "female_member.flv2 = 0 ";
		$sql .= "$where1 $world_where ";
		if($aDomainFlg != 1 && $wShowFlg){
            $sql .= " union all ";
            $sql .= "select ";
            $sql .= "cm_information.cm_hash as hash,";			// 0:ハッシュ
            $sql .= "cm_information.cm_title as nick_name,";	// 1:ニックネーム
            $sql .= "1 as stat,";								// 2:スタット
            $sql .= "0 as cnew,";								// 3:新人情報
            $sql .= "cm_information.cm_img as img,";			// 4:画像
            $sql .= "'cm_info' as chat_mode,";					// 5:チャットモード
            $sql .= "0 as mic,";								// 6:マイク
            $sql .= "0 as fine_flg,";							// 7:高画質チェック用
            $sql .= "'cm_info' as mizugi_flg, ";				// 8:別枠
            $sql .= "NULL as clip, ";							// 9:クリップ（パフォ写真画質アップ）
            $sql .= "0 as machiawase_flg ";						// 10
            $sql .= "from  cm_information ";
            $sql .= "where cm_information.cm_stat = 1 ";
		}
		$sql .= "order by machiawase_flg, rand()";

	// データキャッシュ(2013.06.10)
	$cache_file = "./data/top_img_first" . $mval . ".cache";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 1 ))) {
		$list_data = unserialize(file_get_contents($cache_file));
	} else {
		$result = $db->query($sql);
		if(DB::isError($result)){
			return null;
		}
		$list_data = array();
		while($row = $result->fetchRow()) {
// clip情報[9]がNullの場合 serialize されないみたいなので
// その対応
			if( !isset($row[9]) || is_null($row[9]) ){
				$row[9] = "-";
			}
			$list_data[] = $row;
		}
		$fp = fopen($cache_file, "w");
		fwrite($fp, serialize($list_data));
		fclose($fp);
	}

	return $list_data;

}



/**
* @name get_secondNode
* @brief secondNode表示用のパフォーマー情報を収集
* @return クエリー実行結果
* @param  result: クエリー実行後のオブジェクト参照
* @param  ownerCd: オーナーコード
* @param  aDomainFlg: ドメイン判定用フラグ
* @param  mval: フィルタリングコードを指定
*/
function get_secondNode2(&$result, $ownerCd, $aDomainFlg, $eventType, $mval, $db){

	if($aDomainFlg == 1){
	    //ワールドマシェリ
	    $world_where = "and female_member.world_flg = 1 ";
	}else{
	    $world_where = "and female_member.world_flg = 0 ";
	}

	//	PageSecondNode
	//--------------------------------------------------------
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

	if(preg_match ("/[0-9]+/i",$mval,$regs)){
		$where2 = $sql_where2[$regs[0]];
	}else{
		$where2 = $sql_where2[0];
	}

	$sql  = "";
	$sql  = "select ";
	$sql .= "female_profile.hash,"; 						//0
	$sql .= "female_profile.nick_name,";					//1
	$sql .= "onair.stat, "; 								//2
	$sql .= "female_profile.new_face, ";					//3
	$sql .= "female_profile.img, "; 						//4
	$sql .= "onair.chat_mode, "; 							//5
	$sql .= "female_profile.mic, "; 						//6
	$sql .= "female_member.fine_flg, ";						//7
	$sql .= "onair.mizugi_flg, ";						    //8
	$sql .= "female_profile.clip, ";						//9 パフォ写真画質アップ対応
	$sql .= "onair.machiawase_flg ";						//10
	$sql .= "from (onair LEFT JOIN female_profile ON onair.user_id = female_profile.user_id and ";
	$sql .= "(onair.start_date is null or onair.start_date < now())) ";
	$sql .= "LEFT JOIN female_member ON onair.user_id = female_member.user_id ";
	$sql .= "where female_profile.owner_cd = ".$ownerCd." and ";
	$sql .= "onair.owner_cd = ".$ownerCd." and ";

	if($eventType[0] != 0 &&
	   ( ( $eventType[1] != 9 && $eventType[2] != 9 ) ||
	     ( $eventType[1] != 9 && $eventType[2] != 0 ) ||
	     ( $eventType[2] != 0 && $eventType[2] != 9 ) ) ){
		$sql .= "onair.mizugi_flg = 0  and ";
	}

	$sql .= "female_member.owner_cd = ".$ownerCd." and ";
	$sql .= "female_member.stat = 1  and ";
	$sql .= "female_member.flv2 = 0 $where2 $world_where";
	$sql .= "order by onair.machiawase_flg, rand()";

	// データキャッシュ(2013.06.10)
	$cache_file = "./data/top_img_second" . $mval . ".cache";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 1 ))) {
		$list_data = unserialize(file_get_contents($cache_file));
	} else {
		$result = $db->query($sql);
		if(DB::isError($result)){
			return null;
		}
		$list_data = array();
		while($row = $result->fetchRow()) {
// clip情報[9]がNullの場合 serialize されないみたいなので
// その対応
			if( !isset($row[9]) || is_null($row[9]) ){
				$row[9] = "-";
			}
			$list_data[] = $row;
		}
		$fp = fopen($cache_file, "w");
		fwrite($fp, serialize($list_data));
		fclose($fp);
	}

	return $list_data;

}
