<?
require_once 'common_proc.inc';
require_once 'common_db_slave127.inc';
require_once 'tmpl2.class_ex.inc';
require_once 'M_Point.inc';
require_once 'F_Point.inc';
require_once 'Owner.inc';
require_once 'boy_mail.inc';
require_once 'FormObject.inc';
require_once 'boy_mail_write.inc';
require_once 'mailBOXMaintenance.inc';
mailBOXMaintenanceMale(1);
/////////////////////////////////////////////////////////////////////////////////////////////////////

	//session_start();
	if(!isset($_SESSION['user_id'])){
		$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
		$tmpl->dbgmode(0);
		$msg = "";
		$msg  .= "======================================<br>";
//		$msg  .= "�᡼�������ˤĤ��ޤ��Ƥ�ͭ������͸����<br>�����ӥ��ȤʤäƤ��ޤ�<br><br><br>";
		// ʸ���ѹ� ��#5978 ̵������Υ᡼��������
		$msg  .= "�᡼�������ˤĤ��ޤ��Ƥϲ���͸����<br>�����ӥ��ȤʤäƤ��ޤ�<br><br><br>";
		$msg  .= "�������<a href=\"../member.php\" target=\"_blank\">������</a>����<br>";
		$msg  .= "======================================";
		$tmpl->assign("msg",$msg);
		if(isset($_GET['sid'])){
			$tmpl->assign("hash",$_GET['sid']);
		}
		$tmpl->flush();
		exit;
	}
	$msg="";

	//-------------------------------------------------
	//̵������ξ��������Ǥ��ʤ��Τǥ��顼���̤�ɽ��
	//-------------------------------------------------
	$sql = "SELECT assortment,gold_flg FROM male_member WHERE	 owner_cd = ? AND	 user_id = ?";
	$con = array($ownerCd,$_SESSION['user_id']);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	
	//------------------------------------------------------------------------------------------
	// ̵������Ǥ�᡼��������褦���ѹ�
	// #5978
	/*
	if($row[0] == 0){
		$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
		$tmpl->dbgmode(0);
		$msg = "";
		$msg  .= "======================================<br>";
		$msg  .= "�᡼�������ˤĤ��ޤ��Ƥ�ͭ������͸���Υ����ӥ��ȤʤäƤ��ޤ�<br>";
		$msg  .= "��ͭ������ͤˤʤ�ޤ��ȡ��᡼�����������Ԥ��ޤ�<br>";
		$msg  .= "��ͭ������ͤȤϡ����٤Ǥ�ݥ���ȹ����򤵤줿����ͤȤʤ�ޤ�<br>";
		$msg  .= "���ޥ��������������ʤ��᡼����������줿��硢�᡼�����������¤�<br>";
		$msg  .= "������������ĺ����礬����ޤ��Τ�ͽ�ᤴλ�����������ޤ���<br><br>";
		$msg  .= "����������������������¤��ޤ�����ޥ������̳�ɤޤǸ�Ϣ����������<br><br>";
		$msg  .= "�᡼���������������<a href='/settlement/bank.php' target='_blank'>�ݥ���ȹ���</a><br>";
		$msg  .= "======================================";
		$tmpl->assign("msg",$msg);
		if(isset($_GET['sid'])){
			$tmpl->assign("hash",$_GET['sid']);
		}
		$tmpl->flush();
		exit;
	}
	*/
	//------------------------------------------------------------------------------------------

	$gold_flg = $row[1];

	//----------------------------------
	//�᡼��������̤�ɽ��
	//----------------------------------
	if(!isset($_POST['mode']) || $_POST['mode'] == "login"){
		//���������򤵤�Ƥ��ʤ����(��������)
		if(!isset($_GET['sid'])){
			$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
			$tmpl->dbgmode(0);
			$msg = "";
			$msg  .= "==================================<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "���褬�����Ǥ�<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "==================================";
			$tmpl->assign("msg",$msg);
			$tmpl->flush();
			exit;
		}
		//���������򤵤�Ƥ�����(���ɥ쥹Ģ���鿷������)
		else{
			$sql = "SELECT 	nick_name ";					//0
			$sql .="FROM  	female_profile ";
			$sql .="WHERE	owner_cd = ? ";
			$sql .="AND		hash = ? ";
			$con = array($ownerCd,$_GET['sid']);
			$sth = $dbSlave33->prepare($sql);
			$result = $dbSlave33->execute($sth,$con);
			if(DB::isError($result)){
				err_proc($result->getMessage());
			}
			$row = $result->fetchRow();
			$tmpl = new Tmpl22($tmpl_dir . "chat/mail.html");
			$tmpl->assign("id_2",$_GET['sid']);
			$tmpl->assign("nick_name",$row[0]);
			$tmpl->assign("init_disp2","1");
		}
		$tmpl->assign("msg",$msg);
		$tmpl->assign("subject","");
		$tmpl->assign("body","");
		$tmpl->assign("hash",$_GET['sid']);
		$tmpl->flush();
		exit;
	}

	//----------------------------------
	// �᡼������������
	//----------------------------------
	if($_POST['mode'] == "mail_re" || $_POST['mode'] == "mail_send"){
		if(!isset($_POST['id_2'])){
			//����
			print "�����ʥ��������Ǥ�";
			exit;
		}
		$sql  = "select female_profile.user_id,female_profile.nick_name,female_member.password ";
		$sql .= "from female_profile,female_member ";
		$sql .= "where female_profile.owner_cd = ? and female_profile.hash = ?";
		$sql .= " and female_profile.owner_cd = female_member.owner_cd";
		$sql .= " and female_profile.user_id = female_member.user_id ";
		if(isset($_POST['to_user'])){
			$con = array($ownerCd,$_POST['to_user']);
		}else{
			$con = array($ownerCd,$_POST['id_2']);
		}
		$sth = $dbSlave33->prepare($sql);
		$result = $dbSlave33->execute($sth,$con);
		if(DB::isError($result)){
			err_proc($result->getMessage());
		}
		$row = $result->fetchRow();
		$to_user = $row[0];
		$parm = "l_id={$row[0]}&l_ps={$row[2]}";

		//�����ξ��֡��������ݥ����å� OK�ʤ�᡼������
		if(deny_mail($to_user,$_SESSION['user_id'])){
			// �ٹ��å���������
			$msg =  "<font size=-1>�ʲ��Τ����줫����ͳ�ˤ��᡼����������뤳�Ȥ��Ǥ��ޤ���Ǥ�����<br></font><br>\n";
			$msg .= "<font size=-1 color=red>�������Ԥ���񤵤�Ƥ��롡����<br>\n";
			$msg .= "�������Ԥ��������ݤ����ꤷ�Ƥ�</font><br>\n";
		}else{
			//�ػߥ�ɥ����å�
			check_ng_word($_POST['subject'],$_POST['body']);
			//�ݥ���Ƚ���
			if(!upd_point($to_user,$gold_flg)){
				$msg = "<font size=-1>�ݥ���Ȥ�­��ޤ���<br>\n</font><br>";
			}else{
				//���󥿥������򹹿�
				mail_contact($_SESSION['user_id'],$to_user);
				//�᡼������
				$data = array();
				$data['owner_cd'] = $ownerCd;
				$data['user_id'] = $to_user;
				$data['from_user_id'] = $_SESSION['user_id'];
				$data['subject'] = $_POST['subject'];
				$data['body'] = $_POST['body'];
				$data['stat1'] = "0";
				$data['stat2'] = "1";
				iTSinsert($data,"female_mailbox",$_SESSION['user_id']);

				ikkatu($data);				//����¦�إ᡼���ž��
				tenso_check($to_user,$parm);	//������ž���᡼�������

				//resend_id_update($mail_id_seq);
				//�����ο���᡼�뤬3��ʾ夿�ޤä��顢�����ˤ��Τ餻
				//new_mail_send($to_user);
				$msg = $row[1] . " ����إ᡼����������ޤ�����";
			}
		}
		$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
		$tmpl->dbgmode(0);
		$tmpl->assign("msg",$msg);
		
		$tmpl->assign("hash",$_POST['id_2']);
		$tmpl->flush();
	}

?>
