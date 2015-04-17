<?
/** JSONデータ読み出し時使用（DBアクセス表示完成時削除） */
require_once 'HTTP/Client.php';
require_once 'Jsphon/Decoder.php';

require_once 'man_menu_head.inc';
require_once 'man_menu_foot.inc';
require_once 'man_menu_left.inc';
require_once 'man_menu_right.inc';

/**
* @file mc_common.inc
* @brief 共通関数
* @author itk
* @date 2011-09-12
* @attn: mc_session_routines.incを先に読み込んでね
*/

define ("MACHERIE_NORMAL",	1);
define ("MACHERIE_WORLD",	2);
define ("MACHERIE_BIGLOBE",	3);
define ("MACHERIE_RAKUTEN",	4);
define ("MACHERIE_CINEMA",	5);

define ("MAINTENANCE_MODE_MAIL",	false);

define ("MAINTENANCE_SYSTEM",	"index.html");
define ("MAINTENANCE_MAIL",	"mailmente.html");
define ("MAINTENANCE_CHAT",	"mailmente_chat_neo.html");
define ("MAINTENANCE_IMACHERIE",	"mailmente_imacherie.html");


/**
* @brief アクセス拒否IPかどうか
* @return false:(デフォルト)拒否対象ではない、true:拒否対象
*/
function is_banned_ip(){
	$retval=false;

	if(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == "124.27.137.130"
		|| $_SERVER['REMOTE_ADDR'] == "124.24.206.195"
    )){
		$retval=true;
	}
	return $retval;
}

/**
* @brief マシェリトップページの初期化関数
* @return false:(デフォルト)何らかのエラー発生, true:エラーなし
* @param[out] エラーフラグ
*/
function init_check(&$err_flg){
	$retval=false;

	if(is_member_logged()){//すでにログイン認証済み
		return true;
	}

	if(is_from_login()){//ログインページからの遷移ではない
		$err_flg=1;
		return false;
	}

	if(!isset($_POST['user_id']) || $_POST['user_id']=="" ){//ユーザーIDがない
		return false;
	}
}

/**
* @brief ログインページからの遷移かどうか
*/
function is_from_login(){
	$retval=false;
	if(isset($_POST['mode2']) && strcmp($_POST['mode2'],"login") == 0){
		$retval=true;
	}
	return $retval;
}

/**
* @brief IP拒否発動メールを送る
*/
function ip_alert_mail(){
$str = <<<EOM
以下のＩＰアドレスよりアクセスがあり、ログイン拒否をしました。

・ＩＰアドレス     {$_SERVER['REMOTE_ADDR']}
・ユーザーＩＤ     {$_POST['user_id']}

EOM;
	p_sendmail("info@macherie.tv", "拒否ＩＰアドレスからのアクセスのお知らせ", $str );
	p_sendmail("customer@macherie.tv", "拒否ＩＰアドレスからのアクセスのお知らせ", $str );
}

/**
* @name json_decode
* @brief jsonデータをデコード
* @return jsonデコードデータ
* @param exeScript : POST実行するScript名を指定
* @param mval : 表示する範囲を指定
*
*/
function json_decode($exeScript, $mval){

	global $aDomainFlg;

	$client =& new HTTP_Client();
	if($aDomainFlg == 1){
		$client->post('http://world.macherie.tv/'.$exeScript, $mval);
	} else {
		$client->post('http://www.macherie.tv/'.$exeScript, $mval);
	}
	$response = $client->currentResponse();

	$js = mb_convert_encoding($response["body"], "EUC-JP", "SHIFT_JIS");

	$json = new Jsphon_Decoder();
	return $json->decode($js);

}

