<?
require_once 'Owner.inc';
require_once 'common_proc.inc';
require_once 'common_db.inc';
require_once 'tmpl2.class.inc';
require_once 'IP_check_admin.inc';
require_once 'M_preChat.inc';

	//���å���������
	session_start();
	if(!start_proc()){
		error_disp("�ɣ����ϥѥ���ɤ������Ǥ���");
	}

	if(empty($_GET['chat_mode'])){
		$_GET['chat_mode'] = 0;
	}

	$syotai_flg = 0;
	if(!empty($_GET['syotai'])){
		$syotai_flg = $_GET['syotai'];
	}
	$world_flg = 0;
	if(!empty($_GET['world'])){
		$world_flg = $_GET['world'];
	}


	//������������
	$sql = "SELECT";
	$sql.= " male_member.user_id,";
	$sql.= " male_member.mlv1,";
	$sql.= " male_member.assortment,";
	$sql.= " male_member.gold_flg,";
	$sql.= " male_member.monitor_point,";
	$sql.= " male_member.cm_code,";
	$sql.= " male_member.auth_type,";
	$sql.= " male_point.point ";
	$sql.= "FROM male_member LEFT JOIN male_point ON";
	$sql.= " male_member.user_id = male_point.user_id AND male_member.user_id = male_point.user_id ";
	$sql.= "WHERE male_member.owner_cd = ? and male_member.user_id = ?";
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, array($ownerCd, $_SESSION['user_id']));
	$m_row = $result->fetchRow(DB_FETCHMODE_ASSOC);


	/**
	 * ��������å����γ�ǧ
	 */
	$pc = new M_preChat($ownerCd, $dbMaster, $dbSlave, $m_row);
	//�����ξ������
	$f_row = $pc->get_female($_GET['id']);
	$f_user_id = $f_row['user_id'];
	$f_nick_name = $f_row['nick_name'];


	//��������ڡ������꣱�����ǥ⡼�ɤǤ���³��ǧ
	if(!$pc->cam_cutMode_connect()){
		//��������ڡ������꣱�����ǥ⡼�ɤξ�硢������Ʊ��ѥե����ޡ�����³����ʤ���
		error_disp("̵�������ڡ���ϰ�ͤ������դ���������Ȥʤ�ޤ���");
	}


	//��꤬����åȤǤ��뤫
	$sql  = "SELECT";
	$sql .= " onair.stat,";				//0
	$sql .= " onair.fcs_no,";			//1
	$sql .= " onair.chat_mode,";		//2
	$sql .= " onair.mizugi_flg,";		//3
	$sql .= " onair.CRE_IP ";			//4
	$sql .= " FROM onair ";
	$sql .= "WHERE onair.owner_cd = ? AND onair.user_id = ? AND (onair.start_date is null OR onair.start_date < now() )";
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, array($ownerCd, $f_user_id));
	if(!$row = $result->fetchRow()){
		error_disp("���Ǥ˥������Ȥ��Ƥ���ޤ���");
	}
	$onair_stat = $row[0];
	$onair_fcno = $row[1];
	$onair_mode = $row[2];
	$mizugi_flg = $row[3];
	$cre_ip     = $row[4];
	if(empty($onair_stat)){
		error_disp("���Ǥ˥������Ȥ��Ƥ���ޤ���");
	}

	// 2shot��³�׵�
	if($_GET['chat_mode'] == "0"){
		if(!empty($onair_mode)){
			error_disp("����åȥ⡼�ɤ��㤤�ޤ�");
		}else if($onair_stat != "1"){
			error_disp("�ۤ�������2shot����åȤ��ϤޤäƤ��ޤ���");
		}

	// party��������³�׵�
	}else{
		// URL���㤦
		if($onair_mode == "0"){
			error_disp("����åȥ⡼�ɤ��㤤�ޤ�.");
		}
	}
	// ���ߡ���³
	if($onair_fcno == "Z"){
		error_disp("�ۤ�������2shot����åȤ��ϤޤäƤ��ޤ���");
	}


	//----------------------------------
	// �ϡ��Ȥ�Ȥ��������å�
	$heart = $pc->heart_check();
	if(!empty($heart['confirm'])){
		//�ϡ��Ȥ�Ȥ�����ǧ������̤�ɽ��
		$tmpl = new Tmpl2($tmpl_dir . "chat/heart_window.html");
		$tmpl->assign("id",$_GET['id']);
		$tmpl->assign("chat_mode",$_GET['chat_mode']);
		$tmpl->assign("syotai",$syotai_flg);
		$tmpl->assign("world",$world_flg);
		$heartParam = "heart_num=".$heart['heart_num']."&nick_name=".rawurlencode(mb_convert_encoding($f_nick_name,"UTF-8","EUC-JP"))."&dddd_name=������";
		$tmpl->assign("param",$heartParam);
		$tmpl->flush();
		exit;
	}


	//----------------------------------
	// ���Գ�ǧ
	if($syotai_flg == "1"){
		//�����˾��Ԥ���Ƥ��뤫
		$sth = $dbMaster->prepare("SELECT 1 FROM syotai WHERE from_hash = ? AND to_user_id = ?");
		$result = $dbMaster->execute($sth, array($_GET['id'], $_SESSION['user_id']));
		if(!$row = $result->fetchRow()){
			error_disp("���Ծ��Ϥ��Ǥ˥���󥻥뤵��Ƥ���ޤ���");
		}
		$result = $dbMaster->query("DELETE FROM syotai WHERE to_user_id = ". $dbMaster->quote($_SESSION['user_id']));

		//����åȤ�������ǧ
		if(!$pc->syotai_connect()){
			error_disp("���ԥ���åȤϰ�ͤ������դ��������Ȥʤ�ޤ���");
		}
	}

	//----------------------------------
	//�����ڡ���Ƚ�� �ڤ� �ݥ���ȥ����å�
	if(!$pc->check_connection($heart['use_flg'], $syotai_flg, $mizugi_flg, $_GET['chat_mode'])){
		error_disp("�ݥ���Ȥ�ͭ��ޤ���");
	}

	//----------------------------------
	//��³�Ǥ���
	$fcs_hash = md5($_SESSION['user_id'].time());

	if($_GET['chat_mode'] == "0"){
		//2shot��³
		$tmpl = new Tmpl2($tmpl_dir . "chat/male_chat.html");
		$tmpl->assign("chat","1");
		
	}else if(($_GET['chat_mode'] == "0") || ($_GET['chat_mode'] == "1")){
		//party��³
		$tmpl = new Tmpl2($tmpl_dir . "chat/party.html");
		$tmpl->assign("chat","1");
		
	}else{
		//����
		$tmpl = new Tmpl2($tmpl_dir . "chat/chat_peep.html");
		$tmpl->assign("nozoki","1");
	}

	//���ɥ쥹�֥å���Ͽ������å���³���Υ��å����������Ͽ
	$pm = array("fcs_no"=>$onair_fcno, "fcs_hash"=>$fcs_hash);
	$pc->etc_make_data($pm);
	
	
	$param  = "?id=".$_GET['id']."&hash=".$fcs_hash."&user_id=".$_SESSION['user_id']."&password=".$_SESSION['password'];
	$param .= "&asp=male&chat_mode=".$_GET['chat_mode']."&syotai=".$syotai_flg."&fcs_no=".$onair_fcno."&heartFlg=".$heart['use_flg'];
	$param .= "&world={$world_flg}";

	$tmpl->assign("fcs_no",strtolower($onair_fcno));
	$tmpl->assign("param",$param);
	$tmpl->assign("id",$_GET['id']);
	$tmpl->assign("female_id",$_GET['id']);
	$tmpl->assign("nick_name",$f_nick_name);
	$tmpl->flush();
	exit;

