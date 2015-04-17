<?php
/**
* @file index_ajax.php
* @brief top�ڡ�����jquery-ajax�� (����̵�̤ʤΤ�ä���)
* @author itk
* @date 2011-09-02
*/
require_once 'common_proc.inc';
require_once 'mc_common.inc';
require_once 'mc_session_routines.inc';
//require_once 'mc_db_sashi.inc';
require_once 'mc_db.inc';
require_once 'tmpl2.class.inc';
require_once MODELS_DIR . '/sche_top_flash.php';
require_once MODELS_DIR . '/sche_master.php';

//require_once 'sp_check.inc'; // ���ޥۥ����å��� 2013-01-23

require_once 'sp/common_proc.inc';
global $dbSlave;

if(!isset($_SESSION)){
	session_start();
}

//DB
$db      = new mcDB(getRandomSlave());
$dbSlave = $db->get_resource();


//���ޡ��ȥե���Ƚ��
if(isSmartPhone()){

	//���ޡ��ȥե���ʤΤǥ���ɥ��ɤ�ios��
	/*
	if(isAndroid()){
		if($_COOKIE['sp'] && $_COOKIE['sp'] == 1){

		}
		else{
			$sp_param = "";
			if(!empty($_SERVER["QUERY_STRING"])){
				$sp_param = "?".$_SERVER["QUERY_STRING"];
			}
			Header("Location: /sp/{$sp_param}");
			exit;
		}
	}
	else if(isIos()){

	}
	*/

	if($_COOKIE['sp'] && $_COOKIE['sp'] == 1){

	}
	else{
		$sp_param = "";
		if(!empty($_SERVER["QUERY_STRING"])){
			$sp_param = "?".$_SERVER["QUERY_STRING"];
		}
		Header("Location: /sp/{$sp_param}");
		exit;
	}
}


//���å����(SSL)��³
//continue_sslSession($ownerCd, new mcDB(getRandomSlave()));
//continue_sslSession($ownerCd, $db);

//������������IP?
if(is_banned_ip()){ exit; }

//�������Ȥ��Ƥ���������Ƚ���
if(isset($_POST['logout']) && $_POST['logout'] == "1"){logout();}

//������������̵����
if(check_tmpflag()!=NULL){clear_tmpflag();}



/*
//���Ф餯�Ȥ�ʤ��Τǡ��Ȥ��ޤǥ����ȥ�����: 2011-09-12

//���ƥʥ󥹾��֤μ���:������ʤ����ڡ���ɽ��
if($db->is_on_maintainance_now()){
	//$tmpl = new Tmpl2($tmpl_dir . "charity.html");
	//$tmpl->flush();
	showMaintenancePage(MAINTENANCE_SYSTEM);
	exit;
}
*/



//IP���ݡ�����Ͽɽ��
//����URL����Υ�����POST�б������ɤǤ���(�̾���̲ᤷ�ޤ���)

$disp_login_error = 0;
if(!init_check($disp_login_error)){//�����󥢥�����󤢤�

	if($db->login($ownerCd,$_POST['user_id'],$_POST['password'],$err_code,$nickname,$stat)){//����������
		$dbm = new mcDBM(getMaster());
		$dbMaster = $dbm->get_resource();
		$dbm->male_login($ownerCd,$_POST['user_id']);
		if(strcmp($stat,"0")==0){//����Ͽ�桼�����Ǥ�
			set_status("boyslogin_buy");
			$tmpl = new Tmpl2($tmpl_dir . "member_tmp.html");
			$tmpl->flush();
			exit;
		}
		set_status("boyslogin");
		set_uid($_POST['user_id']);
		set_pass($_POST['password']);
		set_nickname($nickname);

		clear_login();
		if(isset($_POST['save']) && strcmp($_POST['save'],"1")==0){
			save_login();
		}

	}else{//��������
		switch($err_code){
			case "nbc":break;//Naked Boys�Υ����ڡ���Ŭ�Ѻ�
			case "nouid":break;//������user_id�ʤ�
			case "ban_ip"://����IP����Υ��������ʤΤǥ��顼�ڡ���ɽ��
				ip_alert_mail();
				$tmpl = new Tmpl2($tmpl_dir . "login.html");
				$tmpl->dbgmode(0);
				$tmpl->flush();
				exit;break;
			case "wrong_pw":sleep(10);break;//�ѥ��㤤
			default:break;

		}
	}
}

