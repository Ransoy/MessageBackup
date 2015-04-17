<?
require_once 'common_proc.inc';
require_once 'Owner.inc';
require_once 'admin.inc';
require_once 'common_db_in.inc';
require_once("./inc/template.php");

//	Page Setting
/////////////////////////////////////

$pageTitle = "ステルス";
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

// 一覧取得
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
		$tr .= "<td><input type=\"button\" onclick=\"javascript:remove_bankdata('{$row[5]}');\" value=\"削除\"></td>";
		$tr .= "<td><input type=\"button\" onclick=\"javascript:sendmail_bankdata('{$row[5]}');\" value=\"メール\"></td>";
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
<tr><th>経過</th><th>銀行種類</th><th>男性ID</th><th>申請</th><th>削除</th><th>メール</th></tr>
{$tr}
</table>
";


//==============================================
//==============うりあげ========================
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
<tr><th>前日売上</th><th>1日目標</th><th>差額</th><th>今月平均</th><th>平均差額</th></tr>
<tr><td align=\"right\">{$prev_money}</td><td align=\"right\">{$mokuhyo}</td><td align=\"right\">{$sub_prev_money}</td><td align=\"right\">{$ave_money}</td><td align=\"right\">{$sub_ave_money}</td></tr>
</table>";

$str.= "<div>{$html}
<p style=\"font-size:10px;color:#FF00FF;\">
<s>※入金の申告がきてから5分経過したら「口座確認しましたが確認できませんでしたメール」を送信してください。</s><br>
※マシェリ管理画面の「入金処理->銀行振り込み」から入金処理したらこのページから消えます。<br>
※入金は最優先事項です。1秒でも早く処理することによりポイント消費へ繋がります。何が何でも入金処理！<br>
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

$mailsub = '【マシェリ】お知らせ・銀行振込報告につきまして';
$mailbody = <<<TOM

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　【お知らせ】振込完了メール・該当するご入金が確認できませんでした。
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

{$row[1]} 様

日頃からのご愛顧誠にありがとうございます。マシェリ事務局です。

会員様よりご入金の報告（振込完了メール）をお受け取りしました。

ご連絡を頂きました銀行への入金を確認致しましたが、該当するご入金が
確認できませんでした。

ご入金の報告内容（振込完了メール）に間違いがありました場合は、
下記お問い合わせフォームより事務局までご連絡くださいませ。

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　◆　お問い合わせフォーム → http://www.macherie.tv/support.php　◆

　　■　振込名義　：
　　■　振込日時　：
　　■　振込先口座：ジャパンネット・イーバンク・郵便貯金
　　■　振込金額　：

　※お手数ですが上記内容をお送り下さいましたら幸いで御座います。

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

※このメールと行き違いでお手続きをされていましたらお詫び申し上げます。
　お手数ですがこちらのメールを破棄して頂けましたら幸いで御座います。


また、記入内容に間違い等が無い場合、ジャパンネットバンク同士のご入金や
イーバンク銀行同士のご入金であればすぐに確認が取れるかとは思いますが、
もし他銀行からのご入金の場合で15時以降のご入金 並びに土日祝祭日であった
場合、翌営業日からのお取り扱いとなります。

そのため、お客様のご入金の確認は銀行の営業が開始されてからポイント追加
となりますので、予めご了承くださいませ。


　※　営業時間につきましては、各金融機関の規約に基づきます。
　※　詳細につきましてはご利用の金融機関へお問い合わせ下さい。
　※　ご報告内容に誤りがあった場合はご連絡お待ちしております。

お振込み日時やお振込み先の記入ミスが多発しております。ご注意下さい。

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
こちらのメールアドレスは配信専用となっております。お問合せしていただく
場合は下記の【お問合せフォーム】よりお問合せの程宜しくお願い致します。

　　【お問合せフォーム】　http://www.macherie.tv/support.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　【会員様ログインページ】　→　http://www.macherie.tv/member.php

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
┃ □本メールの発信：株式会社アイエヌネットワーク
┃ 東京都新宿区歌舞伎町2-17-9　クロスビル　7F
┃ □お問い合わせ先：マシェリ事務局
┃ customer@macherie.tv
┃─────────────────────────────────
┃□このメールはライブストリーミングUGCサイトマシェリ通じて、当サイト
┃ にご登録・ご利用いただいた方に無料にてお届けしております。登録に
┃　覚えのない方は「本メールが届いている宛先アドレス」を記載のうえ、
┃　上記メールアドレスにご一報ください。
┃─────────────────────────────────
┃□このメールマガジンは、配信を希望されている方全員にお送りしており
┃　ます。配信停止を希望される場合は、下記ページより配信停止手続きを
┃　おこなってください。
┃　http://www.macherie.tv/support.php?ln=mailstop
┃　設定の反映までしばらくの間、メールを差し上げる場合がありますが、
┃　あらかじめご容赦くださいますよう、お願い申し上げます。
┃□お問い合わせにつきましては下記よりお問合せください。
┃　http://www.macherie.tv/male-support/
┃□メンバー名とパスワードをお忘れになった方はこちらから。
┃　http://www.macherie.tv/pass.php
┃
┃発行元：マシェリ事務局　http://www.macherie.tv/
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
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
