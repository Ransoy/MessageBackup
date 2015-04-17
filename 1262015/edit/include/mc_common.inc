<?
/** JSON�ǡ����ɤ߽Ф������ѡ�DB��������ɽ������������� */
require_once 'HTTP/Client.php';
require_once 'Jsphon/Decoder.php';

require_once 'man_menu_head.inc';
require_once 'man_menu_foot.inc';
require_once 'man_menu_left.inc';
require_once 'man_menu_right.inc';

/**
* @file mc_common.inc
* @brief ���̴ؿ�
* @author itk
* @date 2011-09-12
* @attn: mc_session_routines.inc������ɤ߹���Ǥ�
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
* @brief ������������IP���ɤ���
* @return false:(�ǥե����)�����оݤǤϤʤ���true:�����о�
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
* @brief �ޥ�����ȥåץڡ����ν�����ؿ�
* @return false:(�ǥե����)���餫�Υ��顼ȯ��, true:���顼�ʤ�
* @param[out] ���顼�ե饰
*/
function init_check(&$err_flg){
	$retval=false;

	if(is_member_logged()){//���Ǥ˥�������ǧ�ںѤ�
		return true;
	}

	if(is_from_login()){//��������ڡ�����������ܤǤϤʤ�
		$err_flg=1;
		return false;
	}

	if(!isset($_POST['user_id']) || $_POST['user_id']=="" ){//�桼����ID���ʤ�
		return false;
	}
}

/**
* @brief ��������ڡ�����������ܤ��ɤ���
*/
function is_from_login(){
	$retval=false;
	if(isset($_POST['mode2']) && strcmp($_POST['mode2'],"login") == 0){
		$retval=true;
	}
	return $retval;
}

/**
* @brief IP����ȯư�᡼�������
*/
function ip_alert_mail(){
$str = <<<EOM
�ʲ��ΣɣХ��ɥ쥹��ꥢ�����������ꡢ����������ݤ򤷤ޤ�����

���ɣХ��ɥ쥹     {$_SERVER['REMOTE_ADDR']}
���桼�����ɣ�     {$_POST['user_id']}

EOM;
	p_sendmail("info@macherie.tv", "���ݣɣХ��ɥ쥹����Υ��������Τ��Τ餻", $str );
	p_sendmail("customer@macherie.tv", "���ݣɣХ��ɥ쥹����Υ��������Τ��Τ餻", $str );
}

/**
* @name json_decode
* @brief json�ǡ�����ǥ�����
* @return json�ǥ����ɥǡ���
* @param exeScript : POST�¹Ԥ���Script̾�����
* @param mval : ɽ�������ϰϤ����
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
* @brief json�ǡ�������ѥե����ޡ�������������
* @return �ѥե����ޡ��������󥿥�
* @param node : ɽ���оݤΥΡ��ɤ����
* @param decoded : �ǥ������оݤΥǡ��������
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
* @brief �����꡼�¹Է�̤���ѥե����ޡ�������������
* @return �ѥե����ޡ��������󥿥�
* @param result : �����꡼��̤Υ��֥�������
*
*/
function get_targetNode($result){

	$retval = "";

	if(!is_null($result)){

		//���ե饤��
		$ctop[""][""] = "offh";
		$css[""][""] = "off";

		//���ե饤�󤸤�ʤ�������ɡ�onair�����ȤΤ����onair.chat_mode�����ˤʤ뤳�Ȥ�����
		$ctop[1][""] = "offh";
		$css[1][""] = "off";
		$ctop[2][""] = "offh";
		$css[2][""] = "off";
		$ctop[""][1] = "offh";
		$css[""][1] = "off";
		$ctop[""][2] = "offh";
		$css[""][2] = "off";

		//������å�
		$ctop[1][0] = "on2h";
		$css[1][0] = "on";
		//�ѡ��ƥ���
		$ctop[1][1] = "onph";
		$css[1][1] = "pon";
		//�ġ�����å���
		$ctop[2][0] = "2h";
		$css[2][0] = "ch";
		//�ѡ��ƥ�����
		$ctop[2][1] = "ph";
		$css[2][1] = "pc";
		//CM(world)
		$ctop[1]['cm_info'] = "wh";
		$css[1]['cm_info'] = "cm";

		while($row = $result->fetchRow()){

			$cn = stripslashes( addslashes(mb_strimwidth($row[1], 0, 14, "") ) );
			$cn = str_replace(" ", "&nbsp;", $cn);			
			$st = $ctop[$row[2]][$row[5]];
			$cs = $css[$row[2]][$row[5]];
			
			if($row[10] == 1) {
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
				}else if( $row[9] ){ //�������ݤ�
					$retval .= '<li class="pic" style="text-align:center;">';
// ----------------------------------------------------------------------------
// ��Ĺ�ˤʤ�Τ��ɤ� 2013-06-27
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
				$retval .= '<a href="javascript:mO(\''.$row[0].'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

			} else {

				$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'.$row[4].');">';
				$retval .= '<div class="tochat" alt="'.$cn.'" value="'.$row[0].'"></div></li>';
				$retval .= '<li class="name"><a href="javascript:cO(\''.$row[0].'\');">'.$cn.'</a>';
				$retval .='<div class="mail">';

				if($voice == 1){
					$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				$retval .= '<a href="javascript:cO(\''.$row[0].'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

			}

			$retval .= '</li></ul>'."\n";

		}

	}

	return $retval;

}