// ����20ʬ̵�������ڡ�����ե饰����
$schemaster = new sche_master(DBS1_CD);
$free20_flg = $schemaster->getDispTopFree20(1, HONKE);

// �ޥ����Х������
$disp_data = $schemaster->getDispDataRow(2,HONKE);

//�ƥ�ץ�ե���������
if($aDomainFlg == 1){
	//���ɥޥ�����
	$tmpl = new Tmpl2($tmpl_dir . "index_world.html");
} elseif ($free20_flg) {
	// ����20ʬ̵�������ڡ�����
	$tmpl = new Tmpl2("{$tmpl_dir}index_debut_event.html");
}else if($aDomainFlg == 2){
	//SR
    //$tmpl = new Tmpl2("xxsato/" . "index_sr.html");
	//$tmpl = new Tmpl2($tmpl_dir . "index.html");
	$tmpl = new Tmpl2("{$tmpl_dir}index_test.html");
}else{
	//�̾�
	//$tmpl = new Tmpl2($tmpl_dir . "index.html");
	$tmpl = new Tmpl2("{$tmpl_dir}index_test.html");
}

if ($disp_data['text2']<>'') {
	$tmpl->assign("variety_msg", $disp_data['text2']);
} else {
	$tmpl->assign("variety_msg", "");
}

//������������
$tmpl->assign("aTime", date("Y-m-d H:i:s"));


// �ȥåײ����
$topflash = new sche_top_flash(DBS1_CD);
$list_data = $topflash->getDispDataRow(HONKE);
if ($list_data) {
    $tmpl->assign('top_images_file', IMG_URL . 'top/' . $list_data['filename']);
} else {
    $tmpl->assign('top_images_file', 'images/top/flash.swf');
}

/*
 *���ޥ�ͶƳ�Хʡ�ɽ��
 */
if(isSmartPhone()){
	if($_COOKIE['sp'] && $_COOKIE['sp'] == 1){
		 $tmpl->assign('sp_disp', '');
	}
}

/*
//Template
if(is_smartphone()){
	switch($aDomainFlg){
		case 1: $tmpl= new Tmpl22($tmpl_dir . "index_world.html");break;
		case 2: $tmpl= new Tmpl22("xxx-itk-t/" . "index_sr2.html");break;
		default: $tmpl=new Tmpl22("xxx-itk-t/" . "index-itk3-sp.html");break;
	}
}else{
	switch($aDomainFlg){
		case 1: $tmpl= new Tmpl22($tmpl_dir . "index_world.html");break;
		case 2: $tmpl= new Tmpl22("xxx-itk-t/" . "index_sr2.html");break;
		//default: $tmpl=new Tmpl22("xxx-itk-t/" . "index-itk3.html");break;
		default: $tmpl=new Tmpl22("xxx-itk-t/" . "index-itk3-sp.html");break;
	}
}
*/

//���𥳡���
if(isset($_GET)) {
	foreach($_GET as $key => $value) {
		if($key != "m") {
			setcookie ("Advc", "$key", time()+30*24*60*60, "/");
		}
		if(strstr($key, "afid")) {
			setcookie ("Advc_preaf", "$value", time()+30*24*60*60, "/");
		}
	}
}

//	��������Ȥ�����
//------------------------------------------------