/**
* @name json_GetNode
* @brief jsonデータからパフォーマーアイコンを作成
* @return パフォーマーアイコンタグ
* @param node : 表示対象のノードを指定
* @param decoded : デコード対象のデータを指定
*
*/
function json_GetNode($node, $decoded){

	$retval = "";

	foreach($decoded[$node] as $k1 => $v1){

		$retval .= '<ul class="'.$v1["cs"].' gB">'."\n";
		$retval .= '<li class="tag"><img src="/images/face/g/'.$v1["st"].'.gif" alt="'.$v1["cn"].'" width="89" height="27" />';

		switch ($v1["cf"]) {
			case "1":
				$retval .= '<img src="/images/face/g/new.gif" width="27" height="27" />';
				break;
			case "2":
				$retval .= '<img src="/images/face/g/debut.gif" width="27" height="27" />';
				break;
			case "3":
				$retval .= '<img src="/images/face/g/fine.gif" width="27" height="27" />';
				break;
			case "4":
				$retval .= '<img src="/images/face/g/fine_new.gif" width="27" height="27" />';
				break;
			case "5":
				$retval .= '<img src="/images/face/g/fine_debut.gif" width="27" height="27" />';
				break;
			case "7":
				$retval .= '<img src="/images/face/g/fine_check.gif" width="27" height="27" />';
				break;
			case "8":
				$retval .= '<img src="/images/face/g/checkmark.gif" width="27" height="27" />';
				break;
		}

		$retval .= '</li>'."\n";

		if($v1["cs"] != "cm"){

			$retval .= '<li class="pic" style="background-image:url(./imgs/op/120x90/'.$v1["ph"].');">';
			$retval .= '<div class="tochat" alt="'.$v1["cn"].'" value="'.$v1["hs"].'"></div></li>';
			$retval .= '<li class="name"><a href="javascript:pO(\''.$v1["hs"].'\');">'.$v1["cn"].'</a>';
			$retval .='<div class="mail">';

			if($v1["vo"] == 1){
				$retval .= '<img src="/images/face/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
			}
			$retval .= '<a href="javascript:mO(\''.$v1["hs"].'\');"><img src="/images/face/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

		} else {

			$retval .= '<li class="pic" style="background-image:url(/imgs/cm/120x90/'.$v1["ph"].');">';
			$retval .= '<div class="tochat" alt="'.$v1["cn"].'" value="'.$v1["hs"].'"></div></li>';
			$retval .= '<li class="name"><a href="javascript:cO(\''.$v1["hs"].'\');">'.$v1["cn"].'</a>';
			$retval .='<div class="mail">';

			if($v1["vo"] == 1){
				$retval .= '<img src="/images/face/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
			}
			$retval .= '<a href="javascript:cO(\''.$v1["hs"].'\');"><img src="/images/face/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

		}

		$retval .= '</li></ul>'."\n";

	}

	return $retval;

}

/**
* @name get_targetNode
* @brief クエリー実行結果からパフォーマーアイコンを作成
* @return パフォーマーアイコンタグ
* @param result : クエリー結果のオブジェクト
*
*/
function get_targetNode($result){

	$retval = "";

	if(!is_null($result)){

		/* added 2/5/2014 to make the profile image url raltive and dynamic */
		$profImgBase = 'http://p.macherie.tv';
		// in dev environment
		if ('mache-dev.vjsol.jp' == $_SERVER['HTTP_HOST']) {
			$profImgBase = '';
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

		while($row = $result->fetchRow()){

			$cn = stripslashes( addslashes(mb_strimwidth($row[1], 0, 14, "") ) );
			$cn = str_replace(" ", "&nbsp;", $cn);

			$chstat = $row[2];
			$st = $ctop[$chstat][$row[5]];
			$cs = $css[$chstat][$row[5]];

			// if not on chat
			if(2 != $chstat && $row[10] == 1) {
			  $cs = $st = 'machi2h';
			}
			//$st = $ctop[$row[2]][$row[5]];


			if(empty($st)){
				$st = "offh";
			}

			if(empty($cs)){
				$cs = "off";
			}

			$retval .= '<ul class="'.$cs.' gB">'."\n";
			$retval .= '<li class="tag"><img src="http://c.macherie.tv/c/d/images/common/g/'.$st.'.gif" alt="'.$cn.'" width="89" height="27" />';

			if($row[4] == ""){
				$img = "/imgs/op/jyunbi.gif";
			}else{
				$img = $row[4];
			}
			if($row[6] == 1){
				$voice = "1";
			}else{
				$voice = "0";
			}

			if($row[3] == 1){
				$cnew = ($row[7] == "1") ? "4" : "1";
			}elseif($row[3] == 2){
				$cnew = ($row[7] == "1") ? "5" : "2";
			}elseif($row[7] == 1){
				$cnew = "3";
			}else{
				$cnew = "0";
			}

			switch ($cnew) {
				case "1":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/new.gif" width="27" height="27" />';
					break;
				case "2":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/debut.gif" width="27" height="27" />';
					break;
				case "3":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine.gif" width="27" height="27" />';
					break;
				case "4":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_new.gif" width="27" height="27" />';
					break;
				case "5":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_debut.gif" width="27" height="27" />';
					break;
				case "7":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_check.gif" width="27" height="27" />';
					break;
				case "8":
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/checkmark.gif" width="27" height="27" />';
					break;
			}

			$retval .= '</li>'."\n";

			if($cs != "cm"){

				$inner = "";
				if(strcmp($img, "/imgs/op/jyunbi.gif") == 0){
					$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv'.$img.');">';
				}else if( $row[9] ){ //高画質か否か
					$retval .= '<li class="pic" style="text-align:center;">';
// ----------------------------------------------------------------------------
// 横長になるのを防ぐ 2013-06-27
//					$inner .= "<img src='http://p.macherie.tv/imgs/op/120x90/{$img}' style='width:120px;height:90px;' />";
					$inner .= "<img src='http://p.macherie.tv/imgs/op/120x90/{$img}' style='height:90px;' />";
// ----------------------------------------------------------------------------
				}else{
					$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'.$img.');">';
				}

				$retval .= "<div class='tochat' alt='{$cn}' value='{$row[0]}'>{$inner}</div></li>";
				$retval .= '<li class="name"><a href="javascript:pO(\''.$row[0].'\');">'.$cn.'</a>';
				$retval .= '<div class="mail">';

				if($voice == 1){
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				$retval .= '<a href="javascript:mO(\''.$row[0].'\');" target="_blank"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

			} else {

				$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'.$row[4].');">';
				$retval .= '<div class="tochat" alt="'.$cn.'" value="'.$row[0].'"></div></li>';
				$retval .= '<li class="name"><a href="javascript:cO(\''.$row[0].'\');">'.$cn.'</a>';
				$retval .='<div class="mail">';

				if($voice == 1){
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				$retval .= '<a href="javascript:cO(\''.$row[0].'\');" target="_blank"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

			}

			$retval .= '</li></ul>'."\n";

		}

	}

	return $retval;

}