/**
* @name get_targetNode
* @brief �����꡼�¹Է�̤���ѥե����ޡ�������������
* @return �ѥե����ޡ��������󥿥�
* @param result : �����꡼��̤Υ��֥�������
*
*/
function get_targetNode2($room_id, $result){

	$retval = "";

	if(!is_null($result)){
		//���ե饤��
		$ctop[""][""] = "offh";
		$css[""][""] = "off";

		//���ե饤�󤸤�ʤ�������ɡ�onair�����ȤΤ����onair.chat_mode�����ˤʤ뤳�Ȥ�����
		$ctop[1][""] = "offh";
		$css[1][""] = "off";
		$ctop[2][""] = "offh";
		$css[2][""] = "off";
		$ctop[""][1] = "offh";
		$css[""][1] = "off";
		$ctop[""][2] = "offh";
		$css[""][2] = "off";

		//������å�
		$ctop[1][0] = "on2h";
		$css[1][0] = "on";
		//�ѡ��ƥ���
		$ctop[1][1] = "onph";
		$css[1][1] = "pon";
		//�ġ�����å���
		$ctop[2][0] = "2h";
		$css[2][0] = "ch";
		//�ѡ��ƥ�����
		$ctop[2][1] = "ph";
		$css[2][1] = "pc";
		//CM(world)
		$ctop[1]['cm_info'] = "wh";
		$css[1]['cm_info'] = "cm";

		// �ǡ�������å���(2013.06.10)
		$cache_file = "./data/top_img_" . $room_id . ".cache";
		if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 3 ))) {
			$img_data = unserialize(file_get_contents($cache_file));
		} else {
			$img_data = array();
			while($row = $result->fetchRow()) {
// clip����[9]��Null�ξ�� serialize ����ʤ��ߤ����ʤΤ�
// �����б�
				if( !isset($row[9]) || is_null($row[9]) ){
					$row[9] = "-";
				}
				$img_data[] = $row;
			}
			$fp = fopen($cache_file, "w");
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
			    $st = $ctop[$row[2]][$row[5]];
			    $cs = $css[$row[2]][$row[5]];
				
			    if($room_id == 'event2') {
        			if($row[12] == 1) {
        			  $cs = $st = 'machi2h';
        			}
			    }
			    else {
			      if($row[10] == 1) {
			        $cs = $st = 'machi2h';
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
//					}else if( $row[9] ){ //�������ݤ�
					}else if( isset($row[9]) && $row[9] != "-" ){ //�������ݤ�
						$retval .= '<li class="pic" style="text-align:center;">';
// ----------------------------------------------------------------------------
// ��Ĺ�ˤʤ�Τ��ɤ� 2013-06-27
//						$inner .= "<img src='http://p.macherie.tv/imgs/op/120x90/{$img}' style='width:120px;height:90px;' />";
						$inner .= "<img src='http://p.macherie.tv/imgs/op/120x90/{$img}' style='height:90px;' />";
//						$inner .= "<img src='http://c.macherie.tv/imgs/op/120x90/{$img}' style='height:90px;' />";
// ----------------------------------------------------------------------------
					}else{
						$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'.$img.');">';
//						$retval .= '<li class="pic" style="background-image:url(http://c.macherie.tv/imgs/op/120x90/'.$img.');">';
					}

					// ���奤�٥�ȤΤȤ��̿��α����˥ѡ��ƥ�����å���Υѡ��ƥ������ҤȥΥ��������Ҥι���ͤ�ɽ��
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
					$retval .= '<a href="javascript:mO(\''.$row[0].'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

				} else {

					$retval .= '<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'.$row[4].');">';

					$retval .= '<div class="tochat" alt="'.$cn.'" value="'.$row[0].'"></div></li>';
					$retval .= '<li class="name"><a href="javascript:cO(\''.$row[0].'\');">'.$cn.'</a>';
					$retval .='<div class="mail">';

					if($voice == 1){
						$retval .= '<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
					}
					$retval .= '<a href="javascript:cO(\''.$row[0].'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>'."\n";

				}

				$retval .= '</li></ul>'."\n";

			}
		}

	}

	return $retval;

}