/*
//�����Ȥ������ʻ��ޤǥ����ȥ�����: 2011-09-12

// ddos���б��˥塼����
if($db->is_on_ddos_now()){
	$tmpl->assign("ddos","");
}
*/
//̵���Хʡ��������󤷤Ƥʤ��ä���ɽ��
$tmpl->assign('banner_disp_login_area', '');
if(!is_member_logged()){
	$tmpl->assign("muryo_banner","");


	require_once MODELS_DIR . '/sche_right_banner.php';
	$right_banner_models = new sche_right_banner(DBS1_CD);
	$arr = $right_banner_models->getDispDataFromSiteflgArea('1', '3', '1');
	// if (count($arr)>0) {
		$tmpl->assign('banner_disp_login_area', '<div id="gw_login" class="cf">
	<ul>
		<li>
			<a href="/top_login.php"><img src="http://macherie.tv/images/gw_2014/gw_login.jpg" alt="������" /></a>
		</li>
		<li><a href="./registrationfree.php"><img src="http://macherie.tv/images/gw_2014/gw_regist.jpg" alt="��Ͽ" /></a></li>
		<li class="gw_other_login">
			<ul>
				<li>
					<a href="http://www.macherie.tv/open_id/login.php?id=yahoo">
						<img src="http://macherie.tv/images/gw_2014/gw_ID01.jpg" alt="MACHERIETV/yahoo" />
					</a>
				</li>

				<li>
					<a href="http://www.macherie.tv/open_id/login.php?id=google">
						<img src="http://macherie.tv/images/gw_2014/gw_ID02.jpg" alt="google" />
					</a>
				</li>

				<li>
					<a href="http://www.macherie.tv/open_id/twitter_login.php">
						<img src="http://macherie.tv/images/gw_2014/gw_ID03.jpg" alt="twitter" />
					</a>
				</li>

				<li>
					<a href="http://www.macherie.tv/open_id/facebook_login.php">
						<img src="http://macherie.tv/images/gw_2014/gw_ID04.jpg" alt="facebook" />
					</a>
				</li>

				<li>
					<form method="post" action="https://api.id.rakuten.co.jp/openid/auth">
					<input type="hidden" value="http://specs.openid.net/auth/2.0" name="openid.ns" />
					<input type="hidden" value="http://www.macherie.tv/rakuten/if/login.php" name="openid.return_to" />
					<input type="hidden" value="http://specs.openid.net/auth/2.0/identifier_select" name="openid.claimed_id" />
					<input type="hidden" value="http://specs.openid.net/auth/2.0/identifier_select" name="openid.identity" />
					<input type="hidden" value="checkid_setup" name="openid.mode" />
					<input type="image" title="��ŷID�ǥ�������Ͽ" alt="��ŷID�ǥ�������Ͽ" onmouseout="this.src=\'http://macherie.tv/images/gw_2014/gw_ID05.jpg\'" onmouseover="this.src=\'http://macherie.tv/images/gw_2014/gw_ID05on.jpg\'" src="http://macherie.tv/images/gw_2014/gw_ID05.jpg" style="border: 0px;" />
					</form>
				</li>

				<li>
					<form method="post" name="FLOGIN" action="http://www.macherie.tv/biglobe/imacherie.php">
						<input type="hidden" value="login" name="mode2" />
						<input type="hidden" value="login" name="mode_login" />
						<input type="image" title="BIGLOBE��������Ȥǥ�������Ͽ" alt="BIGLOBE��������Ȥǥ�������Ͽ" onmouseout="this.src=\'http://macherie.tv/images/gw_2014/gw_ID06.jpg\'" onmouseover="this.src=\'http://www.macherie.tv/images/gw_2014/gw_ID06on.jpg\'" src="http://macherie.tv/images/gw_2014/gw_ID06.jpg" style="border: 0px;" />
					</form>
				</li>
			</ul>
		</li>
		<li><a href="./performer/"><img src="http://macherie.tv/images/gw_2014/gw_pafologin.jpg" alt="��Ͽ" /></a></li>
	</ul>
</div><!-- gw_login -->
');
	// }
}else{

	$tmpl->assign('banner_disp_login_area', '');

	if($stat = $db->get_login_info($ownerCd, get_uid(), $top_point, $top_unread)){

		$nick_name = get_nickname();

		$after_login_area = new Tmpl2($tmpl_dir . "after_login_area.html");

		if(!$info = $db->get_login_infos($ownerCd,get_uid())){
			err_proc($result->getMessage());
			exit;
		}

		$info_flg = false;
		if($info['mail_auth'] == 1){
			$after_login_area->assign('mail_auth', 1);
			$info_flg = true;
		}

		if($info['card_auth'] == 0){
			$after_login_area->assign('card_auth', 1);
			$info_flg = true;
		}

		if($info_flg){
			$after_login_area->assign('info_flg', true);
		}

		if( $stat === 9 ) {
			logout();
			header("Location:http://".$_SERVER['HTTP_HOST']);

		}

		$nick_name = str_replace(" ", "&nbsp;", $nick_name);
		$after_login_area->assign('nick_name', $nick_name);
		$after_login_area->assign('user_point', $top_point);
		$after_login_area->assign('mail_unread_count', $top_unread);

		$after_login = $after_login_area->flush(1);

		$tmpl->assign('banner_disp_login_area', $after_login);
	}
}

//$db->get_nodeBefore($mval, $ownerCd, $aDomainFlg);
if(isset($_COOKIE['number'])){
	if(preg_match ("/[0-9]+/i",$_COOKIE['number'],$regs)){
		$mval = $regs[0];
	}else{
		$mval = 0;
	}
} else {
	$mval = 0;
}

//Event�ե����륪���ץ�
$fp        = fopen('/var/www/livechat/htdocs/include/event/event.csv','r');
$e_dat     = fgetcsv($fp, 32);
$eventType = $e_dat[0];
fclose($fp);

$opt_mode = "1";
if($mval == 0){
	$opt_mode = "0";
}

////�롼�����
$tmpl->assign("room_event", make_room("event", "m4", $db, $mval, $ownerCd, $aDomainFlg, $e_dat, $opt_mode) );
$tmpl->assign("room_party", make_room("party", "m3", $db, $mval, $ownerCd, $aDomainFlg, $e_dat, $opt_mode) );
$tmpl->assign("room_first", make_room("first", "m", $db, $mval, $ownerCd, $aDomainFlg, $e_dat, $opt_mode) );
$tmpl->assign("room_second", make_room("second", "m2", $db, $mval, $ownerCd, $aDomainFlg, $e_dat, $opt_mode) );
// �Τ줿�ƥޥ�����û� ---------------------------------------
if($db->get_debutAllUserIdList( $id_list )){

	$list_cnt = $_SESSION['newface_id_list_cnt'];

	// �ꥹ�ȼ���
	$tmpl->assign("room_newface_list_cnt", $list_cnt );
	// 1�ڡ����ܤ�ɽ��
	if ($free20_flg) {
		$db->get_debutAllNode(&$result);
		$tmpl->assign("room_newface", get_targetNode($result));
	} else {
		$db->get_debutTargetNode(&$result, $id_list, 1, 0);
		$tmpl->assign("room_newface", get_targetNode2("newface", $result));
	}

	// HTML�ڡ�����ȿ��
#	$tmpl->assign("room_newface", get_targetNode2("newface", $result));
#	$tmpl->assign("room_newface", get_targetNode($result));
} else {
	err_proc($result->getMessage());
	exit;
}


// �Τ줿�ƥޥ�����ûҡʥ���饤��Τߡ�
if ($free20_flg) {
	if( $db->get_debutTargetNodeOnline(&$result_online) ){
		$tmpl->assign("room_newface_online", get_targetNode($result_online) );
	}else{
		exit;
	}
}


//������ʬ����
printOuterFrame(&$tmpl, "�ȥåץڡ���", $db, $ownerCd);

//����å����ѤΥ������
$tmpl->assign("rndnum",rand(10000000,99999999));

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
require_once 'common_db.inc';
require_once 'Owner.inc';

	//�ޥ�����˥塼����ɽ��
	$sth = $dbMaster->prepare("select msg2 from site_announce where owner_cd = ? and use_type = 3 and use_cd = 1");
	$data = array($ownerCd);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
		print_r($result->getMessage());
	}
	if(!$row = $result->fetchRow()){
		return "�����ɥ��顼";
	} else {
		$tmpl->assign("news_view_flg", "1" );

		$tok = strtok($row[0], " \n");
		$news = "";
		while ( $tok != false ) {
	    	if( strpos($tok, "Infoseek Analyzer") != FALSE ){
				if( isset( $_SERVER['HTTP_SSL']) and strcmp($_SERVER['HTTP_SSL'],"YES") == 0 ){
					break;
				}
	    	}
			$news .= $tok."\n";
	    	$tok = strtok("\n");
		}
		// ���ƤΥ�����̵��������
		$tmpl->assign("news_area", strip_tags($news));
//		$tmpl->assign("news_area", $news);
	}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$variety_area = make_variety_area();
