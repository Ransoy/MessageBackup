<?
// -----------------------------------------------
//
// �������᡼������
//
// ��������-
// �����ԡ�-
//
// �ѹ�����
//         2008/03/05 From���ɥ쥹�ѹ���ž������info@����ž������mpocket@����
// -----------------------------------------------
require_once 'webMailService.inc';
require_once 'common_proc.inc';
require_once 'common_db_slave127.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'Owner.inc';
require_once 'operator/operator.inc';
require_once 'mailBOXMaintenance.inc';
mailBOXMaintenanceFemale(1);

include "referer2.php";//REFERER

if($vuelto){
	$referer = "true";
}else{
	$referer = "false";
}

////////////////////////////////////////////////////////////////////////////////////////////////////
	global $ownerCd;

	$sql = "SELECT female_member.world_flg,female_member.agent_code,female_member.stat FROM female_member WHERE female_member.owner_cd = ? AND female_member.user_id = ?";
	$con = array($ownerCd,$_SESSION['user_id']);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	$world_flg = $row[0];
	$stat = $row[2];
	$agent_code_con = '';
	if($row[1] != ""){
		$agent_code_con = 'style="display:none;"';
	}
	$template_name = "";
	if($world_flg == 1){
		$template_name = "_world";
	}

	//ľ�ܸƤФ줿���
	if(!isset($_POST['mode'])){
		if(!isset($_GET['sid'])){
			// ���������򤵤�Ƥ��ʤ����
			$sql  = "SELECT";
			$sql .= " male_member.hash,";			//0
			$sql .= " male_member.nick_name ";		//1
			$sql .= "FROM female_address_book, male_member ";
			$sql .= "WHERE female_address_book.owner_cd = ? AND female_address_book.user_id = ? ";
			$sql .= " AND (female_address_book.address_type = 0 OR female_address_book.address_type = 1) ";
			$sql .= " AND female_address_book.male_user_id = male_member.user_id AND male_member.stat = 1 ";
			$sql .= "ORDER BY male_member.nick_name";
			$con = array($ownerCd,$_SESSION['user_id']);
			$sth = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth,$con);
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			$tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/mail_resend{$template_name}.html");
			$tmpl->dbgmode(0);
			$tmpl->loopset("mail_address" );
			while($row = $result -> fetchRow()){
				$tmpl->assign("id_1",$row[0]);
				$tmpl->assign("nick_name",$row[1]);
				$tmpl->assign("sl_name","");
				$tmpl->loopnext();
			}
			$tmpl->loopset("");
			$tmpl->assign("init_disp1","1");

		}else{
			// ���������򤵤�Ƥ�����
			$search_parm = "";
			$sql = "SELECT nick_name FROM male_member WHERE	owner_cd = ? AND hash = ? ";
			$con = array($ownerCd,$_GET['sid']);
			$sth = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth,$con);
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			$row = $result->fetchRow();
			$tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/mail_resend{$template_name}.html");
			$tmpl->assign("id_2",$_GET['sid']);
			$tmpl->assign("nick_name",$row[0]);
			$tmpl->assign("init_disp2","1");
			if(isset($_GET['search'])){
				$tmpl->assign("search","1");
			}
		}
		$tmpl->assign("subject","");
		$tmpl->assign("body","");

		//�ƥ�ץ졼��
		$sql  = "SELECT";
		$sql .= " tmpl_name,";		//0
		$sql .= " tmpl_id ";		//1
		$sql .= "FROM female_mail_template ";
		$sql .= "WHERE owner_cd = ? AND user_id = ? ";
		$sql .= "ORDER BY tmpl_id";
		$con = array($ownerCd,$_SESSION['user_id']);
		$sth = $dbSlave33->prepare($sql);
		$result = $dbSlave33->execute($sth,$con);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		if($result->numRows() > 0){
			$tmpl->loopset("tmpl_box");
			while($row = $result->fetchRow()){
				$tmpl->assign("tmpl_name",$row[0]);
				$tmpl->assign("tmpl_id",$row[1]);
				$tmpl->assign("sl_tmpl","");
				$tmpl->loopnext();
			}
			$tmpl->loopset("");
		}
		//���������ɻ�
		$_SESSION['ticket'] = md5(uniqid('macherie').mt_rand());
		$tmpl->assign('ticket',htmlspecialchars($_SESSION['ticket'], ENT_QUOTES));
		$tmpl->assign("agent_code", $agent_code_con);
		$tmpl->assign("referer",$referer);//REFERER
		setDairitenParam($tmpl);
		$tmpl->flush();
		exit;
	}


	//�᡼������������(��������������Ȣ����κ��������������꤫�������)
	$check_post_twice = isset($_SESSION['ticket'], $_POST['ticket']) && ($_SESSION['ticket'] === $_POST['ticket']);
	if( $_POST['mode'] == "mail_send" && $check_post_twice){
		unset($_SESSION['ticket']);
		if(isset($_POST['to_user'])){
			$sql = "select user_id,password from male_member where owner_cd = ? and hash = ?";
			$con = array($ownerCd,$_POST['to_user']);
		}else{
			$sql = "select user_id,password from male_member where owner_cd = ? and hash = ?";
			$con = array($ownerCd,$_POST['id_2']);
		}
		$sth = $dbSlave33->prepare($sql);
		$result = $dbSlave33->execute($sth,$con);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		$row = $result->fetchRow();
		$to_user = $row[0];

		$parm = "l_id={$row[0]}&l_ps={$row[1]}";

		// �����Ǥ��뤫�Υ����å�
		if(!deny_mail($to_user,$_SESSION['user_id'])){
			// �ػߥ�ɥ����å�
			check_ng_word($_POST['subject'],$_POST['body']);
			// ���������Ȥ�����
			$search = from_search($_SESSION['user_id'],$to_user);

			$data = array();
			$data['owner_cd'] = $ownerCd;
			$data['user_id'] = $to_user;
			$data['from_user_id'] = $_SESSION['user_id'];
			$data['subject'] = $_POST['subject'];
			$data['body'] = $_POST['body'];
			$data['stat1'] = "0";
			$data['stat2'] = "1";
			$data['first_flg'] = $search;
			iTSinsert($data,"male_mailbox",$_SESSION['user_id']);
			ikkatu($data);
			//----------------
			// mail_id
			$sql  = "select mail_id from male_mailbox ";
			$sql .= "where user_id = ? and from_user_id = ? and cre_date > now() - interval 1 minute and owner_cd = ? ";
			$sql .= "order by cre_date desc limit 1";
			$con = array($to_user, $_SESSION['user_id'], $ownerCd);
			$sth = $dbMaster->prepare($sql);
			$result = $dbMaster->execute($sth,$con);
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			if($row = $result->fetchRow()){
				$mail_id = $row[0];
				recend_update($row[0]);		//resend_id�򹹿�
			}
			tenso_check($to_user,$parm,$stat);	//ž���᡼���ǧ
			$msg = "�᡼����������ޤ�����";
		}else{
			$msg = "<font size=-1>�ʲ��Τ����줫����ͳ�ˤ��᡼����������뤳�Ȥ��Ǥ��ޤ���Ǥ�����<br>\n</font><br>";
			$msg .= "<font size=-1 color=red>�������Ԥ���񤵤�Ƥ��롡����<br>\n";
			$msg .= "�������Ԥ��������ݤ����ꤷ�Ƥ�<br>\n</font>";
		}
		$tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/write_end{$template_name}.html");
		$tmpl->dbgmode(0);
		$tmpl->assign("msg",$msg);
		$tmpl->assign("agent_code", $agent_code_con);
		$tmpl->assign("referer",$referer);//REFERER
		setDairitenParam($tmpl);
		$tmpl->flush();
		exit;
	}

	//�᡼������������(����Ȣ������ֿ�)
	if($_POST['mode'] == "mail_re" && $check_post_twice){
		unset($_SESSION['ticket']);
		$sql = "select user_id,nick_name,password from male_member where owner_cd = ? and hash = ?";
		$con = array($ownerCd,$_POST['to_user']);
		$sth = $dbSlave33->prepare($sql);
		$result = $dbSlave33->execute($sth,$con);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		$row = $result->fetchRow();
		$to_user = $row[0];
		$to_nick = $row[1];
		$parm = "l_id={$row[0]}&l_ps={$row[2]}";

		//�����Ǥ��뤫�Υ����å�
		if(!deny_mail($to_user,$_SESSION['user_id'])){
			//�ػߥ�ɥ����å�
			check_ng_word($_POST['subject'],$_POST['body']);

			from_search($_SESSION['user_id'],$to_user);

			$data = array();
			$data['owner_cd'] = $ownerCd;
			$data['user_id'] = $to_user;
			$data['from_user_id'] = $_SESSION['user_id'];
			$data['subject'] = $_POST['subject'];
			$data['body'] = $_POST['body'];
			$data['stat1'] = "0";
			$data['stat2'] = "1";
			iTSinsert($data,"male_mailbox",$_SESSION['user_id']);
			ikkatu($data);

			//----------------
			// mail_id
			$sql  = "select mail_id from male_mailbox ";
			$sql .= "where user_id = ? and from_user_id = ? and cre_date > now() - interval 1 minute and owner_cd = ? ";
			$sql .= "order by cre_date desc limit 1";
			$con = array($to_user, $_SESSION['user_id'], $ownerCd);
			$sth = $dbMaster->prepare($sql);
			$result = $dbMaster->execute($sth,$con);
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			if($row = $result->fetchRow()){
				$mail_id = $row[0];
				recend_update($row[0]);		//resend_id�򹹿�
			}
			tenso_check($to_user,$parm,$stat);		//ž���᡼���ǧ
			$msg = $to_nick . " ����إ᡼����������ޤ�����";
		}else{
			$msg = "<font size=-1>�ʲ��Τ����줫����ͳ�ˤ��᡼����������뤳�Ȥ��Ǥ��ޤ���Ǥ�����<br>\n</font><br>";
			$msg .= "<font size=-1 color=red>�������Ԥ���񤵤�Ƥ��롡����<br>\n";
			$msg .= "�������Ԥ��������ݤ����ꤷ�Ƥ�<br>\n</font>";
		}
		$tmpl = new Tmpl22($tmpl_dir . "operator/mail_new/res_end{$template_name}.html");
		$tmpl->dbgmode(0);
		$tmpl->assign("msg",$msg);
		$tmpl->assign("agent_code", $agent_code_con);
		$tmpl->assign("referer",$referer);//REFERER
		setDairitenParam($tmpl);
		$tmpl->flush();
	}
	exit;