/**
* @name get_MacheriePortal_Code
* @brief �ޥ�����ݡ�����Ƚ��
* @return �ޥ�����ݡ�����Ƚ�ꥳ����(�ǥե�����������Ѥ�)
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
* @brief ����������ƥʥ�ɽ������
* @return false:(�ǥե����)���ƥʥ���Ǥʤ�, true:���ƥʥ���
* @param disps : ɽ�����������0�����
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
		//������ǤϤʤ��Τ�
		return false;
	}
	if(!empty($disps)){
		//���ƥʥ󥹥ڡ���ɽ��
		mailBOXMaintenancePageDisp(1);
	}
	return true;
}
*/

/**
* @name mailBOXMaintenanceFemale
* @brief �ѥե����ޡ��ʽ����˥��ƥʥ�ɽ������
* @return false:(�ǥե����)���ƥʥ���Ǥʤ�, true:���ƥʥ���
* @param disps : ɽ�����������0�����
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
		//������ǤϤʤ��Τ�
		return false;
	}
	if(!empty($disps)){
		//���ƥʥ󥹥ڡ���ɽ��
		mailBOXMaintenancePageDisp(2);
	}
	return true;
}
*/

/**
* @name mailBOXMaintenancePageDisp
* @brief ���̥��ƥʥ󥹲���ɽ��
* @param sex : 1(����)��2(����)
*/

/*
function mailBOXMaintenancePageDisp($sex){
	global $tmpl_dir;

	if($sex == 2){
		//����
		$tmpl = new Tmpl2( "/var/www/livechat/htdocs/template/maintenance/mailmente.html");
		$tmpl->flush();
		exit;
	}else{
		//����
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
* @brief ���ƥʥ󥹲���ɽ��
* @param templateFile : ���ƥʥ���ɽ��html�ե�����
*/
function showMaintenancePage($templateFile){

	$tmpl = new Tmpl2("/var/www/livechat/htdocs/template/maintenance/".$templateFile);
	$tmpl->flush();
	exit;

}

/**
* @name showMaintenanceTabPage
* @brief ���ƥʥ󥹲���ɽ��
* @param pid : �ѥե����ޡ�ID
* @param templateFile : ���ƥʥ���ɽ��html�ե�����
*/
function showMaintenanceTabPage($pid, $templateFile){

	$tmpl = new Tmpl2("/var/www/livechat/htdocs/template/maintenance/".$templateFile);
	$tmpl->assign("hash", $pid);
	$tmpl->flush();
	exit;

}

/**
* @name printOuterFrame
* @brief �岼��������ʬɽ��
* @param tmpl : �ƥ�ץ졼�ȥ��֥�������
* @param title : ɽ���ڡ���̾��
* @param $db : �ǡ����١������֥�������
* @param $ownerCd : �����ʡ�������
*/
function printOuterFrame(&$tmpl, $title, $db = null, $ownerCd = 0){

	//�إå�����˥塼
	$tmpl->assign("head_menu",getMacherieHeadMenu(get_MacheriePortal_Code(), $tmpl->fname, $title, is_member_logged()));

	//����˥塼
	$tmpl->assign("right_menu",getBoyRightMenu(is_member_logged(), get_MacheriePortal_Code()));

	//����˥塼
	$tmpl->assign("left_menu",getMacherieLeftMenu(is_member_logged(), get_MacheriePortal_Code(), $db, $ownerCd, $tmpl->fname));

	//�եå�����˥塼
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