$tmpl->assign("variety_area", $variety_area);

require_once 'blog/common_blog.inc';
make_blog_content($db);

$tmpl->flush();

if($_SERVER['REMOTE_ADDR'] == "122.213.199.165"){
	//echo session_save_path();
	//echo "<br>sess_";
	//echo session_id();
	//echo "<br>";
	//echo $_SERVER['SERVER_ADDR'];
}

exit;

function make_blog_content($db) {
	global $tmpl, $blogImgUrl;

	$dbSlave = $db->get_resource();
	$dbSlave->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = ' SELECT blog_article.id, blog_article.title,blog_article.is_viewable, blog_article.img_path, blog_article.body, blog_article.cre_id,blog_article.cre_date,' .
				 ' female_profile.hash,female_profile.user_id,female_profile.nick_name,female_profile.img,onair.stat, onair.chat_mode' .
				 ' FROM blog_article' .
				 ' INNER JOIN female_member ON female_member.user_id=blog_article.cre_id AND (female_member.stat<>6 AND female_member.stat<>9)' .
				 ' INNER JOIN female_profile ON female_member.user_id=female_profile.user_id' .
				 ' LEFT JOIN onair ON onair.user_id = female_profile.user_id' .
				 ' WHERE blog_article.is_viewable = ?' .
				 
				 //top no display ugly flg
				 ' AND blog_article.id <> 2260 ' .
				 ' AND blog_article.id <> 2309 ' .
				 
				 
				 ' ORDER BY blog_article.cre_date DESC' .
				 ' LIMIT 8 ';
				 
				 
				 
	$sth = $dbSlave->prepare($sql);
	$result = $dbSlave->execute($sth, array(1));
	if (DB::isError($result)) {
		echo $result->getMessage();
		return;
	}

	$tmpl->loopset('blog_loop');
	while ($row = $result->fetchRow()) {

		$performerImg = (empty($row['img'])) ? '' : "/imgs/op/320x240/{$row['img']}";
		$blogImg =  (empty($row['img_path'])) ? $performerImg : $blogImgUrl . $row['img_path'];

		$toTime = strtotime($row['cre_date']);
		if ($toTime >= strtotime('-1 day')) {
			$tmpl->assign('blog_is_new', 1);
		}

		$tmpl->assign('blog_id', $row['id']);
		$tmpl->assign('blog_title', $row['title']);
		$tmpl->assign('blog_body', removeYT($row['body']));
		$tmpl->assign('blog_cre_date', date('Y-m-d H:i', $toTime));
		$tmpl->assign('hash',$row['hash']);

		$tmpl->assign('blog_performer_img', $performerImg);
		$tmpl->assign('blog_performer_name', $row['nick_name']);
		$tmpl->assign('blog_img', $blogImg);

		if (0 == $row['stat']) {
			$tmpl->assign('blog_performer_status', 'offline');
			$tmpl->assign('blog_performer_status_lbl', '���ե饤��');

		}
		else if (2 == $row['stat']) {
			$tmpl->assign('blog_performer_status', 'onchat');
			$tmpl->assign('blog_performer_status_lbl', '����å���');

		}
		else {
			$tmpl->assign('blog_performer_status', 'online');
			$tmpl->assign('blog_performer_status_lbl', '����饤��');

		}

		$tmpl->loopnext();
	}
	$tmpl->loopset('');
}


