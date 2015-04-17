<?
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'admin.inc';
require_once 'common_db_in.inc';
require_once("./inc/template.php");

//	Page Setting
/////////////////////////////////////

$pageTitle = "���ƥ륹";
$filePath = "./temp/index.html";
$meta .=<<<EOM
<script language="JavaScript">
function gotopage(a){
	document.F1.cpos.value=a;
	document.F1.submit();
}
function del_submit(){
	document.F1.del_f.value="1";
	document.F1.submit();
}
function remove_bankdata(hash){
	document.bank_form1.mode.value = 'remove_bankdata';
	document.bank_form1.hash.value = hash;
	document.bank_form1.submit();
}
function sendmail_bankdata(hash){
	document.bank_form1.mode.value = 'sendmail_bankdata';
	document.bank_form1.hash.value = hash;
	document.bank_form1.submit();
}
</script>
<style type="text/css">
table#table_money td {
	text-align:right;
}
</style>
EOM;

//	Logic
/////////////////////////////////////

$htmltmpl = file_get_contents($filePath);


$mokuhyo = 5360000;
if(isset($_POST['mode'])){
	if($_POST['mode'] == "remove_bankdata" && !empty($_POST['hash'])){
		$sql = "SELECT user_id,stat2 FROM pay_transaction_bank WHERE `hash` = ? LIMIT 1;";
		$result = $dbMaster->query($sql,$_POST['hash']);
		if($row = $result->fetchRow()){
			var_dump($row);
			if($row[1] == "0"){
				$upd = "UPDATE pay_transaction_bank SET stat2 = 1 WHERE `hash` = ? LIMIT 1;";
				$result = $dbMaster->query($upd,$_POST['hash']);
			}
		}
	}
	if($_POST['mode'] == "sendmail_bankdata" && !empty($_POST['hash'])){
		$sql = "SELECT user_id,stat2 FROM pay_transaction_bank WHERE `hash` = ? LIMIT 1;";
		$result = $dbMaster->query($sql,$_POST['hash']);
		if($row = $result->fetchRow()){
			if($row[1] == "0"){
				sendmail_bankdata($row[0]);
				$upd = "UPDATE pay_transaction_bank SET stat2 = 1 WHERE `hash` = ? LIMIT 1;";
				$result = $dbMaster->query($upd,$_POST['hash']);
			}
		}
	}
}
$str="";
//$str.= "<iframe src=\"http://m-mb.jp/index.php?M=M_M_LIST&ID=93cf8cd8010d1e98722ef6a3991d305d&TP=10\" style=\"height:100%;float:left;\"></iframe>";

// ��������
$sql = "select ";
$sql .= "TIMEDIFF(NOW(),bank.upd_date) , ";		//0
$sql .= "bank.biko, ";										//1
$sql .= "bank.user_id, ";									//2
$sql .= "date_format(bank.upd_date,'%m/%d %H:%i'), ";		//3
$sql .= "bank.stat2, ";									//4
$sql .= "bank.hash ";									//5
$sql .= "from pay_transaction_bank bank ";
$sql .= " where bank.owner_cd = 1 AND bank.upd_date > NOW() - interval 1 day AND stat = 1 AND stat2 = 0 ";
$sql .= " order by bank.upd_date ASC ";
$result = $dbMaster->query($sql);
if(DB::isError($result)){
	print $sql;
	exit();
}
$tr = "";
while($row = $result->fetchRow()){
	$tr .= "<tr><td>{$row[0]}</td><td>{$row[1]}&nbsp;</td><td>{$row[2]}</td><td>{$row[3]}</td>";
	if($row[4] == "0"){
		$tr .= "<td><input type=\"button\" onclick=\"javascript:remove_bankdata('{$row[5]}');\" value=\"���\"></td>";
		$tr .= "<td><input type=\"button\" onclick=\"javascript:sendmail_bankdata('{$row[5]}');\" value=\"�᡼��\"></td>";
	}else{
		$tr .= "<td>&nbsp;</td>";
		$tr .= "<td>&nbsp;</td>";
	}
	$tr .= "</tr>\n";
}
$html = "
<form action=\"\" method=\"post\" name=\"bank_form1\">
<input type=\"hidden\" name=\"hash\" value=\"\">
<input type=\"hidden\" name=\"mode\" value=\"\">
</form>
<table border=\"1\" cellspacing=\"0\" class=\"bank_table\">
<tr><th>�в�</th><th>��Լ���</th><th>����ID</th><th>����</th><th>���</th><th>�᡼��</th></tr>
{$tr}
</table>
";