//----------------------------------------------------------
// �����Ǥ��뤫���γ�ǧ
function deny_mail($to_user,$from_user){
	global $ownerCd,$dbSlave33;
	//�����Ƥ��ޤ������
	$sql = "select count(*) from male_member where owner_cd = ? and user_id = ? and stat = 1";
	$con = array($ownerCd,$to_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	if($row[0] <= 0){
		//����Ͽ�β���ǤϤʤ��Τ�
		return true;
	}
	//��������
	$sql = "select count(*) from male_address_book where owner_cd = ? and user_id = ? and female_user_id = ? and address_type = 2";
	$con = array($ownerCd,$to_user,$from_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	if($row[0] >= 1){
		//���ݥ᡼��Υǡ���������
		return true;
	}
	return false;
}

//----------------------------------------------------------
// ž���᡼���ǧ������male_member,male_profile��
function tenso_check($to_user,$parm,$stat){
	global $ownerCd,$dbSlave33;
	if($stat != 1){
		return;
	}

	$from = $_SESSION['nick_name'];
	//ž�����ǧ
	$sql  = "SELECT";
	$sql .= " male_profile.tenso_mail1,";		//0 PC���ɥ쥹ž������ (0:OK��1:NG)
	$sql .= " male_profile.tenso_mail_stat,";	//1 MB���ɥ쥹ž������ (0:OK��1:NG)
	$sql .= " male_profile.kyohi_time1,";		//2 PC���ĥ�����
	$sql .= " male_profile.kyohi_time2,";		//3 PC���ĥ�����
	$sql .= " male_profile.kyohi_time3,";		//4 MB���ĥ�����
	$sql .= " male_profile.kyohi_time4,";		//5 MB���ĥ�����
	$sql .= " male_profile.tenso_mail2,";		//6 ��Х���᡼�륢�ɥ쥹
	$sql .= " male_member.mail,";				//7 PC�᡼�륢�ɥ쥹
	$sql .= " male_member.return_mail_cnt,";	//8 PC�꥿������
	$sql .= " male_member.nick_name, ";			//9 ���
	$sql .= " male_profile.mb_tenso_mail_stat, ";//10 MB���ɥ쥹ž������ (0:OK��1:NG)
	$sql .= " male_member.auth_type ";			//11 ��Ͽ������
	$sql .= "FROM male_profile INNER JOIN male_member";
	$sql .= " ON male_profile.owner_cd = male_member.owner_cd AND male_profile.user_id = male_member.user_id ";
	$sql .= "WHERE male_profile.owner_cd = ? AND male_profile.user_id = ? ";
	$con = array($ownerCd,$to_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	if($row[11] == "7" || $row[11] == "8"){
		//�ޥ����Х���Ͽ�β���ˤϡ�ž���᡼�������ʤ�
		return;
	}
	//���ߤλ�ʬ��
	$now = date("G");
	//�����ͤλ�ʬ��
	$kyohi1 = 0;
	$kyohi2 = 0;
	$kyohi3 = 0;
	$kyohi4 = 0;
	if($row[2]!=""){
		$kyohi1 = $row[2];
	}
	if($row[3]!=""){
		$kyohi2 = $row[3];
	}
	if($row[4]!=""){
		$kyohi3 = $row[4];
	}
	if($row[5]!=""){
		$kyohi4 = $row[5];
	}

	//----
	// ž���᡼��PC�����ꤵ��Ƥ�����
	if($row[0] == 0 ){
		$sender = 'customer@macherie.tv';
		$tensoFlg = 0;

		if($kyohi1 == $kyohi2){
			//������֤�FROM��TO��Ʊ�����
			$tensoFlg = 1;

		}else if($kyohi1 > $kyohi2){
			//TO��FROM�λ��֤�դ��ˤ��ơ����λ�����Ǥʤ�����ž����Ԥ��ޤ���
			if(!($kyohi2 < $now && $now < $kyohi1)){
				$tensoFlg = 1;
			}

		}else if($kyohi1 < $kyohi2){
			//������֤�TO�λ��֤��礭�����
			if(($kyohi1 <= $now && $now < $kyohi2)){
				$tensoFlg = 1;
			}
		}
		if($row[7] == ""){
			// PC��Ͽ���ɥ쥹���ʤ�
			$tensoFlg = 0;
		}
		if($row[8] >= 3){
			$tensoFlg = 0;
		}
		if($tensoFlg == 1){
			mail_send($row[7], $from, 1, $parm, $sender, $row[9],$row[11]);
		}
	}
	//----
	// ž���᡼�룲�����ꤵ��Ƥ�����
	if($row[10] == 0){
		$sender = 'mpocket@macherie.tv';
		$tensoFlg = 0;

		if($kyohi3 == $kyohi4){
			//������֤�FROM��TO��Ʊ�����
			$tensoFlg = 1;

		}else if($kyohi3 > $kyohi4){
			//TO��FROM�λ��֤�դ��ˤ��ơ����λ�����Ǥʤ�����ž����Ԥ��ޤ���
			if(!($kyohi4 < $now && $now < $kyohi3)){
				$tensoFlg = 1;
			}

		}else if($kyohi3 < $kyohi4){
			//������֤�TO�λ��֤��礭�����
			if(($kyohi3 <= $now && $now < $kyohi4)){
				$tensoFlg = 1;
			}
		}
		if($row[6] == ""){
			// ���ɥ쥹���ʤ�
			$tensoFlg = 0;
		}
/*		if($row[1] == "1"){
			// ��������
			$tensoFlg = 0;
		}
*/
		if($tensoFlg == 1){
			mail_send($row[6], $from, 2, $parm, $sender, $row[9],$row[11]);
		}
	}
	return;
}
//----------------------------------------------------------
// ž���᡼������
function mail_send($tenso_mail, $from, $mail_flg, $param, $sender, $to_nick, $auth){
	global $mail_id;
	$title = $_POST['subject'];
	//$title = "�ڥޥ������{$from}���󤫤鿷��᡼�뤬�Ϥ��ޤ�����";
	$title = "�ޥ����ꡡ{$from}���󤫤�ο���᡼��Ǥ���";
	if($mail_flg == 1){
		// ž���᡼�룱��
		$mail_body = mb_strimwidth($_POST['body'], 0, 40, "...");


if($auth==9){//Cinema
$str .= <<<EOM
{$to_nick}�ͤ�

���Ĥ�ޥ���������Ѥ���ĺ�����꤬�Ȥ���¤��ޤ���

{$from}���󤫤鿷��᡼�뤬�Ϥ��Ƥ���ޤ��ΤǤ��Τ餻�פ��ޤ���
���Υ᡼�����ơϨ�����������������������������������������������������

���п͡�{$from}����

�̾��{$_POST['subject']}

�ܡ�ʸ��
{$mail_body}

���᡼�����Ƥ�³���ϥ�������Υ᡼��ܥå����򤴳�ǧ����������

�����᡼��ܥå����آ�
��http://www.macherie.tv/cinema/webmail/mailbox_receive.php

����������������������������������������������������������������������
�������
���Υ᡼����ֿ����Ƥ⳺���ѥե����ޡ��ͤإ᡼����Ϥ��ޤ���
�ֿ��������ϥ�������Υ᡼��ܥå�����ꤴ�ֿ��򤪴ꤤ���ޤ���

�ܥ᡼������긵�᡼�륢�ɥ쥹��ž���᡼���ۿ����ѤȤʤäƤ���ޤ���
����礻����¤��ޤ����顢�����Τ���礻�ե������ꤪ�ꤤ���ޤ���


���ڤ���礻�ե�����ۡ�http://www.macherie.tv/cinema/male-support

����������������������������������������������������������������������
ž���᡼��μ��������ѹ����ۿ���ߤϲ����գң̤˥��������塢
�������ѹ��Ϥ���ԤʤäƤ���������


���ڼ��������ѹ��ۡ�http://www.macherie.tv/cinema/mailmagazin.php

�ۿ������ޥ����ꥵ�ݡ��ȥ��󥿡�
�饤�֥��ȥ꡼�ߥ�UGC������ [ �ޥ����� ]��http://www.macherie.tv/cinema

����������������������������������������������������������������������
���Υ᡼��˽񤫤줿���Ƥ�̵�ǷǺܡ�̵��ʣ����ؤ��ޤ���
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else if($auth==6){//��ŷ
$str .= <<<EOM
{$to_nick}�ͤ�

���Ĥ�ޥ���������Ѥ���ĺ�����꤬�Ȥ���¤��ޤ���

{$from}���󤫤鿷��᡼�뤬�Ϥ��Ƥ���ޤ��ΤǤ��Τ餻�פ��ޤ���
���Υ᡼�����ơϨ�����������������������������������������������������

���п͡�{$from}����

�̾��{$_POST['subject']}

�ܡ�ʸ��
{$mail_body}

���᡼�����Ƥ�³���ϥ�������Υ᡼��ܥå����򤴳�ǧ����������

�����᡼��ܥå����آ�
��http://www.macherie.tv/rakuten/webmail/mailbox_receive.php

����������������������������������������������������������������������
�������
���Υ᡼����ֿ����Ƥ⳺���ѥե����ޡ��ͤإ᡼����Ϥ��ޤ���
�ֿ��������ϥ�������Υ᡼��ܥå�����ꤴ�ֿ��򤪴ꤤ���ޤ���

�ܥ᡼������긵�᡼�륢�ɥ쥹��ž���᡼���ۿ����ѤȤʤäƤ���ޤ���
����礻����¤��ޤ����顢�����Τ���礻�ե������ꤪ�ꤤ���ޤ���


���ڤ���礻�ե�����ۡ�http://www.macherie.tv/rakuten/male-support

����������������������������������������������������������������������
ž���᡼��μ��������ѹ����ۿ���ߤϲ����գң̤˥��������塢
�������ѹ��Ϥ���ԤʤäƤ���������


���ڼ��������ѹ��ۡ�http://www.macherie.tv/rakuten/mailmagazin.php

�ۿ������ޥ����ꥵ�ݡ��ȥ��󥿡�
�饤�֥��ȥ꡼�ߥ�UGC������ [ �ޥ����� ]��http://www.macherie.tv/rakuten

����������������������������������������������������������������������
���Υ᡼��˽񤫤줿���Ƥ�̵�ǷǺܡ�̵��ʣ����ؤ��ޤ���
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else if($auth==4){//Biglobe
$str .= <<<EOM
{$to_nick}�ͤ�

���Ĥ�ޥ���������Ѥ���ĺ�����꤬�Ȥ���¤��ޤ���

{$from}���󤫤鿷��᡼�뤬�Ϥ��Ƥ���ޤ��ΤǤ��Τ餻�פ��ޤ���
���Υ᡼�����ơϨ�����������������������������������������������������

���п͡�{$from}����

�̾��{$_POST['subject']}

�ܡ�ʸ��
{$mail_body}

���᡼�����Ƥ�³���ϥ�������Υ᡼��ܥå����򤴳�ǧ����������

�����ޥ�����آ�
��http://www.macherie.tv/biglobe/

����������������������������������������������������������������������
�������
���Υ᡼����ֿ����Ƥ⳺���ѥե����ޡ��ͤإ᡼����Ϥ��ޤ���
�ֿ��������ϥ�������Υ᡼��ܥå�����ꤴ�ֿ��򤪴ꤤ���ޤ���

�ܥ᡼������긵�᡼�륢�ɥ쥹��ž���᡼���ۿ����ѤȤʤäƤ���ޤ���
����礻����¤��ޤ����顢�����Τ���礻�ե������ꤪ�ꤤ���ޤ���


���ڤ���礻�ե�����ۡ�http://www.macherie.tv/biglobe/male-support/

����������������������������������������������������������������������
ž���᡼��μ��������ѹ����ۿ���ߤϲ����գң̤˥��������塢
�������ѹ��Ϥ���ԤʤäƤ���������


���ڼ��������ѹ��ۡ�http://www.macherie.tv/biglobe/mailmagazin.php

�ۿ������ޥ����ꥵ�ݡ��ȥ��󥿡�
�饤�֥��ȥ꡼�ߥ�UGC������ [ �ޥ����� ]��http://www.macherie.tv/biglobe/

����������������������������������������������������������������������
���Υ᡼��˽񤫤줿���Ƥ�̵�ǷǺܡ�̵��ʣ����ؤ��ޤ���
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else{//�ܲ�
$str .= <<<EOM
{$to_nick}�ͤ�

���Ĥ�ޥ���������Ѥ���ĺ�����꤬�Ȥ���¤��ޤ���

{$from}���󤫤鿷��᡼�뤬�Ϥ��Ƥ���ޤ��ΤǤ��Τ餻�פ��ޤ���
���Υ᡼�����ơϨ�����������������������������������������������������

���п͡�{$from}����

�̾��{$_POST['subject']}

�ܡ�ʸ��
{$mail_body}

���᡼�����Ƥ�³���ϥ�������Υ᡼��ܥå����򤴳�ǧ����������

�����᡼��ܥå����آ�
��http://www.macherie.tv/webmail/mailbox_receive.php

����������������������������������������������������������������������
�������
���Υ᡼����ֿ����Ƥ⳺���ѥե����ޡ��ͤإ᡼����Ϥ��ޤ���
�ֿ��������ϥ�������Υ᡼��ܥå�����ꤴ�ֿ��򤪴ꤤ���ޤ���

�ܥ᡼������긵�᡼�륢�ɥ쥹��ž���᡼���ۿ����ѤȤʤäƤ���ޤ���
����礻����¤��ޤ����顢�����Τ���礻�ե������ꤪ�ꤤ���ޤ���


���ڤ���礻�ե�����ۡ�http://www.macherie.tv/support.php

����������������������������������������������������������������������
ž���᡼��μ��������ѹ����ۿ���ߤϲ����գң̤˥��������塢
�������ѹ��Ϥ���ԤʤäƤ���������


���ڼ��������ѹ��ۡ�http://www.macherie.tv/mailmagazin.php

�ۿ������ޥ����ꥵ�ݡ��ȥ��󥿡�
�饤�֥��ȥ꡼�ߥ�UGC������ [ �ޥ����� ]��http://www.macherie.tv

����������������������������������������������������������������������
���Υ᡼��˽񤫤줿���Ƥ�̵�ǷǺܡ�̵��ʣ����ؤ��ޤ���
copyright(C) MACHERiE All Rights Reserved.

EOM;
}

		//p_sendmail($tenso_mail, $title, $str, $sender);
		mb_send_mail($tenso_mail, $title, $str,"From: {$sender}",'-f return_mailm@macherie.tv' );
		// END
	}else{
		// ž���᡼�룲��
		$mail_body = mb_strimwidth($_POST['body'], 0, 34, "...");
		$str = <<<EOM
{$mail_body}

-- from��{$from} --

�ޥ�����ݥ��åȤؤΥ��������Ϥ����餫��
http://m.macherie.tv/m/mail_body.php?{$param}&mail_id={$mail_id}

��PR��
���Ӥ���᡼����ֿ����Ǥ���֥ޥ����ꡦ�ݥ��åȡפ��������Ѥ�����������ʵ�ǽ�Ȥ��ޤ��Ƥϡ��ޥ�����Υ᡼�������������������μ������ǿ����������α�������ǽ�Ǥ���

�ޥ�����ݥ��åȤΤ���Ͽ�ϡ�����http://m.macherie.tv/m/

����բ� i-mode��Ezweb��Yahoo!�������������ף�����ꥢ���б����Ƥ��ޤ���

EOM;
		if(!ereg('^#', $tenso_mail)){
			mb_send_mail($tenso_mail, $title, $str  ,"From: {$sender}",'-f return_mail_m2@macherie.tv' );
		}
	}
	return;
}
//======================================
function ikkatu($data){
/*
	�����ͤ���ΰ���Ǥ��٤ƤΥ᡼���ž��
	$chkflg = false;
	//URL�����Ϥ���Ƥ������
	if(preg_match("/http/",$data['body'])){
		$chkflg = true;
	}
	//MAIL���ɥ쥹�����Ϥ���Ƥ������
	if(preg_match("/[\w|\.|\-]+\@([\w|\-]+\.)+[\w|\-]+/",$data['body'])){
		$chkflg = true;
	}
*/
	$chkflg = true;
	if($chkflg){
		$str = <<<EOM
����->����
���п�  :{$data['from_user_id']}
����    :{$data['user_id']}
��̾    :{$data['subject']}
��ʸ    :
{$data['body']}
EOM;
		p_sendmail('mail@macherie.tv',"��å����������å����",$str );
	}
	return;
}

//----------------------------------------------------------
//�ֿ������Τ�resend_id�򹹿�
function recend_update($mail_id_seq){
	global $ownerCd,$dbMaster;

	$sql  = "UPDATE female_mailbox SET ";
	$sql .= " resend_id = ?,";
	$sql .=	" upd_date = now(),";
	$sql .= " upd_id = ?,";
	$sql .= " upd_ip = ? ";
	$sql .=	"WHERE  owner_cd = ? AND user_id = ? AND mail_id = ?";
	$con = array($mail_id_seq,$_SESSION['user_id'],$_SERVER['REMOTE_ADDR'],$ownerCd,$_SESSION['user_id'],$_POST['mail_id']);
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
}
//======================================
//�ػߥ�ɥ����å�
function check_ng_word(&$title, &$msg){
	global $ownerCd,$dbSlave33;

	// �ػߥ�ɤ����
	$sql  = "SELECT	ng_word, kubun FROM mail_ng_word WHERE owner_cd = ? ";
	$con = array($ownerCd);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$err_cnt = 0;
	$war_cnt = 0;
	$war_ng_word_list = "";
	$err_ng_word_list = "";
	while($row = $result->fetchRow()) {
		//NG��ɤ���ʸ��¸�ߤ��뤫������å�
		if(mberegi($row[0], $title) || mberegi($row[0], $msg)) {
			//NG���¸�ߤ��롪��
			if($row[1] == 1) {
				//���Τ餻
				$war_cnt++;
				if ( $war_cnt > 1) {
					$war_ng_word_list .= ", ";
				}
				$war_ng_word_list .= $row[0];
			} else if ($row[1] == 2) {
				//���顼
				$err_cnt++;
				if ( $err_cnt > 1) {
					$err_ng_word_list .= ", ";
				}
				$err_ng_word_list .= $row[0];

				//���顼���Ƥ�ߡߡߤ��ִ����롣
				$msg = str_replace($row[0],"�ߡߡ�",$msg);
				$title = str_replace($row[0],"�ߡߡ�",$title);
			}
		}
	}

	//���Τ餻�����0��ʾ�ξ��ϻ�̳�ɤ˥᡼��Ǥ��Τ餻
	if ($war_cnt > 0) {
$str = <<<EOM

���Τ餻�ػߥ�ɤ����Ѥ���ޤ�����
�桼����ID����������������{$_SESSION['user_id']}
���̡�������������������������
���Τ餻�ػߥ�ɿ�������{$war_cnt}
���Τ餻�ػߥ�����ơ���{$war_ng_word_list}

��̾ :
{$title}

��ʸ :
{$msg}

EOM;
		p_sendmail('g-support@macherie.tv',"���Τ餻�ػߥ�ɥ����å����",$str );
	}
	//�ػߥ��ʸ����ȶػߥ�ɷ����꥿����
	return;
}




///////////////////////////////////////////////////////////////////////////////////
function from_search($from_user,$to_user){
	global $ownerCd,$dbMaster,$dbSlave33;

	$first_flg = 0;
	// �����������
	$sql = "select f_mail_cnt from contact_log where owner_cd = ? AND female_id = ? AND male_id = ? ";
	$con = array($ownerCd,$from_user,$to_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	if($row = $result -> fetchRow()){
		$data = array();
		$data['f_mail_cnt'] = $row[0] + 1;
		$data['#datetime1#'] = "now()";
		iTSupdate($data,"contact_log",$from_user,"owner_cd = {$ownerCd} and male_id = '{$to_user}' and female_id = '{$from_user}'");
	}else{
		// �����˽����Ȥ߹�碌�ǲ��˥���åȡ��᡼��Τ���꤬�ʤ����
		if(isset($_POST['search'])){
			$first_flg = 1;
		}
		$data = array();
		$data['owner_cd'] = $ownerCd;
		$data['female_id'] = $from_user;
		$data['male_id'] = $to_user;
		$data['search_flg'] = $first_flg;
		$data['f_mail_cnt'] = 1;
		$data['#datetime1#'] = "now()";
		iTSinsert($data,"contact_log",$from_user);
	}

	// ���ν�������᡼����������
	$search_flg = 0;
	if(isset($_POST['search'])){
		$search_flg = 1;
	}
	$sql = "select send_cnt,search_send_cnt,first_send_cnt from female_mail_count where owner_cd = ? AND user_id = ? ";
	$con = array($ownerCd,$from_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	if(!$row = $result -> fetchRow()){
		$data = array();
		$data['owner_cd'] = $ownerCd;
		$data['user_id'] = $from_user;
		$data['send_cnt'] = 1;
		$data['search_send_cnt'] = $search_flg;
		$data['first_send_cnt'] = $first_flg;
		iTSinsert($data,"female_mail_count",$from_user);
	}else{
		$sql = "UPDATE female_mail_count SET ";
		$sql.= " send_cnt=send_cnt+1, search_send_cnt=search_send_cnt+?, first_send_cnt=first_send_cnt+?, upd_id=?, upd_date = now(), upd_ip=? ";
		$sql.= "WHERE owner_cd = ? AND user_id = ? ";
		$sth = $dbMaster->prepare($sql);
		$data = array($search_flg, $first_flg, $from_user, $_SERVER['REMOTE_ADDR'], $ownerCd, $from_user);
		$result = $dbMaster->execute($sth, $data);
	}
	return $first_flg;
}

?>
