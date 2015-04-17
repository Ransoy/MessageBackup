<?
require_once 'admin.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'CommonDb.php';
require_once 'FormObject.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';
$fobj = new FormObject("ポイント・手数料：管理");				//本フォームは個別の入力チェックが必要
$strWhere = " and owner_cd = " . $_SESSION['ownerCd'] . " ";

/**
 *  Explanation
 * 	Send Message 	=> 191
 *	Send Image 		=> 192	
 *  Receive Message => 193
 *  Receive Image	=> 194
 */
if(isset($_POST['mode'])){
	if($_POST['mode'] == "update"){
		if($_POST['etc191'] != ""){
			$data = array();
			$data['value'] = $_POST['etc191'];
			iTSupdate($data,"point_setting",$_SESSION['id']," id = '191'" . $strWhere);
		}
		// フィリピン女性プレミアムフォトギャラリー加算ポイント
		if($_POST['etc192'] != ""){
			$data = array();
			$data['value'] = $_POST['etc192'];
			iTSupdate($data,"point_setting",$_SESSION['id']," id = '192'" . $strWhere);
		}
		if($_POST['etc193'] != ""){
			$data = array();
			$data['value'] = $_POST['etc193'];
			iTSupdate($data,"point_setting",$_SESSION['id']," id = '193'" . $strWhere);
		}
		if($_POST['etc194'] != ""){
			$data = array();
			$data['value'] = $_POST['etc194'];
			iTSupdate($data,"point_setting",$_SESSION['id']," id = '194'" . $strWhere);
		}
	}
}

if($_POST['point_id']){

	$pId = $_POST['point_id'];
	$pHid = $_POST['point_id_hidden'];

	foreach($pId as $key => $value){
		if($pId[$key] != $pHid[$key]){
			 $data = array();
			$data['value'] = $value;
			iTSupdate($data,"point_setting",$_SESSION['id']," id = '$key' " . $strWhere);
			//echo $strWhere;
		}
	}
}


//テンプレートクラス生成
$tmpl = new Tmpl22($tmpl_dir . "manage/message/message_point_management.html");
$tmpl->dbgmode(0);
$db = new CommonDb();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID > 150 AND ID < 157 ORDER BY ID ASC');
$malePoint = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID > 96 AND ID < 103');
$femaleYen = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID > 102 AND ID < 109');
$femalePoint = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 109 AND ID <= 114');
$imgMalePoint = $db->get();


$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 115 AND ID <= 120');
$imgfemaleYen = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 121 AND ID <= 126');
$imgfemalepoint = $db->get();


$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 127 AND ID <= 132');
$agentPcYen = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 133 AND ID <= 138');
$agentPcPoint = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 139 AND ID <= 144');
$agentSpYen = $db->get();

$db->select('ID, EXPLANATION, VALUE');
$db->from('point_setting');
$db->where('ID >= 145 AND ID <= 150');
$agentSpPoint = $db->get();

$tmpl->loopset('point_message_detail');
for ($i = 0; $i < count($malePoint); $i++) {
	if ($i == 0) {
		$tmpl->assign('show_msg_label', true);
	}
	$senderversion = ($i < 3)? 'PC':'SP';
	$tmpl->assign('sender_verion', $senderversion .'版');
	$tmpl->assign('msg_user_point_des', $malePoint[$i]->EXPLANATION);
	$tmpl->assign('msg_user_point_id', $malePoint[$i]->ID);
	$tmpl->assign('msg_user_point_val', $malePoint[$i]->VALUE);
	$tmpl->assign('msg_female_yen_id', $femaleYen[$i]->ID);
	$tmpl->assign('msg_female_yen_val', $femaleYen[$i]->VALUE);
	$tmpl->assign('msg_female_point_id', $femalePoint[$i]->ID);
	$tmpl->assign('msg_female_point_val', $femalePoint[$i]->VALUE);
	$tmpl->assign('agent_yen_id', $agentPcYen[$i]->ID);
	$tmpl->assign('agent_yen_val', $agentPcYen[$i]->VALUE);
	$tmpl->assign('agent_point_id', $agentPcPoint[$i]->ID);
	$tmpl->assign('agent_point_val', $agentPcPoint[$i]->VALUE);
	$tmpl->loopnext();
}
$tmpl->loopset('');
$tmpl->assign("msg","");


if($_POST['Imgpoint_id']){
	echo '<pre>';
	print_r($_POST['Imgpoint_id']);
	echo '</pre>';
}

$tmpl->loopset('imgpoint_message_detail');
for ($i = 0; $i < count($imgMalePoint); $i++) {
	if ($i == 0) {
		$tmpl->assign('show_msg_label', true);
	}
	$senderversion = ($i < 3)? 'PC':'SP';
	$tmpl->assign('img_sender_verion', $senderversion .'版');
	$tmpl->assign('img_user_point_des', $imgMalePoint[$i]->EXPLANATION);
	$tmpl->assign('img_user_point_id', $imgMalePoint[$i]->ID);
	$tmpl->assign('img_user_point_val', $imgMalePoint[$i]->VALUE);
	$tmpl->assign('img_female_yen_id', $imgfemaleYen[$i]->ID);
	$tmpl->assign('img_female_yen_val', $imgfemaleYen[$i]->VALUE);
	$tmpl->assign('img_female_point_id', $imgfemalepoint[$i]->ID);
	$tmpl->assign('img_female_point_val', $imgfemalepoint[$i]->VALUE);
	$tmpl->assign('agent_syen_id', $agentSpYen[$i]->ID);
	$tmpl->assign('agent_syen_val', $agentSpYen[$i]->VALUE);
	$tmpl->assign('agent_spoint_id', $agentSpPoint[$i]->ID);
	$tmpl->assign('agent_spoint_val', $agentSpPoint[$i]->VALUE);
	$tmpl->loopnext();
}
$tmpl->loopset('');
$tmpl->assign("msg","");


$tmpl->flush();