/**
* @name get_targetNode
* @brief クエリー実行結果からパフォーマーアイコンを作成
* @return パフォーマーアイコンタグ
* @param result : クエリー結果のオブジェクト
*
*/
function get_targetNode2($room_id, $result){

	$retval = "";

	if(!is_null($result)){

		/* added 2/5/2014 to make the profile image url raltive and dynamic */
		$profImgBase = 'http://p.macherie.tv';
		// in dev environment
		if ('mache-dev.vjsol.jp' == $_SERVER['HTTP_HOST']) {
			$profImgBase = '';
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

		// データキャッシュ(2013.06.10)
		$cache_file = "./data/top_img_" . $room_id . ".cache";
		if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 3 ))) {
			$img_data = unserialize(file_get_contents($cache_file));
		} else {
			$img_data = array();
			while($row = $result->fetchRow()) {
// clip情報[9]がNullの場合 serialize されないみたいなので
// その対応
				if( !isset($row[9]) || is_null($row[9]) ){
					$row[9] = "-";
				}
				$img_data[] = $row;
			}
			$fp = @fopen($cache_file, "w");
			if ( $fp ){
				if (flock($fp, LOCK_EX | LOCK_NB)) {
				    fwrite($fp, serialize($img_data));
				    flock($fp, LOCK_UN);
			    }
			    fclose($fp);
			}
		}

		if (is_array($img_data) && count($img_data) > 0) {
			foreach ($img_data as $row) {

				$cn = stripslashes( addslashes(mb_strimwidth($row[1], 0, 14, "") ) );
				$cn = str_replace(" ", "&nbsp;", $cn);

				$chstat = $row[2];
				$st = $ctop[$chstat][$row[5]];
				$cs = $css[$chstat][$row[5]];

				// if not on chat
				if(2 != $chstat){
				  // if event, machiawase is at 12
				  if('event' === substr($room_id, 0, 5)) {
						if($row[12] == 1){
							$cs = $st = 'machi2h';
						}
				  }else{ // machiawase is at 10
						if($row[10] == 1) {
							$cs = $st = 'machi2h';
						}
				  }
				}

				if(empty($st)){
					$st = "offh";
				}

				if(empty($cs)){
					$cs = "off";
				}

				$retval .= '<ul class="'.$cs.' gB">'."\n";
				$retval .= '<li class="tag"><img src="http://c.macherie.tv/c/d/images/common/g/'.$st.'.gif" alt="'.$cn.'" width="89" height="27" />';

				if($row[4] == ""){
					$img = "/imgs/op/jyunbi.gif";
				}else{
					$img = $row[4];
				}
				if($row[6] == 1){
					$voice = "1";
				}else{
					$voice = "0";
				}

				if($row[3] == 1){
					$cnew = ($row[7] == "1") ? "4" : "1";
				}elseif($row[3] == 2){
					$cnew = ($row[7] == "1") ? "5" : "2";
				}elseif($row[7] == 1){
					$cnew = "3";
				}else{
					$cnew = "0";
				}

				switch ($cnew) {
					case "1":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/new.gif" width="27" height="27" />';
						break;
					case "2":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/debut.gif" width="27" height="27" />';
						break;
					case "3":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine.gif" width="27" height="27" />';
						break;
					case "4":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_new.gif" width="27" height="27" />';
						break;
					case "5":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_debut.gif" width="27" height="27" />';
						break;
					case "7":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/fine_check.gif" width="27" height="27" />';
						break;
					case "8":
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/checkmark.gif" width="27" height="27" />';
						break;
				}

				$retval .= '</li>'."\n";

				if($cs != "cm"){
/*
					if(strcmp($img, "/imgs/op/jyunbi.gif") == 0){
						$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv'.$img.');">';
					} else {
						$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'.$img.');">';
					}
					$retval .= '<div class="tochat" alt="'.$cn.'" value="'.$row[0].'"></div></li>';
					$retval .= '<li class="name"><a href="javascript:pO(\''.$row[0].'\');">'.$cn.'</a>';
					$retval .='<div class="mail">';
*/
					$inner = "";
					if(strcmp($img, "/imgs/op/jyunbi.gif") == 0){
						$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv'.$img.');">';
//					}else if( $row[9] ){ //高画質か否か
					}else if( isset($row[9]) && $row[9] != "-" ){ //高画質か否か
						$retval .= '<li class="pic" style="text-align:center;">';
// ----------------------------------------------------------------------------
// 横長になるのを防ぐ 2013-06-27
//						$inner .= "<img src='http://p.macherie.tv/imgs/op/120x90/{$img}' style='width:120px;height:90px;' />";
						$inner .= "<img src='$profImgBase/imgs/op/120x90/{$img}' style='height:90px;' />";
//						$inner .= "<img src='http://c.macherie.tv/imgs/op/120x90/{$img}' style='height:90px;' />";
// ----------------------------------------------------------------------------
					}else{
						$retval .= '<li class="pic" style="background-image:url('.$profImgBase.'/imgs/op/120x90/'.$img.');">';
//						$retval .= '<li class="pic" style="background-image:url(http://c.macherie.tv/imgs/op/120x90/'.$img.');">';
					}

					// 水着イベントのとき写真の右下にパーティチャット中のパーティ入室客とノゾキ入室客の合計値を表示
					if (preg_match("/^event[0-9a-zA-Z]*/", $room_id) && $row[2]=='2' && $row[5]=='1' && $row[10]>0) {
						$cnt = $row[10];
						$px = ($cnt>=10) ? '90px' : '100px';
						$retval.= '<div style="position:relative; text-align:left; font-size:25px; top:60px; left:' . $px . ';" id="div_' . $row[11] . '">
								<span style="position: absolute;font-weight:bold;color:#ff3f00;" class="event_counter" id="' . $row[11] . '">' . $cnt . '</span>
							</div>';
					}
					$retval .= "<div class='tochat' alt='{$cn}' value='{$row[0]}'>{$inner}</div></li>";
					$retval .= '<li class="name"><a href="javascript:pO(\''.$row[0].'\');">'.$cn.'</a>';
					$retval .= '<div class="mail">';

					if($voice == 1){
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
					}
					$retval .= '<a href="javascript:mO(\''.$row[0].'\');" target="_blank"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

				} else {

					$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'.$row[4].');">';

					$retval .= '<div class="tochat" alt="'.$cn.'" value="'.$row[0].'"></div></li>';
					$retval .= '<li class="name"><a href="javascript:cO(\''.$row[0].'\');">'.$cn.'</a>';
					$retval .='<div class="mail">';

					if($voice == 1){
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
					}
					$retval .= '<a href="javascript:cO(\''.$row[0].'\');" target="_blank"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

				}

				$retval .= '</li></ul>'."\n";

			}
		}

	}

	return $retval;

}



