<?
// 2004/11/16 MOD by N.Takeyoshi start
$tmpl_dir = "/var/www/livechat/htdocs/template/";
//$tmpl_dir = "/usr/local/apache/htdocs_mysql/template/";
// 2004/11/16 MOD end
require_once 'Owner.inc';

$page_max_num = 10;		// ���٤�ɽ���������ڡ�����󥯿�
$page_num = 100;			// 1�ڡ����Υ饤���


login_disp55();
function login_disp55(){
	global $disp_login_error;
	global $dbSlave,$ownerCd;
	$disp_login_error = "0";
	
	if(!isset($_POST['mode_login'])){
		return;
		}
	if(!isset($_SESSION['stat'])){
		return;
		}
	if($_SESSION['stat'] == "boyslogin"){
		return;
		}
	if($_SESSION['stat'] == "boyslogin_buy"){
		return;
		}
	if($_POST['mode_login'] != "login"){
		return;
		}
	$disp_login_error = 1;
	if($_POST['user_id'] == ""){
		return;
		}
	if($_POST['password'] == ""){
		return;
		}
	$sth = $dbSlave->prepare("select password,assortment,nick_name,stat from male_member where owner_cd = ? and user_id = ? and (assortment = 0 or assortment = 1) and (stat = 1 or stat = 0)");
	$data = array($ownerCd,$_POST['user_id']);
	$result = $dbSlave->execute($sth, $data);
	if(DB::isError($result)){
		print_r($result->getMessage());
		}
	if(!$row = $result->fetchRow()){
		return;
	}
	if($passwd != $_POST['password']){
		return;
		}
	if($row[3] == "0"){
		$_SESSION['stat'] = "boyslogin_buy";
	}else{
		$_SESSION['stat'] = "boyslogin";
		}
	$_SESSION['user_id'] = $_POST['user_id'];		//���å����˥桼��ID��Ͽ
	$_SESSION['password'] = $_POST['password'];	//���å����˥ѥ���ɤ�Ͽ
	$_SESSION['nick_name'] = $nick_name;		//���å����˥˥å��͡����Ͽ
	//�ǽ������������򹹿�
	$data = array();
	$data['last_login'] = date('Y/m/d H:i:s', time());
	iTSupdate($data,"male_member",$_POST['user_id']," owner_cd = " .$ownerCd. " and user_id = '" . $_POST['user_id'] . "'");
	$tmpl = new Tmpl22($tmpl_dir . "member_tmp.html");
	}

function err_proc($str){
	print $str;
}
function comp($str1,$str2){
	if(strcmp($str1,$str2) == 0){
		return true;
	}
	return false;
}
function disp_number($width,$number){
	$disp = number_format($number);
	$str_len = strlen($disp);
	if($str_len >= $width){
		return $disp;
		}
	$sp = "";
	$sp_len = $width - $str_len;
	for($i=0;$i<$sp_len;$i++){
		$sp .= " ";
		}
	return $sp . $disp;
	}

function iTSinsert($data_hash,$table,$user_id){
	global $dbMaster;
	$str_keys = "";
	$str_values = "";
	$array_values = array();

	foreach($data_hash as $key => $value){
		$str_keys .= " $key, ";
		$str_values .= " ?, ";
		array_push($array_values,$value);
		}
	$str_keys .= " cre_id, ";
	$str_values .= " ? ";
	array_push($array_values,$user_id);
	$str_keys .= " cre_date, ";
	$str_values .= " ,now() ";
	$str_keys .= " upd_date, ";
	$str_values .= " ,now() ";
	$str_keys .= " cre_ip, ";
	$str_values .= " ,? ";
	array_push($array_values,$_SERVER['REMOTE_ADDR']);
	$str_keys .= " upd_id, ";
	$str_values .= " ,? ";
	array_push($array_values,$user_id);
	$str_keys .= " upd_ip ";
	$str_values .= " ,? ";
	array_push($array_values,$_SERVER['REMOTE_ADDR']);

	$sql = "insert into $table ($str_keys) values ($str_values)";
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $array_values);
	if(DB::isError($result)){
		print $sql;
		err_proc($result -> getMessage());
		}
	}