function make_variety_area(){

	global $tmpl_dir;

	$variety_area_file = $tmpl_dir . "variety_area/variety_list.html";
	$area_data = "";
	if (file_exists($variety_area_file)){
		$vari_tmpl = new Tmpl2($variety_area_file);
		$area_data = $vari_tmpl->flush(1);
	}

	return $area_data;

}


/**
* @name make_room
* @brief �롼���html�����
* @param room_id : ID�ط��ǤĤ���prefix�Ȥ�
* @param sel_id : select_box��id
* @param event_tagimg : �����Υѥ��������β������롼��β����ˤʤ�
*
*/
function make_room($room_id, $sel_id, $db, $mval, $ownerCd, $aDomainFlg, $eventType, $opt_mode){

$opti[0] = "�̾�Τޤ�";
$opti[1] = "����饤��ͥ��";
$opti[2] = "�ƥ롼�ऴ��";
$opti[3] = "�̳�ƻ������";
$opti[4] = "����";
$opti[5] = "�ÿ��ۡ���Φ";
$opti[6] = "�쳤";
$opti[7] = "����";
$opti[8] = "��񡦻͹�";
$opti[9] = "�彣������";
$opti[10] = "����";

$opt = "";
for($i=0;$i<=10;$i++){
	if($i == $mval){
		$opt.="<option value='{$i}' selected>{$opti[$i]}</option>\n";
	} else {
		$opt.="<option value='{$i}'>{$opti[$i]}</option>\n";
	}
}

$style="display:none;";
$topimg="";
if($room_id=="first"){//�ǽ�ΥΡ��ɤ�����ɽ���ˤ��Ƥ���
	$style="";
}else if($room_id=="event"){
	///���٥�ȤϤɤ�������ʤΤǡ�
	//2�ĤΥե�����򳫤��󤸤�ʤ��Ƹ����ͤ򤽤Τޤ޻��ꤹ��Ȥ�DB�Ȥ������������ɤ����⡣
	//��������������������ꥷ���ƥब����褦�ʤΤǤ��Τޤޤ�

	//�롼��Υ����ΤȤ���β���
	$csv=fopen('/var/www/livechat/htdocs/include/event/topImg.csv','r');
	while($row = fgetcsv($csv,1024)){
		$roomtag[intval($row[0])] = $row[1];
	}
	if($_SERVER['SERVER_NAME'] == 'mache-dev.vjsol.jp'){
		$topimg="/c/m/images/common/tag/tag_online.gif";
		if($roomtag[ $eventType[0] ]!=""){
			$topimg="/c/m/images/common/tag/".$roomtag[ $eventType[0] ];
			$style="";//���٥�Ȥ�����Ȥ���ɽ���ˤ��Ƥ���
		}
	}else{
		$topimg="http://c.macherie.tv/c/m/images/common/tag/tag_online.gif";
		if($roomtag[ $eventType[0] ]!=""){
			$topimg="http://c.macherie.tv/c/m/images/common/tag/".$roomtag[ $eventType[0] ];
			$style="";//���٥�Ȥ�����Ȥ���ɽ���ˤ��Ƥ���
		}
	}
}

$pIcon = "";
/** ������ɥΡ��ɤ��̾�Τޤޤξ���ɽ����Ԥ�ʤ��褦opt_mode��Ƚ�ꤷ�ޤ� */
if( $room_id != "second" || ( $room_id == "second" && $opt_mode == "1" ) ) {

	/** �ѡ��ƥ��Ρ��ɤ��̾�Τߤξ���ɽ����Ԥ��褦opt_mode��Ƚ�ꤷ�ޤ� */
	if( ( $room_id == "party" && $opt_mode == "0" ) || $room_id != "party" ) {

		$argument = array(&$result, $ownerCd, $aDomainFlg);

		switch ($room_id){
			case "event":
			case "party":
				array_push($argument, $eventType);
				break;
			case "first":
				array_push($argument, $eventType, $mval, true);
				break;
			case "second":
				array_push($argument, $eventType, $mval);
				break;
		}

		if( call_user_func_array( array($db, "get_".$room_id."Node" ), $argument) ){
			if ($free20_flg) {
				$pIcon = get_targetNode($result);
			} else {
				$pIcon = get_targetNode2($room_id.$mval, $result);
			}
#			$pIcon = get_targetNode($result);
		} else {
			err_proc($result->getMessage());
			exit;
		}

  	}

}

$event="";
if($topimg!=""){//���٥�ȥ������������ꤵ��Ƥ���������ꤹ�롣
	$event="style=\"background: url('".$topimg."') no-repeat top left;background-position: 0 0px; width: 320px; height: 41px;\"";
} else if($room_id == "first" && $pIcon != ""){
	switch ($mval) {
		case "0":
		case "1":
			$event ='style="background-position: 0 -910px;"';		//online
			break;
		case "2":
			$event ='style="background-position: 0 -1001px;"';	//party
			break;
		case "3":
			$event ='style="background-position: 0 -91px;"';		//loc1
			break;
		case "4":
			$event ='style="background-position: 0 -182px;"';		//loc2
			break;
		case "5":
			$event ='style="background-position: 0 -273px;"';		//loc3
			break;
		case "6":
			$event ='style="background-position: 0 -364px;"';		//loc4
			break;
		case "7":
			$event ='style="background-position: 0 -455px;"';		//loc5
			break;
		case "8":
			$event ='style="background-position: 0 -546px;"';		//loc6
			break;
		case "9":
			$event ='style="background-position: 0 -637px;"';		//loc7
			break;
		case "10":
			$event ='style="background-position: 0 -728px;"';		//loc8
			break;
		default:
			$event ='style="background-position: 0 -910px;"';		//online
			break;
	}
} else if($room_id == "event" && $pIcon != ""){
	$style="";
} else if($room_id == "party" && $pIcon != ""){
	$style="";
} else if($room_id == "second" && $pIcon != ""){
	$style="";
	switch ($mval) {
		case "0":
			$event ='style="background-position: 0 -910px;"';		//online
			break;
		case "1":
		case "2":
			$event ='style="background-position: 0 0px;"';	//2shot
			break;
		default:
			$event ='style="background-position: 0 -819px;"';		//loc1
			break;
	}
}

if($pIcon == "" && $room_id == "first" ){
	$pIcon =<<<EOM
<div>
<p>����饤����Υѥե����ޡ������ޤ���</p>

</div>
EOM;
} else if( $pIcon == "" && $room_id=="event" ) {
	$style = "display:none;";
}

//�ʲ�HTML
$str=<<<EOM
<ul class="selectArea box" id="{$room_id}Select" style="{$style}">
<li class="left"><div id="tag_{$room_id}" {$event}></div></li>
EOM;

if($_SERVER["HTTP_HOST"] != "world.macherie.tv" && $room_id != "event"){
	$str .= "<li class='left'><select class='select' id='{$sel_id}'>{$opt}</select></li>\n";
}




if($_SERVER["HTTP_HOST"] != "world.macherie.tv"){
	$str .= '<li class="right"><div class="btn_reload" alt="��������"></div></a></li>'."\n";
	$str .= '<li class="right"><div class="btn_auto_reload" alt="��������"></div></li>';
	$str .= '<li class="right"><div class="btn_reload_switch" alt="��ư�����ڤ��ؤ�"><img src="c/m/images/onoffswitch/off.png" alt="��ư�����ڤ��ؤ�" /></div></li>'."\n";

} else {
	$str .= '<li class="right"><div class="btn_reload_w" alt="��������"></div></a></li>'."\n";
}



$str .=<<<EOM

</ul><div id="{$room_id}Area" class="girlsView" style="{$style}">{$pIcon}</div>
EOM;

	return $str;
}

/**
 * Remove youtube tag in preview.
 * @param string $body
 * @return string
 */
function removeYT($body) {
	$end = 0;
	$str = $body;
	while(strpos($str , '&lt;iframe') != false || strpos($str , '&lt;iframe') > -1) {
		$start = strpos($str, '&lt;iframe');
		$end = strpos($str, '&lt;/iframe&gt;') + 15;
		$sc = substr($str, $start, $end-$start);
		$yt = (explode(' ', $sc));
		while($s = array_shift($yt)) {
			if(strpos($s, 'src=&quot;//www.youtube.com/embed/') != false || strpos($s, 'src=&quot;//www.youtube.com/embed/') > -1) {
				$body = str_replace($sc, '', $body);
				break;
			}
		}
		$str = substr($str, $end);
	}
	return $body;
}

?>