//==============================================
//==============���ꤢ��========================
//==============================================
$sql = "select sum(pay_log.money) as money FROM pay_log WHERE pay_log.cre_date >= ? AND pay_log.cre_date <= ? + interval 1 day and pay_log.result = 0 and pay_log.owner_cd = 1 ";
if(DB::isError($result)){
	print $sql;
	exit();
}
$sth = $dbSlave->prepare($sql);
$data = array(date('Y-m-d',strtotime("+ -1 day")),date('Y-m-d',strtotime("+ -1 day")));
$result = $dbSlave->execute($sth,$data);
if($row = $result->fetchRow()){
	$prev_money = $row[0];
	$sub_prev_money = $prev_money - $mokuhyo;
}

$data = array(date('Y-m-d'),date('Y-m-d'));
$result = $dbSlave->execute($sth,$data);
if($row = $result->fetchRow()){
	$today_money = $row[0];
}
$data = array(date('Y-m-01'),date('Y-m-t'));
$result = $dbSlave->execute($sth,$data);
if($row = $result->fetchRow()){
	$all_money = $row[0];
	$ave_money = round($all_money / intval(date("d")));
	$sub_ave_money = $ave_money - $mokuhyo;
}
$prev_money = number_format($prev_money);
$mokuhyo = number_format($mokuhyo);
$sub_prev_money = number_format($sub_prev_money);
$ave_money = number_format($ave_money);
$sub_ave_money = number_format($sub_ave_money);
$html2 = "
<table border=\"1\" cellspacing=\"0\" style=\"font-size:13px\" width=\"300\" id=\"table_money\">
<tr><th>�������</th><th>1����ɸ</th><th>����</th><th>����ʿ��</th><th>ʿ�Ѻ���</th></tr>
<tr><td align=\"right\">{$prev_money}</td><td align=\"right\">{$mokuhyo}</td><td align=\"right\">{$sub_prev_money}</td><td align=\"right\">{$ave_money}</td><td align=\"right\">{$sub_ave_money}</td></tr>
</table>";

$str.= "<div>{$html}
<p style=\"font-size:10px;color:#FF00FF;\">
<s>������ο��𤬤��Ƥ���5ʬ�вᤷ����ָ��³�ǧ���ޤ�������ǧ�Ǥ��ޤ���Ǥ����᡼��פ��������Ƥ���������</s><br>
���ޥ�����������̤Ρ��������->��Կ�����ߡפ���������������餳�Υڡ�������ä��ޤ���<br>
������Ϻ�ͥ�����Ǥ���1�äǤ��᤯�������뤳�Ȥˤ��ݥ���Ⱦ���طҤ���ޤ����������Ǥ����������<br>
</p>
{$html2}
</div>";

$content = $str;
//	Content Replace
/////////////////////////////////////

$htmltmpl = mb_ereg_replace('%%page_title%%',$pageTitle,$htmltmpl);
$htmltmpl = mb_ereg_replace('%%content%%',$content,$htmltmpl);

echo(temp($htmltmpl,$filePath));