/**
* @name get_MacheriePortal_Code
* @brief マシェリポータル判定
* @return マシェリポータル判定コード(デファイン値定義済み)
*/
function get_MacheriePortal_Code() {

	if(preg_match('/biglobe/',$_SERVER['PHP_SELF'])){
		return MACHERIE_BIGLOBE;
	} else if(preg_match('/cinema/',$_SERVER['PHP_SELF'])){
		return MACHERIE_CINEMA;
	} else if(preg_match('/rakuten/',$_SERVER['PHP_SELF'])){
		return MACHERIE_RAKUTEN;
	} else if($_SERVER["HTTP_HOST"] == "world.macherie.tv"){
		return MACHERIE_WORLD;
	} else {
		return MACHERIE_NORMAL;
	}

}

/**
* @name mailBOXMaintenanceMale
* @brief 男性会員メンテナンス表示管理
* @return false:(デフォルト)メンテナンス中でない, true:メンテナンス中
* @param disps : 表示する場合は非0を指定
*/

/*
function mailBOXMaintenanceMale($disps=0){
	//FLG
	$flg = false;
	//$flg = true;

	if(isset($_SESSION['user_id'])
	 && $_SESSION['user_id'] == "stanaka"
	){
		$flg = false;
		//$flg = true;
	}
	if(!$flg){
		//メンテ中ではないので
		return false;
	}
	if(!empty($disps)){
		//メンテナンスページ表示
		mailBOXMaintenancePageDisp(1);
	}
	return true;
}
*/

