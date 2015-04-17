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
//		$msg  .= "メール送信につきましては有料会員様限定の<br>サービスとなっています<br><br><br>";
		// 文言変更 （#5978 無料会員のメール送信）
		$msg  .= "メール送信につきましては会員様限定の<br>サービスとなっています<br><br><br>";
		$msg  .= "ログインは<a href=\"../member.php\" target=\"_blank\">こちら</a>から<br>";
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
	//無料会員の場合は送信できないのでエラー画面を表示
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
	// 無料会員でもメールは送れるように変更
	// #5978
	/*
	if($row[0] == 0){
		$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
		$tmpl->dbgmode(0);
		$msg = "";
		$msg  .= "======================================<br>";
		$msg  .= "メール送信につきましては有料会員様限定のサービスとなっています<br>";
		$msg  .= "※有料会員様になりますと、メール送受信が行えます<br>";
		$msg  .= "※有料会員様とは、一度でもポイント購入をされた会員様となります<br>";
		$msg  .= "※マシェリに相応しくないメールを送信された場合、メール送信に制限を<br>";
		$msg  .= "　かけさせて頂く場合がありますので予めご了承くださいませ。<br><br>";
		$msg  .= "ご不明な点・問題等が御座いましたらマシェリ事務局まで御連絡ください。<br><br>";
		$msg  .= "メール送信される方⇒<a href='/settlement/bank.php' target='_blank'>ポイント購入</a><br>";
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
	//メール作成画面を表示
	//----------------------------------
	if(!isset($_POST['mode']) || $_POST['mode'] == "login"){
		//女性が選択されていない場合(新規作成)
		if(!isset($_GET['sid'])){
			$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
			$tmpl->dbgmode(0);
			$msg = "";
			$msg  .= "==================================<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "宛先が不正です<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "<br>";
			$msg  .= "==================================";
			$tmpl->assign("msg",$msg);
			$tmpl->flush();
			exit;
		}
		//女性が選択されている場合(アドレス帳から新規作成)
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
	// メール送信時処理
	//----------------------------------
	if($_POST['mode'] == "mail_re" || $_POST['mode'] == "mail_send"){
		if(!isset($_POST['id_2'])){
			//不正
			print "不正なアクセスです";
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

		//女性の状態・受信拒否チェック OKならメール送信
		if(deny_mail($to_user,$_SESSION['user_id'])){
			// 警告メッセージ出力
			$msg =  "<font size=-1>以下のいずれかの理由によりメールを送信することができませんでした。<br></font><br>\n";
			$msg .= "<font size=-1 color=red>・受信者が退会されている　　　<br>\n";
			$msg .= "・受信者が受信拒否を設定してる</font><br>\n";
		}else{
			//禁止ワードチェック
			check_ng_word($_POST['subject'],$_POST['body']);
			//ポイント処理
			if(!upd_point($to_user,$gold_flg)){
				$msg = "<font size=-1>ポイントが足りません<br>\n</font><br>";
			}else{
				//コンタクト履歴更新
				mail_contact($_SESSION['user_id'],$to_user);
				//メール送信
				$data = array();
				$data['owner_cd'] = $ownerCd;
				$data['user_id'] = $to_user;
				$data['from_user_id'] = $_SESSION['user_id'];
				$data['subject'] = $_POST['subject'];
				$data['body'] = $_POST['body'];
				$data['stat1'] = "0";
				$data['stat2'] = "1";
				iTSinsert($data,"female_mailbox",$_SESSION['user_id']);

				ikkatu($data);				//管理側へメールを転送
				tenso_check($to_user,$parm);	//女性へ転送メールを送信

				//resend_id_update($mail_id_seq);
				//女性の新着メールが3件以上たまったら、女性にお知らせ
				//new_mail_send($to_user);
				$msg = $row[1] . " さんへメールを送信しました。";
			}
		}
		$tmpl = new Tmpl22($tmpl_dir . "chat/mail_end.html");
		$tmpl->dbgmode(0);
		$tmpl->assign("msg",$msg);
		
		$tmpl->assign("hash",$_POST['id_2']);
		$tmpl->flush();
	}

?>