function sendmail_bankdata($user_id){
global $ownerCd,$dbMaster;
require_once 'common_db_mail.inc';

$sql = "SELECT mail,nick_name,send_flg FROM male_member WHERE owner_cd = ? AND user_id = ? LIMIT 1;";
$data = array($ownerCd,$user_id);
$result = $dbMaster->query($sql,$data);
if($row = $result->fetchRow()){

$mailsub = '�ڥޥ�����ۤ��Τ餻����Կ������ˤĤ��ޤ���';
$mailbody = <<<TOM

����������������������������������������������������������������������
���ڤ��Τ餻�ۿ�����λ�᡼�롦�������뤴���⤬��ǧ�Ǥ��ޤ���Ǥ�����
����������������������������������������������������������������������

{$row[1]} ��

��������Τ��������ˤ��꤬�Ȥ��������ޤ����ޥ������̳�ɤǤ���

����ͤ�ꤴ��������ʿ�����λ�᡼��ˤ򤪼�����ꤷ�ޤ�����

��Ϣ���ĺ���ޤ�����Ԥؤ�������ǧ�פ��ޤ��������������뤴���⤬
��ǧ�Ǥ��ޤ���Ǥ�����

�������������ơʿ�����λ�᡼��ˤ˴ְ㤤������ޤ������ϡ�
�������䤤��碌�ե��������̳�ɤޤǤ�Ϣ���������ޤ���

����������������������������������������������������������������������

���������䤤��碌�ե����� �� http://www.macherie.tv/support.php����

������������̾������
��������������������
����������������¡�����ѥ�ͥåȡ������Х󥯡�͹������
��������������ۡ���

����������Ǥ����嵭���Ƥ����겼�����ޤ����鹬���Ǹ�¤��ޤ���

����������������������������������������������������������������������

�����Υ᡼��ȹԤ��㤤�Ǥ���³���򤵤�Ƥ��ޤ����餪�ͤӿ����夲�ޤ���
��������Ǥ���������Υ᡼����˴�����ĺ���ޤ����鹬���Ǹ�¤��ޤ���


�ޤ����������Ƥ˴ְ㤤����̵����硢����ѥ�ͥåȥХ�Ʊ�ΤΤ������
�����Х󥯶��Ʊ�ΤΤ�����Ǥ���Ф����˳�ǧ�����뤫�Ȥϻפ��ޤ�����
�⤷¾��Ԥ���Τ�����ξ���15���ʹߤΤ����� �¤Ӥ������˺����Ǥ��ä�
��硢��Ķ�������Τ���갷���Ȥʤ�ޤ���

���Τ��ᡢ�����ͤΤ�����γ�ǧ�϶�ԤαĶȤ����Ϥ���Ƥ���ݥ�����ɲ�
�Ȥʤ�ޤ��Τǡ�ͽ�ᤴλ�����������ޤ���


�������ĶȻ��֤ˤĤ��ޤ��Ƥϡ��ƶ�ͻ���ؤε���˴�Ť��ޤ���
�������ܺ٤ˤĤ��ޤ��ƤϤ����Ѥζ�ͻ���ؤؤ��䤤��碌��������
��������������Ƥ˸�꤬���ä����Ϥ�Ϣ���Ԥ����Ƥ���ޤ���

�������������䤪��������ε����ߥ���¿ȯ���Ƥ���ޤ�������ղ�������

����������������������������������������������������������������������
������Υ᡼�륢�ɥ쥹���ۿ����ѤȤʤäƤ���ޤ�������礻���Ƥ�������
���ϲ����Ρڤ���礻�ե�����ۤ�ꤪ��礻�������������ꤤ�פ��ޤ���

�����ڤ���礻�ե�����ۡ�http://www.macherie.tv/support.php

����������������������������������������������������������������������

���ڲ���ͥ�����ڡ����ۡ�����http://www.macherie.tv/member.php

��������������������������������������������������������������������
�� ���ܥ᡼���ȯ����������ҥ������̥ͥåȥ��
�� ����Կ��ɶ�����Į2-17-9�������ӥ롡7F
�� �����䤤��碌�衧�ޥ������̳��
�� customer@macherie.tv
��������������������������������������������������������������������
�������Υ᡼��ϥ饤�֥��ȥ꡼�ߥ�UGC�����ȥޥ������̤��ơ���������
�� �ˤ���Ͽ�������Ѥ�������������̵���ˤƤ��Ϥ����Ƥ���ޤ�����Ͽ��
�����Ф��Τʤ����ϡ��ܥ᡼�뤬�Ϥ��Ƥ��밸�襢�ɥ쥹�פ򵭺ܤΤ�����
�����嵭�᡼�륢�ɥ쥹�ˤ����󤯤�������
��������������������������������������������������������������������
�������Υ᡼��ޥ�����ϡ��ۿ����˾����Ƥ����������ˤ����ꤷ�Ƥ���
�����ޤ����ۿ���ߤ��˾�������ϡ������ڡ�������ۿ���߼�³����
���������ʤäƤ���������
����http://www.macherie.tv/support.php?ln=mailstop
���������ȿ�ǤޤǤ��Ф餯�δ֡��᡼��򺹤��夲���礬����ޤ�����
�������餫���ᤴ�ƼϤ��������ޤ��褦�����ꤤ�����夲�ޤ���
�������䤤��碌�ˤĤ��ޤ��Ƥϲ�����ꤪ��礻����������
����http://www.macherie.tv/male-support/
�������С�̾�ȥѥ���ɤ�˺��ˤʤä����Ϥ����餫�顣
����http://www.macherie.tv/pass.php
��
��ȯ�Ը����ޥ������̳�ɡ�http://www.macherie.tv/
��������������������������������������������������������������������
copyright(C)2010 MACHERiE All Rights Reserved.

TOM;

$sql  = "INSERT INTO male_mailbox (";
$sql .= "owner_cd,user_id,from_user_id,subject,body,stat1,stat2,cre_id,cre_ip,upd_id,upd_ip,cre_date,admin_send_flg";
$sql .= ") VALUES ( ";
$sql .= "?,?,?,?,?,1,1,?,?,?,?,?,?) ";
$sth = $dbMaster->prepare($sql);
$array_values = array();
array_push($array_values,$ownerCd);		//owner_cd
array_push($array_values,$user_id);					//user_id
array_push($array_values,"fmmanager");				//from_user_id
array_push($array_values,$mailsub);
array_push($array_values,$mailbody);
array_push($array_values,$user_id);			//cre_id
array_push($array_values,$_SERVER['REMOTE_ADDR']);	//cre_ip
array_push($array_values,$user_id);			//upd_id
array_push($array_values,$_SERVER['REMOTE_ADDR']);	//upd_ip
array_push($array_values,date("Y-m-d H:i:00"));				//cre_date
array_push($array_values,1);						//admin_send_flg
$dbMaster->execute($sth,$array_values);

if($row[2] == "0"){
$sql  = "insert into mail_send (";
$sql .= "owner_cd,";
$sql .= "mail,";
$sql .= "kenmei,";
$sql .= "naiyou,";
$sql .= "send_time,";
$sql .= "cre_date,";
$sql .= "cre_id,";
$sql .= "cre_ip,";
$sql .= "return_addr ";
$sql .= ") values ( ";
$sql .= "?,?,?,?,?,now(),?,?,?) ";
$sth = $dbMail->prepare($sql);
$array_values = array();
array_push($array_values,$ownerCd);
array_push($array_values,$row[0]);
array_push($array_values,$mailsub);
array_push($array_values,$mailbody);
array_push($array_values,date("Y-m-d H:i:00"));
array_push($array_values,$user_id);
array_push($array_values,$_SERVER['REMOTE_ADDR']);
array_push($array_values, "return_mailm@macherie.tv");
$dbMail->execute($sth, $array_values);
}
}else{
	return false;
}

}
?>
