<?
// -----------------------------------------------
//
// 女性：メール送信
//
// 作成日：-
// 作成者：-
//
// 変更履歴：
//         2008/03/05 Fromアドレス変更（転送１：info@〜、転送２：mpocket@〜）
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

	//直接呼ばれた場合
	if(!isset($_POST['mode'])){
		if(!isset($_GET['sid'])){
			// 男性が選択されていない場合
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
			// 男性が選択されている場合
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

		//テンプレート
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
		//２重送信防止
		$_SESSION['ticket'] = md5(uniqid('macherie').mt_rand());
		$tmpl->assign('ticket',htmlspecialchars($_SESSION['ticket'], ENT_QUOTES));
		$tmpl->assign("agent_code", $agent_code_con);
		$tmpl->assign("referer",$referer);//REFERER
		setDairitenParam($tmpl);
		$tmpl->flush();
		exit;
	}


	//メール送信時処理(新規送信・送信箱からの再送・お気に入りからの送信)
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

		// 送信できるかのチェック
		if(!deny_mail($to_user,$_SESSION['user_id'])){
			// 禁止ワードチェック
			check_ng_word($_POST['subject'],$_POST['body']);
			// この男性との履歴
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
				recend_update($row[0]);		//resend_idを更新
			}
			tenso_check($to_user,$parm,$stat);	//転送メール確認
			$msg = "メールを送信しました。";
		}else{
			$msg = "<font size=-1>以下のいずれかの理由によりメールを送信することができませんでした。<br>\n</font><br>";
			$msg .= "<font size=-1 color=red>・受信者が退会されている　　　<br>\n";
			$msg .= "・受信者が受信拒否を設定してる<br>\n</font>";
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

	//メール送信時処理(受信箱からの返信)
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

		//送信できるかのチェック
		if(!deny_mail($to_user,$_SESSION['user_id'])){
			//禁止ワードチェック
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
				recend_update($row[0]);		//resend_idを更新
			}
			tenso_check($to_user,$parm,$stat);		//転送メール確認
			$msg = $to_nick . " さんへメールを送信しました。";
		}else{
			$msg = "<font size=-1>以下のいずれかの理由によりメールを送信することができませんでした。<br>\n</font><br>";
			$msg .= "<font size=-1 color=red>・受信者が退会されている　　　<br>\n";
			$msg .= "・受信者が受信拒否を設定してる<br>\n</font>";
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
// 送信できるか？の確認
function deny_mail($to_user,$from_user){
	global $ownerCd,$dbSlave33;
	//あいてがまだ会員？
	$sql = "select count(*) from male_member where owner_cd = ? and user_id = ? and stat = 1";
	$con = array($ownerCd,$to_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	if($row[0] <= 0){
		//本登録の会員ではないので
		return true;
	}
	//受信拒否
	$sql = "select count(*) from male_address_book where owner_cd = ? and user_id = ? and female_user_id = ? and address_type = 2";
	$con = array($ownerCd,$to_user,$from_user);
	$sth = $dbSlave33->prepare($sql);
	$result = $dbSlave33->execute($sth,$con);
	if(DB::isError($result)){
		err_proc($result->getMessage());
	}
	$row = $result->fetchRow();
	if($row[0] >= 1){
		//拒否メールのデータがある
		return true;
	}
	return false;
}

//----------------------------------------------------------
// 転送メール確認処理（male_member,male_profile）
function tenso_check($to_user,$parm,$stat){
	global $ownerCd,$dbSlave33;
	if($stat != 1){
		return;
	}

	$from = $_SESSION['nick_name'];
	//転送先確認
	$sql  = "SELECT";
	$sql .= " male_profile.tenso_mail1,";		//0 PCアドレス転送許可 (0:OK、1:NG)
	$sql .= " male_profile.tenso_mail_stat,";	//1 MBアドレス転送許可 (0:OK、1:NG)
	$sql .= " male_profile.kyohi_time1,";		//2 PC許可タイム
	$sql .= " male_profile.kyohi_time2,";		//3 PC許可タイム
	$sql .= " male_profile.kyohi_time3,";		//4 MB許可タイム
	$sql .= " male_profile.kyohi_time4,";		//5 MB許可タイム
	$sql .= " male_profile.tenso_mail2,";		//6 モバイルメールアドレス
	$sql .= " male_member.mail,";				//7 PCメールアドレス
	$sql .= " male_member.return_mail_cnt,";	//8 PCリターン回数
	$sql .= " male_member.nick_name, ";			//9 相手
	$sql .= " male_profile.mb_tenso_mail_stat, ";//10 MBアドレス転送許可 (0:OK、1:NG)
	$sql .= " male_member.auth_type ";			//11 登録サイト
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
		//マシェバラ登録の会員には、転送メールを送らない
		return;
	}
	//現在の時分秒
	$now = date("G");
	//設定値の時分秒
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
	// 転送メールPCが設定されている場合
	if($row[0] == 0 ){
		$sender = 'customer@macherie.tv';
		$tensoFlg = 0;

		if($kyohi1 == $kyohi2){
			//設定時間のFROMとTOが同じ場合
			$tensoFlg = 1;

		}else if($kyohi1 > $kyohi2){
			//TOとFROMの時間を逆さにして、この時間内でない場合に転送を行います。
			if(!($kyohi2 < $now && $now < $kyohi1)){
				$tensoFlg = 1;
			}

		}else if($kyohi1 < $kyohi2){
			//設定時間のTOの時間が大きい場合
			if(($kyohi1 <= $now && $now < $kyohi2)){
				$tensoFlg = 1;
			}
		}
		if($row[7] == ""){
			// PC登録アドレスがない
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
	// 転送メール２が設定されている場合
	if($row[10] == 0){
		$sender = 'mpocket@macherie.tv';
		$tensoFlg = 0;

		if($kyohi3 == $kyohi4){
			//設定時間のFROMとTOが同じ場合
			$tensoFlg = 1;

		}else if($kyohi3 > $kyohi4){
			//TOとFROMの時間を逆さにして、この時間内でない場合に転送を行います。
			if(!($kyohi4 < $now && $now < $kyohi3)){
				$tensoFlg = 1;
			}

		}else if($kyohi3 < $kyohi4){
			//設定時間のTOの時間が大きい場合
			if(($kyohi3 <= $now && $now < $kyohi4)){
				$tensoFlg = 1;
			}
		}
		if($row[6] == ""){
			// アドレスがない
			$tensoFlg = 0;
		}
/*		if($row[1] == "1"){
			// 拒否設定
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
// 転送メール送信
function mail_send($tenso_mail, $from, $mail_flg, $param, $sender, $to_nick, $auth){
	global $mail_id;
	$title = $_POST['subject'];
	//$title = "【マシェリ】{$from}さんから新着メールが届きました。";
	$title = "マシェリ　{$from}さんからの新着メールです。";
	if($mail_flg == 1){
		// 転送メール１へ
		$mail_body = mb_strimwidth($_POST['body'], 0, 40, "...");


if($auth==9){//Cinema
$str .= <<<EOM
{$to_nick}様へ

いつもマシェリをご利用して頂きありがとう御座います。

{$from}さんから新着メールが届いておりますのでお知らせ致します。
━［メール内容］━━━━━━━━━━━━━━━━━━━━━━━━━━━

差出人：{$from}さん

件　名：{$_POST['subject']}

本　文：
{$mail_body}

※メール内容の続きはサイト内のメールボックスをご確認ください。

　▽メールボックスへ▽
　http://www.macherie.tv/cinema/webmail/mailbox_receive.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
※ご注意
このメールに返信しても該当パフォーマー様へメールは届きません。
返信される場合はサイト内のメールボックスよりご返信をお願いします。

本メールの送り元メールアドレスは転送メール配信専用となっております。
お問合せ等御座いましたら、下記のお問合せフォームよりお願いします。


　【お問合せフォーム】　http://www.macherie.tv/cinema/male-support

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
転送メールの受信設定変更や配信停止は下記ＵＲＬにアクセス後、
［設定変更］から行なってください。


　【受信設定変更】　http://www.macherie.tv/cinema/mailmagazin.php

配信元：マシェリサポートセンター
ライブストリーミングUGCサイト [ マシェリ ]　http://www.macherie.tv/cinema

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
このメールに書かれた内容の無断掲載、無断複製を禁じます。
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else if($auth==6){//楽天
$str .= <<<EOM
{$to_nick}様へ

いつもマシェリをご利用して頂きありがとう御座います。

{$from}さんから新着メールが届いておりますのでお知らせ致します。
━［メール内容］━━━━━━━━━━━━━━━━━━━━━━━━━━━

差出人：{$from}さん

件　名：{$_POST['subject']}

本　文：
{$mail_body}

※メール内容の続きはサイト内のメールボックスをご確認ください。

　▽メールボックスへ▽
　http://www.macherie.tv/rakuten/webmail/mailbox_receive.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
※ご注意
このメールに返信しても該当パフォーマー様へメールは届きません。
返信される場合はサイト内のメールボックスよりご返信をお願いします。

本メールの送り元メールアドレスは転送メール配信専用となっております。
お問合せ等御座いましたら、下記のお問合せフォームよりお願いします。


　【お問合せフォーム】　http://www.macherie.tv/rakuten/male-support

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
転送メールの受信設定変更や配信停止は下記ＵＲＬにアクセス後、
［設定変更］から行なってください。


　【受信設定変更】　http://www.macherie.tv/rakuten/mailmagazin.php

配信元：マシェリサポートセンター
ライブストリーミングUGCサイト [ マシェリ ]　http://www.macherie.tv/rakuten

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
このメールに書かれた内容の無断掲載、無断複製を禁じます。
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else if($auth==4){//Biglobe
$str .= <<<EOM
{$to_nick}様へ

いつもマシェリをご利用して頂きありがとう御座います。

{$from}さんから新着メールが届いておりますのでお知らせ致します。
━［メール内容］━━━━━━━━━━━━━━━━━━━━━━━━━━━

差出人：{$from}さん

件　名：{$_POST['subject']}

本　文：
{$mail_body}

※メール内容の続きはサイト内のメールボックスをご確認ください。

　▽マシェリへ▽
　http://www.macherie.tv/biglobe/

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
※ご注意
このメールに返信しても該当パフォーマー様へメールは届きません。
返信される場合はサイト内のメールボックスよりご返信をお願いします。

本メールの送り元メールアドレスは転送メール配信専用となっております。
お問合せ等御座いましたら、下記のお問合せフォームよりお願いします。


　【お問合せフォーム】　http://www.macherie.tv/biglobe/male-support/

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
転送メールの受信設定変更や配信停止は下記ＵＲＬにアクセス後、
［設定変更］から行なってください。


　【受信設定変更】　http://www.macherie.tv/biglobe/mailmagazin.php

配信元：マシェリサポートセンター
ライブストリーミングUGCサイト [ マシェリ ]　http://www.macherie.tv/biglobe/

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
このメールに書かれた内容の無断掲載、無断複製を禁じます。
copyright(C) MACHERiE All Rights Reserved.

EOM;
}else{//本家
$str .= <<<EOM
{$to_nick}様へ

いつもマシェリをご利用して頂きありがとう御座います。

{$from}さんから新着メールが届いておりますのでお知らせ致します。
━［メール内容］━━━━━━━━━━━━━━━━━━━━━━━━━━━

差出人：{$from}さん

件　名：{$_POST['subject']}

本　文：
{$mail_body}

※メール内容の続きはサイト内のメールボックスをご確認ください。

　▽メールボックスへ▽
　http://www.macherie.tv/webmail/mailbox_receive.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
※ご注意
このメールに返信しても該当パフォーマー様へメールは届きません。
返信される場合はサイト内のメールボックスよりご返信をお願いします。

本メールの送り元メールアドレスは転送メール配信専用となっております。
お問合せ等御座いましたら、下記のお問合せフォームよりお願いします。


　【お問合せフォーム】　http://www.macherie.tv/support.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
転送メールの受信設定変更や配信停止は下記ＵＲＬにアクセス後、
［設定変更］から行なってください。


　【受信設定変更】　http://www.macherie.tv/mailmagazin.php

配信元：マシェリサポートセンター
ライブストリーミングUGCサイト [ マシェリ ]　http://www.macherie.tv

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
このメールに書かれた内容の無断掲載、無断複製を禁じます。
copyright(C) MACHERiE All Rights Reserved.

EOM;
}

		//p_sendmail($tenso_mail, $title, $str, $sender);
		mb_send_mail($tenso_mail, $title, $str,"From: {$sender}",'-f return_mailm@macherie.tv' );
		// END
	}else{
		// 転送メール２へ
		$mail_body = mb_strimwidth($_POST['body'], 0, 34, "...");
		$str = <<<EOM
{$mail_body}

-- from　{$from} --

マシェリポケットへのアクセスはこちらから
http://m.macherie.tv/m/mail_body.php?{$param}&mail_id={$mail_id}

【PR】
携帯からメールの返信ができる「マシェリ・ポケット」を是非ご利用ください。主な機能としましては、マシェリのメールの送受信、お得情報の受信、最新ログイン情報の閲覧が可能です。

マシェリポケットのご登録は　→　http://m.macherie.tv/m/

※注意※ i-mode、Ezweb、Yahoo!ケータイ、主要３キャリアに対応しています。

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
	お客様からの依頼ですべてのメールを転送
	$chkflg = false;
	//URLが入力されていた場合
	if(preg_match("/http/",$data['body'])){
		$chkflg = true;
	}
	//MAILアドレスが入力されていた場合
	if(preg_match("/[\w|\.|\-]+\@([\w|\-]+\.)+[\w|\-]+/",$data['body'])){
		$chkflg = true;
	}
*/
	$chkflg = true;
	if($chkflg){
		$str = <<<EOM
女性->男性
差出人  :{$data['from_user_id']}
宛先    :{$data['user_id']}
件名    :{$data['subject']}
本文    :
{$data['body']}
EOM;
		p_sendmail('mail@macherie.tv',"メッセージチェック報告",$str );
	}
	return;
}

//----------------------------------------------------------
//返信したのでresend_idを更新
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
//禁止ワードチェック
function check_ng_word(&$title, &$msg){
	global $ownerCd,$dbSlave33;

	// 禁止ワードを取得
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
		//NGワードが本文に存在するかをチェック
		if(mberegi($row[0], $title) || mberegi($row[0], $msg)) {
			//NGワード存在する！！
			if($row[1] == 1) {
				//お知らせ
				$war_cnt++;
				if ( $war_cnt > 1) {
					$war_ng_word_list .= ", ";
				}
				$war_ng_word_list .= $row[0];
			} else if ($row[1] == 2) {
				//エラー
				$err_cnt++;
				if ( $err_cnt > 1) {
					$err_ng_word_list .= ", ";
				}
				$err_ng_word_list .= $row[0];

				//エラー内容を×××に置換する。
				$msg = str_replace($row[0],"×××",$msg);
				$title = str_replace($row[0],"×××",$title);
			}
		}
	}

	//お知らせ件数が0件以上の場合は事務局にメールでお知らせ
	if ($war_cnt > 0) {
$str = <<<EOM

お知らせ禁止ワードが使用されました。
ユーザーID　　　　　　　：{$_SESSION['user_id']}
性別　　　　　　　　　　：女性
お知らせ禁止ワード数　　：{$war_cnt}
お知らせ禁止ワード内容　：{$war_ng_word_list}

件名 :
{$title}

本文 :
{$msg}

EOM;
		p_sendmail('g-support@macherie.tv',"お知らせ禁止ワードチェック報告",$str );
	}
	//禁止ワード文字列と禁止ワード件数をリターン
	return;
}




///////////////////////////////////////////////////////////////////////////////////
function from_search($from_user,$to_user){
	global $ownerCd,$dbMaster,$dbSlave33;

	$first_flg = 0;
	// 過去やり取り履歴
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
		// この男女の組み合わせで過去にチャット・メールのやり取りがない場合
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

	// この女性の総メール送受信数
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