function iTSupdate($data_hash,$table,$user_id,$where){
	global $dbMaster;
	$str_sql = "";
	$array_values = array();

	foreach($data_hash as $key => $value){
		$str_sql .= " $key = ? ,";
		array_push($array_values,$value);
		}
	$str_sql .= " upd_date = now() , ";
	$str_sql .= " upd_id = ? , ";
	array_push($array_values,$user_id);
	$str_sql .= " upd_ip = ? , ";
	array_push($array_values,$_SERVER['REMOTE_ADDR']);
	$str_sql .= " upd_date = now() ";

	$sql = "update $table set $str_sql where $where";

	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $array_values);
	if(DB::isError($result)){
		print $sql;
		err_proc($result -> getMessage());
		}
	}

function getCPCode(){
	global $dbSlave,$dbMaster;

	return($dbMaster->nextId("cpcodes"));

	}

function iTSdel($table,$where){
	global $dbMaster;
	if($where == ""){
		print "��郎�ʤ��ʤ�ơ�����";
		exit;
		}

	$sql = "delete from $table where $where";
	$dbMaster->query($sql);
	if(DB::isError($result)){
		print $sql;
		err_proc($result -> getMessage());
		}
	}

//-------------              change              ----------------//
function p_sendmail($to,$subject,$body,$sender="",$send_to=false){

$foot = <<<EOM

EOM;

	if($sender != ""){
	    if($send_to){
		//�ޥ����ꥹ���åդ˥᡼������
		mb_send_mail("info@macherie.tv", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("rainbow_kanri2@hotmail.com", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("sutaffuninomiya@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("xxasukamaruxx@hotmail.com", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("maiko_4@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("suit-0305@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("akatomato@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("pop_the_top@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("kobayashi-shibuya@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("takuhito0420@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );

	    }else{
		//��Ͽ�Ԥ�����
		mb_send_mail($to, $subject, $body  ,"From: {$sender}",'-f info@macherie.tv' );
	    }

	} else {
		mb_send_mail($to, $subject, $body  ,"From: info@macherie.tv",'-f info@macherie.tv' );
	}
	return;
}

function p_sendmail2($to,$subject,$body,$sender="",$send_to=false){
	if($sender != ""){
	   if($send_to){
		mb_send_mail("info@macherie.tv", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("rainbow_kanri2@hotmail.com", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("sutaffuninomiya@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("xxasukamaruxx@hotmail.com", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("maiko_4@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("suit-0305@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("akatomato@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("pop_the_top@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("kobayashi-shibuya@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
		mb_send_mail("takuhito0420@hotmail.co.jp", $subject, $body  ,$sender,'-f info@macherie.tv' );
	   }else{
		mb_send_mail($to, $subject, $body  ,"From: {$sender}",'-f info@macherie.tv' );
	   }

	} else {
		mb_send_mail($to, $subject, $body  ,"From: info@macherie.tv",'-f info@macherie.tv' );
	}
	return;
}
//--------------------------------------------------------------------------//
//������ꥵ�������ƥ��ԡ�

function ResizeCopy2($srcFile,$destFile,$width,$height){
        $size_array = GetImageSize($srcFile);
        if($size_array[1] != 0){
                $rt1 = $size_array[0]/$width;
                $rt2 = $size_array[1]/$height;
                $scc = max($rt1,$rt2);

                $width2 = $size_array[0] / $scc;
                $height2 = $size_array[1] / $scc;

                $newimg = ImageCreateTrueColor($width2, $height2);
                $color = ImageColorAllocate ($newimg, 255, 252, 218);
                imagefill($newimg,0,0,$color);
                $source = ImageCreateFromJPEG($srcFile);
                ImageCopyResampled($newimg,$source,0,0,0,0,$width2,$height2,$size_array[0],$size_array[1]);
                ImageJPEG($newimg,$destFile);
                }
        }

//�᡼��ƥ�ץ졼��	
function mail_template($id,$to,$data = ""){
	global $dbSlave;
	$sql = "";
	$sql .= "select ";
	$sql .= "msg, ";           //0
	$sql .= "subject, ";       //1
	$sql .= "send, ";          //2
	$sql .= "footer, ";        //3
	$sql .= "sender_addr ";   //4
	$sql .= "from mail_template where id = $id ";

	$result = $dbSlave->query($sql);

	if($row = $result -> fetchRow()){
		$msg = $row[0];
		//-------change------// 
		$msg_add = "";// info@macherie.tv�ȥޥ����ꥹ���åդؤ������᡼������
		//--------------------/
		$subject = $row[1];
		$send = $row[2];
		$footer = $row[3];
		$sender = $row[4];

	}
	$id_value = intval($id);
	if($send == 1){
		if(isset($data)){
			foreach($data as $key => $value){
				switch($key){
				case "nick_name";
					$msg = str_replace("/--�˥å��͡���--/",$value,$msg);
					break;
				case "shimei";
					$msg = str_replace("/--��̾--/",$value,$msg);
					break;
				case "password";
					$msg = str_replace("/--�ѥ����--/",$value,$msg);
					break;
				case "user_id";
					$msg = str_replace("/--�ɣ�--/",$value,$msg);
					$msg_add .= "     ID        : " . $value . "\n";
					break;
				case "mail";
					$msg = str_replace("/--�᡼�륢�ɥ쥹--/",$value,$msg);
					$msg_add .= "�᡼�륢�ɥ쥹 : " . $value . "\n";
					break;
				case "hash";
					$msg = str_replace("/--�ϥå���--/",$value,$msg);
					break;
				case "money";
					$msg = str_replace("/--�������--/",$value,$msg);
					break;
				case "point";
					$msg = str_replace("/--�ݥ����--/",$value,$msg);
					break;
				}
			}
		}
		$msg = str_replace("/--�ɣХ��ɥ쥹--/",$_SERVER['REMOTE_ADDR'],$msg);
		if($footer == 1){
			if($sender != ""){
				p_sendmail($to,$subject,$msg,$sender);
		//------      add            ----//
				if(($id_value == 1) || ($id_value == 5) || ($id_value == 41)){
					p_sendmail($to,"�������������Ͽ����",$msg_add,"From: macherie_mans<{$sender}>",true);
		//			p_sendmail($to,$subject,$msg,$sender,true);//test��ǧ
				}
		//--------------------------------//
			}else{
				p_sendmail($to,$subject,$msg);
			}
		}else{
			if($sender != ""){
				p_sendmail2($to,$subject,$msg,$sender);
			//--------           add        ------------//
				if(($id_value == 1) || ($id_value == 5)  || ($id_value == 41)){
			//		p_sendmail2($to,$subject,$msg,$sender,true);//test��ǧ
					p_sendmail2($to,"�������������Ͽ����",$msg_add,"From: macherie_mans<{$sender}>",true);
				}
			//--------------------------------------------//
			}else{
				p_sendmail2($to,$subject,$msg);
			}
		}
	}
}

// ���ڡ���HTML�����ʰ�����������̤����������������Ƭ�ݥ���ȡ�
function getPageFeedCreHtml($num,$pos){
	global $page_max_num;		// ���٤�ɽ���������ڡ�����󥯿�
	global $page_num;			// 1�ڡ����Υ饤���
	$strHtml = "";

	$page = (int)($pos / ($page_num * $page_max_num)) * $page_max_num + 1; 
	if($pos < ($page_num * $page_max_num)){
		$strHtml = "<tr><td width='100'></td>";
	}
	else{
		$tmp = (($page - 1) * $page_num) - ($page_num * $page_max_num);
		$strHtml = "<tr><td width='100'><a href='javascript:goto_page(".$tmp.")'>����".$page_max_num."�ڡ���</a></td>";
	}

	$i=($page - 1) * $page_num;
	$max_page = ($page - 1) + $page_max_num;

	$strHtml .= "<td width='400' align='center'>";
	$pageLink = $page;
	while($i<$num){
		if($pageLink > $max_page){
			break;
		}
		if($i == $pos){
			$strHtml .= $pageLink . "��";
		}
		else{
			$strHtml .= "<a href='javascript:goto_page(".$i.")'>".$pageLink."</a>��";
		}
		$i+=$page_num;
		$pageLink++;
	}
	$strHtml .= "</td>";

	$max = (int)($num / $page_num);
	if((int)($num % $page_num) > 0){
		$max += 1;
	}

//	$leftOverPage = (int)($max % $page_max_num);

//print("aaa=".$page."<br>");
//print("bbb=".$num."<br>");
//print("ccc=".$max."<br>");

	if(($page + $page_max_num) > $max){
		$strHtml .= "<td align='right'></td></tr>";
	}
	else{
		$tmp = (($page - 1) * $page_num) + ($page_num * $page_max_num);
		$strHtml .= "<td align='right'><a href='javascript:goto_page(".$tmp.")'>����".$page_max_num."�ڡ���</a></td></tr>";
	}

	return $strHtml;
}

function get_back_img_path_settlement(){

	global $dbMaster,$ownerCd;
	
	//���å���������
	$back_img = "images/back.gif";		//�طʲ�����VIP�Ѥ��̾��Ѥ�ʬ����
	if(isset($_SESSION['stat']) && (comp($_SESSION['stat'],"boyslogin_buy") || comp($_SESSION['stat'],"boyslogin"))){
		//--------------
		//����������
		//--------------
		/* ��������������ۤ���� */
		$sql = "select all_money from male_member where owner_cd = '". $ownerCd . " ' and user_id = ?";
		$data = array($_SESSION['user_id']);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $data);
		if(DB::isError($result)){
		}
		$row = $result -> fetchRow();
		$all_money = $row[0];
		
		if ( $all_money >= 50000 ) {
			//------------
			//VIP�����
			//------------
			$back_img = "vip_images/back.gif";
		} else {
			//-----------------
			//�̾�����ɽ��
			//-----------------
			$back_img = "images/back.gif";
		}
	} else {
		//-----------------
		//�̾�����ɽ��
		//-----------------
		$back_img = "images/back.gif";
	}
	return $back_img;
}
function update_male_all_money($user_id, $money){
	global $dbMaster,$ownerCd;
	$sql = "UPDATE male_member SET all_money  = all_money + {$money} WHERE owner_cd = '". $ownerCd . " ' and user_id = ?";
	$data = array($user_id);
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
	}
	//������ɲ���������
	$sql = "SELECT value FROM point_setting WHERE id = '30'";
	$data = array();
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
	}
	$row = $result -> fetchRow();
	$gold_money = $row[0];
	
	//����Ѷ�ۤ����
	$sql = "SELECT all_money FROM male_member WHERE user_id =?";
	$data = array($user_id);
	$sth = $dbMaster->prepare($sql);
	$result = $dbMaster->execute($sth, $data);
	if(DB::isError($result)){
	}
	$row = $result -> fetchRow();
	$all_money = $row[0];
	
	//������ɾ���ۤ������Ѷ�ۤ��礭����Х�����ɥե饰�򣱤ˤ���
	if ( $gold_money <= $all_money) {
		$sql = "UPDATE male_member SET gold_flg  = 1 WHERE owner_cd = '". $ownerCd . " ' and user_id = ? and gold_flg = 0";
		$data = array($user_id);
		$sth = $dbMaster->prepare($sql);
		$result = $dbMaster->execute($sth, $data);
		if(DB::isError($result)){
		}
	}
	
}

?>