//--------------------------------------
// ��İ���̤����
//--------------------------------------
function error_disp($msg){
	$id = "";
	if(isset($_GET['id'])){
		$id = $_GET['id'];
	}

	$param = "?id=".$id;
	if(isset($_GET['world']) && $_GET['world'] == "1"){
		$param .= "&world=".$_GET['world'];
	}
	$param .= "&emsg=".urlencode($msg);
	header("location: shicho.php{$param}");
	exit;
}

//--------------------------------------
//�������Ȥץ�å�
//--------------------------------------
function start_proc(){
	global $ownerCd,$dbMaster,$dbSlave;

	//���Ǥ˥�����ǧ�ںѤ�
	if(isset($_SESSION['stat'])){
		if(comp($_SESSION['stat'],"boyslogin")){
			return true;
		}
	}

	//����������å�
	if(empty($_GET['user_id'])){
		return false;
	}
	if(empty($_GET['password'])){
		return false;
	}

	$w_user_id = $_GET['user_id'];
	$_POST['user_id'] = $_GET['user_id'];
	$_POST['password'] = $_GET['password'];
	$usr_result = check_mailLogin($dbMaster, $dbSlave, $ownerCd);
	if($usr_result['status']){
		$w_user_id = $usr_result['user_id'];
	}else{
		if($usr_result['count'] > 1){
			//�᡼�륢�ɥ쥹����ʣ���Ƥ���ΤǤ����ǽ�λ������
			return false;
		}
	}

	//�����ʬ => ����̵�������1:ͭ����������� => �����֥�å��Ǥʤ�
	$sth = $dbMaster->prepare("select password,assortment,nick_name,stat from male_member where owner_cd = ? and user_id = ? and (assortment = 0 or assortment = 1) and stat = 1");
	$data = array($ownerCd,$w_user_id);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	if(!$row = $result->fetchRow()){
		return false;
	}
	$passwd = $row[0];
	$assort = $row[1];
	$nick_name = $row[2];
	$stat = $row[3];

	if(comp($passwd,$_GET['password'])){
		$_SESSION['stat'] = "boyslogin";			//���å����˥�������֤�Ͽ
		$_SESSION['user_id'] = $w_user_id;			//���å����˥桼��ID��Ͽ
		$_SESSION['password'] = $_GET['password'];	//���å����˥ѥ���ɤ�Ͽ
		$_SESSION['nick_name'] = $nick_name;		//���å����˥˥å��͡����Ͽ

		//�ǽ�������
		$sth = $dbMaster->prepare("update male_member set last_login = now() where owner_cd = ? and user_id = ?");
		$data = array($ownerCd,$w_user_id);
		$result = $dbMaster->execute($sth, $data);

		$sql = "insert into male_login (owner_cd,user_id,cre_date,cre_ip) values({$ownerCd},?,now(),'{$_SERVER['REMOTE_ADDR']}')";
		$dbMaster->query($sql,$w_user_id);
		//������塢���å������ѹ�
		male_login_session($dbMaster, $_SESSION['user_id']);

		return true;
	}else{
		//�ѥ���ɤ��㤦�ΤǺ�����
		sleep(8);
		return false;
	}
}

?>