/**
* @name mailBOXMaintenanceFemale
* @brief パフォーマー（女性）メンテナンス表示管理
* @return false:(デフォルト)メンテナンス中でない, true:メンテナンス中
* @param disps : 表示する場合は非0を指定
*/

/*
function mailBOXMaintenanceFemale($disps=0){
	//FLG
	$flg = false;
	//$flg = true;

	if(isset($_SESSION['user_id'])
	 && $_SESSION['user_id'] == "stgirl"
	){
		$flg = false;
		//$flg = true;
	}
	if(!$flg){
		//メンテ中ではないので
		return false;
	}
	if(!empty($disps)){
		//メンテナンスページ表示
		mailBOXMaintenancePageDisp(2);
	}
	return true;
}
*/

/**
* @name mailBOXMaintenancePageDisp
* @brief 性別メンテナンス画面表示
* @param sex : 1(男性)，2(女性)
*/

/*
function mailBOXMaintenancePageDisp($sex){
	global $tmpl_dir;

	if($sex == 2){
		//女性
		$tmpl = new Tmpl2( "/var/www/livechat/htdocs/template/maintenance/mailmente.html");
		$tmpl->flush();
		exit;
	}else{
		//男性
		if(isset($_SERVER["PHP_SELF"])){
			if(preg_match("/\/chat\/mail.php/",$_SERVER["PHP_SELF"])){
				$tmpl = new Tmpl2( "/var/www/livechat/htdocs/template/maintenance/mailmente_chat.html");
				$tmpl->flush();
				exit;
			}
			if(preg_match("/\/imacherie\/i_mailbox.php/",$_SERVER["PHP_SELF"])){
				$tmpl = new Tmpl2( "/var/www/livechat/htdocs/template/maintenance/mailmente_imacherie.html");
				$tmpl->flush();
				exit;
			}
		}
	}
	$tmpl = new Tmpl2( "/var/www/livechat/htdocs/template/maintenance/mailmente.html");
	$tmpl->flush();
	exit;

}
*/

/**
* @name showMaintenancePage
* @brief メンテナンス画面表示
* @param templateFile : メンテナンス中表示htmlファイル
*/
function showMaintenancePage($templateFile){

	$tmpl = new Tmpl2("/var/www/livechat/htdocs/template/maintenance/".$templateFile);
	$tmpl->flush();
	exit;

}

/**
* @name showMaintenanceTabPage
* @brief メンテナンス画面表示
* @param pid : パフォーマーID
* @param templateFile : メンテナンス中表示htmlファイル
*/
function showMaintenanceTabPage($pid, $templateFile){

	$tmpl = new Tmpl2("/var/www/livechat/htdocs/template/maintenance/".$templateFile);
	$tmpl->assign("hash", $pid);
	$tmpl->flush();
	exit;

}

/**
* @name printOuterFrame
* @brief 上下左右枠部分表示
* @param tmpl : テンプレートオブジェクト
* @param title : 表次ページ名称
* @param $db : データベースオブジェクト
* @param $ownerCd : オーナーコード
*/
function printOuterFrame(&$tmpl, $title, $db = null, $ownerCd = 0){

	//ヘッダーメニュー
	$tmpl->assign("head_menu",getMacherieHeadMenu(get_MacheriePortal_Code(), $tmpl->fname, $title, is_member_logged()));

	//右メニュー
	$tmpl->assign("right_menu",getBoyRightMenu(is_member_logged(), get_MacheriePortal_Code()));

	//左メニュー
	$tmpl->assign("left_menu",getMacherieLeftMenu(is_member_logged(), get_MacheriePortal_Code(), $db, $ownerCd, $tmpl->fname));

	//フッターメニュー
	$tmpl->assign("foot_menu",getMacherieFootMenu(get_MacheriePortal_Code(), $tmpl->fname));

}


if (!isset($_SESSION)) {
	session_start();
}
//save or clear filters
if (isset($_SESSION['save_filter'])) {
	unset($_SESSION['save_filter']);
	$save = true;
}

?>
